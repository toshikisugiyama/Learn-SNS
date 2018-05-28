<?php 
    
  //DBに接続
  require('dbconnect.php');
  //feed_idを取得
  $feed_id = $_GET["feed_id"];
  //Delete文(SQL文)
  //DLETE FROM テーブル名  WHERE 条件; <-条件がないと、全部削除される
  $sql = "DELETE FROM `feeds` WHERE `feeds`.`id` = ?";
  //SQL実行
  $data = array($feed_id);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);

  //一覧に戻る
  header("Location: timeline.php");
 ?>