# Hackfest Mini-CTF 2016

This repository contains the setup script and the challenges of the OWASP Mini-CTF 2016 presented at Hackfest.

## Setup 

### Base 

```
# This should install most of the required package
cd deploy
sh package.sh

# This should do the Apache configuration for the scoreboard and the challenges
python deploy-folder.py ABSOLUTE_PATH_TO_CHALLENGE_FOLDER DOMAIN_NAME

# Fix permission for some challenges
cd ../challenges/
sh ../deploy/fix-www-data-permission.sh
```

### Optional

For SSL, Let's Encrypt works pretty well with the setup. Just use the basic profile (keep HTTP, it's required for the challenges).

https://www.digitalocean.com/community/tutorials/how-to-secure-apache-with-let-s-encrypt-on-ubuntu-16-04