<?php
    $DirNameLocal=dirname(__FILE__).'/';
    error_reporting( 0 );
    ini_set('display_errors', false);

    // define constants
    define("VERSION","4.0.2");
    define("USE_SERVER_PATH", false);

    if (USE_SERVER_PATH == true){
        if( isset($_SERVER["PATH_TRANSLATED"]) ){
            $pathTranslated = dirname($_SERVER["PATH_TRANSLATED"]);
        } else {
            $pathTranslated = dirname($_SERVER["SCRIPT_FILENAME"]);
        }
        $sitePath = dirname($pathTranslated);
    }else{
        $sitePath = realpath($DirNameLocal . "..");
    }
    $def = parse_ini_file($sitePath ."/bvs-site-conf.php");

    foreach ($def as $key => $value){
        define($key, $value);
    }

    // Secured Array
    $checked  = array();

    $lang = false;
    $checked['lang'] = $def["DEFAULT_LANGUAGE"];
    if( isset($_REQUEST["lang"]) && !empty($_REQUEST["lang"]) ){
        $lang = $_REQUEST["lang"];
    }else if( isset($_COOKIE["clientLanguage"]) && !empty ($_COOKIE["clientLanguage"])){
        $lang = $_COOKIE["clientLanguage"];
    }
    if($lang){
        if( preg_match('/^[a-z][a-z](-[a-z][a-z])?$/i',$lang) ){
            $checked['lang'] = $lang;
            setCookie("clientLanguage",$lang,time()+60*60*24*30,"/");
        }
    }
    $lang = $checked['lang'];


    if ( isset($_GET["component"]) && !ereg("^[0-9]+$", $_GET["component"]) )
        die("404 - File Not Found");
    else
        $checked['component'] = $_GET["component"];

    if ( isset($_GET["item"]) && !ereg("^[0-9]+$", $_GET["item"]) )
        die("404 - File Not Found");
    else
        $checked['item'] = $_GET["item"];

    if ( isset($_GET["id"]) && !ereg("^[0-9]+$", $_GET["id"]) )
        die("404 - File Not Found");
    else
        $checked['id'] = $_GET["id"];

    $localPath['html']= $def['DATABASE_PATH'] . "html/" . $checked['lang'] . "/";
    $localPath['xml'] = $def['DATABASE_PATH'] . "xml/" . $checked['lang'] . "/";
    $localPath['ini'] = $def['DATABASE_PATH'] . "ini/" . $checked['lang'] . "/";

?>
