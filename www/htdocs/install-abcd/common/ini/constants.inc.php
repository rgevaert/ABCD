<?php
/**
 * @desc        Constants for Web Install module  
 * @package     [ABCD] Web Install module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/

/* INIT CONFIGURATIONS */
define("NAME_SYSTEM","ABCD - Web Install module");
define("_WEBMASTER","bruno.neofiti@bireme.org");
define("_VERSION","1.0");
define("BVS_TEMP_DIR",BVS_ROOT_DIR."/../../temp");
define("BVS_LOG_DIR",BVS_TEMP_DIR);
define("BVS_COMMON_DIR",BVS_ROOT_DIR . "/common");
define("BVS_PUBLIC_DIR",BVS_ROOT_DIR . "/public");
define("BVS_LANG_DIR",BVS_ROOT_DIR . "/lang");
define("BVS_TPL_DIR",BVS_PUBLIC_DIR . "/templates");
define("DEBUG",false);
/** cria  o erro report E_STRICT caso na exista E_STRICT >= PHP5.0 **/
if(!(defined("E_STRICT"))){
	define("E_STRICT",2048);
}

date_default_timezone_set("America/Sao_Paulo"); 
$BVS_CONF["metaAuthor"] = "BIREME|OPAS|OMS";
$BVS_CONF["authorURI"] = "http://www.bireme.org/";
$BVS_CONF["copyright"] = date("Y");
$BVS_CONF["metaPublisher"] = "PFI";
$BVS_CONF["msgContactOwnText"] = "BIREME [PFI]";
$BVS_CONF["images_dir"] = dirname($_SERVER['PHP_SELF'])."/public/images/common";
$BVS_CONF["install_dir"] = dirname($_SERVER['PHP_SELF']);
$BVS_LANG["index"] = dirname($_SERVER['PHP_SELF']);
$BVS_CONF["version"] = _VERSION;
# Copyrighttext for the startpage
$BVS_CONF["copyright_eintrag"] = "BIREME|OPAS|OMS - PFI";

/**Path to database storage **/
getcwd();
chdir("../../");
$root_dir = getcwd();
                
?>