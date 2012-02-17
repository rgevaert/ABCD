<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      profile_edit.php
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
global $arrHttp;
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../common/header.php");
include("../config.php");
$lang=$_SESSION["lang"];
include("../lang/dbadmin.php");
include("../lang/profile.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";
echo "<body>\n";
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}else{
	$encabezado="";
}
?>
<script src=../dataentry/js/lr_trim.js></script>
<script>
function returnObjById( id ){
    if (document.getElementById)
        var returnVar = document.getElementById(id);
    else if (document.all)
        var returnVar = document.all[id];
    else if (document.layers)
        var returnVar = document.layers[id];
    return returnVar;
}

function getElement(psID) {
	if(!document.all) {
		return document.getElementById(psID);

	} else {
		return document.all[psID];
	}
}

function DeleteProfile(Profile){	if (confirm("<?PHP echo $msgstr["DELETE"]?> "+Profile))
		self.location.href="profile_edit.php?profile="+Profile+"&Opcion=delete&encabezado=<?php echo $encabezado?>"
}

function AllDatabases(){
	ixdb=document.profile.elements.length
	for (id=0;id<ixdb;id++){		ctrl=document.profile.elements[id].name
		if (ctrl.substr(0,3)=="db_") {
			if (document.profile.db_ALL.checked){				document.profile.elements[id].checked=true
			}else{
				document.profile.elements[id].checked=false
			}		}
		c=ctrl.split("_")
		if (c[1]=="pft" || c[1]=="fmt"){			if (c[2]=="ALL"){
				if (document.profile.db_ALL.checked){
					document.profile.elements[id].checked=true
				}else{
					document.profile.elements[id].checked=false
				}
			}		}
	}}

function AllPermissions(){
	ixdb=document.profile.elements.length
	for (id=0;id<ixdb;id++){
		ctrl=document.profile.elements[id].name

		if (ctrl.substr(0,7)=="CENTRAL") {
			if (document.profile.CENTRAL_ALL.checked)
				document.profile.elements[id].checked=true
			else
				document.profile.elements[id].checked=false
		}
	}
}

function AllPermissionsCirculation(){
	ixdb=document.profile.elements.length
	for (id=0;id<ixdb;id++){
		ctrl=document.profile.elements[id].name

		if (ctrl.substr(0,4)=="CIRC") {
			if (document.profile.CIRC_CIRCALL.checked)
				document.profile.elements[id].checked=true
			else
				document.profile.elements[id].checked=false
		}
	}
}

function AllPermissionsAcquisitions(){
	ixdb=document.profile.elements.length
	for (id=0;id<ixdb;id++){
		ctrl=document.profile.elements[id].name

		if (ctrl.substr(0,3)=="ACQ") {
			if (document.profile.ACQ_ACQALL.checked)
				document.profile.elements[id].checked=true
			else
				document.profile.elements[id].checked=false
		}
	}
}
function ValidateName(Name){
	bool=  /^[a-z][\w]+$/i.test(Name)
 	if (bool){
        return true
   	}else {
      	return false
   	}
}

function SendForm(){
	Name=Trim(document.profile.profilename.value)
	re=/  /gi
	Name=Name.replace(re,' ')
	re=/ /gi
	Name=Name.replace(re,'_')
	document.profile.profilename.value=Name	if (Name==""){		alert("<?php echo $msgstr["MISSPROFNAME"]?>")
		return	}
	if (!ValidateName(Name)){
		alert("<?php echo $msgstr["INVPROFNAME"]?>")
		return
	}
	if (Trim(document.profile.profiledesc.value)==""){
		alert("<?php echo $msgstr["MISSPROFDESC"]?>")
		return
	}
    document.profile.submit()}
</script>

<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["PROFILES"]?>
	</div>
	<div class="actions">
<?php echo "<a href=\"users_adm.php?xx=s"."$encabezado\" class=\"defaultButton backButton\">";?>
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" /></a>
<?php if (isset($arrHttp["Opcion"])and $arrHttp["Opcion"]!="delete"){	  echo "<a href=\"javascript:SendForm()\" class=\"defaultButton saveButton\">";?>
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo $msgstr["SAVE"]?></strong></span></a>
<?php } ?>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
	<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/profiles.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp; &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
 	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/profiles.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: profile_edit.php";
?>
</font>
	</div>
<div class="middle form">
	<div class="formContent">
<form name=profile action=profile_save.php onsubmit="javascript:return false" >
<?php
if (isset($arrHttp["encabezado"]))
	echo "<input type=hidden name=encabezado value=S>\n";
if (!isset($arrHttp["Opcion"])){	DisplayProfiles();}else{	switch ($arrHttp["Opcion"]){		case "edit":
			EditProfile();
			break;
		case "new":
			NewProfile("");
			break;
		case "delete":
			DeleteProfile();
			break;	}}


echo "</form></div>
</div>
</center>";
include("../common/footer.php");
echo "</body></html>\n";



