<?php
/*
 * File where the XML is mounted, is not a class
 * but a file with, many functions
 */

/*
 * As the data is storage in differentes fields in each methodology (dblil, marc and cepal).
 * This function format an array in according where the data is storage in a
 * database that follow the marc methodology.
 *
 * @param $allData array
 * @return $data array
 */
function arrayToMarc($allData){

    $totalReg = count($allData);
    if(is_array($allData)) {
        reset($allData);
        $data = array();
        while (list($key, $val) = each($allData)) {

            if($allData[$key]["110"]){
                    $creator = $allData[$key]["110"].$allData[$key]["700"];
            }else{
                    $creator = $allData[$key]["100"].$allData[$key]["700"];
            }

            if($allData[$key]["600"]){
                    $subject = $allData[$key]["600"];
            }elseif($allData[$key]["610"]){
                    $subject = $allData[$key]["610"];
            }elseif($allData[$key]["611"]){
                    $subject = $allData[$key]["611"];
            }else{
                    $subject = $allData[$key]["630"];
            }


            $data[] = array(
                    "mfn" => $allData[$key]["mfn"],
                    "identifier" => $allData[$key]["1"].$allData[$key]["3"],
                    "creator" => $creator,
                    "title" => $allData[$key]["245"],
                    "publisher" => $allData[$key]["260"],
                    "language" => substr($allData[$key]["8"],"35","3"),
                    "date" => substr($allData[$key]["8"],"7","4"),
                    "type" => $allData[$key]["9"],
                    "description" => $allData[$key]["520"],
                    "subject" => $subject,
                    "format" => "text/html"
            );
        }
    }

    array_push($data,$totalReg);
    return $data;

}

/*
 * As the data is storage in differentes fields in each methodology (dblil, marc and cepal).
 * This function format an array in according where the data is storage in a
 * database that follow the cepal methodology.
 *
 * @param $allData array
 * @return $data array
 */
function arrayToCepal($allData){

    if(is_array($allData)) {
        reset($allData);
        $data = array();
        while (list($key, $val) = each($allData)) {

            if($allData[$key]["6"] == "a"){
                if($allData[$key]["10"]){
                        $creator = $allData[$key]["10"];
                }elseif($allData[$key]["11"]){
                        $creator = $allData[$key]["11"];
                }else{
                        $creator = $allData[$key]["24"];
                }
                $title = $allData[$key]["12"];

            }else{
                if($allData[$key]["16"]){
                        $creator = $allData[$key]["16"];
                }elseif($allData[$key]["17"]){
                        $creator = $allData[$key]["17"];
                }else{
                        $creator = $allData[$key]["24"];
                }
                $title = $allData[$key]["18"];
            }

            if($title == ""){
                $title = $allData[$key]["30"];
            }

            $data[] = array(
                    "mfn" => $allData[$key]["mfn"],
                    "identifier" => $allData[$key]["2"],
                    "creator" => $creator,
                    "title" => $title,
                    "publisher" => $allData[$key]["38"],
                    "language" => $allData[$key]["64"],
                    "date" => $allData[$key]["44"],
                    "type" => $allData[$key]["9"],
                    "description" => $allData[$key]["72"],
                    "subject" => $allData[$key]["76"],
                    "format" => "text/html"
            );
        }
    }

    return $data;
}


/*
 * As the data is storage in differentes fields in each methodology (dblil, marc and cepal).
 * This function format an array in according where the data is storage in a
 * database that follow the dblil methodology.
 *
 * @param $allData array
 * @return $data array
 */
