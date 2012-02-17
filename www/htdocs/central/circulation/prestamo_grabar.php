<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      prestamo_grabar.php
 * @desc:      Loan save
 * @author:    Guilda Ascencio
 * @since:     20091203
 * @version:   1.0
 *
 * == BEGIN LICENSE ==
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *   
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *   
 * == END LICENSE ==
*/
session_start();
$_SESSION["login"]="abcd";
include("../config.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
$ValorCapturado="0001P\n";
$ValorCapturado.="0010".$arrHttp["inven"]."\n";
$ValorCapturado.="0020".$arrHttp["usuario"]."\n";
$ValorCapturado.="0030".$arrHttp["fp"]."\n";
$ValorCapturado.="0035".$arrHttp["hp"]."\n";
$ValorCapturado.="0040".$arrHttp["fd"]."\n";
$ValorCapturado.="0045".$arrHttp["hd"]."\n";
$ValorCapturado.="0070".$arrHttp["tu"]."\n";
$ValorCapturado.="0080".$arrHttp["to"]."\n";
$ValorCapturado.="0100".$arrHttp["referencia"]."\n";
echo "<xmp>".$ValorCapturado."</xmp>";
die;
$ValorCapturado=urlencode($ValorCapturado);
$IsisScript=$xWxis."crear_registro.xis";
$Formato=$db_path."trans/pfts/en/trans";
$query = "&base=trans&cipar=$db_path"."par/trans.par&login=".$_SESSION["login"]."&Formato=$Formato&Mfn=&ValorCapturado=".$ValorCapturado;
include("../common/wxis_llamar.php");

header("Location: usuario_prestamos_presentar.php?base=users&usuario=".$arrHttp["usuario"]);
?>