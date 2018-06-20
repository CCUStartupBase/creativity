<?php
function getDeptfin(){
			$text=file_get_contents('http://deptfin.ccu.edu.tw/news/list.php?type=all');  
			preg_match_all('/<tr[^>]*>[^>]*<td[^>]*>(\d{4}-\d{2}-\d{2})<\/td>[^<]*[^<]*<td[^>]*id=([0-9]{1,})[^>]*>(.*)<\/td>[^<]*<\/tr>/',$text,$match);//抓日期 內容id 標題
			//echo $match[1][0]."</br>".$match[2][0]."</br>".$match[3][0]."</br>";//顯示日期 內容id 標題
			 
			//$content = getContent('http://deptfin.ccu.edu.tw/news/news_read.php?id=126')."</br>";
			//echo $content;
			mysql_query("SET NAMES UTF8");
			for($i=0;$i<count($match[1]);$i++){
				$URL = 'http://deptfin.ccu.edu.tw/news/news_read.php?id='.$match[2][$i];//連結網址
				//$title = iconv("big5","UTF-8",$match[2][$i]); 
				//$date = iconv("big5","UTF-8",$match[1][$i]);
				//$content = iconv("big5","UTF-8",getContent($URL));
				$date = $match[1][$i]; //日期
				$title = $match[3][$i]; //標題
				$content = getContent($URL); //內容
				$result = mysql_query("INSERT INTO news_copy (CATEGORY,TITLE,CONTENT,BEGINTIME,ENDTIME,DATE,filename) VALUES ('Deptfin','".$title."','".$content."','".$date."','','".$date."','')");
				mysql_query("ALTER IGNORE TABLE news_copy ADD UNIQUE INDEX(CATEGORY, TITLE, DATE)");
			}
			select();
		}
		function select(){
			mysql_query("SET NAMES UTF8");		
			$result = mysql_query("SELECT * from news_copy where CATEGORY='Deptfin' order by BEGINTIME desc LIMIT 0 , 10");
			while($row = mysql_fetch_array($result)){
				// $data = $row['TITLE']."@@@".$row['CONTENT']."@@@".$row['BEGINTIME'];
				$data = $row['NID'];
				echo "<tr><td width=\"40\"><input type=\"checkbox\" name=\"dataset[]\" value='$data'/></td><td width=\"90\">".$row['BEGINTIME']."</td><td width=\"448\"><a href='show.php?NID=".$row['NID']."'>".$row['TITLE']."</a></td></tr>";
			}
		}
		function getContent($URL){
			$text2=file_get_contents($URL);
			preg_match_all('/<body>(.*)<\/body>/s',$text2,$match2);
			
			preg_match_all('/href="([^"]+)"/',$match2[1][0],$match3);
			$match3[1][0]='http://deptfin.ccu.edu.tw'.$match3[1][0];
			//將$match2[1][0]中的href內容取代成$match3[1][0]
			$match2[1][0] = preg_replace('/href="([^"]+)"/','href="'.$match3[1][0].'"',$match2[1][0]);
			$match2[1][0] = preg_replace("/<div id = 'n_title'>.*<\/div>/",' ',$match2[1][0]);	//replace title 
			//$cont = $match2[1][0];
			$cont = strip_tags($match2[1][0]);
			//iconv("big5","UTF-8",strip_tags($match2[1][0])); 
			$cont = str_replace(array("\r","\n","\r\n","\t","\s"), "</br>", $cont);
			return $cont;
		}
?>