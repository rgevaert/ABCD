<?php
/**
 * @desc        Classes
 * @package     [ABCD] Web Install module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/  


	/**************************************** confApp ****************************************/

function confApp($appFileName,$appName){

		$installDir = $_REQUEST["field"]["installDir"];
		$confApp = $installDir.$appFileName.".php";
		$confAppnew = $installDir.$appFileName."New.php";
		$installDir = substr($installDir,2); //apenas para windows
		
		//lendo o novo .def(deve estar vazio)
		if (!$abrir = fopen($confAppnew, "a")) {
	         echo  "Erro abrindo arquivo ($confApp)";
	         exit;
	    }else{
			if($appName == "ABCD"){ 
				if(confABCD($installDir, $confApp, $abrir)){ $error = true; };
			}
			if($appName == "VHL"){ 
				if(confVHL($installDir, $confApp, $abrir)){ $error = true; }; 
			}
			if($appName == "Iah"){ 
				if(confIah($installDir, $confApp, $abrir)){ $error = true; };
			}

			unlink($confApp);
			rename($confAppnew,$confApp);

	    }
		
		return $error;
}

	/**************************************** confIah ****************************************/
function confIah($installDir, $confApp, $abrir){
	
		$error = true; 
		//lendo o arquivo .def
		$lines = file ($confApp);
		foreach ($lines as $line_num => $line) {

			/*
			 * Configuracao do Iah
			 */
			$varPATHCGI = substr($line, 0, 12);
			$varPATH_DATABASE = substr($line, 0, 13);

			if($varPATHCGI == "PATH_CGI-BIN"){
				$lines[$line_num]="PATH_CGI-BIN=$installDir/iah/scripts/\n";
				$error = false;
			}
			
			if($varPATH_DATABASE == "PATH_DATABASE"){
				$installBase = str_replace("htdocs", "bases", $installDir);
				$lines[$line_num]="PATH_DATABASE=$installBase/\n";
				$error = false;
			}

			//grava o novo .def
			if (!fwrite($abrir, $lines[$line_num])) {
		        print "Erro escrevendo no arquivo ($confApp)";
		        exit;
		    }
		}
		//FECHA O ARQUIVO 
		fclose($abrir);
		return $error;
}

	/**************************************** confVHL ****************************************/

function confVHL($installDir, $confApp, $abrir){
	
		$error = true; 
		//lendo o arquivo .def
		$lines = file ($confApp);
		foreach ($lines as $line_num => $line) {

			/*
			 * Configuracao do VHL
			 */
			$varSITE_PATH = substr($line,0,9);
			$varDATABASE_PATH = substr($line,0,13);

			if($varSITE_PATH == "SITE_PATH"){
				$lines[$line_num]="SITE_PATH=$installDir/site/\n";
				$error = false;
			}
			
			if($varDATABASE_PATH == "DATABASE_PATH"){
				$installBase = str_replace("htdocs", "bases", $installDir);
				$lines[$line_num] = "DATABASE_PATH=$installBase/site/\n";
				$error = false;
			}

			//grava o novo .def
			if (!fwrite($abrir, $lines[$line_num])) {
		        print "Erro escrevendo no arquivo ($confApp)";
		        exit;
		    }
		}
		//FECHA O ARQUIVO 
		fclose($abrir);
		return $error;
}

	/**************************************** confABCD ****************************************/

function confABCD($installDir, $confApp, $abrir){
	
		$error = true; 
		//lendo o arquivo .def
		$lines = file ($confApp);
		foreach ($lines as $line_num => $line) {

			/*
			 * Configuracao do ABCD
			 */
			$varDb_path = substr($line,1,7);
			$varWxis = substr($line,1,4);
			$xWxis = substr($line,1,5);
			$FCKConfigurationsPath = substr($line,1,21);

			if($varDb_path == "db_path"){
				$installBase = str_replace("htdocs", "bases", $installDir);
				$lines[$line_num]="\$db_path=\"$installBase\";\n";
				$error = false;
			}
			if($varWxis == "Wxis"){
				$installCgi = str_replace("htdocs", "cgi-bin", $installDir);
				$lines[$line_num]="\$Wxis=\"$installCgi/wxis.exe\";\n";
				$error = false;
			}

			if($FCKConfigurationsPath == "FCKConfigurationsPath"){
				$lines[$line_num] = "\$FCKConfigurationsPath=\"$installDir/php/dataentry/fckconfig.js\";\n";
				$error = false;
			}

			if($xWxis == "xWxis"){
				$lines[$line_num] = "\$xWxis=\"$installDir/php/dataentry/wxis/\";\n";
				$error = false;
			}

			//grava o novo .def
			if (!fwrite($abrir, $lines[$line_num])) {
		        print "Erro escrevendo no arquivo ($confApp)";
		        exit;
		    }
		}
		//FECHA O ARQUIVO 
		fclose($abrir);
		return $error;
}

?>





