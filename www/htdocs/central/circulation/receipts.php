<?php
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include ("../config.php");
include ("../lang/dbadmin.php");
include ("../lang/prestamo.php");

include("../common/get_post.php");

include("../common/header.php");
include("../common/institutional_info.php");
$ayuda="receipts.html";
$archivo="";
$pr_loan="";
$pr_return="";
$pr_fine="";
$pr_statment="";
$pr_solvency="";
if (file_exists($db_path."trans/pfts/".$_SESSION["lang"]."/receipts.lst")){	$archivo=$db_path."trans/pfts/".$_SESSION["lang"]."/receipts.lst";
}else{	if (file_exists($db_path."trans/pfts/".$_SESSION["lang"]."/receipts.lst"))
		$archivo=$db_path."trans/pfts/".$lang_db."/receipts.lst";}
if ($archivo!=""){	$fp=file($archivo);
	foreach ($fp as $value){		$v=explode('|',$value);
		switch($v[0]){			case "pr_loan":
				$pr_loan=$v[1];
				break;
			case "pr_return":
				$pr_return=$v[1];
				break;
			case "pr_fine":
				$pr_fine=$v[1];
				break;
			case "pr_statment":
				$pr_statment=$v[1];
				break;
			case "pr_solvency":
				$pr_solvency=$v[1];
				break;		}	}}
?>
<script language="javascript1.2" src="../dataentry/js/lr_trim.js"></script>
<script>
function CheckName(fn,Ctrl){	res= /^[a-z][\w]+$/i.test(fn)
	if (res==false){
		alert("<?php echo $msgstr["errfilename"]?>");
		Ctrl.focus()
		return false
	}}
function Guardar(){	nombre=Trim(document.receipts.pr_loan.value)
	if (nombre!=""){		res=CheckName(nombre,document.receipts.pr_loan)
		if (res==false){			return
		}
	}
	nombre=Trim(document.receipts.pr_return.value)
	if (nombre!=""){		res=CheckName(nombre,document.receipts.pr_return)
		if (res==false){
			return
		}
	}
	nombre=Trim(document.receipts.pr_fine.value)
	if (nombre!=""){
		res=CheckName(nombre,document.receipts.pr_fine)
		if (res==false){
			return
		}
	}
	nombre=Trim(document.receipts.pr_statment.value)
	if (nombre!=""){
		res=CheckName(nombre,document.receipts.pr_statment)
		if (res==false){
			return
		}
	}
	nombre=Trim(document.receipts.pr_solvency.value)
	if (nombre!=""){
		res=CheckName(nombre,document.receipts.pr_solvency)
		if (res==false){
			return
		}
	}
	document.receipts.submit()}
</script>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["receipts"]?>
	</div>
	<div class="actions">
		<a href="javascript:history.back()" class="defaultButton backButton">
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo  $msgstr["back"]?></strong></span>
		</a>
		<a href=javascript:Guardar() class="defaultButton saveButton">
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo $msgstr["update"]?></strong></span>
		</a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">

<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/<?php echo $ayuda?> target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/".$ayuda." target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: receipts.php";
?></font>
</div>
<div class="middle form">
	<div class="formContent">
	<form name=receipts action=receipts_ex.php method=post onsubmit="return false">
	<table>
<?php
echo "<tr><td>".$msgstr["pr_loan"]."</td><td><input type=text name=pr_loan size=15 value='$pr_loan'>.pft</td>";
echo "<tr><td>".$msgstr["pr_return"]."</td><td><input type=text name=pr_return size=15 value='$pr_return'>.pft</td>";
echo "<tr><td>".$msgstr["pr_fine"]."</td><td><input type=text name=pr_fine size=15 value='$pr_fine'>.pft</td>";
echo "<tr><td>".$msgstr["pr_statment"]."</td><td><input type=text name=pr_statment size=15 value='$pr_statment'>.pft</td>";
echo "<tr><td>".$msgstr["pr_solvency"]."</td><td><input type=text name=pr_solvency size=15 value='$pr_solvency'>.pft</td>";
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

