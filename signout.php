<?php 
    session_start();

    //ブラウザから$_SESSIONを削除
    $_SESSION = [];
    //サーバー内から削除
    session_destroy();


    header("Location: signin.php");
    exit();
 ?>