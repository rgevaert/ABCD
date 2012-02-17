<?php
/**
 * @desc        Constants for OAI module
 * @package     [ABCD] OAI module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/  

/* INIT CONFIGURATIONS */
define("BVS_COMMON_DIR",BVS_ROOT_DIR . "/common");
date_default_timezone_set("America/Sao_Paulo"); 

/**Path to database storage **/
getcwd();
chdir("../../");
$root_dir = getcwd();
$BVS_CONF['URLWS'] = "http://" . $_SERVER['HTTP_HOST'] . "/isisws/isiswsdl.php?wsdl";
$BVS_CONF['PATH2DBLIL'] = "$root_dir/bases/dblil/data/dblil";
$BVS_CONF['PATH2CEPAL'] = "$root_dir/bases/biblo/data/biblo";
$BVS_CONF['PATH2MARC'] = "$root_dir/bases/marc/data/marc";

?>