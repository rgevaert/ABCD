<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      fdt_include.php
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
//TABLE COLUMNS
	$rows_title=array();
	$rows_title[0]=$msgstr["row"];
	$rows_title[1]=$msgstr["type"];
	$rows_title[2]=$msgstr["tag"];
	$rows_title[3]=$msgstr["title"];
	$rows_title[4]="&nbsp;I&nbsp;";
	$rows_title[5]="&nbsp;R&nbsp;";
	$rows_title[6]=$msgstr["subfields"];
	$rows_title[7]=$msgstr["preliteral"];
	$rows_title[8]=$msgstr["inputtype"];
	$rows_title[9]=$msgstr["rows"];
	$rows_title[10]=$msgstr["cols"];
	$rows_title[11]=$msgstr["type"];
	$rows_title[12]=$msgstr["name"];
	$rows_title[13]=$msgstr["prefix"];
	$rows_title[14]=$msgstr["browse"];
	$rows_title[15]=$msgstr["listas"];
	$rows_title[16]=$msgstr["extractas"];
	$rows_title[17]=$msgstr["valdef"];
	$rows_title[18]=$msgstr["help"];
	$rows_title[19]=$msgstr["url_help"];

//FIELDS TYPES
	$field_type=array();
	$field_type["F"]=$msgstr["ft_f"];
	$field_type["AI"]=$msgstr["ft_ai"];
	$field_type["S"]=$msgstr["ft_s"];
	$field_type["M"]=$msgstr["ft_m"];
	$field_type["M5"]=$msgstr["ft_m5"];
	$field_type["LDR"]=$msgstr["ft_ldr"];
	$field_type["T"]=$msgstr["ft_t"];
	$field_type["L"]=$msgstr["ft_l"];
	$field_type["H"]=$msgstr["ft_h"];
	$field_type["OD"]=$msgstr["ft_od"];
	$field_type["ISO"]=$msgstr["ft_iso"];
	$field_type["OC"]=$msgstr["ft_oper"];
	$field_type["DC"]=$msgstr["ft_date"];


//HTML INPUT TYPE
	$input_type=array();
	$input_type["X"]=$msgstr["it_x"];
	$input_type["XF"]=$msgstr["it_xf"];
	$input_type["TB"]=$msgstr["it_tb"];
	$input_type["P"]=$msgstr["it_p"];
	$input_type["D"]=$msgstr["it_d"];
	$input_type["ISO"]=$msgstr["it_iso"];
	$input_type["S"]=$msgstr["it_s"];
	$input_type["M"]=$msgstr["it_m"];
	$input_type["C"]=$msgstr["it_c"];
	$input_type["R"]=$msgstr["it_r"];
	$input_type["A"]=$msgstr["it_a"];
	$input_type["B"]=$msgstr["it_b"];
	$input_type["U"]=$msgstr["it_u"];
	$input_type["RO"]=$msgstr["it_ro"];
	$input_type["I"]=$msgstr["it_i"];

//PICKLISTS
	$pick_type["D"]=$msgstr["plt_d"];
	$pick_type["T"]=$msgstr["plt_t"];
	if ($arrHttp["Opcion"]!="new") $pick_type["P"]=$msgstr["plt_p"];

?>