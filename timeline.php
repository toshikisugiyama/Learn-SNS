<?php
  // timeline.phpの処理を記載
    session_start();
    // var_dump($_SESSION);

    //require(dbconnect)
    // SELECT usersテーブルから　$_SESSIONに保存されているidを一件だけ取り出す
    //$signin_user に取り出したレコードを代入する
    //写真と名前をレコードから取り出す
    //$img_name に写真のファイル名を代入する
    //$nameに名前を代入する

    require('dbconnect.php');

    $sql = 'SELECT * FROM `users` WHERE `id`=?';
    $data = array($_SESSION['id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    $signin_user = $stmt->fetch(PDO::FETCH_ASSOC);

    $img_name = $signin_user['img_name'];
    $name = $signin_user['name'];

    // var_dump($signin_user);
    // echo $signin_user['name'];
    // echo $signin_user['img_name'];

    $errors = array();
    // ボタン押した時
    if (!empty($_POST)) {
      $feed = $_POST['feed'];
      // 空じゃない時
      if ($feed != '') {
        $sql = 'INSERT INTO `feeds` SET `feed`=?, `user_id` = ?, `created`= NOW()';
        $data = array($feed, $signin_user['id']);
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);

        header('Location: timeline.php');
        exit();

      }else{
        // 空の時　エラー処理
        $errors['feed'] = 'blank';
      }
    }


// pagingの処理

    $page = '';// ページ番号が入る変数
    $page_row_number = 5;// 1ページあたりに表示するデータの数
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    }else{
      // get送信されているページ数がない場合、1ページ目とみなす。
      $page = 1;
    }
    // これと同じことをしている関数
    // if ($page == 0) {
    //   $page = 1;
    // }
    // max: カンマ区切りで羅列された数字の中から最大の数を返す
    $page = max($page,1);

    // データの件数から、最大ページ数を計算する。
    $sql_count = "SELECT COUNT(*) AS `cnt` FROM `feeds`";

    // SQL実行
    $stmt_count = $dbh->prepare($sql_count);
    $stmt_count->execute();

    $record_cnt = $stmt_count->fetch(PDO::FETCH_ASSOC);

    // ページ数計算
    // ceil 小数点の切り上げができる関数 2.1 -> 3 に変換できる
    $all_page_number = ceil($record_cnt['cnt'] / $page_row_number);


    // 不正に大きい数字を指定された場合、最大ページ番号に変換
    // これと同じことができる関数
    // if ($page > $all_page_number) {
    //   $page = $all_page_number;
    // }
    // min: カンマ区切りの数字の中から最小の数値を取得する関数
    $page = min($page,$all_page_number);

    // データを取得する開始番号を計算
    $start = ($page -1)*$page_row_number;

    //検索ボタンが押されたら、あいまい検索
    //検索ボタンが押された=GET送信されたsearch_wordというキーのデータがある
    if (isset($_GET['search_word']) == 1) {
        //あいまい検索用SQL(like演算子)
        $sql = 'SELECT `feeds`.*,`users`.`name`,`users`.`img_name` FROM `feeds` LEFT JOIN `users` ON `feeds`.`user_id`=`users`.`id` WHERE `feeds`.`feed` LIKE "%'.$_GET['search_word'].'%"';
    }else{
    //通常(検索ボタンを押していない時)は全件取得

    //LEFT JOINで全件取得
    $sql = "SELECT `feeds`.*,`users`.`name`,`users`.`img_name` FROM `feeds` LEFT JOIN `users` ON `feeds`.`user_id`=`users`.`id` WHERE 1 ORDER BY `feeds`.`created` DESC LIMIT $start, $page_row_number" ;
   }

    $data = array();
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    //excuteで取得したタイミングではObject型
    //Array型に変換する必要がある
    //PDOでは、fetch()を使用する
    //fetchで取得出来るデータは１レコードずつ
    //1 fetch 1record
    //fetchするごとに次のレコードを指定する