function arrayToDblil($allData){
    
    if(is_array($allData)) {
        reset($allData);
        $data = array();
        while (list($key, $val) = each($allData)) {

            if($allData[$key]["5"] == "M" && $allData[$key]["6"] == "m"){
                    $type = "book";
            }elseif($allData[$key]["5"] == "M" && $allData[$key]["6"] == "am"){
                    $type = "book article";
            }elseif($allData[$key]["5"] == "Ms" && $allData[$key]["6"] == "ms"){
                    $type = "book in series";
            }elseif($allData[$key]["5"] == "S" && $allData[$key]["6"] == "as"){
                    $type = "magazine articles in series";
            }else{
                    $type = "article";
            }

            if($allData[$key]["6"] == "a"){
                    if($allData[$key]["10"]){
                            $creator = $allData[$key]["10"];
                    }elseif($allData[$key]["11"]){
                            $creator = $allData[$key]["11"];
                    }else{
                            $creator = $allData[$key]["24"];
                    }
                    $title = $allData[$key]["12"];

            }else{
                    if($allData[$key]["16"]){
                            $creator = $allData[$key]["16"];
                    }elseif($allData[$key]["17"]){
                            $creator = $allData[$key]["17"];
                    }else{
                            $creator = $allData[$key]["24"];
                    }
                    $title = $allData[$key]["18"];
            }

            if($title){
                    $title = $allData[$key]["30"];
            }


            $data[] = array(
                    "mfn" => $allData[$key]["mfn"],
                    "identifier" => $allData[$key]["1"].$allData[$key]["2"],
                    "creator" => $creator,
                    "title" => $title,
                    "publisher" => $allData[$key]["62"],
                    "language" => $allData[$key]["40"],
                    "date" => $allData[$key]["65"],
                    "type" => $type,
                    "description" => $allData[$key]["83"],
                    "subject" => $allData[$key]["87"],
                    "format" => "text/html"
            );
        }
    }

    return $data;
}




/*
 * Function that mount the begin of  XML, and it is common to verbs
 * GetRecord, ListRecords, ListSets, ListIdentifiers.
 *
 * @param $verb sting (GetRecord, ListRecord, etc)
 * @param $payload
 * @param $returnXml
 * @return $envelop xml
 *
 */
function generateOAI_packet ( $verb, $payload )
{
    global $identifier, $metadataPrefix, $from, $until, $set, $resumptionToken;

    /*
     * The variable $envelop is where the xml is mounted. Data is add to it in
     * other functions but it still a local variable.
     */
    $envelop  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
    $envelop .= "<OAI-PMH xmlns=\"http://www.openarchives.org/OAI/2.0/\"\n";
    $envelop .= "         xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n";
    $envelop .= "         xsi:schemaLocation=\"http://www.openarchives.org/OAI/2.0/\n";
    $envelop .= "          http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd\">\n";

    $responseDate = gmdate ( "Y-m-d\TH:i:s\Z" );

    $envelop .= " <responseDate>" . $responseDate . "</responseDate>\n";
    $envelop .= " <request verb=\"" . $verb . "\"";

    if ( $metadataPrefix && !$resumptionToken ){
            $envelop .= " metadataPrefix=\"" . $metadataPrefix . "\"";
    }
    if ( $identifier && !$resumptionToken  ){
            $envelop .= " identifier=\"" . $identifier . "\"";
    }
    if ( $from  && !$resumptionToken ){
                    $envelop .= " from=\"" . $from . "\"";
    }
    if ( $until  && !$resumptionToken ){
            $envelop .= " until=\"" . $until . "\"";
    }
    if ( $set  && !$resumptionToken ){
            $envelop .= " set=\"" . $set . "\"";
    }
    if ( $resumptionToken ){
            $envelop .= " resumptionToken=\"" . $resumptionToken . "\"";
    }

    $request_uri = "http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
    $envelop .= ">" . $request_uri . "</request>\n";
    $envelop .= $payload;
    $envelop .= "<".$verb.">\n";

    return $envelop;
}

/*
 * Function that mount the begin of  XML, and it is common to all verbs.
 * Return an piece of XML.
 * @param $verb string
 */
function headXML($verb){

        @header("Content-Type: text/xml");
        $envelop  = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
        $envelop .= "<OAI-PMH xmlns=\"http://www.openarchives.org/OAI/2.0/\"\n";
        $envelop .= "         xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n";
        $envelop .= "         xsi:schemaLocation=\"http://www.openarchives.org/OAI/2.0/\n";
        $envelop .= "          http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd\">\n";

        $responseDate = gmdate ( "Y-m-d\TH:i:s\Z" );
        $envelop .= "<responseDate>" . $responseDate . "</responseDate>\n";
        $envelop .= "<request verb=\"" . $verb . "\">http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]."</request>\n";

        return $envelop;
}


/*
 * Function that mount an XML based in errors or missing itens, and print it
 * @param $verb string
 * @param $errorMsg string
 * @param $errorCode int
 */
