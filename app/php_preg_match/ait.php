<?php
require("connect.php");
function getAit(){//抓資料
	global $conn;
			$text=file_get_contents('http://www.ait.ccu.edu.tw/news.asp'); 
			preg_match_all('/<td[^>]*>([\d]+\/[\d]+\/[\d]+)<\/td>/',$text,$match); //抓日期
			preg_match_all('/<td[^>]*>[^>]*<a href="(.*?)">(.*?)<\/td>/s',$text,$match2);  //抓 URL & 標題
			
			 mysqli_query($conn,"SET NAMES UTF8");
			for($i=0;$i<count($match[1]);$i++){
				$URL = 'http://www.ait.ccu.edu.tw/'.$match2[1][$i];
				
				$date = iconv("big5","UTF-8",$match[1][$i]);
				$title = iconv("big5","UTF-8",strip_tags($match2[2][$i])); 
				$content = getContent($URL);//抓內容
				
				 mysqli_query($conn,"INSERT INTO news_copy (CATEGORY,TITLE,CONTENT,BEGINTIME,ENDTIME,DATE,filename) VALUES ('Ait','".$title."','".$content."','".$date."','','".$date."','')");
				 mysqli_query($conn,"ALTER IGNORE TABLE news_copy ADD UNIQUE INDEX(TITLE)");
			}
			//select();//顯示
		}
		function select(){
			 mysqli_query($conn,"SET NAMES UTF8");		
			$result =  mysqli_query($conn,"SELECT * from news_copy where CATEGORY='Ait' order by BEGINTIME desc LIMIT 0 , 10");
			while($row = mysqli_fetch_array($result)){
				$data = $row['NID'];
				echo "<tr><td width=\"40\"><input type=\"checkbox\" name=\"dataset[]\" value='$data'/></td><td width=\"90\">".$row['BEGINTIME']."</td><td width=\"448\"><a href='show.php?NID=".$row['NID']."'>".$row['TITLE']."</a></td></tr>";
			}
		}
		function getContent($URL){
			$text=file_get_contents($URL);
			$cont = '';
			preg_match_all('/<p class="MsoNormal">(.*)<\/p>/is',$text,$match3);//抓內容
			for($i=0;$i<count($match3[1]);$i++){
				$sentance = iconv("big5","UTF-8",strip_tags($match3[1][$i]));
				$cont = $cont.$sentance;
			}
			$cont = str_replace("\n","</br>", $cont);
			return $cont;
		}

		getAit();
		
?>
 