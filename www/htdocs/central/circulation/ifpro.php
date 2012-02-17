<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      ifpro.php
 * @desc:      Search
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

include ("../common/header.php");
?>

<script language=javascript>

function EjecutarBusqueda(F,desde){
	switch (desde){
		case 1:
/* buscar los terminos de este diccionario*/
			TerminosSeleccionados=""
			document.forma1.Opcion.value="buscar_en_este"
			for (i=0;i<document.forma1.terminos.length;i++){
				if (document.forma1.terminos.options[i].selected==true){
					Termino="\""+document.forma1.terminos.options[i].value+"\""
					if (TerminosSeleccionados==""){
						TerminosSeleccionados=Termino
					}else{
						TerminosSeleccionados=TerminosSeleccionados+" or "+Termino
					}
				}
			}
       		re = / /g
/*'       	TerminosSeleccionados=TerminosSeleccionados.replace(re,"+") */
			document.forma1.Expresion.value=TerminosSeleccionados

			document.forma1.action="buscar.php"
<?php
	if (isset($arrHttp["prestamo"]) or isset($arrHttp["Target"])) {
       echo " document.forma1.target=window.opener.name\n";
   }
?>
			document.forma1.submit()
<?php if (isset($arrHttp["prestamo"]) or isset($arrHttp["Target"])) {
       echo " self.close()\n";
   }
?>
			break
		case 2:
/* Pasar los términos seleccionados */
			TerminosSeleccionados=""
			for (i=0;i<document.forma1.terminos.length;i++){
				if (document.forma1.terminos.options[i].selected==true){

					Termino=document.forma1.terminos.options[i].value
					//len_t=<?php echo strlen($arrHttp["prefijo"])."\n"?>
					Termino="\""+Termino+"\""
					if (TerminosSeleccionados==""){
						TerminosSeleccionados=Termino
					}else{
						TerminosSeleccionados=TerminosSeleccionados+" or "+Termino
					}
				}
			}
			Ctrl_pass=eval("window.opener.<?php echo $arrHttp["Diccio"]?>")
			Ctrl_pass.value=TerminosSeleccionados
   			self.close()
			break
		case 4:
/* Más términos */
			document.forma1.Opcion.value="mas_terminos"
			document.forma1.submit()
			break
		case 3:
/* Continuar */
			document.forma1.Opcion.value="mas_terminos"
			document.forma1.LastKey.value=document.forma1.prefijo.value+document.forma1.IR_A.value
			document.forma1.submit()
			break
	}
}
</script>
<div class="middle list">
<FORM METHOD="Post" name=forma1 action=diccionario.php onSubmit="Javascript:return false">
<input type=hidden name=Opcion value='<?php echo $arrHttp["Opcion"]?>'>
<input type=hidden name=session_id value='<?php echo $arrHttp["session_id"]?>'>
<input type=hidden name=base value='<?php echo $arrHttp["base"]?>'>
<input type=hidden name=cipar value='<?php echo $arrHttp["cipar"]?>'>
<input type=hidden name=Formato value='<?php echo $arrHttp["Formato"]?>'>
<input type=hidden name=prologo value='<?php echo $arrHttp["prologo"]?>'>
<input type=hidden name=epilogo value='<?php echo $arrHttp["epilogo"]?>'>
<input type=hidden name=Expresion value="">
<input type=hidden name=prefijo value='<?php echo $arrHttp["prefijo"]?>'>
<input type=hidden name=Diccio value='<?php echo $arrHttp["Diccio"]?>'>
<input type=hidden name=desde value=<?php if (isset($arrHttp["desde"])) echo $arrHttp["desde"]?>>
<input type=hidden name=id value=<?php echo $arrHttp["id"]?>>
<?php if (isset($arrHttp["prestamo"])) {
	echo "<input type=hidden name=prestamo value=".$arrHttp["prestamo"].">";

  }
 ?>

<input type=hidden name=campo value="<?php echo urldecode($arrHttp["campo"])?>">
<br>

  <table xwidth="640" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="20" >&nbsp; </td>
      <td colspan="2" valign="middle" class="menusec2">
		<?php echo $msgstr["indicede"].": ".urldecode($arrHttp["campo"])?></td>
      <tr>
      <td width="20">&nbsp;</td>
      <td colspan="2" valign="top" class="menusec1">
		<?php echo $msgstr["selmultiple"]?>;
      </td>
      <td width="15" height="9">&nbsp;</td>
    </tr>
    <tr>
      <td width="20">&nbsp;</td>
      <td rowspan="2" width="5" align="left" valign="top">&nbsp;</td>
      <td rowspan="2" xwidth="585" valign=top>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td xwidth=250>
              <select name=terminos  size=16 multiple xwidth=250 onChange=Javascript:EjecutarBusqueda("",2)>
              <OPTION VALUE="">
