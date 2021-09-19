<HTML>
<HEAD>
<META HTTP-EQUIV="content-type" CONTENT="text/HTML;charset=utf-8">
<TITLE>写真のアップロード</TITLE>
</HEAD>
<BODY>

<?php
print <<<_HTML_
<FORM enctype="multipart/form-data" method="POST" action="$_SERVER[PHP_SELF]">
 アップロードするファイル：<INPUT type="file" name="upfile"> <BR>
 場所：<INPUT type="text" name="location"> <BR>
 お気に入り：
 <SELECT name="favorite">
 <OPTION value="true" selected>はい</OPTION>
 <OPTION value="false">いいえ</OPTION>
 </SELECT> <BR>
 <INPUT type="submit" value="アップロード">
 <INPUT type="reset" value="キャンセル">
</FORM>
_HTML_;
if (isset($_FILES['upfile']) && is_uploaded_file($_FILES['upfile']['tmp_name'])) {
  include('./db_login.php'); // データベースにログインするための情報を読み込む
  include('./exif_tool.php'); // exif情報を扱うための関数を読み込む
 
  date_default_timezone_set('Asia/Tokyo');
  $newname = date("YmdHis");
  $newname .= ".";
  $newname .= pathinfo($_FILES['upfile']['name'],PATHINFO_EXTENSION);
  if (! move_uploaded_file($_FILES['upfile']['tmp_name'], './photo/' . $newname)) {
  die("アップロードに失敗しました。<BR>");
  }
  // MySQLに接続する
  $dbconnect = mysqli_connect($host, $username, $passwd, $dbname);
  if (!$dbconnect) {
    die('データベースの接続に失敗しました。');
  }
  // utf-8形式の日本語の設定をする
  if (! mysqli_set_charset($dbconnect, "utf8")) {
  die("日本語の設定ができません: <BR>");
  } 
}
// exifから情報を取得する
$exif = exif_read_data("./photo/" . $newname, 0, true);
if (isset($exif)) { // exif情報があるかどうかをチェック
$datetime = get_datetime($exif); // exifから撮影日時を取得
$pdate = $datetime['year'] . "-" . $datetime['month'] . "-" . $datetime['day']; // 撮影日を$pdateに代入
$ptime = $datetime['hour'] . ":" . $datetime['minute'] . ":" . $datetime['second']; // 撮影時間を$ptimeに代入
if ($gps = get_gps($exif)) { // exifからgps情報を取得
$plocation = get_city($gps[0], $gps[1]); // gps情報から都市名を取得し、$plocationに代入
} else {
$plocation = "場所不明"; // gps情報がない場合、$plocationに「場所不明」と代入
}
} else { // exif情報がない場合の処理
$pdate = date("Y-m-d"); // exif情報がない場合、現在の日を$pdateに代入
$ptime = date("H:i:s"); // exif情報がない場合、現在の時刻を$ptimeに代入
$plocation = "場所不明"; // exif情報がない場合、$plocationに「場所不明」と代入
}
// 入力された撮影場所を登録する
if ($_POST['location']) { // フォームで撮影場所を入力しているかどうかをチェック
$plocation = $_POST['location']; // 撮影場所が入力されている場合、$plocationにその場所を代入
}

// アップロードされた日時を登録する
$udate = date("Y-m-d");
$utime = date("H:i:s");
// INSERT文を作成する
$query = "INSERT INTO `1717043` VALUES('" . $newname . "','" . $pdate . "','" . $ptime . "','". $plocation . "','" . $udate . "','"
. $utime . "'," . $_POST['favorite'] . ")";
 $result = mysqli_query($dbconnect, $query); // データベースへの書き込み
 if (! $result) {
 die("データベースにデータを保存できませんでした: <BR>");
 }
 print mysqli_affected_rows($dbconnect) . "件のデータを追加しました。<BR>";
 // トランザクションをコミットする
 $query = "COMMIT";
 $result = mysqli_query($dbconnect, $query);
 if (! $result) {
 die("データベースをコミットできません: <BR>");
 }
 mysqli_close($dbconnect);

?>
</BODY>
</HTML> 