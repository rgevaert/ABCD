<?php
/**
 * @desc        Controller file to OAI
 * @package     [ABCD] OAI module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009xxx
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/  

require_once("./common/ini/config.ini.php");

//Cath all the parameters passed in the URL
$verb = $_REQUEST["verb"];
$methodology = $_REQUEST["methodology"];
$metadataPrefix = $_REQUEST["metadataPrefix"];
$identifier = $_REQUEST["identifier"];
$methodology = $_REQUEST["database"];
$from = $_REQUEST["from"];
$path = $_REQUEST["path"];
$error = false;
$request_uri = "http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];

//Try to cath empty parameters
if ( !isset($verb) || empty($verb) )
{
        $errorMsg = "Illegal OAI verb";
        $errorCode = "badVerb";
        $error = true;

}else{

    //Try to cath empty parameters
    if ( !isset ($metadataPrefix) || empty ($metadataPrefix) )
    {
            $errorMsg = "Missing or empty metadataPrefix";
            $errorCode = "badArgument";
            if($verb == "ListSets"){
                $error = false;
            }else{
                $error = true;
            }
            
    }else{
            if( $metadataPrefix  != "oai_dc"){
                    $errorMsg = "The value of the metadataPrefix argument is not supported by the repository";
                    $errorCode = "cannotDisseminateFormat";
                    $error = true;
            }
    }

    //Try to cath wrong parameters
    if ( isset($verb) && $verb == "GetRecord" ){
            if ( !isset($identifier) || empty($identifier) || !preg_match('/^[0-9]+$/', $identifier) )
            {
                    $errorMsg = "No matching identifier";
                    $errorCode = "idDoesNotExist";
                    $error = true;
            }
    }

    //if the database is not in the default path
    if (isset($path) && $path != ""){
        $fullpath = getcwd()."/bases/".$path;
    }

    /*
     * Select database if its passed by parameter.
     * In OAI-PMH this parameter dont exist, so if it is empty select database dblil.
     * if it is a wrong value, catch error.
     */
    if (isset($methodology) && $methodology != ""){
            switch ($methodology) {
                    case "dblil":
                            $methodology = "dblil";
                            break;
                    case "cepal":
                           $methodology = "cepal";
                            break;
                    case "marc":
                            $methodology = "marc";
        break;
                    default:
                            $errorMsg = "No matching database";
                            $errorCode = "databaseDoesNotExist";
                            $error = true;
            }
    }else{
            $methodology = "dblil";
    }

    /*
     * Cath witch verb is passed by parameter. Then call a function that mount
     * an XML based in this verb and print it.
     * If $verb is a wrong value, catch error.
     */
    
    switch ($verb) {
            case "ListRecords":
                    if($error == false){
                            $dataModel = new $methodology();
                            listRecords($metadataPrefix, $methodology, $fullpath);
                    }
                    break;
            case "GetRecord":
                    if($error == false){
                            $dataModel = new $methodology();
                            getRecord($identifier, $methodology, $fullpath);
                    }
                    break;
            case "Identify":
                    print headXML($verb);
                    print identify();
                    $error = false;
                    break;
            case "ListIdentifiers":
                    if($error == false){
                            $dataModel = new $methodology();
                            listRecords($metadataPrefix, $methodology);
                    }
                    break;
            case "ListSets":
                    listRecords($metadataPrefix, $methodology);
                    break;
            case "ListMetadataFormats":
                    print headXML($verb);
                    print listMetadataFormats();
                    $error = false;
                    break;
            default:
                    $verb="";
                    $errorMsg = "Illegal OAI verb";
                    $errorCode = "badVerb";
    }
}

//If have errors, show message 
if ($errorMsg && $error == true)
{
    print verbError($verb, $errorMsg, $errorCode);

}

?>			