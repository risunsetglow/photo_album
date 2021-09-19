<HTML>
<HEAD>
<META HTTP-EQUIV="content-type" CONTENT="text/HTML;charset=utf-8">
<TITLE>写真アルバム</TITLE>
<STYLE type="text/css">
img.album_photo {
 width: 500px;
 height: auto;
 max-width: 100%;
 max-height: 100%;
}
</STYLE>
<STYLE>
table td {
	background: #fff;
}
table tr:nth-child(odd) td {
	background: #eee;
}
.selected_option{
  display: none;
}
</STYLE>
</HEAD>
<BODY>
<?php
include('./exif_tool.php'); 
print <<<_HTML_
<FORM method="POST" action="$_SERVER[PHP_SELF]">
 期間：
 <SELECT name="select">
 <OPTION class="selected_option" value="" selected>選択してください</OPTION>
 <OPTION value="photo_date">撮影日</OPTION>
 <OPTION value="upload_date">アップロード日</OPTION>
 </SELECT>
 &nbsp;
 <SELECT name="fromyear">
 <OPTION value="" selected>--</OPTION>
 <OPTION value="2010">2010</OPTION>
 <OPTION value="2011">2011</OPTION>
 <OPTION value="2012">2012</OPTION>
 <OPTION value="2013">2013</OPTION>
 <OPTION value="2014">2014</OPTION>
 <OPTION value="2015">2015</OPTION>
 <OPTION value="2016">2016</OPTION>
 <OPTION value="2017">2017</OPTION>
 <OPTION value="2018">2018</OPTION>
 <OPTION value="2019">2019</OPTION>
 <OPTION value="2020">2020</OPTION>
 </SELECT>年
 <SELECT name="fromMonth">
 <OPTION value="" selected>--</OPTION>
 <OPTION value="1">1</OPTION>
 <OPTION value="2">2</OPTION>
 <OPTION value="3">3</OPTION>
 <OPTION value="4">4</OPTION>
 <OPTION value="5">5</OPTION>
 <OPTION value="6">6</OPTION>
 <OPTION value="7">7</OPTION>
 <OPTION value="8">8</OPTION>
 <OPTION value="9">9</OPTION>
 <OPTION value="10">10</OPTION>
 <OPTION value="11">11</OPTION>
 <OPTION value="12">12</OPTION>
 </SELECT>月
 ～
 <SELECT name="toyear">
 <OPTION value="" selected>--</OPTION>
 <OPTION value="2010">2010</OPTION>
 <OPTION value="2011">2011</OPTION>
 <OPTION value="2012">2012</OPTION>
 <OPTION value="2013">2013</OPTION>
 <OPTION value="2014">2014</OPTION>
 <OPTION value="2015">2015</OPTION>
 <OPTION value="2016">2016</OPTION>
 <OPTION value="2017">2017</OPTION>
 <OPTION value="2018">2018</OPTION>
 <OPTION value="2019">2019</OPTION>
 <OPTION value="2020">2020</OPTION>
 </SELECT>年 
 <SELECT name="toMonth">
 <OPTION value="" selected>--</OPTION>
 <OPTION value="1">1</OPTION>
 <OPTION value="2">2</OPTION>
 <OPTION value="3">3</OPTION>
 <OPTION value="4">4</OPTION>
 <OPTION value="5">5</OPTION>
 <OPTION value="6">6</OPTION>
 <OPTION value="7">7</OPTION>
 <OPTION value="8">8</OPTION>
 <OPTION value="9">9</OPTION>
 <OPTION value="10">10</OPTION>
 <OPTION value="11">11</OPTION>
 <OPTION value="12">12</OPTION>
 </SELECT>月
 <BR>
 ※期間を指定する際は、撮影日またはアップロード日のどちらかを必ず選んでください
 <BR>
 場所：<INPUT type="text" name="location">
<BR>
<input type="radio" name="favorite" value="" checked>すべて</input>
<input type="radio" name="favorite" value="true">お気に入りのみ</input>
 
 <br>
 表示順：
 <SELECT name="sort">
 <OPTION class="selected_option" value="" value="" selected>選択してください（任意）</OPTION>
 <OPTION value="">--</OPTION>
 <OPTION value="photo_date ASC">撮影日 昇順</OPTION>
 <OPTION value="photo_date DESC">撮影日 降順</OPTION>
 <OPTION value="upload_date ASC">アップロード日 昇順</OPTION>
 <OPTION value="upload_date DESC">アップロード日 降順</OPTION>
 </SELECT>
 <br>