/*$record = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($record);
echo "</pre>";


$record = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($record);
echo "</pre>";


$record = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($record);
echo "</pre>";


exit();*/






    // 表示用の配列を初期化
    $feeds = array();

    while (true) {
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record == false) {
            break;
        }

        // comment テーブルから今取得できているfeedに対してのデータを取得
        $comment_sql = "SELECT `c`. *,`u`.`name`,`u`.`img_name` FROM `comments` AS `c` LEFT JOIN `users` AS`u` ON `c`.`user_id` =`u`.`id` WHERE `feed_id` = ?";
        $comment_data = array($record["id"]);

        // sql実行
        $comment_stmt = $dbh->prepare($comment_sql);
        $comment_stmt->execute($comment_data);
        // コメントを格納するための変数
        $comments_array = array();

        while (true) {
          $comment_record = $comment_stmt->fetch(PDO::FETCH_ASSOC);
          if ($comment_record == false) {
            break;
          }
          // 取得したコメントのデータを追加代入(重要)
          $comments_array[] = $comment_record;
        }
        // 一行分の変数(連想配列)に、新しくcommentsというキーを追加し、コメント情報を代入(超重要!!)
        $record["comments"] = $comments_array;

        // like数を取得するSQL文を作成
        $like_sql = "SELECT COUNT(*) AS `like_cnt` FROM `likes` WHERE `feed_id`=?";
        $like_data = array($record["id"]);
        // SQL文を実行
        $like_stmt = $dbh->prepare($like_sql);
        $like_stmt->execute($like_data);
        // like数を取得
        $like = $like_stmt->fetch(PDO::FETCH_ASSOC);

        $record["like_cnt"] = $like["like_cnt"];


        //like済みか判断するSQLを作成
        $like_flag_sql = "SELECT COUNT(*) AS `like_flag` FROM `likes` WHERE `user_id`=? AND `feed_id`=?";

        $like_flag_data = array($_SESSION["id"], $record["id"]);

        //SQL実行
        $like_flag_stmt = $dbh->prepare($like_flag_sql);
        $like_flag_stmt->execute($like_flag_data);
        //likeしている数を取得
        $like_flag = $like_flag_stmt->fetch(PDO::FETCH_ASSOC);

        if ($like_flag["like_flag"] > 0) {
          $record["like_flag"] = 1;
        }else{
          $record["like_flag"] = 0;
        }

        // いいね済みのみのリンクが押されたときは、配列にいいね！しているものだけを代入する
        if (isset($_GET["feed_select"]) && ($_GET["feed_select"] == "likes") && ($record["like_flag"] == 1)) {
          $feeds[] = $record;
        }

        //feed_selectが指定されてないときは全件表示
        if (!isset($_GET["feed_select"]) || ($_GET["feed_select"] == "news")) {
          $feeds[] = $record;
        }

        // 新着順が押されたとき
        // if (isset($_GET["feed_select"]) && ($_GET["feed_select"] == "news")) {
        //   $feeds[] = $record;
        // }

    }

    // echo "<pre>";
    // var_dump($feeds);
    // echo "</pre>";
/*$c = count($feeds);*/
// for ($i=0; $i < $c; $i++) {
//   // echo $feeds[$i]['feed'];
//   // echo '<br>';
// }

// foreach ($feeds as $feed) {
  
