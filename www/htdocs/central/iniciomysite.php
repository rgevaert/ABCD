<?php

global $Permiso, $arrHttp,$valortag,$nombre,$userid,$db;
$arrHttp=Array();
session_start();
require_once ("config.php");
require_once('nusoap/nusoap.php');


$page="";
if (isset($_REQUEST['GET']))
	$page = $_REQUEST['GET'];
else
	if (isset($_REQUEST['POST'])) $page = $_REQUEST['POST'];

if (!(eregi("^[a-z_./]*$", $page) && !eregi("\\.\\.", $page))) {
	// Abort the script
	die("Invalid request");

}
$valortag = Array();


function LeerRegistro() {

// la variable $llave permite retornar alguna marca que esté en el formato de salida
// identificada entre $$LLAVE= .....$$

$llave_pft="";
global $llamada, $valortag,$maxmfn,$arrHttp,$OS,$Bases,$xWxis,$Wxis,$Mfn,$db_path,$wxisUrl,$empwebservicequerylocation,$empwebserviceusersdb,$db;


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


      $params = array('queryParam'=>array("query"=> array('login'=>$arrHttp["login"])), 'database' =>$empwebserviceusersdb);
      $result = $client->call('searchUsers', $params, 'http://kalio.net/empweb/engine/query/v1' , '');

      //print_r($result);
      //die;

      $myllave ="";

      //Esto se ha complejizado con el asunto de la incorporación de mas de una base de datos

      if (is_array($result['queryResult']['databaseResult']['result']['userCollection']))
      {
        $vectoruno = $result['queryResult']['databaseResult']['result']['userCollection'];

        if (is_array($vectoruno['user']))
        {
          //Hay una sola base y ahí está el usuario
          $myuser = $vectoruno['user'];
          $db = $empwebserviceusersdb;
        }
        else if (is_array($vectoruno[0]))
        {
          // hay un vector de dbnames, hay que encontrar en cual de ellos está el user, si está en mas de uno
          // joderse, se toma el primero
          foreach ($vectoruno as $elementos)
          {
            if (is_array($elementos['user']))
            {
              $myuser = $elementos['user'];
              $db = $elementos['!dbname'];
            }
          }

        }

        // Con el myuser recuperado me fijo si es que el passwd coincide

        if (($myuser['password']==$arrHttp["password"]) &&  (strlen($arrHttp["password"])>3))
        {
              $vectorAbrev=$myuser;
              //print_r($vectorAbrev);
              //die;
              $myllave = $vectorAbrev['id']."|";
              $myllave .= "1|";
              $myllave .= $vectorAbrev['name']."|";
              $valortag[40] = "\n";
        }


      }
	  return $myllave;

}

function VerificarUsuario(){
Global $arrHttp,$valortag,$Path,$xWxis,$session_id,$Permiso,$msgstr,$db_path,$nombre,$userid,$lang;
 	$llave=LeerRegistro();
 	if ($llave!=""){
  		$res=explode("\|",$llave);
  		$userid=$res[0];
  		$_SESSION["mfn_admin"]=$res[1];
  		$mfn=$res[1];
  		$nombre=$res[2];
		$arrHttp["Mfn"]=$mfn;
  		$Permiso="|";
  		$P=explode("\n",$valortag[40]);
  		foreach ($P as $value){
  			$value=substr($value,2);
  			$ix=strpos($value,'^');
    		$Permiso.=substr($value,0,$ix)."|";
    	}
 	}else{
 		echo "<script>
 		self.location.href=\"../indexmysite.php?login=N&lang=".$lang."\";
 		</script>";
  		die;
 	}
}

/////
/////   INICIO DEL PROGRAMA
/////


$query="";
include("common/get_post.php");

//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";


if (isset($arrHttp["lang"])){
	$_SESSION["lang"]=$arrHttp["lang"];
	$lang=$arrHttp["lang"];
}else{
	if (!isset($_SESSION["lang"]))
    $_SESSION["lang"]=$lang;
}

require_once ("lang/mysite.php");
require_once("lang/lang.php");



if (isset($arrHttp["action"]))
{
    if ($arrHttp["action"]!='clear')
    {
      $_SESSION["action"]=$arrHttp["action"];
      $_SESSION["recordId"]=$arrHttp["recordId"];
    }
    else
    {
      $_SESSION["action"]="";
      $_SESSION["recordId"]="";
    }
}





if (!$_SESSION["userid"] || !$_SESSION["permiso"]=="mysite".$_SESSION["userid"])
{

      	if (isset($arrHttp["reinicio"])){
      		$arrHttp["login"]=$_SESSION["login"];
      		$arrHttp["password"]=$_SESSION["password"];
      		$arrHttp["startas"]=$_SESSION["permiso"];
      		$arrHttp["lang"]=$_SESSION["lang"];
            $arrHttp["db"]=$_SESSION["db"];

      	}
      	VerificarUsuario();

      	$_SESSION["lang"]=$arrHttp["lang"];


        $_SESSION["userid"]=$userid;
      	$_SESSION["login"]=$arrHttp["login"];
      	$_SESSION["password"]=$arrHttp["password"];
      	$_SESSION["permiso"]="mysite".$userid;
      	$_SESSION["nombre"]=$nombre;
        $_SESSION["db"]=$db;

}

//print_r ($msgstr);
include("homepagemysite.php");

?>