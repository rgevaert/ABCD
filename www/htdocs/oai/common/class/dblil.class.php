<?php
/**
 * @desc        Database control Class
 * @package     [ABCD] OAI module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/  

class dblil
{
    var $registro;
    var $totalRecords = 0;

    function __construct()
    {
            global $configurator;
            global $isisBroker;

            $this->registro = new Record();

    }
    function dblil() {
            $this->__construct();
    }

    /*
     * Function that pass XML to wxis-modules and it return the data based
     * in this XML. After that create an array with this data and return it
     */
    function setRecords($identifier, $path)
    {
        global $BVS_CONF, $configurator, $isisBroker, $DBLIL_TAG_NAME;

        $xmlparameters = "<parameters>\n";
        if($path){
            $xmlparameters .= "<database>".$path."</database>\n";
        }else{
            $xmlparameters .= "<database>".$configurator->getPath2Dblil()."</database>\n";
        }
            $xmlparameters .= "<search>$</search>\n";
        if($identifier){
            $xmlparameters .= "<from>".$identifier."</from>\n";
            $xmlparameters .= "<count>1</count>\n";
        }else{
            $xmlparameters .= "<from>1</from>\n";
        }
            $xmlparameters .= "<gizmo>GIZMO_XML</gizmo>\n";
            $xmlparameters .= "<xml_header>yes</xml_header>\n";
            $xmlparameters .= "<reverse>Off</reverse>\n";
            $xmlparameters .= "</parameters>\n";

            $rawxml = $isisBroker->search($xmlparameters);
            $posicion1 = strpos($rawxml,"<record");
            $posicion2 = strpos($rawxml,"</record>");

            $recordList = null;
            $i=0;
            while ($posicion1>0)
            {
                    $elemento = substr($rawxml,$posicion1,$posicion2-$posicion1+9);
                    $record = new Record();
                    $record->unserializeFromString($elemento);
                    $tempField = $record->campos;
                    $tempRecord = array();

                    while (list($key,$val) = each($tempField)) {
                        if($tempField[$key]->subcampos[0]->content != null){
                            //without subfields
                            $tempRecord += array($tempField[$key]->tag => $tempField[$key]->subcampos[0]->content);
                        }else{
                            //with subfields
                            $tempRecord += array($tempField[$key]->tag => $tempField[$key]->contenido);
                        }

                    }
                    $tempRecord += array("mfn" => $record->getMfn());
                    ksort($tempRecord);
                    $recordList[] = $tempRecord;
                    $tempRecord = null;
                    $rawxml = substr($rawxml,$posicion2+1);
                    $posicion1 = strpos($rawxml,"<record");
                    $posicion2 = strpos($rawxml,"</record>");
            }

            $this->setTotalRecords($recordList[0][1002]);
            return $recordList;
    }

    /*********************** getRecords ***********************/
    function getRecords($identifier, $path)
    {
            return  $this->setRecords($identifier, $path);
    }

    /*********************** setTotalRecords ***********************/
    function setTotalRecords($total)
    {
            $this->totalRecords = $total;
    }

    /*********************** totalDblil ***********************/
    function totalDblil()
    {       
        global $BVS_CONF, $configurator, $isisBroker, $DBLIL_TAG_NAME;

            $xmlparameters = "<parameters>\n";
            $xmlparameters .= "<database>".$configurator->getPath2Dblil()."</database>\n";
            $xmlparameters .= "<search>$</search>\n";
            $xmlparameters .= "<from>1</from>\n";
            $xmlparameters .= "<to>99999</to>\n";
            $xmlparameters .= "<count>1</count>\n";
            $xmlparameters .= "<gizmo>GIZMO_XML</gizmo>\n";
            $xmlparameters .= "<xml_header>yes</xml_header>\n";
            $xmlparameters .= "<reverse>Off</reverse>\n";
            $xmlparameters .= "</parameters>\n";

            $rawxml = $isisBroker->search($xmlparameters);
            $posicion1 = strpos($rawxml,"<record");
            $posicion2 = strpos($rawxml,"</record>");
            $recordList = null;
            $i=0;

            while ($posicion1>0)
            {
                $elemento = substr($rawxml,$posicion1,$posicion2-$posicion1+9);

                $record = new Record();
                $record->unserializeFromString($elemento);
                $tempField = $record->campos;
                $tempRecord = array();

                while (list($key,$val) = each($tempField)) {
                        if(array_key_exists($tempField[$key]->tag,$tempRecord)) {
                                $varTemp = $tempRecord[$tempField[$key]->tag];
                                $tempRecord[$tempField[$key]->tag] = array_merge($varTemp,$tempField[$key]->contenido);
                        }else {
                                $tempRecord += array($tempField[$key]->tag => $tempField[$key]->contenido);
                        }
                }

                $tempRecord += array("mfn" => $record->getMfn());
                ksort($tempRecord);
                $recordList[] = $tempRecord;
                $tempRecord = null;
                $rawxml = substr($rawxml,$posicion2+1);
                $posicion1 = strpos($rawxml,"<record");
                $posicion2 = strpos($rawxml,"</record>");

            }

            return $recordList[0][1002];
    }

}

?>