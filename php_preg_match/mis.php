<?php  
	//getMis();//抓資管系資料 http://www.mis.ccu.edu.tw/index.aspx 資管系的最新消息
	// select();//檢查資料庫中的資料
	 //echo "HELLO";
		function getMis($link){//抓取最新消息
			$text=file_get_contents('https://www.mis.ccu.edu.tw/api/news/list/'.$link);
			//echo $text;
			preg_match_all('/\"title\":\"(.*?)\"/i',$text,$match1);//抓標題
			//echo $match1[1][0]."</br>";//標題
			
			preg_match_all('/\"time\":\"(.*?)T/i',$text,$match2);//抓時間
			//echo $match2[1][0]."</br>";//時間
	
			preg_match_all('/\"content\":\"(.*?)\\\n\",\"/i',$text,$match3);//抓內容
			//echo $match3[1][0]."</br>";//內容
			
			mysql_query("SET NAMES UTF8");
			for($i=0;$i<count($match1[1]);$i++){
				$title = $match1[1][$i];//抓標題
				$date = $match2[1][$i];//抓時間
				$content = strip_tags($match3[1][$i]);//抓內容
		//		echo $title."</br>".$date."</br>".$content."</br>";
				mysql_query("INSERT INTO news_copy (CATEGORY,TITLE,CONTENT,BEGINTIME,ENDTIME,DATE,filename) VALUES ('Mis','".$title."','".$content."','".$date."','','".$date."','')");
				mysql_query("ALTER IGNORE TABLE news_copy ADD UNIQUE INDEX(TITLE)");
			}
		}
		function select(){//從資料庫中顯示10筆資料
			mysql_query("SET NAMES UTF8");		
			$result = mysql_query("SELECT * from news_copy where CATEGORY='Mis' order by BEGINTIME desc LIMIT 0 , 10");
			while($row = mysql_fetch_array($result)){
				$data = $row['NID'];
				echo "<tr><td width=\"40\"><input type=\"checkbox\" name=\"dataset[]\" value='$data'/></td><td width=\"90\">".$row['BEGINTIME']."</td><td width=\"448\"><a href='show.php?NID=".$row['NID']."'>".$row['TITLE']."</a></td></tr>";
			}
		}
		
		//前大大寫的，目前沒用到
		function getContent($URL){
			$text=file_get_contents($URL);
			$text=str_replace(array("\r","\n","\t","\s"), '', $text);
			preg_match_all('#<table (.*)<\/table>#i',$text,$match3);//抓內容
			return $match3[0][0];
		}
?>  
<?php 
	$link=array("dept","speech","other"); //資管系有三個公告:系所公告、演講公告、其他公告
	for($i=0;$i<3;$i++){
		getMis($link[$i]);
	}
	select();
?>