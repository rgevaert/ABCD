<?php

require_once('../nusoap/nusoap.php');
require_once ("../config.php");


$proxyhost = isset($_POST['proxyhost']) ? $_POST['proxyhost'] : '';
$proxyport = isset($_POST['proxyport']) ? $_POST['proxyport'] : '';
$proxyusername = isset($_POST['proxyusername']) ? $_POST['proxyusername'] : '';
$proxypassword = isset($_POST['proxypassword']) ? $_POST['proxypassword'] : '';
$useCURL = isset($_POST['usecurl']) ? $_POST['usecurl'] : '0';
$client = new nusoap_client($empwebservicequerylocation, false,
						$proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
	exit();
}


$params = array('queryParam'=>array("query"=> array('recordId'=>$_REQUEST["id"])), 'database' =>$empwebserviceobjectsdb);
$result = $client->call('searchObjects', $params, 'http://kalio.net/empweb/engine/query/v1' , '');
$objeto = $result[queryResult][databaseResult][result][modsCollection][mods];

echo utf8_encode($objeto["titleInfo"]["title"].$objeto["name"][0]["namePart"]);

?>