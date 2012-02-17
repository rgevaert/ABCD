<?php
require_once ("../config.php");
require_once('../nusoap/nusoap.php');
/*if (isset($arrHttp["lang"])){
	$_SESSION["lang"]=$arrHttp["lang"];
	$lang=$arrHttp["lang"];
  }else{
  	if (!isset($_SESSION["lang"]))
      $_SESSION["lang"]=$lang;
  } */
session_start();
 $lang=$_SESSION["lang"];
 require_once ("../lang/mysite.php");
 require_once("../lang/lang.php");

 if (!isset($_SESSION["permiso"])) die;



      //Invocación al web-service

      $proxyhost = isset($_POST['proxyhost']) ? $_POST['proxyhost'] : '';
      $proxyport = isset($_POST['proxyport']) ? $_POST['proxyport'] : '';
      $proxyusername = isset($_POST['proxyusername']) ? $_POST['proxyusername'] : '';
      $proxypassword = isset($_POST['proxypassword']) ? $_POST['proxypassword'] : '';
      $useCURL = isset($_POST['usecurl']) ? $_POST['usecurl'] : '0';
      $client = new nusoap_client($empwebservicetranslocation, false,
          						$proxyhost, $proxyport, $proxyusername, $proxypassword);

      $err = $client->getError();
      if ($err) {
          	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
          	echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
          	exit();
      }

      $user = new soapval('userId','',$_REQUEST["userId"]);
      $record = new soapval('recordId','',$_REQUEST["recordId"]);
      $volume= new soapval('volumeId','',$_REQUEST["volumeId"]);
      $category = new soapval('objectCategory','',$_REQUEST["objectCategory"]);
      $userdb = new soapval('userDb','',$_REQUEST["db"]);
      $objdb = new soapval('objectDb','',$empwebserviceobjectsdb);
      $library = new soapval('objectLocation','',$_REQUEST["library"]);
      //$from = new soapval('startDate','','20090322131211');

      $param1 = array ( "name" => "operatorLocation" );
      $myparam1 = new soapval ('param','',$_REQUEST["library"],'http://kalio.net/empweb/engine/trans/v1',false,$param1);

      $param1 = array ( "name" => "operatorId" );
      $myparam2 = new soapval ('param','','mysite',false,false,$param1);

      //$param1 = array ( "name" => "acceptEndDate" );
      //$myparam3 = new soapval ('param','','20090323131211',false,false,$param1);

      $myparams = new soapval ('params','',array($myparam1,$myparam2));
      $myextension = new soapval ('transactionExtras','',$myparams,'','');


      $params = array ($user,$record,$volume,$category,$userdb,$objdb,$library,$from,$myextension);

      //print_r($params);
      // Acá obtengo los datos generales
      $result = $client->call('waitSingle', $params, 'http://kalio.net/empweb/engine/trans/v1' , '');

      //echo "Databse". $_REQUEST["db"];
      //print_r($result);




      $myresult=$msgstr["failed_operation"];
      $legend="";
      $image="mysite/img/flag.png";
      $success = false;

      //print_r($result);

      for ($i=0;$i<sizeof($result['transactionResult']['processResult']);$i++)
      {
        if (sizeof($result['transactionResult']['processResult'][$i]['msg'])>0)
        {
           //Aca hay que internacionalizar
           //print_r($result['transactionResult']['processResult'][$i]['msg']['text'][1]);
           $intermedio = $result['transactionResult']['processResult'][$i]['msg']['text'];
           if (is_array($intermedio))
           {
             $auxi="";
             foreach ($intermedio as $mensaje)
             {
               if ($mensaje["!lang"]==$lang)
                 $legend=$mensaje["!"];
               else if ($mensaje["!lang"]=="en")
                 $auxi=$mensaje["!"];
             }

             if (strlen($legend)==0)
                $legend=$auxi;
           }
           else
            $legend=$intermedio;
        }


        if (sizeof($result['transactionResult']['processResult'][$i]['result'])>0)
        {
          //print_r($result['transactionResult']['processResult'][$i]['result']);
          //Aca esta el resultado definitivo de la transaccion
          $success=true;

        }
      }




      $resultbuff=serialize($result);
      include_once("legends.php");


      if ($success)
      {
            $myresult=$msgstr["success_operation"];
            if (strlen($legend)>0)
                $image="mysite/img/important.png";
            else
                $image="mysite/img/clean.png";

      }


      echo "<div><table><tr rowspan=2>";
      echo "<td><img src='".$image."' /></td>";
      echo "<td><h2>".utf8_encode($myresult)."</h2></td></tr><tr><td><h3>".utf8_encode($legend)."</h3></td>";
      echo "</tr></table></div>";

      //echo $client->request;




      //print_r ($result);

?>