<INPUT type="submit" name="album" value="表示">
</FORM>
_HTML_;
if (isset($_POST['album'])) {
 include('./db_login.php'); // データベースにログインするための情報を読み込む
 // MySQLに接続する
 $dbconnect = mysqli_connect($host, $username, $passwd, $dbname);
  if (!$dbconnect) {
    die('データベースの接続に失敗しました。');
  }

 // utf-8形式の日本語の設定をする
if (! mysqli_set_charset($dbconnect, "utf8")) {
 die("日本語の設定ができません: <BR>" . mysqli_error($dbconnect));
}

 // SELECT文
$query = "SELECT * FROM `1717043` WHERE";
$change = 0;

//fromyear && fromMonth
if ($_POST['fromyear'] && $_POST['fromMonth']){
  if($_POST['select']){
    $query = $query . " " . $_POST['select'] . ">='" . $_POST['fromyear'] . "-" . $_POST['fromMonth'] . "-1'";
    $change = 1;
  }else{
    print "<script type='text/javascript'>alert('「撮影日」または「アップロード日」のどちらかを指定して下さい');</script>";
  }
  
//fromyear のみ
} elseif ($_POST['fromyear']){
  if($_POST['select']){
    $query = $query . " " . $_POST['select'] . ">='" . $_POST['fromyear'] . "-1-1'";
    $change = 1;
  }else{
    print "<script type='text/javascript'>alert('「撮影日」または「アップロード日」のどちらかを指定して下さい');</script>";
  }
  
}

//toyear && toMonth
if($_POST['toyear'] && $_POST['toMonth']){
  if($_POST['select']){
    if($change){
      $query = $query . " AND ";
    }
    $query = $query . " " . $_POST['select'] . "<='" . $_POST['toyear'] . "-" . $_POST['toMonth'] . "-31'" ;
    $change = 1;
  }else{
    print "<script type='text/javascript'>alert('「撮影日」または「アップロード日」のどちらかを指定して下さい');</script>";
  }

//toyear のみ
} elseif ($_POST['toyear']){
  if($_POST['select']){
    if($change){
      $query = $query . " AND ";
    }
    $query = $query . " " . $_POST['select'] . "<='" . $_POST['toyear'] . "-12-31'" ;
    $change = 1;
  }else{
    print "<script type='text/javascript'>alert('「撮影日」または「アップロード日」のどちらかを指定して下さい');</script>";
  }
}

//場所
if($_POST['location']){
  if($change){
    $query = $query . " AND ";
  }
  $query = $query . " photo_location='" . $_POST['location'] . "'" ;
  // echo $query . "<br>";
  $change = 1;
}

//お気に入り
if($_POST['favorite']){
  if($change){
    $query = $query . " AND ";
  }
  $query = $query . " favorite='true'" ;
  $change = 1;
}
//表示順
if($_POST['sort']){
  if($change){
    $query = $query . " ORDER BY ". $_POST['sort']."" ;
    // print $query . "<br>";
  }else{
  $query = "SELECT * FROM `1717043` ORDER BY ". $_POST['sort'] ."";
  }
}
// 全て表示
if(!$change){
  $query = "SELECT * FROM `1717043` ORDER BY photo_date";
 }

// print $query . "<br>";

 $result = mysqli_query($dbconnect, $query); // データベースへの問い合わせ
 if (! $result) { // SELECT文が不適切な場合
 die("データを取得できません: <BR>" . mysqli_error($dbconnect));
 }
 if (! mysqli_num_rows($result)) { // 条件に合う結果がなかった場合
 die("条件に合うデータがありません<BR>");
 }

echo mysqli_num_rows($result) . "件見つかりました" . "<br>";
print "<table border=1>";
print "<TR><TH>写真</TH><TH>撮影日</TH><TH>都市名</TH><TH>アップロード日</TH></TR>";
 while ($result_row = mysqli_fetch_row($result)) {
  print "<tr>";
  print "<td>";
 print "<IMG src=\"./photo/" . $result_row[0] . "\" class=\"album_photo\">";
 print "</td>";
 print "<td>";
 print date("Y年n月j日", strtotime($result_row[1]));
 print "</td>";
 print "<td>";
 print $result_row[3];
 print "</td>";
 print "<td>";
 print date("Y年n月j日", strtotime($result_row[4]));
 print "</td>";

 }
 print "</tr>";
 mysqli_close($dbconnect);
}

print "</table>";
?>
</BODY>
</HTML>