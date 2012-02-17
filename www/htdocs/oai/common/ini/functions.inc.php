<?php
/**
 * @desc        File of system configuration
 * @package     [ABCD] OAI module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/  

/**
 * @desc  generate a randon string
 * @param string $size Size of generated string
 * @param boolean $with_numbers option to use numbers
 * @param boolean $with_tiny_letters option to use tiny letters
 * @param boolean $with_capital_letters option to use capital letters
 * @access public
 **/
function string_generator($size=10, $with_numbers=true, $with_tiny_letters=true, $with_capital_letters=true)
{
	global $string_g;

	$string_g = "";
	$sizeof_lchar = 0;
	$letter = "";
	$letter_tiny = "abcdefghijklmnopqrstuvwxyz";
	$letter_capital = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$letter_number = "0123456789";

	if($with_tiny_letters == true)
	{
		$sizeof_lchar += 26;
		if (isset($letter))
		{
			$letter .= $letter_tiny;
		} else {
			$letter = $letter_tiny;
		}
	}

	if($with_capital_letters == true)
	{
		$sizeof_lchar += 26;
		if (isset($letter))
		{
			$letter .= $letter_capital;
		} else {
			$letter = $letter_capital;
		}
	}

	if($with_numbers == true)
	{
		$sizeof_lchar += 10;
		if (isset($letter))
		{
			$letter .= $letter_number;
		} else {
			$letter = $letter_number;
		}
	}
	if($sizeof_lchar > 0)
	{
		srand((double)microtime()*date("YmdGis"));
		for($cnt = 0; $cnt < $size; $cnt++)
		{
			$char_select = rand(0, $sizeof_lchar - 1);
			$string_g .= $letter[$char_select];
		}
	}
	return $string_g;
}
/**
 * @desc Transforma os dados de um XML em array
 * @param strint URI $xml
 * @return array
 */
function fetch_xml($xml)
{
	if(@is_file($xml)){		
		$xml_data = file_get_contents($xml);
	}
	else{
		$xml_data = $xml;
	}
	$parser = xml_parser_create();
	xml_parse_into_struct($parser, $xml_data, &$assoc_arr, &$idx_arr);
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	reset($assoc_arr);
	echo print_r($assoc_arr);
	die;
	$root_tag = $assoc_arr[0]['tag'];
	$base_tag = strtolower($assoc_arr[1]['tag']);
	$i = 0;
	foreach($assoc_arr as $key => $element){		
		if($element['tag'] != $root_tag){
			if(!preg_match('/^\s+$/', $element['value'])){
				$tag = strtolower($element['tag']);
				$items[$i][$tag] = $element['value'];
				if($tag == $base_tag){
					$i++;
				}
			}
			elseif(isset($element['attributes'])){
				$items[$i]['mfn'] = $element['attributes']['MFN'];
			}
		}
	}
	return $items;
}
/**
 * @desc Function array_combine exist only PHP > 5
 * 		  use to combine two arrays with keys
 * @param array
 * @param array
 * @return array
 */
if(!(function_exists("array_combine"))) {
	function array_combine($a, $b)
	{
   		$result = array();
   		while(($key = each($a)) && ($val = each($b)))
   		{
      		$result[$key[1]] = $val[1];
   		}
   		
   		return($result);
	}
}


  
/**
 * @return void
 * @param int        $errno       numero do erro
 * @param string     $errmsg      mensagem de erro
 * @param string     $filename    nome do arquivo
 * @param int        $linenum     n�mero da linha
 * @param array      $vars        diversas vari�veis do sistema ($_SERVER
 * @desc funcao que servira de callBack
*/
function erros($errno, $errmsg, $filename, $linenum, $vars) {
	$erro = new Erro($errno, $errmsg, $filename, $linenum, $vars);
}

/**
 * @desc Funcao que reduz uma string passada de acordo com a qtd de caracteres
 * @param string $str string a ser reduzida
 * @param int $char Qtd de caracteres a serem retirados
 * @return string
 */
function makeShorterText($str, $char)
{
	$str = ereg_replace("[[:space:]]+", " ", $str);
	$arrStr = explode(" ", $str);
	$shortStr = "";
	$num = count($arrStr);
	if ($num > $char) {
		for ($j = 0; $j <= $char; $j++) {
			$shortStr .= $arrStr[$j]." ";
		}
		$shortStr .= "...";
	}else {
		$shortStr = $str;
	}
	return $shortStr;
}

function append_to_log($logstr)
{
	$timestamp = date("M d H:i:s");
	$path = BVS_LOG;
	$logfile = "/isisws.log";
	$log_append_str = "$timestamp " .$logstr;
	//echo $path.$logfile;
	if(file_exists($path.$logfile) && is_writeable($path.$logfile))
	{
		$fp = fopen($path.$logfile, 'a');
		fputs($fp, "$log_append_str/n");
		fclose($fp);
	}
	else if(!file_exists($path.$logfile) && is_writeable($path))
	{
		touch($path.$logfile);
		chmod($path.$logfile, 0600);
		$fp = fopen($path.$logfile, 'a');
		fputs($fp, "$log_append_strrn/n");
		fclose($fp);
	}
	else
	{
		trigger_error("Unable to write to file..",E_WARNING);
	}
}


/**
 * Funcao que recebe o xml do IsisScript e processa os dados
 * gerando um array com todos os campos, subcampos e repetitivos
 *
 * @param xml $xmlstr
 */
function xmlIsisToArrayField($xmlstr)
{
    $xml = new SimpleXMLElement($xmlstr);

    $record = array();
    $tmp_record = array();
    $_temp = array();

    foreach($xml->record->field as $field)
    {
        $fieldNames = $field->attributes();

        if(count($field->subfield) > 0) {
            $subfield = "";
            foreach ($field->subfield as $k => $v)
            {
                $subfieldAtt = $v->attributes();
                $subfield .= "^{$subfieldAtt["id"]}$v";
            }
            $tmp_record[]["{$fieldNames["tag"]}"] = array(trim($subfield),trim("{$field[0]}"));
        }else{
            $tmp_record[]["{$fieldNames["tag"]}"] = array(trim("{$field[0]}"));
        }
    }
    foreach ($tmp_record as $i => $content){
        foreach ($content as $tag => $val) {
            if(!(key_exists($tag,$_temp))) {
                $xi = 0;
            }
            $_temp[$xi]= $val[1] . $val[0];
            $xi++;
        }
        $record[]["$tag"] = $_temp;
        $_temp = array();
    }
    return $record;
}

?>