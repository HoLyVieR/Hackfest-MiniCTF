<?php
  $xpath = '';
  $items = array();

  if (isset($_GET['q'])) {
    $xpath = "/all/items/item[contains(@name, '" . $_GET['q'] . "')]";
    $xml = new SimpleXMLElement(file_get_contents('e4e0e681682368b1.xml'));
    $result = $xml->xpath($xpath);

    while(list( , $node) = each($result)) {
      $attr = $node->attributes();
      $item = array();
      foreach($attr as $val) {
        $item[] = $val;
      }
      $items[] = $item;
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
          <label for="q">Search</label>
          <input type="text" name="q" class="form-control" id="q" />
        </div>

        <button type="submit" class="btn btn-primary">Search</button>
        <br /><br />
        <?php if ($xpath) { ?><div class="alert alert-info"><?php echo htmlentities($xpath); ?></div> <?php } ?>
        <br /><br />
        <table class="table table-striped">
          <tr>
            <th>Name</th><th>Description</th>
          </tr>
          <?php 
            foreach ($items as $val) {
          ?>
            <tr>
              <td><?php echo $val[0] ?></td>
              <td><?php echo $val[1] ?></td>
            </tr>
          <?php 
            }
          ?>
        </table>
      </form>
    </div>
  </body>
</html>