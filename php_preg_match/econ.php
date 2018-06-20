<?php

//抓經濟系資料
	// getEcon(1);// http://econ.ccu.edu.tw/list_news.php?type=1//行政
	// getEcon(3);// http://econ.ccu.edu.tw/list_news.php?type=3//課程
	// getEcon(4);// http://econ.ccu.edu.tw/list_news.php?type=4//榮譽榜
	// select();//檢查資料庫中的資料
	// class ECON{
		function getEcon($type){
			$text=file_get_contents('http://econ.ccu.edu.tw/list_news.php?type='.$type);   
			preg_match_all('#onClick=\"MM_openBrWindow(\S\'(.*)\',\'.*\',\'width=500,height=450\'\S)\">(.*)<\/a><\/div>#i',$text,$match); //抓標題 & URL
			preg_match_all('#<div id=\"a_list\" style=\"width:18\S;color:\S990000;\">(.*)<\/div>#i',$text,$match1);//抓時間

			mysql_query("SET NAMES 'UTF8'");
			for($i=0;$i<count($match[3]);$i++){
				$URL = iconv("big5","UTF-8",$match[2][$i]);
				$title = iconv("big5","UTF-8",$match[3][$i]);//抓title 
				$content = "<p>".$title."</p><a target=\"_blank\" href=\"http://econ.ccu.edu.tw/".$URL."\">http://econ.ccu.edu.tw/".$URL."</a>";//切記：不要用 ' ，不然會無法存到DB去
				$date = iconv("big5","UTF-8",$match1[1][$i]);//抓date
				// echo $title."</br>".$date."</br>".$content."</br>";
				mysql_query("INSERT INTO news_copy (CATEGORY,TITLE,CONTENT,BEGINTIME,ENDTIME,DATE,filename) VALUES ('Econ','".$title."','".$content."','".$date."','','".$date."','')");
				mysql_query("ALTER IGNORE TABLE news_copy ADD UNIQUE INDEX(CATEGORY, TITLE, DATE)");
			}
			// print_r($match);
			
		}
		
		function select(){
			mysql_query("SET NAMES UTF8");		
			$result = mysql_query("SELECT DISTINCT * from news_copy where CATEGORY='Econ' order by BEGINTIME desc LIMIT 0 , 10");
			while($row = mysql_fetch_array($result)){
				// $data = $row['TITLE']."@@@".$row['CONTENT']."@@@".$row['BEGINTIME'];
				$data = $row['NID'];
				echo "<tr><td width=\"40\"><input type=\"checkbox\" name=\"dataset[]\" value='$data'/></td><td width=\"90\">".$row['BEGINTIME']."</td><td width=\"448\"><a href='show.php?NID=".$row['NID']."'>".$row['TITLE']."</a></td></tr>";
			}
		}
		function getContent($URL){
			$URL = iconv("big5","UTF-8",$URL);
			return $URL;
			
		}
?>
 <?php
	getEcon(1);
	getEcon(2);
	getEcon(3);
	getEcon(4);
	select();
	?>