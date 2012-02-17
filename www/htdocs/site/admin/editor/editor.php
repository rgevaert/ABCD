<?
require_once("../auth_check.php");

auth_check_login();

$lang = ( $_REQUEST['lang'] != '' ? $_REQUEST['lang'] : 'pt' );

$admPath = substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'editor.php'));

?>
<html>
  <head>
    <title>BVS-Site Admin</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	
	<script type="text/javascript" src="../../bvs-mod/FCKeditor/fckeditor.js"></script>
	
	<script type="text/javascript">
      window.onload = function()
      {
        var editor = new FCKeditor( 'buffer' ) ;
        editor.BasePath = "../../bvs-mod/FCKeditor/";
   	    editor.Height = "96%";
		editor.Config["AutoDetectLanguage"] = false ;
		editor.Config["ProcessHTMLEntities"] = false;
		editor.Config["DefaultLanguage"] = "<?if ($lang =='pt') echo 'pt-br'; else $lang;?>" ;
		
		editor.Config["LinkBrowserURL"] = "<?=$admPath?>filemanager/browser/browser.html?Connector=connectors/php/connector.php";
		editor.Config["LinkUploadURL"]  = "<?=$admPath?>filemanager/upload/php/upload.php?Type=File";
		editor.Config["ImageBrowserURL"]= "<?=$admPath?>filemanager/browser/browser.html?Type=Image&Connector=connectors/php/connector.php";
		editor.Config["ImageUploadURL"] = "<?=$admPath?>filemanager/upload/php/upload.php?Type=Image";
		editor.Config["FlashBrowserURL"]= "<?=$admPath?>filemanager/browser/browser.html?Type=Flash&Connector=connectors/php/connector.php";
		editor.Config["FlashUploadURL"] = "<?=$admPath?>filemanager/upload/php/upload.php?Type=Flash";
		
        editor.ReplaceTextarea() ;
      }
	  
  	  function updateContent() {
		// Get the editor instance that we want to interact with.
		var editor = FCKeditorAPI.GetInstance('buffer') ;
		// Get the editor contents in XHTML.
		opener.HTMLAreaElement.value = editor.GetXHTML(true);			
		
		window.close();
	  }

	  var msgModify = opener.HTMLAreaModifyButtonLabel;
	  var msgCancel = opener.HTMLAreaCancelButtonLabel;

    </script>
	
	<link rel="stylesheet" href="../../css/admin/adm.css" type="text/css" />

  </head>

  <body style="margin: 2px;">
  	<form action="#" onsubmit="updateContent()" name="formEditor">
		<script language="JavaScript">
			document.write('<textarea id="buffer">');
			document.write(opener.HTMLAreaElement.value);
			document.write('</textarea>');
	
			document.write('<div align="center">');			
			document.write('  <input type="button" name="cancel" value="' + msgCancel + '" onclick="javascript: window.close();" class="button"/>');
			document.write('  <input type="submit" name="ok" value="' + msgModify + '" class="button"/>');
			document.write('</div>');
		</script>
	</form>
  </body>
  
</html>
  