function verbError($verb, $errorMsg, $errorCode){

        $envelop = headXML($verb);
        $result = "<error code=\"".$errorCode."\">".$errorMsg."</error>\n";
        $envelop .= $result;
        $envelop .= "</OAI-PMH>\n";
        print $envelop;
}


/*
 * Function that mount an XML based in the Identify verb, and return it
 */
function identify(){

        $request_uri = "http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
        $envelop = "<Identify>\n";
        $envelop .= "<repositoryName>ABCD - Libraries and Documentation Centers Automation System</repositoryName>\n";
        $envelop .= "<baseURL>".$request_uri."</baseURL>\n";
        $envelop .= "<protocolVersion>2.0</protocolVersion>\n";
        $envelop .= "<adminEmail>bruno.neofiti@bireme.org</adminEmail>\n";
        $envelop .= "<earliestDatestamp>1970-01-01</earliestDatestamp>\n";
        $envelop .= "<deletedRecord>no</deletedRecord>\n";
        $envelop .= "<granularity>YYYY-MM-DD</granularity>\n";
        $envelop .= "</Identify>\n";
        $envelop .= "</OAI-PMH>\n";
        return $envelop;
}

/*
 * Function that mount an XML based in the ListMetadataFormats verb, and return it
 */
function listMetadataFormats()
{
        $envelop = "<ListMetadataFormats>\n";
        $envelop .= "<metadataFormat>\n";
        $envelop .= "<metadataPrefix>oai_dc</metadataPrefix>\n";
        $envelop .= "<schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>\n";
        $envelop .= "<metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>\n";
        $envelop .= "</metadataFormat>\n";
        $envelop .= "</ListMetadataFormats>\n";
        $envelop .= "</OAI-PMH>\n";
        return $envelop;
}



/*
 * Function that mount an XML based in the ListRecords, ListSets and
 *  ListIdentifiers verbs. Mount an XML calling the functions
 * generateOAI_packet and db2Xml, then add time to XML and print it.
 *
 * @param $metadataPrefix string oai_dc
 * @param $methodology string (marc, cepa, dblil)
 * @param $path string OS path
 */
function listRecords($metadataPrefix, $methodology, $path){

        for($i=0; $i<$totalRec; $i++){
                //second part of xml
                $returnXml .= db2Xml($arrDatabase, $i, $_REQUEST["verb"]);
        }
        //first part of xml
        $oai_packet = generateOAI_packet($_REQUEST["verb"], "");
        //join both parts
        $envelop = $oai_packet.$returnXml;
        //add data information
        $responseDate = gmdate ( "Y-m-d\TH:i:s\Z" );
        $envelop .= "<resumptionToken>".$responseDate."</resumptionToken>\n";
        $envelop .= "</".$_REQUEST["verb"].">";
        $envelop .= "</OAI-PMH>\n";
        print $envelop;
}



/*
 * Function that mount an XML based in the GetRecords verb.
 * First pick the data (through a function of each database)
 * If the data is not empty, mount an XML calling the functions
 * generateOAI_packet and db2Xml, then add time to XML and print it.
 *
 * @param $identifier int (mfn)
 * @param $methodology sting (marc, cepal or dblil)
 * @param $fullpath string (OS path)
 */
function getRecord($identifier, $methodology, $fullpath)
{
    //create a data model, based in witch database was selected
    $dataModel = new $methodology();
    //Data model call method gerRecords to pick data from database
    $allData = $dataModel->getRecords($identifier, $fullpath);
    /*
     * Use code above to see one iten, with all fields:
     * print_r($allData[0]);
     * Use code above to see one iten, with field 8:
     * print_r($allData[0][8]);
     */
    //print_r(substr($allData[0]["8"],"35","3"));

    switch ($methodology) {
        //Pass the data to a function tha put it on XML format
        case "dblil":
                $arrDatabase = arrayToDblil($allData);
                break;
        case "cepal":
                $arrDatabase = arrayToCepal($allData);
                break;
        case "marc":
            print $fullpath;
                $arrDatabase = arrayToMarc($allData);
                break;
        default:
                $arrDatabase = arrayToDblil($allData);
    }

    //try to cath an error
    if($arrDatabase == ""){

        $errorMsg = "No matching identifier";
        $errorCode = "idDoesNotExist";
        //print an error
        print verbError($_REQUEST["verb"], $errorMsg, $errorCode);

    }else{

        //first part of xml
        $oai_packet = generateOAI_packet( $_REQUEST["verb"], "");
        //second part of xml
        $returnXml .= db2Xml($arrDatabase, "0", $_REQUEST["verb"]);
        //join booth parts
        $envelop = $oai_packet.$returnXml;
        //add date information to xml
        $responseDate = gmdate ( "Y-m-d\TH:i:s\Z" );
        $envelop .= "<resumptionToken>".$responseDate."</resumptionToken>\n";
        $envelop .= "</GetRecord>\n";
        $envelop .= "</OAI-PMH>\n";
        //print xml
        print $envelop;
   }
}


