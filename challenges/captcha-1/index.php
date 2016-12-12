<?php
  if (isset($_POST['state']) && isset($_POST['value'])) {
    $state = json_decode(base64_decode($_POST['state']));

    if ($state->solution === $_POST['value']) {
      $good = true;
    } else {
      $good = false;
    }
  }

  $solution = bin2hex(random_bytes(16));
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>
    <div class="container">
      <br />
      <h3>Prove that you are a robot !</h3>
      <p>
        SHA1(?) = <?php echo sha1($solution); ?>
      </p>
      <?php if ($good === true) { ?>
        <div class="alert alert-success"><?php echo file_get_contents('.flag'); ?></div>
      <?php } ?>
      <?php if ($good === false) { ?>
        <div class="alert alert-danger">Wrong value !</div>
      <?php } ?>
      <form method="POST">
        <input type="hidden" name="state" value="<?php echo base64_encode(json_encode(array('solution' => $solution))) ?>" />

        <div class="form-group">
          <label for="value">Captcha</label>
          <input type="text" name="value" class="form-control" id="value" />
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
  </body>
</html>
