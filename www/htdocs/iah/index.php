<?php

$base = ($_REQUEST['base'] != '' ?  $_REQUEST['base'] : 'DBLIL');
$lang = ($_REQUEST['lang'] != '' ?  $_REQUEST['lang'] : 'es');
$form = $_REQUEST['form'];

header("Location: /cgi-bin/wxis.exe/iah/scripts/?IsisScript=iah.xis&lang=" . $lang . "&base=" . $base);

?>