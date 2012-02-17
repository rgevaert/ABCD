<?php
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include ("../config.php");
include ("../lang/admin.php");
include ("../lang/prestamo.php");

include("../common/get_post.php");

include("../common/header.php");
include("../common/institutional_info.php");

function ValidarArchivo($file){
global $db_path,$lang_db;
	if ($file=="") return true;
	if (file_exists($db_path."trans/pfts/".$_SESSION["lang"]."/$file.pft")){
		return true;
	}else{
		if (file_exists($db_path."trans/pfts/".$lang_db."/$file.pft")){
			return true;
		}else{
			return false;
		}
	}
}

$ayuda="receipts.html";
$archivo="";
$pr_loan="";
$pr_return="";
$pr_fine="";
$pr_statment="";
$pr_solvency="";
$fp=fopen($db_path."trans/pfts/".$_SESSION["lang"]."/receipts.lst","w");
if (isset($arrHttp["pr_loan"])) $pr_loan=$arrHttp["pr_loan"];
$res=fwrite($fp,"pr_loan|$pr_loan\n");
if (isset($arrHttp["pr_return"])) $pr_return=$arrHttp["pr_return"];
$res=fwrite($fp,"pr_return|$pr_return\n");
if (isset($arrHttp["pr_fine"])) $pr_fine=$arrHttp["pr_fine"];
$res=fwrite($fp,"pr_fine|$pr_fine\n");
if (isset($arrHttp["pr_statment"])) $pr_statment=$arrHttp["pr_statment"];
$res=fwrite($fp,"pr_statment|$pr_statment\n");
if (isset($arrHttp["pr_solvency"])) $pr_solvency=$arrHttp["pr_solvency"];
$res=fwrite($fp,"pr_solvency|$pr_solvency\n");
fclose($fp);
?>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["receipts"]?>
	</div>
	<div class="actions">
		<a href="configure_menu.php?encabezado=s" class="defaultButton backButton">
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo  $msgstr["menu"]?></strong></span>
		</a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">

<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/<?php echo $ayuda?> target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/".$ayuda." target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: receipts_ex.php";
?></font>
</div>
<div class="middle form">
	<div class="formContent">
	<form name=receipts action=receipts_ex.php method=post onsubmit="return false">
	<table>
<?php
echo "<tr><td>".$msgstr["pr_loan"].":</td><td>$pr_loan";
if (!ValidarArchivo($pr_loan)) echo "<font color=red> ".$msgstr["notfound"]."</font>";
echo "</td>";
echo "<tr><td>".$msgstr["pr_return"].":</td><td>$pr_return";
if (!ValidarArchivo($pr_return)) echo "<font color=red> ".$msgstr["notfound"]."</font>";
echo "</td>";
echo "<tr><td>".$msgstr["pr_fine"].":</td><td>$pr_fine";
if (!ValidarArchivo($pr_fine)) echo "<font color=red> ".$msgstr["notfound"]."</font>";
echo "</td>";
echo "<tr><td>".$msgstr["pr_statment"].":</td><td>$pr_statment";
if (!ValidarArchivo($pr_statment)) echo "<font color=red> ".$msgstr["notfound"]."</font>";
echo "</td>";
echo "<tr><td>".$msgstr["pr_solvency"].":</td><td>$pr_solvency";
if (!ValidarArchivo($pr_solvency)) echo "<font color=red> ".$msgstr["notfound"]."</font>";
echo "</td>";
?>
	</table>
    </form>
	</div>
</div>
</center>
<?php
include("../common/footer.php");
?>
</body>
</html>

