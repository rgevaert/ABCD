<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      databases_configure_update.php
 * @desc:      Update the configuration of an bibliographic database
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
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
$lang=$_SESSION["lang"];
include("../lang/dbadmin.php");
include("../lang/prestamo.php");

include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";

function GuardarPft($Pft,$base){global $msgstr,$db_path,$arrHttp;
	$dir=$db_path.$arrHttp["base"]."/loans";
	if (!file_exists($dir)){		$res=mkdir($dir);
		if (!$res) {			echo $dir." ".$msgstr["foldernotc"];
			die;		}
		$dir.="/".$_SESSION["lang"];
		if (!file_exists($dir)){
			$res=mkdir($dir);
			if (!$res) {				echo $dir." ".$msgstr["foldernotc"];
				die;			}
		}
	}
	$fp=fopen($base,"w");
	fwrite($fp,$Pft);	echo "<xmp>".$Pft."</xmp><p>$base <strong>". $msgstr["saved"]."</strong><hr>";
}

include("../common/header.php");
$encabezado="";
include("../common/institutional_info.php");
echo "
		<div class=\"sectionInfo\">
			<div class=\"breadcrumb\">".
				$msgstr["sourcedb"].". ".$msgstr["loan"].". ".$msgstr["configure"]."
			</div>
			<div class=\"actions\">\n";

				echo "<a href=\"databases.php?encabezado=s\" class=\"defaultButton backButton\">
					<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
					<span><strong>". $msgstr["back"]."</strong></span>
				</a>
			</div>
			<div class=\"spacer\">&#160;</div>
		</div>
		<div class=\"middle form\">
			<div class=\"formContent\">\n";

$object_db=$arrHttp["base"];
$Pft="";
if (isset($arrHttp["invkey"])){
	echo "<h5><font color=darkred>". $msgstr["invkey"]." - ".$msgstr["nckey"]."</font></H5>";
	$Pft="IN ".$arrHttp["invkey"];
}

if (isset($arrHttp["nckey"])and trim($arrHttp["nckey"])!=""){	$Pft.="\nNC ".$arrHttp["nckey"];
}

GuardarPft($Pft,$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_conf.tab");

if (isset($arrHttp["num_i"])){
	echo "<h5><font color=darkred>". $msgstr["pft_ninv"]."</font></H5>";
	$Pft=stripslashes($arrHttp["num_i"]);
	GuardarPft($Pft,$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_inventorynumber.pft");
}

if (isset($arrHttp["num_c"])){
	echo "<h5><font color=darkred>". $msgstr["pft_nclas"]."</font></H5>";
	$Pft=stripslashes($arrHttp["num_c"]);
	GuardarPft($Pft,$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_cn.pft");
}

if (isset($arrHttp["bibref"])){
	echo "<h5><font color=darkred>". $msgstr["pft_obj"]."</font></H5>";
	$Pft=stripslashes($arrHttp["bibref"]);
	GuardarPft($Pft,$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_display.pft");
}

if (isset($arrHttp["bibstore"])){
	echo "<h5><font color=darkred>". $msgstr["pft_store"]."</font></H5>";
	$Pft=stripslashes($arrHttp["bibstore"]);
	GuardarPft($Pft,$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_store.pft");
}

if (isset($arrHttp["loandisp"])){
	echo "<h5><font color=darkred>". $msgstr["pft_loandisp"]."</font></H5>";
	$Pft=stripslashes($arrHttp["loandisp"]);
	GuardarPft($Pft,$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_show.pft");
}

if (isset($arrHttp["tm"])){
	echo "<h5><font color=darkred>". $msgstr["pft_typeofr"]."</font></H5>";
	$Pft=stripslashes($arrHttp["tm"]);
	GuardarPft($Pft,$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_typeofobject.pft");
}

?>
</div></div>
<?php include ("../common/footer.php");?>
</body>
</html>