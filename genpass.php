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

<body>
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

    function main() {
      // 主函数
      if (isset($_POST['username']) && $_POST['username']
        && isset($_POST['token']) && $_POST['token']) {

        $config = file_get_contents(__DIR__ . '/config.json');
        if ($config === false) {
          $contents = '<div class="danger">';
          $contents .= 'CONFIG.JSON NOT FOUND';
          $contents .= '</div>';
          return $contents;
        }
        $config = json_decode($config, true);
        if (!($config['DB_HOST'] && $config['DB_USER'] && $config['DB_PASS'] && $config['DB_NAME'])) {
          $contents = '<div class="danger">';
          $contents .= 'CONFIG.JSON NOT VALID';
          $contents .= '</div>';
          return $contents;
        }
        $db = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']);
        if (mysqli_connect_errno()) {
          $contents = '<div class="danger">';
          $contents .= mysqli_connect_error();
          $contents .= '</div>';
          return $contents;
        }

        $username = $_POST['username'];
        $token = $_POST['token'];

        $selectSQL = 'SELECT expiration_date FROM odyssey WHERE username = "'.$username.'" AND expiration_date > NOW();';
        $result = $db->query($selectSQL);
        $row = $result->fetch_assoc();
        if (isset($row['expiration_date'])) {
          $contents = '<div class="warning">';
          $contents .= date('Y年n月j日 H:i', strtotime($row['expiration_date']));
          $contents .= '</div>';
          $result->free();
          $db->close();
          return $contents;
        }

        $selectSQL = 'SELECT token, password, password_hash, expiration_date, expiration_date_hash FROM odyssey WHERE username = "'.$username.'";';
        $result = $db->query($selectSQL);
        $row = $result->fetch_assoc();
        if (isset($row['token'])) {
          if ($row['token'] != $token) {
            $contents = '<div class="danger">';
            $contents .= 'WRONG TOKEN';
            $contents .= '</div>';
            $result->free();
            $db->close();
            return $contents;
          } elseif (!password_verify($row['expiration_date'], $row['expiration_date_hash'])) {
            $contents = '<div class="danger">';
            $contents .= 'WRONG EXPIRATION DATE';
            $contents .= '</div>';
            $result->free();
            $db->close();
            return $contents;
          }
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $row) {
          $password = $row['password'];
          $expirationDate = $row['expiration_date'];
          if (password_verify($password, $row['password_hash'])) {
            $data = ["('{$username}', '{$password}', '{$expirationDate}')"];
            $insertSQL = 'INSERT INTO history (username, password, expiration_date) VALUES '.join(', ', $data).';';
            mysqli_query($db, $insertSQL);
            break;
          }
        }

        $deleteSQL = 'DELETE FROM odyssey WHERE username = "'.$username.'";';
        mysqli_query($db, $deleteSQL);

        $password = genPass(8);
        $passHash = password_hash($password, PASSWORD_BCRYPT);

        $expirationDate = date('Y-m-d 07:30:00', strtotime('+1 day'));
        if (date('Hi') < '0730') {
          $expirationDate = date('Y-m-d 07:30:00');
        }
        if (isset($_POST['period']) && is_numeric($_POST['period'])) {
          $expirationDate = date('Y-m-d H:i:s', strtotime('+'.$_POST['period'].' hours'));
        }
        $expirationDateHash = password_hash($expirationDate, PASSWORD_BCRYPT);

        $data = ["('{$username}', '{$token}', '{$password}', '{$passHash}', '{$expirationDate}', '{$expirationDateHash}')"];
        $count = 1;
        while ($count < 50) {
          $newPass = genPass(8);
          if (!in_array($newPass, $data)) {
            $data[] = "('{$username}', '{$token}', '{$newPass}', '{$passHash}', '{$expirationDate}', '{$expirationDateHash}')";
            $count++;
          }
        }
        shuffle($data);

        $insertSQL = 'INSERT INTO odyssey (username, token, password, password_hash, expiration_date, expiration_date_hash) VALUES '.join(', ', $data).';';
        mysqli_query($db, $insertSQL);

        $contents = '<div class="primary">';
        $contents .= $password;
        $contents .= '</div>';

        $result->free();
        $db->close();
        return $contents;

      } else {
        header('Location:index.php');
      }
    }

    echo main();

  ?>
  <script src="js/jquery.min.js"></script>
  <script src="js/master.js"></script>
</body>

</html>
