import os
import sys
import subprocess
import OpenSSL
import json
import random

WD = os.path.dirname(os.path.realpath(__file__))
CERT_FILE = "selfsigned.crt"
KEY_FILE = "selfsigned.key"

def docker_image_exists(image_name):
	p = subprocess.Popen(["docker", "images", "-q", image_name], stdout=subprocess.PIPE)
	output = p.communicate()[0]
	return len(output) > 2

def mkdir(directory):
	if not os.path.exists(directory):
		os.makedirs(directory)

def get_directories(path):
	dirs = [os.path.join(path, item) for item in os.listdir(path)]
	if "." in dirs:
		dirs.remove(".")
	if ".." in dirs:
		dirs.remove("..")
	return dirs

def build_compose_file(imagename, path, id):
	content = """
web:
  image: %s
  ports:
    - "127.0.0.1:%i:80"
  volumes:
    - %s:/var/www/html"""

	return id + 1500, content % (imagename, id + 1500, path)

def create_self_signed_cert(cert_dir):
    if not os.path.exists(os.path.join(cert_dir, CERT_FILE)) or not os.path.exists(os.path.join(cert_dir, KEY_FILE)):
        # create a key pair
        k = OpenSSL.crypto.PKey()
        k.generate_key(OpenSSL.crypto.TYPE_RSA, 2048)

        # create a self-signed cert
        cert = OpenSSL.crypto.X509()
        cert.get_subject().C = "CA"
        cert.get_subject().ST = "AutoConf"
        cert.get_subject().L = "AutoConf"
        cert.get_subject().O = "AutoConf"
        cert.get_subject().OU = "NSA"
        cert.get_subject().CN = "AutoConf"
        cert.set_serial_number(random.randint(0, 2<<160 - 1))
        cert.gmtime_adj_notBefore(0)
        cert.gmtime_adj_notAfter(1*365*24*60*60)
        cert.set_issuer(cert.get_subject())
        cert.set_pubkey(k)
        cert.sign(k, 'sha256')

        open(os.path.join(cert_dir, CERT_FILE), "wt").write(OpenSSL.crypto.dump_certificate(OpenSSL.crypto.FILETYPE_PEM, cert))
        open(os.path.join(cert_dir, KEY_FILE), "wt").write(OpenSSL.crypto.dump_privatekey(OpenSSL.crypto.FILETYPE_PEM, k))

if len(sys.argv) < 3:
	print "Usage %s [directory] [hostname]"
	exit()

print "Checking for docker images ..."
if not docker_image_exists("php:7.0-apache") or not docker_image_exists("php:7.0-apache-phantombot"):
	print "Installing php:7.0-apache ..."
	subprocess.call(["docker", "load", "-i", os.path.join(WD, "../docker-images/php_apache_7.0")])
	
	print "Installing php:7.0-apache-phantombot ..."
	subprocess.call(["docker", "load", "-i", os.path.join(WD, "../docker-images/php_apache_7.0_phantombot")])


challenges = get_directories(sys.argv[1])
challenge_id = 0

apache_configuration = """
<VirtualHost *:80>
	ServerAdmin webmaster@localhost
	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined

	ProxyRequests Off
	ProxyPreserveHost On
	%s
</VirtualHost>
"""

apache_proxy_configuration = """
	ProxyPass /%s http://127.0.0.1:%d/ retry=0 timeout=5
	ProxyPassReverse /%s http://127.0.0.1:%d/
	"""

apache_tmp = ""
ctfd_json_data = '{ "challenges" : [ %s ] }'
ctfd_json_data_tmp = ""

