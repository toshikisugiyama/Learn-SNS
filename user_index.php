<?php 
  session_start();
  // DB接続
  require('dbconnect.php');
  require('function.php');
  // ユーザーの一覧を表示するため取得する

  $signin_user = get_signin_user($dbh,$_SESSION["id"]);
  // SQL文作成
  $sql = 'SELECT * FROM `users`';

  // SQL実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute();

  // 繰り返し文の中でフェッチ(配列に保存)
  while (true) {
    $record = $stmt->fetch(PDO::FETCH_ASSOC);



    if ($record == false) {
      break;
    }

// つぶやき数を取得するSQL文を作成
    $feed_sql = "SELECT COUNT(*) AS `feed_cnt` FROM `feeds` WHERE `user_id`=?";
    //今回は$record["id"]はusers.idです。
    $feed_data = array($record["id"]);
    // SQL文を実行
    $feed_stmt = $dbh->prepare($feed_sql);
    $feed_stmt->execute($feed_data);
    // つぶやき数を取得
    $feed = $feed_stmt->fetch(PDO::FETCH_ASSOC);
    // $feed = array("feed_cnt"=>3);

    $record["feed_cnt"] = $feed["feed_cnt"];

    $users[] = $record;
  }

  // ↓データ保存した配列を表示で使用する




 ?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Learn SNS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body style="margin-top: 60px; background: #E4E6EB;">
    <?php include('navbar.php'); ?>

  <div class="container">
    <?php foreach ($users as $user) { ?>

    <div class="row">
      <div class="col-xs-12">

        
          <div class="thumbnail">
            <div class="row">
              <div class="col-xs-1">
                <img src="user_profile_img/<?php echo $user["img_name"] ?>" width="80">
              </div>
              <div class="col-xs-11">
                名前 <?php echo $user["name"]; ?><br>
                <a href="profile.php?user_id=<?php echo $user["id"]; ?>" style="color: #7F7F7F;"><?php echo $user["created"]; ?>からメンバー</a>
              </div>
            </div>
            
            <div class="row feed_sub">
              <div class="col-xs-12">
                <span class="comment_count">つぶやき数 : <?php echo $user["feed_cnt"] ?></span>
              </div>
            </div>
          </div><!-- thumbnail -->
      </div><!-- class="col-xs-12" -->
    </div><!-- class="row" -->
    <?php } ?>
  </div><!-- class="cotainer" -->
  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>
</html>