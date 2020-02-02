<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>奥德修斯之钥 | 23-6.site</title>
  <link rel="icon" href="favicon.png">
  <link rel="apple-touch-icon-precomposed" href="favicon.png">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/master.css">
</head>

<body style="font-size: 50px;">
  <?php

    // 主函数
    if (isset($_POST['username']) && $_POST['username']
      && isset($_POST['token']) && $_POST['token']) {

      $config = file_get_contents(__DIR__ . '/config.json');
      if ($config === false) {
        die('CONFIG.JSON NOT FOUND');
      }
      $config = json_decode($config, true);
      $db = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']);
      if (mysqli_connect_errno()) {
        die(mysqli_connect_error());
      }

      $username = $_POST['username'];
      $token = $_POST['token'];

      $selectSQL = 'SELECT token, expiration_date, password, password_hash FROM odyssey WHERE username = "'.$username.'" AND expiration_date > NOW();';
      $result = $db->query($selectSQL);
      $row = $result->fetch_assoc();
      if (isset($row['expiration_date'])) {
        $result->free();
        $db->close();
        echo($row['expiration_date']);
        return false;
      }

      $selectSQL = 'SELECT token FROM odyssey WHERE username = "'.$username.'";';
      $result = $db->query($selectSQL);
      $row = $result->fetch_assoc();
      if (isset($row['token']) && $row['token'] != $token) {
        echo('WRONG TOKEN');
        $result->free();
        $db->close();
        return false;
      }

      $selectSQL = 'SELECT password, password_hash FROM odyssey WHERE username = "'.$username.'";';
      $result = $db->query($selectSQL);
      $rows = $result->fetch_all(MYSQLI_ASSOC);
      foreach ($rows as $row) {
        $password = $row['password'];
        if (password_verify($password, $row['password_hash'])) {
          echo($password);
          break;
        }
      }

      $result->free();
      $db->close();
      return true;

    } else {
      header('Location:index.html');
    }

  ?>
  <script src="js/jquery.min.js"></script>
  <script src="js/master.js"></script>
</body>

</html>