for challenge in challenges:
	print "Setting up '%s' ..." % challenge
	
	challenge_id += 1
	uniq_name = os.path.basename(challenge)
	compose_dir = os.path.join(WD + "/challenges/", uniq_name)
	mkdir(compose_dir)

	# Take down previous docker
	os.chdir(compose_dir)
	subprocess.call(["docker-compose", "stop"])

	# Build a new compose file
	image_name = "php:7.0-apache"
	image_name_override = os.path.join(challenge, ".baseimage")
	if os.path.exists(image_name_override):
		image_name = open(image_name_override, "rb").read().strip()

	port, compose_content = build_compose_file(image_name, challenge, challenge_id)
	open(os.path.join(compose_dir, "docker-compose.yml"), "wb").write(compose_content)

	# Start it
	subprocess.call(["docker-compose", "up", "-d"])

	apache_tmp += apache_proxy_configuration % (uniq_name, port, uniq_name, port)

	# Configure the challenge data
	name = json.dumps(open(os.path.join(challenge, ".name"), "rb").read().strip())
	category = json.dumps(open(os.path.join(challenge, ".category"), "rb").read().strip())
	description = open(os.path.join(challenge, ".description"), "rb").read().strip()
	value = int(open(os.path.join(challenge, ".value"), "rb").read().strip())
	flag = json.dumps(open(os.path.join(challenge, ".flag"), "rb").read().strip())

	description += "<br /><br /><a href='http://%s/%s/'>Challenge</a>." % (sys.argv[2], uniq_name)
	description = json.dumps(description)

	ctfd_challenge_template = '{ "name" : %s, "category" : %s, "message" : %s, "value" : %d, "key" : [ { "flag" : %s, "type" : 0 } ], "files" : [] }'
	ctfd_json_data_tmp += ctfd_challenge_template % (name, category, description, value, flag)
	ctfd_json_data_tmp += ","

ctfd_json_data_tmp = ctfd_json_data_tmp[:-1]

# Write new Apache configuration and restart
print "Setting up the HTTP Apache configuration ..."
open("/etc/apache2/sites-available/000-default.conf", "wb").write(apache_configuration % apache_tmp)
subprocess.call(["service", "apache2", "restart"])

# Configure SSL CTFd
print "Setting up the HTTPS Apache configuration ..."
mkdir("/etc/apache2/ssl/")
create_self_signed_cert("/etc/apache2/ssl/")

apache_configuration_ssl = """
<VirtualHost *:443>
	SSLEngine On
	SSLCertificateFile /etc/apache2/ssl/selfsigned.crt
	SSLCertificateKeyFile /etc/apache2/ssl/selfsigned.key

	ProxyRequests Off
	ProxyPreserveHost On
	ProxyPass / http://127.0.0.1:4000/ retry=0 timeout=5
	ProxyPassReverse / http://127.0.0.1:4000/

	RequestHeader set X-Scheme https
	RequestHeader set X-Forwarded-Proto 'https'

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
"""

open("/etc/apache2/sites-available/default-ssl.conf", "wb").write(apache_configuration_ssl)
subprocess.call(["a2ensite", "default-ssl.conf"]) 
subprocess.call(["service", "apache2", "reload"])
subprocess.call(["service", "apache2", "restart"])

print "Configuring CTFd ..."
os.chdir(os.path.join(WD, "../CTFd/"))
open("../CTFd-import/data.json", "wb").write(ctfd_json_data % ctfd_json_data_tmp)
subprocess.call(["python", "import.py", "../CTFd-import/data.json"])

open("serve.py", "wb").write("""
from CTFd import create_app

class ReverseProxied(object):
    def __init__(self, app):
        self.app = app

    def __call__(self, environ, start_response):
        script_name = environ.get('HTTP_X_SCRIPT_NAME', '')
        if script_name:
            environ['SCRIPT_NAME'] = script_name
            path_info = environ['PATH_INFO']
            if path_info.startswith(script_name):
                environ['PATH_INFO'] = path_info[len(script_name):]

        scheme = environ.get('HTTP_X_SCHEME', '')
        if scheme:
            environ['wsgi.url_scheme'] = scheme
        return self.app(environ, start_response)

app = create_app()
app.wsgi_app = ReverseProxied(app.wsgi_app)
app.run(debug=False, threaded=True, host="127.0.0.1", port=4000)
""")

print "Starting CTFd ..."
mkdir("CTFd/logs/")
subprocess.call(["/bin/bash", "-c", "python serve.py &>> CTFd/logs/out.log &"])



