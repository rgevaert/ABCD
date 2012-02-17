<?php


include("common/header.php");
require_once ("config.php");

$def = parse_ini_file($db_path."abcd.def");

$query="";
include("common/get_post.php");



if (isset($arrHttp["lang"])){
	$_SESSION["lang"]=$arrHttp["lang"];
	$lang=$arrHttp["lang"];
}else{
	if (!isset($_SESSION["lang"]))
    $_SESSION["lang"]=$lang;
}

require_once ("lang/mysite.php");
require_once("lang/lang.php");



?>

<html>
<head>
	<!-- Stylesheets -->
		<link rel="stylesheet" rev="stylesheet" href="css/template.css" type="text/css" media="screen"/>

</head>
<body class="mysite">
<script>
    function callMySite(id)
    {
        var str_aux = "/central/iniciomysite.php?action=reserve&recordId="+id+"&lang=<?php echo $lang; ?>";
        janela = window.open(str_aux,"EMPWeb");

        janela.moveTo(0,0);
        janela.resizeTo(screen.width,screen.height);

    }
</script>

<div class="headingmysite">
    <div class="institutionalInfo">
				<h1>BIREME - Centro Latino Americano e do Caribe de Informação em Ciências da Saúde</h1>
				<h2>ABCD</h2>
			</div>

			<div class="userInfo">

			</div>
			<div class="spacer">&#160;</div>

</div>
<div class="middle homepage">
<?php

require_once('nusoap/nusoap.php');
require_once ("config.php");

//set_time_limit(60);

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


$params = array('queryParam'=>array("query"=> array('recordId'=>$_REQUEST["copyId"])), 'database' =>$empwebserviceobjectsdb);
$result = $client->call('searchObjects', $params, 'http://kalio.net/empweb/engine/query/v1' , '');

//print_r($result);
//die;




$miscopias = $result[queryResult][databaseResult][result][modsCollection][mods][extension][holdingsInfo][copies][copy];


if (is_array($miscopias))
{

    if ($miscopias[copyId]!='')
    {
        $copias[0] = $miscopias;
    }
    else
    {
        $copias = $miscopias;
    }


    for ($i=0;$i<=sizeof($copias)-1;$i++)
    {
      //echo $copias[$i][copyId]." - ".$copias[$i][copyLocation]." - ".$copias[$i][objectCategory]."<br>";

      $params = array('copyId'=>$copias[$i][copyId], 'database' => $empwebserviceobjectsdb);
      $result =  $client->call('getCopyStatus', $params, 'http://kalio.net/empweb/engine/query/v1' , '');

      //print_r ($result[copyStatus][loans][loan][userId]);
      if (sizeof($result[copyStatus][loans])>0)
      {
        $copias[$i][loanfrom]=$result[copyStatus][loans][loan][startDate];
        $value = substr($result[copyStatus][loans][loan][endDate],6,2)."/".substr($result[copyStatus][loans][loan][endDate],4,2)."/".substr($result[copyStatus][loans][loan][endDate],0,4);
        $copias[$i][status]=$msgstr["notavailable"];
        $copias[$i][loanto]= $value;
      }
      else
      {
        $copias[$i][status]=$msgstr["available"];
      }
    }
}

?>

<table>
     <tr>
        <th><?php echo $msgstr["inventory"]; ?></th>
        <th><?php echo $msgstr["volume"]; ?></th>
        <th><?php echo $msgstr["library"]; ?></th>
        <th><?php echo $msgstr["objtype"]; ?></th>
        <th><?php echo $msgstr["status"]; ?></th>
        <th><?php echo $msgstr["loaneduntil"]; ?></th>
     </tr>

    <?php
       if (is_array($copias)>0)
       {
         for ($i=0;$i<=sizeof($copias)-1;$i++)
         {
     ?>

     <tr>
        <td><?php  echo $copias[$i][copyId]; ?></td>
        <td><?php  echo $copias[$i][volumeId]; ?></td>
        <td><?php  echo $copias[$i][copyLocation]; ?></td>
        <td><?php  echo $copias[$i][objectCategory]; ?></td>
        <td><?php  echo $copias[$i][status]; ?></td>
        <td><?php  echo $copias[$i][loanto]; ?></td>
     </tr>

    <?php
        }
       }
     ?>


</table>

<br/>

<hr />
<br/>
<?php
  if (is_array($copias))
  {
?>
<div style="text-align: center">
    <input type="button" value="<?php echo $msgstr["makereservation"]; ?>" onClick="javascript:callMySite('<?php echo $_REQUEST["copyId"] ?>')"/>
    <input type="button" value="<?php echo $msgstr["close"]; ?>" onClick="javascript:self.close()"/>
</div>
<?php
 }



?>



</div>
		<div class="footermysite">
			<div class="systemInfo">
				<strong>ABCD <?php echo $def["VERSION"] ?></strong>
				<span><?php echo $def["LEGEND1"]; ?></span>
				<a href="<?php echo $def["URL1"]; ?>" target=_blank><?php echo $def["URL1"]; ?></a>
			</div>
			<div class="distributorLogo">
				<a href="<?php echo $def["URL2"]; ?>" target=_blank><span><?php echo $def["LEGEND2"]; ?></span></a>
			</div>
			<div class="spacer">&#160;</div>
		</div>	

</body>
</html>