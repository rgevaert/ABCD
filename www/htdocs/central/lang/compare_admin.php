<?php


include("../config.php");
$table=$_GET["table"];
echo "<strong>$table</strong>";
$file=file($db_path."lang/00/$table");
foreach ($file as $value){	$x=explode('=',$value);
	$msgstr[$x[0]][0]=trim($x[1]);}
if (file_exists($db_path."lang/en/$table")){
	$file=file($db_path."lang/en/$table");
	foreach ($file as $value){
		$x=explode('=',$value);
		$msgstr[$x[0]]["en"]=trim($x[1]);
	}
}
if (file_exists($db_path."lang/es/$table")){
	$file=file($db_path."lang/es/$table");
	foreach ($file as $value){
		$x=explode('=',$value);
		$msgstr[$x[0]]["es"]=trim($x[1]);
	}
}
if (file_exists($db_path."lang/fr/$table")){
	$file=file($db_path."lang/fr/$table");
	foreach ($file as $value){
		$x=explode('=',$value);
		$msgstr[$x[0]]["fr"]=trim($x[1]);
	}
}
if (file_exists($db_path."lang/pt/$table")){
	$file=file($db_path."lang/pt/$table");
	foreach ($file as $value){
		$x=explode('=',$value);
		$msgstr[$x[0]]["pt"]=trim($x[1]);
	}
}
echo "<table>";
echo "<th>Código</th><th>00</th><th>Inglés</th><th>Español</th><th>Francés</th><th>Portugués</th>";
foreach ($msgstr as $key=>$value){	echo "<tr><td>$key</td>";	foreach ($value as $msg){
		echo "<td>$msg</td>";	}}
echo "<table>";
?>