// }





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
    <div class="row">
      <div class="col-xs-3">
        <ul class="nav nav-pills nav-stacked">
          <?php if (isset($_GET["feed_select"]) && ($_GET["feed_select"]) == "likes") { ?>
          <li><a href="timeline.php?feed_select=news">新着順</a></li>
          <li class="active"><a href="timeline.php?feed_select=likes">いいね！済み</a></li>
          <?php }else{ ?>
          <li class="active"><a href="timeline.php?feed_select=news">新着順</a></li>
          <li><a href="timeline.php?feed_select=likes">いいね！済み</a></li><?php } ?>
          <!-- <li><a href="timeline.php?feed_select=follows">フォロー</a></li> -->
        </ul>
      </div>
      <div class="col-xs-9">
        <div class="feed_form thumbnail">
          <form method="POST" action="">
            <div class="form-group">
              <textarea name="feed" class="form-control" rows="3" placeholder="Happy Hacking!" style="font-size: 24px;"></textarea><br>
              <?php if (isset($errors['feed']) && $errors['feed'] == 'blank') { ?>
                  <p class="alert alert-danger">投稿データを入力してください</p>
              <?php } ?>
            </div>
            <input type="submit" value="投稿する" class="btn btn-primary">
          </form>
        </div>

        <!-- 繰り返し -->
        <?php foreach ($feeds as $feed) { ?>
          <div class="thumbnail">
            <div class="row">
              <div class="col-xs-1">
                <img src="user_profile_img/<?php echo $feed['img_name']; ?>" width="40">
              </div>
              <div class="col-xs-11">
                <?php echo $feed['name']; ?><br>
                <a href="#" style="color: #7F7F7F;"><?php echo $feed['created']; ?></a>
              </div>
            </div>
            <div class="row feed_content">
              <div class="col-xs-12" >
                <span style="font-size: 24px;"><?php echo $feed['feed']; ?></span>
              </div>
            </div>
            <div class="row feed_sub">
              <div class="col-xs-12">


                <?php if ($feed["like_flag"] == 0) { ?>
                    <a href="like.php?feed_id=<?php echo $feed["id"]; ?>">
                      <button class="btn btn-default btn-xs"><i class="fa fa-thumbs-up" aria-hidden="true"></i>いいね！</button>
                    </a>
                <?php }else{ ?>
                    <a href="unlike.php?feed_id=<?php echo $feed["id"]; ?>">
                      <button class="btn btn-default btn-xs"><i class="fa fa-thumbs-down" aria-hidden="true"></i>いいね！を取り消す</button></a>
                <?php } ?>

                <?php if ($feed["like_cnt"] != 0) { ?>
                    <span class="like_count">いいね数 : <?php echo $feed["like_cnt"]; ?></span>
                <?php } ?>


                <a href="#collapseComment<?php echo $feed["id"]; ?>" data-toggle="collapse" aria-expanded="false">

                  <?php if ($feed["comment_count"] == 0) { ?>
                  <span class="comment_count">コメント</span>
                  <?php }else{ ?>
                  <span class="comment_count">コメント数：<?php echo $feed["comment_count"]; ?></span>
                  <?php } ?>

                </a>
                  <?php if ($feed["user_id"] == $_SESSION["id"]) { ?>
                  <a href="edit.php?feed_id=<?php echo $feed["id"]; ?>" class="btn btn-success btn-xs">編集</a>
                  <a onclick = "return confirm('ほんとに消すの？？？？？？？？？？？？？？');"href="delete.php?feed_id=<?php echo $feed["id"]; ?>" class="btn btn-danger btn-xs">削除</a>
                  <?php } ?>
              </div>

              <!-- コメントが押されたら表示される領域 -->
              <!-- <div class="collapse" id="collapseComment">
                表示の確認！
              </div> -->
              <?php include("comment_view.php"); ?>
            </div>
          </div>
          <?php } ?>
        <div aria-label="Page navigation">
          <ul class="pager">

            <?php if ($page <= 1) { ?>
            <li class="previous disabled"><a href="#"><span aria-hidden="true">&larr;</span> Newer</a></li>
            <?php }else{ ?>
            <li class="previous"><a href="timeline.php?page=<?php echo $page - 1 ?>"><span aria-hidden="true">&larr;</span> Newer</a></li>
            <?php } ?>

            <?php if ($page == $all_page_number) { ?>
            <li class="next disabled"><a href="#">Older <span aria-hidden="true">&rarr;</span></a></li>
            <?php }else{ ?>
            <li class="next"><a href="timeline.php?page=<?php echo $page + 1 ?>">Older <span aria-hidden="true">&rarr;</span></a></li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>
</html>
