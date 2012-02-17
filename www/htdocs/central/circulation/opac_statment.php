<?php session_start();
include("../common/get_post.php");
$_SESSION["lang"]=$arrHttp["lang"];
?>
<html>
<title>Statment</title>
<script src=../dataentry/js/lr_trim.js></script>
<Script>
function EnviarForma(){	if (Trim(document.reserva.usuario.value)==""){		alert("Debe suministrar su número de carnet")
		return	}
	document.reserva.submit()}
function PoliticaReserva(){	msgwin=window.open("politica_reserva.html","politica","width=500,height=400, resizable, scrollbars")
	msgwin.focus()}
</script>
<body>
<font face=arial size=2>
<form name=reserva action=opac_statment_ex.php onsubmit="javascript:return false">
<?php

echo "<strong>Solicitud de estado de cuenta</strong>";
echo "<p>Por favor, suministre su número de carnet";
echo "<p><input type=text name=usuario>\n";
echo "<input type=hidden name=vienede value=ecta_web>\n";
echo " &nbsp; <input type=submit value=Enviar onclick=javascript:EnviarForma()>";
?>
</form>
</font>
</html>

