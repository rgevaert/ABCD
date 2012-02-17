<div class="heading">
	<div class="institutionalInfo">
		<h1><img src=../images/logoabcd.jpg><?php echo $institution_name?></h1>
	</div>
	<div class="userInfo">
		<span><?php echo $_SESSION["nombre"]?></span>,
	<?php echo $_SESSION["profile"]?> |
<?php
if (isset($_SESSION["newindow"]) or isset($arrHttp["newindow"])){
	echo "<a href='javascript:top.location.href=\"../dataentry/logout.php\";top.close()' xclass=\"button_logout\"><span>[logout]</span></a>";}else{	echo "<a href=\"../dataentry/logout.php\" xclass=\"button_logout\"><span>[logout]</span></a>";}
?>
	</div>
	<div class="spacer">&#160;</div>
</div>