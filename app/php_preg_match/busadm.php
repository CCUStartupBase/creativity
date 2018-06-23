<?php
function getBusadm(){
			$text=file_get_contents('http://busadm.ccu.edu.tw/include/posts.php?sub_id=6');
			$text=str_replace(array("\r","\n","\t","\s"), '', $text);			
			preg_match_all('#<a class="click_block" href="(.*?)">#i',$text,$match); //抓LINK
			
			$text=file_get_contents('http://busadm.ccu.edu.tw/include/news.php');
			$text=str_replace(array("\r","\n","\t","\s"), '', $text);			
			preg_match_all('#<a class="click_block" href="(.*?)">#i',$text,$match1); //抓LINK
			$ALL_ARRAY = array_merge($match[1],$match1[1]);
			// print_r($ALL_ARRAY);
			// preg_match_all('#<td align=\"center\" background=\"images\/b-line.jpg\">(\d+-\d+-\d+)<\/td>#i',$text,$match1);//抓時間
			// preg_match_all('#<a href=\"(.*)\" >#i',$text,$match2);//內容連結
			// echo "http://busadm.ccu.edu.tw/include/".$match[1][0];
			mysql_query("SET NAMES UTF8");
			for($i=0;$i<count($ALL_ARRAY);$i++){
				$text1=file_get_contents("http://busadm.ccu.edu.tw/include/".$ALL_ARRAY[$i]);  
				$text1=str_replace(array("\r","\n","\t","\s"), '', $text1);
				// echo "$text1</br>";
				preg_match_all('#<div class="span7 pull-left" id="posts_content" >(.*?)<\/div>#i',$text1,$match_content); //抓content
				$content = $match_content[1][0];
				if($content ==""){
					preg_match_all('#<div class="span7 pull-left" id="news_content" >(.*?)<\/div>#i',$text1,$match_content); //抓content
					$content = $match_content[1][0];
				}
				preg_match_all('#<blockquote>  <p>(.*?)<\/p>  <small>(\d+-\d+-\d+)<\/small><\/blockquote>#i',$text1,$match_other); //抓title,time
				$title = $match_other[1][0];
				$time = $match_other[2][0];
				 
				// echo "http://busadm.ccu.edu.tw/include/".$ALL_ARRAY[$i]."</br> "."$title </br> $time </br> $content </br>*******</br>";
				mysql_query("INSERT INTO news_copy (CATEGORY,TITLE,CONTENT,BEGINTIME,ENDTIME,DATE,filename) VALUES ('Busadm','".$title."','".$content."','".$time."','','".$time."','')");
				mysql_query("ALTER IGNORE TABLE news_copy ADD UNIQUE INDEX(CATEGORY, TITLE, DATE)");
			}
			select();
		}
		function select(){
			mysql_query("SET NAMES UTF8");		
			$result = mysql_query("SELECT * from news_copy where CATEGORY='Busadm' order by BEGINTIME desc LIMIT 0 , 10");
			while($row = mysql_fetch_array($result)){
				$data = $row['NID'];
				echo "<tr><td width=\"40\"><input type=\"checkbox\" name=\"dataset[]\" value='$data'/></td><td width=\"90\">".$row['BEGINTIME']."</td><td width=\"448\"><a href='show.php?NID=".$row['NID']."'>".$row['TITLE']."</a></td></tr>";
			}
		}
		function getContent($URL){
			$SITE = "http://140.123.169.1/ccu-doba/".$URL;
			$text=file_get_contents($SITE);
			$text=str_replace(array("\r","\n","\t","\s"), '', $text);
			// echo $text;
			preg_match_all('#<td width=\"\d+\">(.*?)<\/td>#i',$text,$match3);//抓內容
			// print_r($match3);
			return $match3[1][1];
			
		}
?> 