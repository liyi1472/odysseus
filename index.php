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

  <form action="genpass.php" method="post">
    <label>用户名</label>
    <input type="text" name="username" value="liyi1472">
    <br>
    <label>令　牌</label>
    <input type="password" name="token" value="">
    <br>
    <label>有效期</label>
    <select name="period">
      <option value="1">1小时</option>
      <option value="2">2小时</option>
      <option value="4" selected>4小时</option>
      <option value="6">6小时</option>
      <option value="8">8小时</option>
      <option value="12">12小时</option>
      <option value="24">24小时</option>
      <option value="48">48小时</option>
      <option value="72">72小时</option>
      <option value="120">120小时</option>
    </select>
    <br>
    <button type="submit" class="btn btn-primary btn-sm">生成密码</button>
  </form>

  <hr>

  <form action="getpass.php" method="post">
    <label>用户名</label>
    <input type="text" name="username" value="liyi1472">
    <br>
    <label>令　牌</label>
    <input type="password" name="token" value="">
    <br>
    <button type="submit" class="btn btn-primary btn-sm">解锁密码</button>
  </form>

  <script src="js/jquery.min.js"></script>
  <script src="js/master.js"></script>
</body>

</html>