function DisplayProfiles(){global $db_path,$msgstr,$encabezado;
	echo "<table>";
	$fp=file($db_path."par/profiles/profiles.lst");
	foreach ($fp as $val){
		$val=trim($val);
		if ($val!=""){
			$p=explode('|',$val);
			if ($p[0]!="adm"){
				echo "<tr><td>".$p[1]." (".$p[0].")</td><td><a href=profile_edit.php?profile=".$p[0]."$encabezado&Opcion=edit>".$msgstr["EDIT"]."</A> | ";
				echo "<a href=javascript:DeleteProfile(\"".$p[0]."\")>".$msgstr["delete"]."</A></td>";
			}
		}
	}
	echo "</table>\n";
	echo "<a href=profile_edit.php?Opcion=new&encabezado=s>".$msgstr["new"]."</a>";}

function DeleteProfile(){global $db_path,$msgstr,$lang_db,$arrHttp,$xWxis,$Wxis;
// READ ACCES DATABASE AND FIND IF THE PROFILE IS IN USE
	$IsisScript=$xWxis."leer_mfnrange.xis";
	$query = "&base=acces&cipar=$db_path"."par/acces.par"."&Pft=v40^a/";
	include("../common/wxis_llamar.php");
	 foreach ($contenido as $linea){	 	if (trim($linea)==$arrHttp["profile"]){	 		echo "<h2>".$msgstr["INUSE"]."<h2>";
	 		return;	 	}
	}
    $fp=file($db_path."par/profiles/profiles.lst");
    $new=fopen($db_path."par/profiles/profiles.lst","w");
    foreach ($fp as $prof){    	$p=explode("|",$prof);
    	if ($p[0]!=trim($arrHttp["profile"]))
    		$res=fwrite($new,$prof);    }
    fclose($new);
    $res=unlink($db_path."par/profiles/".$arrHttp["profile"]);
	if ($res==0){
		echo $arrHttp["profile"].": The file could not be deleted";
	}else{
		echo "<h2>".$arrHttp["profile"]." ".$msgstr["deleted"]."</h2>";
	}
}

function EditProfile(){global $db_path,$msgstr,$lang_db,$arrHttp;
    $fp=file($db_path."par/profiles/".$arrHttp["profile"]);
    NewProfile($arrHttp["profile"]);}

