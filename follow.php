<?php 

//session変数を使えるようにする
session_start();
//DBに接続
require('dbconnect.php');
//feed_idを取得
$follower_id = $_GET["follower_id"];

var_dump($_SESSION);
var_dump($follower_id);
//SQL文作成(INSERT文)
$sql = "INSERT INTO `followers` (`id`, `user_id`, `follower_id`) VALUES (NULL, ?, ?);";
//SQL実行
$data = array($_SESSION['id'], $follower_id);
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
//一覧に戻る
header("Location: profile.php");
 ?>