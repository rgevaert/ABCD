<?php
//Convierte un archivo TXT con rótulos a TagIsis, en base a un archivo de conversión que debe tener el siguiente formato:
//Primera línea: Separador de registros
//líneas subsiguientes: esquema de conversión suministrado de la siguiente forma:
//Rótulo|Tag Isis|Separador ocurrencias|Subcampos|Delimitadores

function Rotulos2Tags($Rotulos,$Texto){
Global $noLocalizados;
	$Texto=trim($Texto);
	$ixt=-1;
	$salida=array();
	foreach($Rotulos as $key => $value){
//		echo "$value[0]<br>";
		$inicio=strpos($Texto,$value[0]);
		if ($inicio===false){
		}else{
			$in=$inicio;
			$inicio=$inicio+strlen($value[0]);
			$fin=strpos($Texto,'$$',$inicio);
			if ($fin ===false) $fin=strlen($Texto);
			$var=substr($Texto,$inicio,$fin-$inicio);
			$salida[$key]=str_replace("\n"," ",$var);
			$Texto=substr($Texto,0,$in).substr($Texto,$fin);
		}
	}
	$noLocalizados=$Texto;
	return $salida;
	
}
?>
