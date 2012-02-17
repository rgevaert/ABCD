<?PHP
/* ini configuration */ 

include("../../php/include.php");

ini_set("error_reporting", E_PARSE|E_ERROR|E_WARNING);	//Enable errors, but not basic them
//ini_set("error_reporting", E_ALL);					//View all errors
//ini_set("error_reporting", 0);  						//Disable all errors

ini_set("register_globals", "off");
ini_set("upload_max_filesize","10MB");		// Maximum allowed size for uploaded files

/* Define constants of system */

/* Directories used for the project */
define("pFNOW", dirname(__FILE__));
define("pHOME", $def['SITE_PATH']);

/* Including necessary files */
require_once($sitePath . "/php/xmlRoot_functions.php");
require_once(pFNOW . "/include_functions.php");

$cfg["lang"] = ($_REQUEST['lang'] ? $_REQUEST['lang'] : "pt");
$cfg["base_directory"] = "local/";
$cfg["allowed_extensions"] = "(gif|bmp|png|jpg|jpeg|doc|txt|xml|pdf)";

?>
