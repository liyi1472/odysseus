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

    // 生成密码
    function genPass($bit) {
      $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $pass = array();
      $alphaLength = strlen($alphabet) - 1;
      for ($i = 0; $i < $bit; $i++) {
          $code = rand(0, $alphaLength);
          $pass[] = $alphabet[$code];
      }
      return implode($pass);
    }

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

      $selectSQL = 'SELECT expiration_date FROM odyssey WHERE username = "'.$username.'" AND expiration_date > NOW();';
      $result = $db->query($selectSQL);
      $row = $result->fetch_assoc();
      if (isset($row['expiration_date'])) {
        echo($row['expiration_date']);
        $result->free();
        $db->close();
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

      $deleteSQL = 'DELETE FROM odyssey WHERE username = "'.$username.'";';
      mysqli_query($db, $deleteSQL);

      $password = genPass(8);
      $passHash = password_hash($password, PASSWORD_BCRYPT);

      $period = 4;
      if (isset($_POST['period']) && is_numeric($_POST['period'])) {
        $period = $_POST['period'];
      }
      $expirationDate = date('Y-m-d H:i:s', strtotime('+ '.$period.' hours'));

      $data = ["('{$username}', '{$token}', '{$password}', '{$passHash}', '{$expirationDate}')"];
      $count = 0;
      while ($count < 99) {
        $newPass = genPass(8);
        if (!in_array($newPass, $data)) {
          $data[] = "('{$username}', '{$token}', '{$newPass}', '{$passHash}', '{$expirationDate}')";
          $count++;
        }
      }

      $insertSQL = 'INSERT INTO odyssey (username, token, password, password_hash, expiration_date) VALUES '.join(', ', $data).';';
      mysqli_query($db, $insertSQL);

      echo($password);

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
