<html lang="en">
  <head>
    <meta charset="utf-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>
    <div class="container">
      <h3>Page Viewer</h3>
      <form method="GET">
        <div class="form-group">
          <label for="page">Page name</label>
          <select name="page" class="form-control" id="page">
            <option value="page1">Page 1</option>
            <option value="page2">Page 2</option>
            <option value="page3">Page 3</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary">View</button>
      </form>

      <br />
      <?php if (isset($_GET['page'])) { ?>
      <pre><?php include_once($_GET['page'] . '.php'); ?></pre>
      <?php } ?>
    </div>
  </body>
</html>
