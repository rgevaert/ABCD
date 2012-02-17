<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      capturar_main.php
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
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../config.php");


include("../lang/admin.php");
include("../lang/soporte.php");

$lang=$_SESSION["lang"];
require_once ('leerregistroisispft.php');

include("../common/get_post.php");
//foreach ($arrHttp as $var => $value) echo "$var=$value<br>";

$prefijo="";
if (isset($arrHttp["prefijo"])) $prefijo=$arrHttp["prefijo"];
		$arrHttp["Opcion"]="STATUS";
		$arrHttp["IsisScript"]="control.xis";
		$llave=LeerRegistro();
		$stat=explode('|',$llave);
		$llave=substr($stat[2],7);
?>
		<HTML>
				<Title>Capturar</title>
				<head>
				<script languaje=javascript>
				var NombreBaseCopiara='<?php echo $arrHttp["base"]?>'
				var base=''
				var cipar=''
				var basecap='<?php echo $arrHttp["base"]?>'
				var ciparcap='<?php echo $arrHttp["base"]?>.par'
				var Formato='<?php echo $xFormato[$arrHttp["base"]]?>'
				var marc=''
				var tl=''
				var nr=''
				var Mfn=0
				var xeliminar=0
				var xeditar=''
				var ModuloActivo="Capturar"
				var cnvtabsel=''
				ValorCapturado=''
				buscar=''
				ep=''
				ConFormato=true
				function ActivarFormato(Ctrl){
					if (xeditar=='S'){
						alert('<?php echo $msgstr["aoc"]?>');
						Ctrl.checked=!Ctrl.checked
						return
					}else{
						if (Ctrl.checked){
							ConFormato=true
						}else{
							ConFormato=false
						}
						if (mfn>0){
							mfn=mfn-1
							Menu('proximo')
						}
					}
				}
				function AbrirVentanaAyuda(){
					insWindow = window.open('../html/ayuda.html', 'Ayuda', 'location=no,width=700,height=550,scrollbars=yes,top=10,left=100,resizable');
					insWindow.focus()
				}
				function CapturarRegistro(){
				cnv=""
				if (cnvtabsel!="") cnv="&cnvtabsel="+cnvtabsel
	loc="fmt.php?Opcion=presentar_captura&Mfn="+Mfn+"&ver=N&base="+NombreBaseCopiara+"&cipar="+NombreBaseCopiara+".par&basecap="+top.basecap+"&ciparcap="+top.basecap+".par"+cnv
	window.opener.top.xeditar="S"
	window.opener.top.main.location=loc
	window.opener.focus()

}

</script>
</head>
<frameset cols=410,* border=yes>
	<frame name=indice src=alfa.php?<?php echo "capturar=S&base=".$arrHttp["base"]."&cipar=".$arrHttp["cipar"]."&prefijo=".urlencode($arrHttp["prefijo"])."&formato_e=".urlencode(stripslashes($arrHttp["formato_e"]))."&fc=".$arrHttp["fc"]."&html=ayuda_captura.html"?> scrolling=no frameborder=no  marginheight=0   MARGINWIDTH=0 >
	<frame name=main src="" scrolling=yes frameborder=yes marginheight=2   MARGINWIDTH=0 >

</frameset>
</HTML>
<script>


