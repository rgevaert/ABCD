<?php
/**
 * @desc        File of system configuration
 * 				this file have all the parameters necessary to run the system  
 * @package     [ABCD] OAI module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/  

define("BVS_ROOT_DIR",getcwd());
require_once("constants.inc.php");

ini_set('include_path', BVS_COMMON_DIR);

/** Arquivos de classes e funcoes usadas neste sistema **/
require_once(BVS_COMMON_DIR . "/plugins/nusoap/lib/nusoap.php");
require_once(BVS_COMMON_DIR . "/class/beans/configurator.class.php");
require_once(BVS_COMMON_DIR . "/class/beans/model.class.php");
require_once(BVS_COMMON_DIR . "/class/beans/broker.class.php");
require_once(BVS_COMMON_DIR . "/class/dblil.class.php");
require_once(BVS_COMMON_DIR . "/class/cepal.class.php");
require_once(BVS_COMMON_DIR . "/class/marc.class.php");
require_once(BVS_COMMON_DIR . "/ini/functions.inc.php");
require_once(BVS_COMMON_DIR . "/plugins/error.inc.php");
require_once(BVS_COMMON_DIR . "/class/mountXml.class.php");
/**
 * Instaced the primitive class
 */
$configurator = new Configurator();
$isisBroker = new IsisBroker();
@header("Content-Type: text/xml");

//change the function will go mananger the error report from now
$old_error_handler = set_error_handler('erros');
?>