function NewProfile($profile){global $db_path,$msgstr,$lang_db;
	$fprofile=file($db_path."par/profiles/profiles.tab");
	$module="CENTRAL";
	foreach ($fprofile as $p){		$p=trim($p);
		if ($p=="[CIRCULATION]"){
			$module="CIRC";
		}else{			if ($p=="[ACQUISITIONS]"){				$module="ACQ";			}else{
				$p=trim($p);
				if ($p!=""){					$p_el=explode("=",$p);
					$profile_usr[$module."_".$p_el[0]]="";
				}			}		}	}
	if ($profile!=""){
		$fprofile=file($db_path."par/profiles/".$profile);
		foreach ($fprofile as $p){			$p=trim($p);
			if ($p!=""){				$p_el=explode("=",$p);
				$profile_usr[$p_el[0]]=$p_el[1];
			}
		}	}
	echo "<table>";
	echo "<tr><td>".$msgstr["PROFILENAME"]."</td><td><input type=text name=profilename size=15 value=\"";
	if (isset($profile_usr["profilename"])) echo $profile_usr["profilename"];
	echo "\"></td>";
	echo "<tr><td>".$msgstr["PROFILEDESC"]."</td><td><input type=text name=profiledesc size=80 value=\"";
	if (isset($profile_usr["profiledesc"])) echo $profile_usr["profiledesc"];
	echo "\"></td>";
	echo "</table>";
	$fp=file($db_path."bases.dat");
	echo "<div style=\"position:relative;overflow:auto;height:300px;border-style:double;\">";
	echo "<table bgcolor=#cccccc class=listTable>";
	echo "<th>".$msgstr["DATABASES"]."</th><th>".$msgstr["DISPLAYFORMAT"]."</th><th>".$msgstr["WORKSHEET"]."</TH>";
	echo "<tr><td bgcolor=white><input type=checkbox name=db_ALL value=ALL onclick=AllDatabases()><strong>ALL</strong></td><TD bgcolor=white></TD><TD bgcolor=white></TD>\n";
	foreach ($fp as $dbs){
		$dbs=trim($dbs);

		if ($dbs!=""){			$dd=explode('|',$dbs);
			if ($dd[0]!="acces" and $dd[0]!="suggestions" and $dd[0]!="purchaseorder" and $dd[0]!="copies"
			    and $dd[0]!="loanobjects" and $dd[0]!="suspml" and $dd[0]!="trans" and $dd[0]!="reserva"){				echo "<tr><td valign=top bgcolor=white><input type=checkbox name=db_".$dd[0]." value=".$dd[0];
				if (isset($profile_usr["db_".$dd[0]])) echo " checked";
				echo "><strong>".$dd[1]." (".$dd[0].")</strong></td>\n";
				echo "<td bgcolor=white valign=top>";
				$file=$db_path.$dd[0]."/pfts/".$_SESSION["lang"]."/formatos.dat";
				if (!file_exists($file)){					$file=$db_path.$dd[0]."/pfts/".$lang_db."/formatos.dat";				}
				$checked="";
				if (isset($profile_usr[$dd[0]."_pft_ALL"])) $checked=" checked";
				echo "<input type=checkbox name=".$dd[0]."_pft_ALL $checked>".$msgstr["ALL"]."<br>\n";
				if (file_exists($file)){
					$pft=file($file);
					foreach($pft as $val){						$val=trim($val);
						if ($val!=""){							$p=explode('|',$val);
							$checked="";
							if (isset($profile_usr[$dd[0]."_pft_".$p[0]])) $checked=" checked";
							echo "<input type=checkbox name=".$dd[0]."_pft_".$p[0]." value=".$p[0]." $checked>".$p[1]." (".$p[0].")<br>\n";						}
					}
				}else{					echo "&nbsp;";				}
				echo "</td>";
				echo "<td bgcolor=white valign=top>";
				$file=$db_path.$dd[0]."/def/".$_SESSION["lang"]."/formatos.wks";
				if (!file_exists($file)){
					$file=$db_path.$dd[0]."/def/".$lang_db."/formatos.wks";
				}
				$checked="";
				if (isset($profile_usr[$dd[0]."_fmt_ALL"])) $checked=" checked";
				echo "<input type=checkbox name=".$dd[0]."_fmt_ALL $checked>".$msgstr["ALL"]."<br>\n";
				if (file_exists($file)){
					$pft=file($file);
					foreach($pft as $val){
						$val=trim($val);
						if ($val!=""){
							$p=explode('|',$val);
							$checked="";
							if (isset($profile_usr[$dd[0]."_fmt_".$p[0]])) $checked=" checked";
							echo "<input type=checkbox name=".$dd[0]."_fmt_".$p[0]." value=".$p[0]." $checked>".$p[1]." (".$p[0].")<br>\n";
						}
					}
				}else{					echo   "&nbsp";				}
				echo "</td>";
			}
		}	}
	echo "</table>";
	echo "</div>";
	echo "<p><h2>".$msgstr["PERMISSIONS"]."</h2>";
	echo "<strong>".$msgstr["CATALOGUING"]."</Strong>";
	$fprofile=file($db_path."par/profiles/profiles.tab");
	echo "<table class=listTable><td valign=top>";
	$ix=0;
	$count=0;
	foreach ($fprofile as $prof){		if (substr($prof,0,13)=="[CIRCULATION]")
			break;
		$count=$count+1;
	}
	$count=$count/3;
	$module="CENTRAL";
	foreach ($fprofile as $prof){		$ixcont=$ixcont+1;
		$prof=trim($prof);
		if ($prof!=""){
			if (substr($prof,0,13)=="[CIRCULATION]"){				echo "</table>";
				echo "<p><strong>".$msgstr["CIRCULATION"]."</strong>";
				echo "<table class=listTable><td valign=top>";
				$module="CIRC";
				$ix=0;
				$ixcont=0;
			}else{				if (substr($prof,0,14)=="[ACQUISITIONS]"){
					echo "</table>";
					echo "<p><strong>".$msgstr["ACQUISITIONS"]."</strong>";
					echo "<table class=listTable><td valign=top>";
					$ix=0;
					$ixcont=0;
					$module="ACQ";
				}else{
					if ($ixcont>$count) {						echo "</td><td valign=top>";
						$ixcont=0;					}
					$ix=strpos($prof,"=");
					$pa=substr($prof,0,$ix);
					$pb=substr($prof,$ix+1);
					echo "<input type=checkbox name=".$module."_$pa value=Y ";
					if ($pa=="ALL"){
						echo " onclick=AllPermissions()";
					}else{						if ($pa=="CIRCALL"){							echo " onclick=AllPermissionsCirculation()";						}else{							if ($pa=="ACQALL"){								echo " onclick=AllPermissionsAcquisitions()";							}else{
								switch ($module){									case "CENTRAL":
										echo " onclick=\"document.profile.".$module."_ALL.checked=false \"";
										break;
									case "CIRC":
										echo " onclick=\"document.profile.".$module."_CIRCALL.checked=false \"";
										break;
									case "ACQ":
										echo " onclick=\"document.profile.".$module."_ACQALL.checked=false \"";
										break;								}							}						}					}

					if (isset($profile_usr[$module."_".$pa]) and $profile_usr[$module."_".$pa]=="Y") echo " checked";
					echo ">".$msgstr[$pa]."<br>\n";
				}
			}
		}
	}
	echo "</td></table>";}
?>

