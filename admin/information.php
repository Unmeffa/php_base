<?php
include("include/variable.php");
include('scripts/handleInformation.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title><?= $information->get("name") ?> - Administration</title>
  <link href="css/styles.css" rel="stylesheet">
</head>

<body class="min-h-full">
  <?php include("include/menu.php") ?>
  <?php include("include/information.php") ?>
</body>

</html>