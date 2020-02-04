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

          $contents = '<table class="table table-bordered table-hover text-center table-light passwords">';
          $contents .= '<thead><tr><th>有效期限</th><th>密码原文</th></tr></thead>';
          $contents .= '<tbody>';

          $rows = $result->fetch_all(MYSQLI_ASSOC);
          foreach ($rows as $row) {
            $password = $row['password'];
            $expirationDate = date('Y年n月j日 H:i', strtotime($row['expiration_date']));
            if (password_verify($password, $row['password_hash'])) {
              $contents .= '<tr class="table-primary"><td><b>';
              $contents .= $expirationDate;
              $contents .= '</b></td><td><b>';
              $contents .= $password;
              $contents .= '</b></td></tr>';
              break;
            }
          }

          $selectSQL = 'SELECT password, expiration_date FROM history WHERE username = "'.$username.'" order by expiration_date desc limit 5;';
          $result = $db->query($selectSQL);
          $rows = $result->fetch_all(MYSQLI_ASSOC);
          $firstRowFlag = true;
          foreach ($rows as $row) {
            $password = $row['password'];
            $expirationDate = date('Y年n月j日 H:i', strtotime($row['expiration_date']));
            if ($firstRowFlag) {
              $firstRowFlag = false;
              $contents .= '<tr class="table-warning"><td>';
            } else {
              $contents .= '<tr><td>';
            }
            $contents .= $expirationDate;
            $contents .= '</td><td>';
            $contents .= $password;
            $contents .= '</td></tr>';
          }

          $contents .= '</tbody></table>';

          $result->free();
          $db->close();
          return $contents;

        } else {
          header('Location:index.php');
        }
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
