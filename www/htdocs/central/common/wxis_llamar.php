<?php
	if (isset($arrHttp["lock"]) and $arrHttp["lock"]=="S"){		$query.="&lock=S";	}

	if (isset($wxisUrl) and $wxisUrl!=""){
		$cont="";
		$url=$wxisUrl."?IsisScript=$IsisScript".$query."&cttype=Y";
		$url.="&path_db=".$db_path;
		$fp = fopen($url, 'r');
		// keep reading until there's nothing left
		while ($line = fread($fp, 1024)) {
	   		$cont .= $line;
		}
		$contenido=explode("\n",$cont);
	}else{
		$query.="&path_db=".$db_path;
		putenv('REQUEST_METHOD=GET');
		putenv('QUERY_STRING='."?xx=".$query);
		$contenido="";
	 	exec("\"".$Wxis."\" IsisScript=$IsisScript",$contenido);
	}
//$fp=fopen("/abcd/www/scripts","a");
//fwrite($fp,$_SERVER["PHP_SELF"]." ".$IsisScript." ".$query."\n");
//fclose($fp);
?>