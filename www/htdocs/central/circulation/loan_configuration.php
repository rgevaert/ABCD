<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      loan_configuration.php
 * @desc:      Configuration of the user's module
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
session_start();
require_once("config.php");
require_once($db_path."lang/en/prestamo.php");
require_once($db_path."lang/$lang/prestamo.php");

function LeerArchivosConfiguracion($Base){
//
//Lectura de la configuración de los archivos del usuario
//
//Prefijo para localizar el número de inventario y el número de clasificación

	$uskey="";
	$archivo=$db_path.$base."/def/loans_uskey.tab";
	$fp=file_exists($archivo);
	if ($fp){
		$fp=file($archivo);
		foreach ($fp as $value){
			$value=trim($value);
    		if ($value!="")$uskey=$value;
		}
	}
//Formato para extraer el código del usuario
	$pft_uskey="@".$db_path.$base."/def/loans_uskey.pft";
//Formato para extraer el tipo de usuario
	$pft_ustype="@".$db_path.$base."/def/loans_ustype.pft";
//Formato para extraer la vigencia del usuario
	$pft_usvig="@".$db_path.$base."/def/loans_usvig.pft");
//Formato para desplegar la información del usuario
	$pft_usdisp="@".$db_path.$base."/def/loans_usdisp.pft";

//
// Parámetros requeridos para configurar la base de datos con los objetos de préstamo
//
	$archivo=$db_path.$base."/def/loans_conf.tab";
	$fp=file_exists($archivo);
	if ($fp){
		$fp=file($archivo);
		foreach ($fp as $value){

			$ix=strpos($value," ");
			$tag=trim(substr($value,0,$ix));
			switch($tag){
				case "IN": $prefix_in=substr($value,$ix);
					break;
				case "NC": $prefix_nc=substr($value,$ix);
			}
		}
	}
    $pft_totalitems="@".$db_path.$base."/def/loans_totalitems.pft";  //Total items
	$pft_in="@".$db_path.$base."/def/loans_inventorynumber.pft";     //Número de inventario
	$pft_nc="@".$db_path.$base."/def/loans_cn.pft");                 //Número de clasificación
	$pft_dispobj="@".$db_path.$base."/def/loans_display.pft";        //Visualizar el registro
	$pft_storobj="@".$db_path.$base."/def/loans_store.pft";          //almacenar el registro
	$pft_loandisp="@".$db_path.$base."/def/loans_show.pft");         //Mostrar el registro desde préstamos
	$pft_typeofr="@".$db_path.$base."/def/loans_typeofobject.pft");  //Obtener el tipo de objeto
?>