<?
require_once("../php/include.php");
require_once("../php/common.php");
require_once("auth_check.php");

auth_check_login();

function stripFromText($haystack, $bfstarttext, $endsection) {
	$startpostext = $bfstarttext;
	$startposlen = strlen($startpostext);
	$startpos = strpos($haystack, $startpostext);
	$endpostext = $endsection;
	$endposlen = strlen($endpostext);
	$endpos = strpos($haystack, $endpostext, $startpos);
	
	return substr($haystack, $startpos + $startposlen, $endpos - ($startpos + $startposlen));
}

$back = $_SERVER["HTTP_REFERER"];
$id = $_REQUEST["id"];
$page = $_REQUEST["page"];

$xmlSave = $def['DATABASE_PATH'] . "xml/" . $checked['lang'] . "/" . $id . ".xml";

if ($id == "" || $lang == "") {
  die("error: missing parameter id or lang");
}

if ( file_exists($xmlSave) ){
	$xml = getDoc($xmlSave);
	
	if ( strpos($xml,'<url>') > 0 ){
		// retira marcas CDATA
		$buffer = trim(stripFromText($xml, "<url>","</url>"));
	}else{ 
		$buffer = "";
	}
}else{
	$buffer = "";
}

$messageArray = array (
"es" =>
	array (
		"title" => "Administración: Biblioteca Virtual en Salud",
		"available" => "Disponible",
		"unavailable" => "Indisponible",
		"exit" => "Salir",
		"save" => "Graba",
		"url"  => "Enlace del archivo RSS ",
	),
"pt" =>
	array (
		"title" => "Administração: Biblioteca Virtual em Saúde",
		"available" => "Disponível",
		"unavailable" => "Indisponível",
		"exit" => "Sai",
		"save" => "Grava",
		"url"  => "Link para o arquivo RSS ",
	),
"en" =>
	array (
		"title" => "Administration: Virtual Health Library",
		"available" => "Available",
		"unavailable" => "Unavailable",		
		"exit" => "Exit",
		"save" => "Save",
		"url"  => "RSS link",
	),
);
$message = $messageArray[$lang];

?>
<html>
  <head>
    <title>BVS-Site Admin</title>
	<link rel="stylesheet" href="../css/admin/adm.css" type="text/css" />
	<script language="javascript">
		function updateView(){
			var url = document.getElementById("buffer").value;
			url = url.replace(/%HTTP_HOST%/,"<?=$_SERVER["HTTP_HOST"]?>");
			window.status = url;			
			rss_preview.location = url;
		}	 
	</script>
  </head>

  <body>
		<form name="formPage" action="../php/xmlRoot.php" method="post">
		
			<input type="hidden" name="xml" value="xml/pt/adm.xml" />
			<input type="hidden" name="xsl" value="xsl/adm/menu.xsl" />
			<input type="hidden" name="lang" value="<?=$lang?>" />
			<input type="hidden" name="id" value="<?=$id?>" />
			<input type="hidden" name="xmlSave" value="<?=$xmlSave?>" />
			<input type="hidden" name="xslSave" value="xsl/adm/save-rss.xsl" />
			<span class="identification">
				<center><?=$message["title"]?></center>
			</span>
			<hr size="1" noshade="" />
			<table width="100%" border="0" cellpadding="4" cellspacing="0" class="bar">
				<tr valign="top">
					<td align="left" valign="middle"><?=$page?> <b>|</b>
						<select name="available" size="1">
							<option value="yes"><?=$message["available"]?></option>
							<option value="no"><?=$message["unavailable"]?></option>
						</select>
						<b>|</b>
						<a href="javascript: formPage.submit()">
							<?=$message["save"]?>
						</a>
					</td>
					<td align="right" valign="middle">
						<a href="../admin/admFrames.php?lang=<?=$lang?>" target="_top"><?=$message["exit"]?></a>
					</td>
				</tr>
			</table>
			<hr size="1" noshade="" />
			<br />
			<table width="100%" cellpadding="0" cellspacing="0" class="button-list">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td>&#160;</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<table width="100%" class="tree-edit">
				<tr valign="top">
					<td>
					  <?=$message["url"]?>
				 	  <input type="text" name="buffer" id="buffer" size="70" value="<?=$buffer?>">
					  <input type="button" value="verificar" onclick="javascript:updateView();"/>
	  				</td>
				</tr>
			</table>
			<br/>
			<table width="100%" cellpadding="0" cellspacing="0" class="button-list">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td valign="middle" width="140">RSS</td>
								<td>		
								<iframe src="" name="rss_preview" style="background-color: #ffffff; width: 700px; height: 220px"/>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
  </body>
</html>

