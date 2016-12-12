<?php
  if (isset($_FILES['image'])) {
    $folderid = bin2hex(random_bytes(16));
    $uploaddir = '/var/www/html/uploads/' . $folderid . '/';
    mkdir($uploaddir);
    $uploadfile = $uploaddir . basename($_FILES['image']['name']);

    if(is_array(getimagesize($_FILES['image']['tmp_name']))) {
      if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
        $text = 'Image uploaded to "/upload-2/uploads/' . $folderid . '/' . basename($_FILES['image']['name']). '".';
      } else {
        $error = 'Error while uploading file.';
      }
    } else {
      $error = 'File is not an image.';
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
      <h3>Image upload</h3>
      <br />
      <?php if (isset($text)) { ?><div class="alert alert-success"><?php echo $text; ?></div><?php } ?>
      <?php if (isset($error)) { ?><div class="alert alert-danger"><?php echo $error; ?></div><?php } ?>
      <br />
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="image">Your image</label>
          <input type="file" name="image" id="image" class="form-control"  /> 
        </div>

        <button type="submit" class="btn btn-primary">Upload</button>
      </form>
    </div>
  </body>
</html>