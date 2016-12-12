<?php
  if (isset($_GET['search'])) {
    if (strpos($_GET['search'], '"') !== false || strpos($_GET['search'], '\\') !== false) {
      $command = 'Quotes and backslashes are not allowed !';
    } else {
      $command = 'grep -i "' . $_GET['search'] . '" items.txt';
      exec($command, $result);
      $result = implode("\n", $result);
    }
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
      <form method="GET">
        <div class="form-group">
          <label for="search">Search</label>
          <input type="text" name="search" class="form-control" id="search" />
        </div>

        <button type="submit" class="btn btn-primary">Search</button>
        <br /><br />
        <?php if ($command) { ?><div class="alert alert-info"><?php echo htmlentities($command); ?></div> <?php } ?>
        <br /><br />
        <?php if (isset($result)) { ?>
        <pre><?php echo $result; ?></pre>
        <?php } ?>
      </form>
    </div>
  </body>
</html>