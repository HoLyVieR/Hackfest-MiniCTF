<html lang="en">
  <head>
    <meta charset="utf-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
  </head>

  <script>
    function validate() {
      var username = document.getElementById("username").value;
      var password = document.getElementById("password").value;
      var tpassword = "";

      for (var i=0; i<password.length; i++) {
        tpassword += String.fromCharCode(password.charCodeAt(i) - 1);
      }

      good = username == "admin" && tpassword == "password";

      if (!good) {
        alert("Wrong username / Password !");
      }

      return good;
    }
  </script>

  <body>
    <div class="container">
      <br /><br />
      <?php if (isset($_POST['username']) && $_POST['username'] === 'admin' && isset($_POST['password']) && $_POST['password'] === 'qbttxpse') { ?>
        <div class="alert alert-success"><?php echo file_get_contents('.flag'); ?></div>
      <?php } ?>
      <br />
      <form method="POST" onsubmit="return validate();">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" name="username" class="form-control" id="username" />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="text" name="password" class="form-control" id="password" />
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
      </form>
    </div>
  </body>
</html>