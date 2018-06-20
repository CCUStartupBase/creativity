<?php
function getEMBA(){
			$text=file_get_contents('http://www.emba.ccu.edu.tw/view/default/content/news.php');   
			preg_match_all('#<td class="title"><a href="(.*)">(.*)<\/a>#i',$text,$match); //抓標題 & URL
			preg_match_all('#<td align="center" class="data">(.*)<\/td>#i',$text,$match1);//抓時間
			mysql_query("SET NAMES UTF8");
			
			for($i=0;$i<count($match[1]);$i++){
				$title = $match[2][$i];//抓標題
				$date = str_replace("|","",$match1[1][$i]);
				$date = str_replace("&nbsp;","",$date);//抓時間
				$content = getContent($match[1][$i]);//抓內容
				mysql_query("INSERT INTO news_copy (CATEGORY,TITLE,CONTENT,BEGINTIME,ENDTIME,DATE,filename) VALUES ('EMBA','".$title."','".$content."','".$date."','','".$date."','')");
				// $data = $title."@@@".$content."@@@".$date;
				// echo "<tr><td width=\"40\"><input type=\"checkbox\" name=\"dataset[]\" value='$data'/></td><td width=\"90\">$date</td><td width=\"448\">$title</td></tr>";
				mysql_query("ALTER IGNORE TABLE news_copy ADD UNIQUE INDEX(CATEGORY, TITLE, DATE)");
			}
			select();
		}
		function select(){
			mysql_query("SET NAMES UTF8");		
			$result = mysql_query("SELECT * from news_copy where CATEGORY='EMBA' order by BEGINTIME desc LIMIT 0 , 10");
			while($row = mysql_fetch_array($result)){
				// $data = $row['TITLE']."@@@".$row['CONTENT']."@@@".$row['BEGINTIME'];
				$data = $row['NID'];
				echo "<tr><td width=\"40\"><input type=\"checkbox\" name=\"dataset[]\" value='$data'/></td><td width=\"90\">".$row['BEGINTIME']."</td><td width=\"448\"><a href='show.php?NID=".$row['NID']."'>".$row['TITLE']."</a></td></tr>";
			}
		}
		function getContent($URL){
			$SITE = "http://www.emba.ccu.edu.tw/view/default/content/".$URL;
			$text=file_get_contents($SITE);
			$text=str_replace(array("\r","\n","\t","\s"), '', $text);
			preg_match_all('#<div style="width:94%;margin:0 auto;">(.*)<\/div><br#i',$text,$match3);//抓內容
			return $match3[1][0];
			
		}
?>