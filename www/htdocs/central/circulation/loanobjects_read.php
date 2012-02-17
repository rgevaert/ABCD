<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      loanobjects_read.php
 * @desc:      
 * @author:    Guilda Ascencio
 * @since:     20091203
 * @version:   1.0
 *
 * == BEGIN LICENSE ==
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *   
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *   
 * == END LICENSE ==
*/
$rows_title[0]=$msgstr["tit_tm"];
$rows_title[1]=$msgstr["tit_tu"];
$rows_title[2]=$msgstr["tit_np"];
$rows_title[3]=$msgstr["tit_lpn"];
$rows_title[4]=$msgstr["tit_lpr"];
$rows_title[5]=$msgstr["tit_unid"];
$rows_title[6]=$msgstr["tit_renov"];
$rows_title[7]=$msgstr["tit_multa"];
$rows_title[8]=$msgstr["tit_multar"];
$rows_title[9]=$msgstr["tit_susp"];
$rows_title[10]=$msgstr["tit_suspr"];
$rows_title[11]=$msgstr["tit_reserva"];
$rows_title[12]=$msgstr["tit_permitirp"];
$rows_title[13]=$msgstr["tit_permitirr"];
$rows_title[14]=$msgstr["tit_copias"];
$rows_title[15]=$msgstr["tit_limusuario"];
$rows_title[16]=$msgstr["tit_limobjeto"];
$rows_title[17]=$msgstr["tit_inf"];

$archivo=$db_path."circulation/def/".$_SESSION["lang"]."/typeofitems.tab";
if (!file_exists($archivo)) $archivo=$db_path."circulation/def/".$lang_db."/typeofitems.tab";
if (file_exists($archivo)){
	$fp=file($archivo);
}else{
	echo $msgstr["nopolicy"];
	die;
}
$ix=0;
foreach ($fp as $value) {	if (!empty($value)) {
		$Ti=explode('|',$value);
		$politica[$Ti[0]][$Ti[1]]=trim($value);
  	}
}
?>