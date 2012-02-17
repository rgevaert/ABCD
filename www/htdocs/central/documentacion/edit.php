<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      edit.php
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
include("../config.php");
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
if (!isset($arrHttp["archivo"])) {
    $arrHttp["archivo"]="";
}
if (!isset($arrHttp["carpeta"])) {
    $arrHttp["carpeta"]="";
}
$t=explode('/',$arrHttp["archivo"]);
$a=$db_path."documentacion/".$arrHttp["archivo"];
unset($fp);
$texto="";
if (file_exists($a)){
	$fp = file($a);
}else{
	$a=$db_path."documentacion/en/".$t[1]."/".$t[2];
	if (file_exists($a)) $fp=file($a);

}
if (isset($fp)) foreach ($fp as $value) $texto.= trim($value);
$texto=str_replace("'","`",$texto)
//
?>
<html>
	<head>
		<title>Archivos de Ayuda</title>
	</head>

		<script type="text/javascript" src="fckeditor.js"></script>
		<script type="text/javascript">
// FCKeditor_OnComplete is a special function that is called when an editor
// instance is loaded ad available to the API. It must be named exactly in
// this way.

function Enviar(){

	document.FCKfrm.submit()
}
function FCKeditor_OnComplete( editorInstance )
{
	// Show the editor name and description in the browser status bar.
	//document.getElementById('eMessage').innerHTML = 'Instance "' + editorInstance.Name + '" loaded - ' + editorInstance.Description ;

	// Show this sample buttons.
	//document.getElementById('eButtons').style.visibility = '' ;
}

function InsertHTML()
{
	// Get the editor instance that we want to interact with.
	var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;

	// Check the active editing mode.
	if ( oEditor.EditMode == FCK_EDITMODE_WYSIWYG )
	{
		// Insert the desired HTML.
		oEditor.InsertHtml( '- This is some <b>sample</b> HTML -' ) ;
	}
	else
		alert( 'You must be on WYSIWYG mode!' ) ;
}

function SetContents()
{
	// Get the editor instance that we want to interact with.
	var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;

	// Set the editor contents (replace the actual one).
	//oEditor.SetHTML( '<?php echo $texto?>' ) ;
}

function GetContents()
{
	// Get the editor instance that we want to interact with.
	var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;

	// Get the editor contents in XHTML.
	alert( oEditor.GetXHTML( true ) ) ;		// "true" means you want it formatted.
}

function ExecuteCommand( commandName )
{
	// Get the editor instance that we want to interact with.
	var oEditor = FCKeditorAPI.GetInstance('FCK') ;

	// Execute the command.
	oEditor.Commands.GetCommand( commandName ).Execute() ;
}

function GetLength()
{
	// This functions shows that you can interact directly with the editor area
	// DOM. In this way you have the freedom to do anything you want with it.

	// Get the editor instance that we want to interact with.
	var oEditor = FCKeditorAPI.GetInstance('FCKeditor') ;

	// Get the Editor Area DOM (Document object).
	var oDOM = oEditor.EditorDocument ;

	var iLength ;

	// The are two diffent ways to get the text (without HTML markups).
	// It is browser specific.

	if ( document.all )		// If Internet Explorer.
	{
		iLength = oDOM.body.innerText.length ;
	}
	else					// If Gecko.
	{
		var r = oDOM.createRange() ;
		r.selectNodeContents( oDOM.body ) ;
		iLength = r.toString().length ;
	}

	alert( 'Actual text length (without HTML markups): ' + iLength + ' characters' ) ;
}
		</script>
	</head>
	<body>
    <a href=http://docs.fckeditor.net/FCKeditor_2.x/Users_Guide/Quick_Reference target=_blank><?php echo $msgstr["fckeditor"]?></a>
		<form action="procesar.php" method="post"  name=FCKfrm onSubmit="Enviar();return false">
		<?php echo $msgstr["edhlp"]?>:<input type=hidden name=archivo value='<?php echo $arrHttp["archivo"]. "'>".$arrHttp["archivo"]?>
			<script type="text/javascript">
var sBasePath = '<?php echo $FCKEditorPath?>' ;
var oFCKeditor = new FCKeditor( 'FCK','100%','500' ) ;
oFCKeditor.BasePath	= sBasePath ;
oFCKeditor.Config["CustomConfigurationsPath"] = "<?php echo $FCKConfigurationsPath?>"
oFCKeditor.Config["DefaultLanguage"]		= "<?php echo $_SESSION["lang"]?>" ;
//oFCKeditor.Config["DocType"] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' ;

//$FCK_UserFilesPath="/abcd/php/img/";   //Este parámetro lo usa el FCKEditor para el path hacia los archivos de imágenes
//$FCK_UserFilesAbsolutePath="c:\\inetpub\\wwwroot\\abcd\\php\\img\\";   //este parámetro es la ruta real hacia los archivos de imágenes
//	$UPGRADE="N";
oFCKeditor.Value	= '<?php echo str_replace('php',$app_path,$texto)?>' ;
oFCKeditor.Create() ;

			</script>
			<input type=hidden name=Opcion>
			<input type=hidden name=archivo_o value="<?php echo $arrHttp["archivo"]?>">
			<br>
			<input type="submit" value="<?php echo $msgstr["save"]?>" onClick=javascript:document.FCKfrm.Opcion.value="Revisar">  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
			<input type="submit" value="<?php echo $msgstr["close"]?>" onclick=javascript:self.close()>
		</form>
		<div>&nbsp;</div>
	</body>
</html>
<script>

</script>