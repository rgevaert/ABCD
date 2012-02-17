<?php
/**
 * @desc        File of system configuration
 * 				this file have all the parameters necessary to run the system  
 * @package     [ABCD] Web Install module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/  
define("BVS_ROOT_DIR",getcwd());
require_once("constants.inc.php");

ini_set('include_path', BVS_COMMON_DIR);

require_once(BVS_COMMON_DIR . "/class/setup.class.php");
require_once(BVS_COMMON_DIR . "/plugins/smarty/libs/Smarty.class.php");
require_once(BVS_COMMON_DIR . "/ini/language.ini.php");

/**
 * Instaced the primitive class
 */

/** instanced of smarty class **/
$smarty = new Smarty();
/** config the smarty **/
//Template directorys
$smarty->template_dir = BVS_TPL_DIR . "/interface/";
$smarty->compile_dir = BVS_TEMP_DIR . '/templates_c/interface/';
$smarty->config_dir = BVS_COMMON_DIR . '/plugins/smarty/configs/';
$smarty->cache_dir = BVS_TEMP_DIR . '/cache/';
$smarty->caching = false;

@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
@header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
@header("Cache-Control: no-store, no-cache, must-revalidate");
@header("Cache-Control: post-check=0, pre-check=0", false);
@header("Pragma: no-cache");
@header("Content-type: text/html; charset=".$BVS_LANG["metaCharset"]);

//change the function will go mananger the error report from now
$old_error_handler = set_error_handler('erros');
?>