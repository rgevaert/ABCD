<?php

if (isset($msg_path) and $msg_path!="")
	$path=$msg_path;
else
	$path=$db_path;
$a=$path."/lang/".$_SESSION["lang"]."/dbadmin.tab";
if (file_exists($a)) {	$fp=file($a);
	foreach($fp as $var=>$value){
		$value=str_replace('"','&#34;',$value);
		$value=str_replace("'",'&#39;',$value);		if (!empty($value)) {
			$m=explode('=',$value);
			$m[0]=trim($m[0]);			if (!isset($msgstr[$m[0]]))$msgstr[$m[0]]=trim($m[1]);		}	}}

$a=$path."/lang/00/dbadmin.tab";
if (file_exists($a)) {
	$fp=file($a);
	foreach($fp as $var=>$value){
		$value=str_replace('"','&#34;',$value);
		$value=str_replace("'",'&#39;',$value);
		if (!empty($value)) {
			$m=explode('=',$value);
			$m[0]=trim($m[0]);
			if (!isset($msgstr[$m[0]])) $msgstr[$m[0]]=trim($m[1]);
		}
	}
}
?>