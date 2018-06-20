<?php
// require "vendor/autoload.php";
// use PHPHtmlParser\Dom;
// use App\Services\Connector;


// function gettime(){
//     $dom = new Dom;
//     $dom->loadFromUrl('http://www.ccu.edu.tw/news_list.php');

    
// }

// gettime();

$str = 'hypertext language programming';
$chars = preg_split('/ /', $str, -1, PREG_SPLIT_OFFSET_CAPTURE);
echo $chars;

?>