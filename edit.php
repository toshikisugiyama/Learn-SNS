<?php
  require('dbconnect.php');
  //feed_idを取得
  $feed_id = $_GET["feed_id"];

  //更新ボタンが押されたら(POST送信されたデータが存在したら)
  if (!empty($_POST) ) {
    //Update文でDBに保存
    //UPDATE テーブル名 SET カラム名=値(,カラム名2＝値2) WHERE 条件;
    // UPDATE `feeds` SET `feed` = 'どこにいるの？' WHERE `feeds`.`id` = 6;

    $update_sql = "UPDATE`feeds` SET `feed` = ? WHERE `feeds`.`id` = ?";

    $data = array($_POST["feed"],$feed_id);

    $stmt = $dbh->prepare($update_sql);
    $stmt->execute($data);
    //一覧に戻る
    header("Location: timeline.php");
  }
  //編集したいfeeds tableのデータを取得して、入力欄に初期表示しましょう

  //ポイント
  //書いた人の情報も表示したいので、テーブル結合を使う(timelineと同じもの)
  //編集したいfeeds tableのデータは一件だけです(繰り返し処理は必要ないよ)

  //SQL文作成
  //"""(ダブルクオーテーション)で変数を囲んで中身を表示する方法：変数展開
  //ambiguous: あいまい
    $sql = "SELECT feeds.*, `users`.`name`, `users`.img_name FROM feeds LEFT JOIN users ON `feeds`.`user_id`=`users`.`id` WHERE `feeds`.`id`=$feed_id";

  //SQL文実行
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
  //フェッチ(データ取得)
    $feed = $stmt->fetch(PDO::FETCH_ASSOC);
  //↓HTML内にデータ表示の処理を記述

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
<body style="margin-top: 60px">
    <div class="container">
      <div class="row">
        <!-- ここにコンテンツ -->
        <div class="col-xs-4 col-xs-offset-4">
          <form class="form-group" method="post">
            <img src="user_profile_img/<?php echo $feed["img_name"] ?>" width="60">
            <?php echo $feed["name"] ?><br>
            <?php echo $feed["created"] ?><br>
            <textarea name="feed" class="form-control"><?php echo $feed["feed"] ?></textarea>
            <input type="submit" value="更新" class="btn btn-warning btn-xs">
          </form>
        </div>
      </div>
    </div>

  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>
</html>