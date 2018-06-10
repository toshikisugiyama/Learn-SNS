<?php 

  // サインインしているユーザーの情報を取得して、返す関数
  // 引数＄dbh：データベース接続オブジェクト
  // 引数$user_id：サインインしているユーザーのid
  // 使い方はget_signin_user($dbh,$_SESSION["id"]);
  function get_signin_user($dbh,$user_id){
    $sql = 'SELECT * FROM `users` WHERE `id`=?';
    $data = array($_SESSION['id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    // $signin_userに取り出したレコードを代入する
    $signin_user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $signin_user;
  }

  function check_signin($user_id){
    if (!isset($_SESSION['id'])) {
      header("Location: signin.php");
      exit();// このタイミングで処理を中断する
    }
  }

 ?>