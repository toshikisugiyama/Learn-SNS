<?php 

//session変数を使えるようにする
session_start();
//DBに接続
require('dbconnect.php');
//feed_idを取得
$feed_id = $_GET["feed_id"];
//SQL文作成(INSERT文)
$sql = "INSERT INTO `likes` (`id`, `user_id`, `feed_id`) VALUES (NULL, ?, ?);";
//SQL実行
$data = array($_SESSION['id'], $feed_id);
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
//一覧に戻る
header("Location: timeline.php");
 ?>