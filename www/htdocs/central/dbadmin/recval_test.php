<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      recval_test.php
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
include("../config.php");
include("../common/header.php");
if (!isset($_SESSION['lang'])) $_SESSION["lang"]="es";
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
$vc=explode("\n",$arrHttp["ValorCapturado"]);
$Pft=array();
$ix=-1;
foreach ($vc as $var=>$value) {	if (!empty($value)) {
		$ix=$ix+1;
		$Pft[$ix]["TAG"]=substr($value,0,4);
		$xx=substr($value,4);
		$xx=explode('$$|$$',$xx);
		$Pft[$ix]["PFT"]=urldecode($xx[0]);
		$Pft[$ix]["FATAL"]=$xx[1];
	}
}
$formato="";
$ixt=-1;
foreach ($Pft as $value){	$ixt=$ixt+1;
	if (substr(trim($value["PFT"]),0,1)=="@"){		$pft_file=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".trim(substr($value["PFT"],1));
		if (!file_exists($pft_file)) $pft_file=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/".trim(substr($value["PFT"],1));
		$value["PFT"]="@".$pft_file;
	}
	$formato= $formato."'".$ixt."|".$value["TAG"]."  ','  ',".$value["PFT"].",  /,mpl '$$$$'"  ;
	$Html[$ixt]="<tr><td bgcolor=white valign=top>".$value["TAG"]."</td><td bgcolor=white valign=top><font face=\"courier new\">".$value["PFT"]."</td>";
}
$formato=urlencode(trim($formato));
$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&Pft=".$formato."&from=".$arrHttp["Mfn"]."&to=".$arrHttp["Mfn"];
$IsisScript=$xWxis."leer_mfnrange.xis";
include("../common/wxis_llamar.php");
?>
<html>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
	</div>
	<div class="actions">
<?php echo "<a href=\"javascript:self.close()\" class=\"defaultButton cancelButton\">";
?>
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo $msgstr["close"]?></strong></span></a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="middle form">
	<div class="formContent">
<?

echo "<h5>&nbsp; &nbsp; Script: recval_test.php</h5>";
$recval_pft="";
$recval_pft=implode("<BR>",$contenido);
if (!strpos($recval_pft,'execution error')===false){
    echo $recval_pft;
   	die;
}

echo "<p><table bgcolor=#eeeeee cellspacing=3 border=0>
<tr><td>".$msgstr["tag"]."</td><td>".$msgstr["pftval"]."</td><td>".$msgstr["recval"]."</td></tr>";

$t=explode('$$$$',$recval_pft);
foreach ($t as $salida){	if (!empty($salida)) {
		$ix_sal=explode('|',$salida);
	    $ixt=$ix_sal[0];
	    $salida=$ix_sal[1];
	    $ix=strpos($salida,' ');
	    if ($ix===false){	    	$campo="";	    }else{	    	$campo=substr($salida,$ix+1);	    }
		echo  $Html[$ixt];
		if ($campo!="")
			echo "<td valign=top bgcolor=white>".nl2br($campo)."</td>";
		else
			echo "<td bgcolor=white>&nbsp;</td>";
	}
}
echo "<tr><td colspan=3><a href=javascript:self.close() class=>".$msgstr["close"]."</a></td></tr>";
echo "</table>";

echo "</div></div></body>
</html>";
?>