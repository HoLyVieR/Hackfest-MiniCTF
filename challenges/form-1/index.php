<html lang="en">
  <head>
    <meta charset="utf-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>
    <div class="container">
      <br /><br />
      <?php if (isset($_GET['getflag'])) { ?>
        <div class="alert alert-success"><?php echo file_get_contents(".flag"); ?></div>
      <?php } ?>
      <br />
      <form method="GET">
        <button type="submit" name="getflag" value="1" class="btn-lg btn btn-primary " style="position: absolute;" onmouseover="this.style.top = (window.innerHeight || document.body.clientHeight)*Math.random(); this.style.left = (window.innerWidth || document.body.clientWidth)*Math.random(); ">Click me !</button>
      </form>
    </div>
  </body>
</html>