<?php
/** 
* @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd 
* @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
* @file:      upload.php 
* @desc:       
* @author:    Guilda Ascencio 
* @since:     20091203 
* @version:   1.0 
* 
* == BEGIN LICENSE == 
* 
*    This program is free software: you can redistribute it and/or modify 
*    it under the terms of the GNU Lesser General Public License as 
*    published by the Free Software Foundation, either version 3 of the 
*    License, or (at your option) any later version. 
* 
*    This program is distributed in the hope that it will be useful, 
*    but WITHOUT ANY WARRANTY; without even the implied warranty of 
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
*    GNU Lesser General Public License for more details. 
*   
*    You should have received a copy of the GNU Lesser General Public License 
*    along with this program.  If not, see <http://www.gnu.org/licenses/>. 
*   
* == END LICENSE == 
*/ 
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include ("../config.php");
$lang=$_SESSION["lang"];
include("../lang/admin.php");
include("../lang/soporte.php");
include("../common/get_post.php");

$path_to_file=$db_path.$arrHttp["path"]."/";
$files = $_FILES['userfile'];

if (!preg_match("/$/", $path_to_file)) $path_to_file = $path_to_file."/";
foreach ($files['name'] as $key=>$name) {  	echo "$key=$name<br>";
  	$max=get_cfg_var ("upload_max_filesize");
    if ((int)$files['size'][$key]==0){    	$max=get_cfg_var ("upload_max_filesize");
    	echo "upload_max_filesize = $max<br>";    	echo $msgstr["maxfilesiz"]; 
    	die;    }
	if ($files['size'][$key]) {
      // clean up file name
   		$name = preg_replace("/[^a-z0-9._]/", "",
        	str_replace(" ", "_",
            	str_replace("%20", "_", strtolower($name)
            	)
            )
         );
  		$location = $path_to_file.$name;
		echo "<html>\n";
		echo "<title>".$msgstr["uploadfile"]."</title>\n";
		echo "<script language=javascript src=js/lr_trim.js></script>\n";
		echo "<body>\n";
		echo "<font face=verdana>\n";
		echo $location;
  		if (!copy($files['tmp_name'][$key],$location)){
		    echo "<p>". $msgstr["archivo"]." ".$location." ".$msgstr["notransferido"];
		}else{
		  	echo "<p>*** ". $msgstr["archivo"]." ".$location." ".$msgstr["transferido"];
		  	if (isset($arrHttp["Tag"])){
			  	echo "<script>
					  campo=window.opener.document.forma1.".$arrHttp["Tag"].".value
					  if (Trim(campo)==\"\")
					  	campo+=\"$name\"\n
					  else
					  	campo+='\\r'+\"$name\"\n";
					  if (isset($arrHttp["subc"]) and $arrHttp["descripcion"]!="")
					  	echo "campo+=\"^".$arrHttp["subc"].$arrHttp["descripcion"]."\"\n";
		              echo "
					  window.opener.document.forma1.".$arrHttp["Tag"].".value=campo
					  window.opener.document.forma1.".$arrHttp["Tag"].".rows++
					  self.close()
					  </script>
			  		  ";

			}
  			unlink($files['tmp_name'][$key]);
  		}
   	}
}
	echo "</body>\n";
	echo "</html>\n";
?>