<?php
if (!isset($_COOKIE['admin'])) {
  setcookie('admin', 'false');
}
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <br /><br />
      <?php if (isset($_COOKIE['admin']) && $_COOKIE['admin'] === 'true') { ?>
        <div class="alert alert-success"><?php echo file_get_contents('.flag'); ?></div>
      <?php } ?>

      <?php if (isset($_POST['password'])) { ?>
        <div class="alert alert-danger">Wrong password !</div>
      <?php } ?>
      <br />
      <form method="POST">
        <div class="form-group">
          <label for="password">Password</label>
          <input type="text" name="password" class="form-control" id="password" />
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
      </form>
    </div>
  </body>
</html>