/*
 * Function that mount itens common to verbs ListRecords, ListSets, ListRecords
 * and GetRecord. It return a piece of an XML.
 *
 * @param $arrDatabase array
 * @param $dataIdentified int (mfn)
 * @param $verb string (GetRecord, ListRecords, ListSets, ListRecords)
 */
function db2Xml($arrDatabase, $dataIdentified, $verb){

        $year = substr($arrDatabase[$dataIdentified]["date"],0,4);
        $month = substr($arrDatabase[$dataIdentified]["date"],4,2);
        $day = substr($arrDatabase[$dataIdentified]["date"],6,2);
        $date = $year."-".$month."-".$day;

        if($verb == "ListRecords" || $verb == "GetRecord")
        {
                $envelop = "<record>\n";
        }

        if($verb == "ListSets" || $verb == "GetRecord"){
                $envelop .= "<set>\n";
                $envelop .= "<setSpec>".$arrDatabase[$dataIdentified]["identifier"]."</setSpec>\n";
                $envelop .= "<setName>".$arrDatabase[$dataIdentified]["title"]."</setName></set>\n";
        }else{
                $envelop .= "<header>\n";
        $envelop .= "<identifier>".$arrDatabase[$dataIdentified]["identifier"]." ".$dataIdentified."</identifier>\n";
                $envelop .= "<datestamp>".$date."</datestamp>\n";
                $envelop .= "<setSpec>".$arrDatabase[$dataIdentified]["identifier"]."</setSpec></header>\n";
        }
        if($verb == "ListRecords" || $verb == "GetRecord")
        {
                $envelop .= "<metadata>
                        <oai-dc:dc xmlns:oai-dc=\"http://www.openarchives.org/OAI/2.0/oai_dc/\" \n
                        xmlns:dc=\"http://purl.org/dc/elements/1.1/\" \n
                        xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" \n
                        xsi:schemaLocation=\"http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd\">\n";
                $envelop .= "<dc:title><![CDATA[".$arrDatabase[$dataIdentified]["title"]."]]></dc:title>\n";
                $envelop .= "<dc:creator><![CDATA[".$arrDatabase[$dataIdentified]["creator"]."]]></dc:creator>\n";
                $envelop .= "<dc:subject><![CDATA[".$arrDatabase[$dataIdentified]["subject"]."]]></dc:subject>\n";
                $envelop .= "<dc:description><![CDATA[".$arrDatabase[$dataIdentified]["description"]."]]></dc:description>\n";
                $envelop .= "<dc:publisher><![CDATA[".$arrDatabase[$dataIdentified]["publisher"]."]]></dc:publisher>\n";
                $envelop .= "<dc:date><![CDATA[".$arrDatabase[$dataIdentified]["date"]."]]></dc:date>\n";
                $envelop .= "<dc:type><![CDATA[".$arrDatabase[$dataIdentified]["type"]."]]></dc:type>\n";
                $envelop .= "<dc:format><![CDATA[".$arrDatabase[$dataIdentified]["format"]."]]></dc:format>\n";
                $envelop .= "<dc:identifier><![CDATA[".$arrDatabase[$dataIdentified]["identifier"]."]]></dc:identifier>\n";
                $envelop .= "<dc:language><![CDATA[".$arrDatabase[$dataIdentified]["language"]."]]></dc:language>\n";
                $envelop .= "</oai-dc:dc>\n</metadata>\n";
                $envelop .= "</record>\n";
        }
        return 	$envelop;
}

?>
