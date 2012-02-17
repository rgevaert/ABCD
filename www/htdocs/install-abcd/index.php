<?php 
/**
 * @desc        Controller file to Web Install Module
 * @package     [ABCD] Web Install module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/  

require_once("./common/ini/config.ini.php"); 

	if(isset($_GET["action"]) && !preg_match("=/=",$_GET["action"]) ) {

		$page = 'index';
		
		/*
		 * ABCD Installation
		 */
		$pathABCD = "/central/config";
		$nameABCD = "ABCD";
		if(confApp($pathABCD,$nameABCD)){ $error = true; }
		
		/*
		 * VHL-site Installation
		 */
		$pathVHLsite = "/site/bvs-site-conf";
		$nameVHLsite = "VHL";
		if(confApp($pathVHLsite,$nameVHLsite)){ $error = true; }

		/*
		 * Iah Installation
		 */
		$pathIah = "/iah/scripts/iah.def";
		$nameIah = "Iah";
		if(confApp($pathIah,$nameIah)){ $error = true; }

		if(!$error){
			$listRequest = "success";
			$smartyTemplate = "success";
		}else{
			user_error($BVS_LANG["errorInstall"],E_USER_ERROR);
		}
	
	}else{

		$page = 'index';
		$listRequest = "install";
		$smartyTemplate = "install";
	}


/** Registramos as variaveis $smartyTemplate que auxilia a $listRequest **/
$smarty->assign("smartyTemplate",$smartyTemplate);
$smarty->assign("listRequest",$listRequest);

$vars = array();
$smarty->assign("OBJECTS",$vars);
$smarty->assign("BVS_CONF",$BVS_CONF);
$smarty->assign("BVS_LANG",$BVS_LANG);
// Exibe o template montado
$smarty->display($page.'.tpl.php');
?>