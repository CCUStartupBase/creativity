<?php
require "connect.php";
require "vendor/autoload.php";
use PHPHtmlParser\Dom;
use App\Services\Connector;

function getCCU()
{
    global $conn;
    $text = file_get_contents('http://www.ccu.edu.tw/bulletin_list.php?type=0');
    $text = str_replace(array("\r", "\n", "\t", "\s"), '', $text);
    // echo $text;

    //preg_match_all('#<a href="showMsg.php(.*?)">(.*?)<\/a>.*?(\d+-\d+-\d+)#i',$text,$match); //抓link , title , date
    preg_match_all('#<span class="news_time">(\d+)\s+-\s+(\d+)\s+-\s+(\d+)<\/span><a href="show_bulletin.php(.*?)" title="(.*?)">.*?<\/a>#i', $text, $match); //抓link , title , date
    // print_r($match);

    mysqli_query($conn, "SET NAMES UTF8");
    for ($i = 0; $i < count($match[1]); $i++) {
        $title = $match[5][$i];
        $date = $match[1][$i] . "-" . $match[2][$i] . "-" . $match[3][$i]; //確認一下時間的格式
        /*********************************************************/
        $link = 'http://www.ccu.edu.tw/show_bulletin.php' . $match[4][$i];
        $text1 = file_get_contents($link);
        $text1 = str_replace(array("\r", "\n", "\t", "\s"), '', $text1);
        //preg_match_all('#<table border=\"1\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bordercolor=\"\WFFFFFF\" id=\"table2\" bgcolor=\"\WEEFAFF\">(.*?)<\/table>#i',$text1,$match1);//抓content
        preg_match_all('#<p class="board_content">(.*?)<\/p>#i', $text1, $match1); //抓content
        $content = $match1[0][0];
        /*********************************************************/

        // echo "$title $date</br>";
        // if(check($title,$date)) break;//確認一下時間的格式
        //mysqli_query($conn, "INSERT INTO news_copy (CATEGORY,TITLE,CONTENT,BEGINTIME,ENDTIME,DATE,filename) VALUES ('CCU','" . $title . "','" . $content . "','" . $date . "','','" . $date . "','')");
        //mysqli_query($conn, "ALTER IGNORE TABLE news_copy ADD UNIQUE INDEX(TITLE)");
    }
}
function check($TITLE, $DATE)
{
    global $conn;
    mysqli_query($conn, "SET NAMES UTF8");
    $result = mysqli_query($conn, "SELECT * from news_copy where TITLE='" . $TITLE . "' AND DATE = '" . $DATE . "'");
    if ($row = mysqli_fetch_array($result)) {
        return true;
    }
}
function select()
{
    global $conn;
    mysqli_query($conn, "SET NAMES UTF8");
    $result = "";
    /*if($number == ""){
    $result = mysqli_query($conn, "SELECT * from news_copy where CATEGORY='CCU' order by BEGINTIME desc LIMIT 0 , 10");
    }else{
    $result = mysqli_query($conn, "SELECT * from news_copy where CATEGORY='CCU' order by BEGINTIME desc LIMIT 0 , $number");
    }*/
    $result = mysqli_query($conn, "SELECT * from news_copy where CATEGORY='CCU' order by BEGINTIME desc LIMIT 0 , 50");
    while ($row = mysqli_fetch_array($result)) {
        // $data = $row['TITLE']."@@@".$row['CONTENT']."@@@".$row['BEGINTIME'];
        $data = $row['NID'];
        echo "<tr><td width=\"40\"><input type=\"checkbox\" name=\"dataset[]\" value='$data'/></td><td width=\"90\">" . $row['BEGINTIME'] . "</td><td width=\"448\"><a href='show.php?NID=" . $row['NID'] . "'>" . $row['TITLE'] . "</a></td></tr>";
    }
}
function getCCUTitle()
{
    global $conn;
    $text = file_get_contents("http://www.ccu.edu.tw/news_list.php");
    $text = str_replace(array("\r", "\n", "\t", "\s"), '', $text);
    // echo $text;

    // preg_match_all('#<span style=".*?">.*?<\/span><span style=".*?">(.*?)<\/span>#i',$text,$match); //抓文章的title
    //preg_match_all('#<div align="right"><a href="(.*?)">.*?<\/a><\/div>#i',$text,$match); //抓詳全文的link
    preg_match_all('#<div class="news_list_word"><a href="(.*?)"><p class="news_list_title">#i', $text, $match); //抓詳全文的link
    // print_r($match);
    foreach ($match[1] as $link) {
        getContent_Title($link);
        // echo $link;
    }
}
function getContent_Title($link)
{
    global $conn;
    $BASE = "http://www.ccu.edu.tw/";
    $text = file_get_contents($BASE . $link);
    $text = str_replace(array("\r", "\n", "\t", "\s"), '', $text);

    // preg_match_all('#<center><img src="(.*?)"#i',$text,$img); //抓文章宣傳海報
    // echo $BASE.$img[1][0];

    //抓context

    $dom = new Dom;
    $dom->load($text);
    $context = $dom->find('.news_content');
    $content_replace = $context->text;//轉為字串
    //echo $content_replace;
 
    //$content_replace = str_replace($img[1][0],$BASE.$img[1][0],$content_replace);//取代照片URL
    //echo $content_replace;

    //抓文章time
    
    $a = $dom->find('.news_extra');
    $b = $a->text;//轉為字串
    $replace_example = explode("&nbsp",$b);  
    $c =  $replace_example[2];
    preg_match_all('/\d+/', $c, $match);  
    $time="{$match[0][0]}-{$match[0][1]}-{$match[0][2]}";
    //echo $time;



    //preg_match_all('#<p class="news_extra">發佈日期\s+&nbsp;\s+\/\s+&nbsp;\s+(\d+)\s+-\s+(\d+)\s+-\s+(\d+)<br>#i', $text, $time1); //抓文章time
    //$time="{$time1[1][0]}."-".{$time1[2][0]}."-".{$time1[3][0]}";
    //echo $time;

    preg_match_all('#<p class="news_title">(.*?)<\/p>#i', $text, $title); //抓文章title
    //echo $title[1][0];
    $data = array();
    $data [0] = array( "CATEGORY" => 'CCU', "TITLE" =>$title[1][0],  "CONTENT" => $content_replace, "DATE" =>$time );
    echo json_encode($data,JSON_UNESCAPED_UNICODE);

    mysqli_query($conn, "SET NAMES UTF8");
    mysqli_query($conn, "INSERT INTO news_copy (CATEGORY,TITLE,CONTENT,BEGINTIME,ENDTIME,DATE,filename) VALUES ('CCU','".$title[1][0]."','".$content_replace."','".$time."','','".$time."','')");
    mysqli_query($conn, "INSERT INTO news_copy (CATEGORY,TITLE,CONTENT,BEGINTIME,ENDTIME,DATE,filename) VALUES ('CCU','".$title[1][0]."','','".$time."','','".$time."','')");
    mysqli_query($conn, "ALTER IGNORE TABLE news_copy ADD UNIQUE INDEX(TITLE)");
}

?>
<?php
getCCU();
getCCUTitle();
//select();

?>
