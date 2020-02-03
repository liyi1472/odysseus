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

    // 主函数
    if (isset($_POST['username']) && $_POST['username']
      && isset($_POST['token']) && $_POST['token']) {

      $config = file_get_contents(__DIR__ . '/config.json');
      if ($config === false) {
        echo('<div class="danger">');
        die('CONFIG.JSON NOT FOUND');
        echo('</div>');
      }
      $config = json_decode($config, true);
      if (!($config['DB_HOST'] && $config['DB_USER'] && $config['DB_PASS'] && $config['DB_NAME'])) {
        echo('<div class="danger">');
        die('CONFIG.JSON NOT VALID');
        echo('</div>');
      }
      $db = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']);
      if (mysqli_connect_errno()) {
        echo('<div class="danger">');
        die(mysqli_connect_error());
        echo('</div>');
      }

      $username = $_POST['username'];
      $token = $_POST['token'];

      $selectSQL = 'SELECT expiration_date FROM odyssey WHERE username = "'.$username.'" AND expiration_date > NOW();';
      $result = $db->query($selectSQL);
      $row = $result->fetch_assoc();
      if (isset($row['expiration_date'])) {
        echo('<div class="warning">');
        echo(date('Y年n月j日 H:i', strtotime($row['expiration_date'])));
        echo('</div>');
        $result->free();
        $db->close();
        return false;
      }

      $selectSQL = 'SELECT token, password, password_hash, expiration_date, expiration_date_hash FROM odyssey WHERE username = "'.$username.'";';
      $result = $db->query($selectSQL);
      $row = $result->fetch_assoc();
      if (isset($row['token'])) {
        if ($row['token'] != $token) {
          echo('<div class="danger">');
          echo('WRONG TOKEN');
          echo('</div>');
          $result->free();
          $db->close();
          return false;
        } elseif (!password_verify($row['expiration_date'], $row['expiration_date_hash'])) {
          echo('<div class="danger">');
          echo('WRONG EXPIRATION DATE');
          echo('</div>');
          $result->free();
          $db->close();
          return false;
        }

        echo('<table class="table table-bordered table-hover text-center table-light passwords">');
        echo('<thead><tr><th>有效期限</th><th>密码原文</th></tr></thead>');
        echo('<tbody>');

        $rows = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $row) {
          $password = $row['password'];
          $expirationDate = date('Y年n月j日 H:i', strtotime($row['expiration_date']));
          if (password_verify($password, $row['password_hash'])) {
            echo('<tr class="table-primary"><td><b>');
            echo($expirationDate);
            echo('</b></td><td><b>');
            echo($password);
            echo('</b></td></tr>');
            break;
          }
        }

        $selectSQL = 'SELECT password, expiration_date FROM history WHERE username = "'.$username.'" order by expiration_date desc limit 10;';
        $result = $db->query($selectSQL);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $firstRowFlag = true;
        foreach ($rows as $row) {
          $password = $row['password'];
          $expirationDate = date('Y年n月j日 H:i', strtotime($row['expiration_date']));
          if ($firstRowFlag) {
            $firstRowFlag = false;
            echo('<tr class="table-warning"><td>');
          } else {
            echo('<tr><td>');
          }
          echo($expirationDate);
          echo('</td><td>');
          echo($password);
          echo('</td></tr>');
        }

        echo('</tbody></table>');

        $result->free();
        $db->close();
        return true;
      } else {
        header('Location:index.php');
      }
    } else {
      header('Location:index.php');
    }

  ?>
  <script src="js/jquery.min.js"></script>
  <script src="js/master.js"></script>
</body>

</html>
