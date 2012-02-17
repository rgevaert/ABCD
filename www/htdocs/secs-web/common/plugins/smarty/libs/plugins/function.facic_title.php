<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

die("candidato a apagar - smarty faci_title");
/**
 * Smarty {facic_title} function plugin
 *
 * Type:     function<br>
 * Name:     facic_title<br>
 * Input:<br>
 *           - title     (required) - array of title of  the facic data
 * Purpose:  Print the Title name relateda one collection of facic
  * @author Domingos Teruel <domingos.teruel@terra.com.br>
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_facic_title($params, &$smarty)
{
	global $BVS_CONF, $configurator, $isisBroker, $FACIC_TAG_NAME;		
		$xmlparameters = "<parameters>\n";
		$xmlparameters .= "<database>".$configurator->getPath2title()."</database>\n";
		//Used when has search		
		$xmlparameters .= "<search>I={$params['title']}</search>\n";
		$xmlparameters .= "<from>1</from>\n";
		$xmlparameters .= "<count>1</count>\n";
		
		$xmlparameters .= "<gizmo>GIZMO_XML</gizmo>\n";
		$xmlparameters .= "<xml_header>yes</xml_header>\n";
		$xmlparameters .= "<reverse>Off</reverse>\n";
		$xmlparameters .= "</parameters>\n";			
		if(isset($_REQUEST["title"]) && $_REQUEST["title"] != "") {
			$rawxml = $isisBroker->search($xmlparameters);	
		}else{
			user_error("Pesquisa Invalida, Sem Título para pesquisar");
		}
				
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
			//array_push($recordList,$tempRecord);
						
			$tempRecord = null;

			$rawxml = substr($rawxml,$posicion2+1);

			$posicion1 = strpos($rawxml,"<record");
			$posicion2 = strpos($rawxml,"</record>");

		}
		
		return $recordList[0][100];	
}

?>
