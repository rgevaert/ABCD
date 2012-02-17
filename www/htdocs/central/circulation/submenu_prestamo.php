<?php

if (isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_LOAN"])){
?>
		<a href="prestar.php?encabezado=s<?php echo $link_u?>" ><strong>
			<?php echo $msgstr["loan"]?></strong></a> |
<?php }
if (isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_RENEW"])){
?>
		<a href="renovar.php?encabezado=s<?php echo $link_u?>" ><strong>
			<?php echo $msgstr["renew"]?></strong></a> |
<?php }
if (isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_RESERVE"])){
?>
<!--		<a href="reservar.php?encabezado=s<?php echo $link_u?>" ><strong>
			<?php echo $msgstr["reserve"]?></strong></a> |   -->
<?php }
if (isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_RETURN"])){
?>
		<a href="devolver.php?encabezado=s"><strong>
			<?php echo $msgstr["return"]?></strong></a> |
<?php }
if (isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_SUSPEND"])){
?>
		<a href="sanctions.php?encabezado=s"><strong>
			<?php echo $msgstr["suspend"]."/".$msgstr["fine"]?></strong></a> |
<?php }?><br>
		<a href="estado_de_cuenta.php?encabezado=s<?php echo $link_u?>"><strong>
			<?php echo $msgstr["statment"]?></strong></a> |

		<a href="situacion_de_un_objeto.php?encabezado=s<?php echo $link_u?>"><strong>
			<?php echo $msgstr["ecobj"]?></strong></a> |

		<a href="../common/inicio.php?reinicio=s&modulo=loan"><strong>
			<?php echo "menu"?></strong></a> &nbsp; &nbsp; &nbsp;

