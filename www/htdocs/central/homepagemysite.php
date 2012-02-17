<?php


include("common/header.php");
require_once ("config.php");


$query="";
include("common/get_post.php");

//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";





function fechaAsString($fecha)
{
  if (strlen($fecha)>=8)
  {
      $tp = mktime(substr($fecha,8,2),substr($fecha,10,2),substr($fecha,12,2),substr($fecha,4,2),substr($fecha,6,2),substr($fecha,0,4));

      return date("Y-m-d H:i:s",$tp);
  }
  else
      return "<font color='red'>".$msgstr["datenull"]."</font>";
}


function getUserStatus()
{
    global $empwebservicequerylocation,$empwebserviceusersdb,$userid;

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


      $params = array('id'=>$_SESSION["userid"] , 'database' => $empwebserviceusersdb );

      // Acá obtengo los datos generales
      $result = $client->call('searchUsersById', $params, 'http://kalio.net/empweb/schema/userstatus/v1' , '');
      //print_r($result);
      //die;



      if (is_array($result['queryResult']['databaseResult']['result']['userCollection']))
      {
          $vectoruno = $result['queryResult']['databaseResult']['result']['userCollection'];
          //print_r($vectoruno);

          if (is_array($vectoruno['user']))
          {
            //Hay una sola base y ahí está el usuario
              $vectorAbrev = $vectoruno['user'];
              //$mydb =  $empwebserviceusersdb;
              $mydb =  $vectoruno['!dbname'];
          }
          else if (is_array($vectoruno[0]))
          {
            // hay un vector de dbnames, hay que encontrar en cual de ellos está el user, si está en mas de uno
            // joderse, se toma el primero

            foreach ($vectoruno as $elementos)
            {
              if (is_array($elementos['user']))
              {
                //print_r($elementos);
                $mydb =  $elementos['!dbname'];
                $vectorAbrev = $elementos['user'];
              }
            }
            //die;

          }

      }


      //echo "MYDB=".$mydb;

      // Increíblemente hay datos que se recuperan con la bdd de la cual el usuario sale
      // y otros que se recuperan buscando sobre '*'. TODO-Ver que carajo es eso...

      $params = array('id'=>$_SESSION["userid"] , 'database' => $mydb );
      $resulta = $client->call('getUserStatus', $params, 'http://kalio.net/empweb/schema/userstatus/v1' , '');

      //print_r($resulta);
      //echo "<br>";


      $params = array('id'=>$_SESSION["userid"] , 'database' => '*'  );
      $resultb = $client->call('getUserStatus', $params, 'http://kalio.net/empweb/schema/userstatus/v1' , '');

      //print_r($resultb);

      $resultc = array_merge($resulta['userStatus'],$resultb['userStatus']);
      $resultactual['userStatus']=$resultc;

      // Prestamos
      if (is_array($resultactual['userStatus']['loans']))
      {
        if ($resultactual['userStatus']['loans']['loan']['userId']!='')
            $vectorAbrev['loans']=$resultactual['userStatus']['loans'];
        else
            $vectorAbrev['loans']=$resultactual['userStatus']['loans']['loan'];

      }



      // Suspensiones
      if (is_array($resultactual['userStatus']['suspensions']))
      {

        if ($resultactual['userStatus']['suspensions']['suspension']['userId']!='')
            $vectorAbrev['suspensions']=$resultactual['userStatus']['suspensions'];
        else
            $vectorAbrev['suspensions']=$resultactual['userStatus']['suspensions']['suspension'];

      }


      // Reservas
      if (is_array($resultactual['userStatus']['waits']))
      {
        if ($resultactual['userStatus']['waits']['wait']['userId']!='')
            $vectorAbrev['waits']=$resultactual['userStatus']['waits'];
        else
            $vectorAbrev['waits']=$resultactual['userStatus']['waits']['wait'];

      }

      //Multas
      if (is_array($resultactual['userStatus']['fines']))
      {
        if ($resultactual['userStatus']['fines']['fine']['userId']!='')
            $vectorAbrev['fines']=$resultactual['userStatus']['fines'];
        else
            $vectorAbrev['fines']=$resultactual['userStatus']['fines']['fine'];

      }


     //print_r($vectorAbrev['waits']);



      return $vectorAbrev;

}


function getRecordStatus()
{
    global $empwebservicequerylocation,$empwebserviceobjectsdb,$userid;

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


      $params = array('queryParam'=>array("query"=> array('recordId'=>$_SESSION["recordId"])), 'database' =>$empwebserviceobjectsdb);
      $result = $client->call('searchObjects', $params, 'http://kalio.net/empweb/engine/query/v1' , '');


      $resumen =   $result["queryResult"]["databaseResult"]["result"]["modsCollection"]["mods"];

      $vectorAbrev["id"] =   $_SESSION["recordId"];
      $vectorAbrev["title"] = $resumen["titleInfo"]["title"];
      $vectorAbrev["publisher"] = $resumen["originInfo"]["publisher"];
      $vectorAbrev["year"] = $resumen["originInfo"]["dateIssued"];

      if ($resumen["extension"]["holdingsInfo"]["copies"]["copy"]["copyId"]!="")
      {
        $vectorAbrev["copies"]["info"] = 1;
      }
      else
      {
        $vectorAbrev["copies"]["info"] = sizeof($resumen["extension"]["holdingsInfo"]["copies"]["copy"]);

        $opciones = array();
        foreach ($resumen["extension"]["holdingsInfo"]["copies"]["copy"] as $elemento)
        {
          if ($elemento["volumeId"])
              array_push($opciones,$elemento["volumeId"]);
        }


        //Opciones para volúmen
        if (sizeof($opciones)>0)
        {
          $vectorAbrev["copies"]["options"] = $opciones;
        }
      }

      $buffer = "";



      // Autores heterogeneo
      if ($resumen["name"]["namePart"]!="")
      {
        $buffer = $resumen["name"]["namePart"];
      }
      else
      {

        for ($i=0;$i<sizeof($resumen["name"]);$i++)
        {
                     $buffer.=$resumen["name"][$i]["namePart"]." / ";
        }
      }

      $vectorAbrev["authors"] = $buffer;

      //Copias heterogeneas

      //print_r($resumen);

      if ($resumen["extension"]["holdingsInfo"]["copies"]["copy"]["copyLocation"]!="")
      {
         $vectorAbrev["library"] = $resumen["extension"]["holdingsInfo"]["copies"]["copy"]["copyLocation"];
         $vectorAbrev["objectType"][0] = $resumen["extension"]["holdingsInfo"]["copies"]["copy"]["objectCategory"];
      }
      elseif ($resumen["extension"]["holdingsInfo"]["copies"]["copy"][0]["copyLocation"]!="")
      {
         $vectorAbrev["library"] = $resumen["extension"]["holdingsInfo"]["copies"]["copy"][0]["copyLocation"];

         $miscopias = $resumen["extension"]["holdingsInfo"]["copies"]["copy"];
         $i=0;
         foreach ($miscopias as $copia)
         {
           $vectorAbrev["objectType"][$i++] = $copia["objectCategory"];
         }
      }

      return $vectorAbrev;

}




?>

	<head>

		<title>ABCD MySite-plug in</title>

		<meta http-equiv="Expires" content="-1" />
		<meta http-equiv="pragma" content="no-cache" />
  		<meta name="robots" content="all" />
		<meta http-equiv="keywords" content="" />
		<meta http-equiv="description" content="" />
		<!-- Stylesheets -->
        <link rel="stylesheet" type="text/css" href="mysite/yui/build/fonts/fonts-min.css" />
        <link rel="stylesheet" type="text/css" href="mysite/yui/build/button/assets/skins/sam/button.css" />
        <link rel="stylesheet" type="text/css" href="mysite/yui/build/container/assets/skins/sam/container.css" />
        <script type="text/javascript" src="mysite/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>

        <script type="text/javascript" src="mysite/yui/build/connection/connection-min.js"></script>
        <script type="text/javascript" src="mysite/yui/build/element/element-min.js"></script>
        <script type="text/javascript" src="mysite/yui/build/button/button-min.js"></script>
        <script type="text/javascript" src="mysite/yui/build/dragdrop/dragdrop-min.js"></script>
        <script type="text/javascript" src="mysite/yui/build/container/container-min.js"></script>


        <!-- Stylesheets -->
		<link rel="stylesheet" rev="stylesheet" href="css/templatemysite.css" type="text/css" media="screen"/>


		<!--[if IE]>
			<link rel="stylesheet" rev="stylesheet" href="css/bugfixes_ie.css" type="text/css" media="screen"/>
		<![endif]-->
		<!--[if IE 6]>
			<link rel="stylesheet" rev="stylesheet" href="css/bugfixes_ie6.css" type="text/css" media="screen"/>
		<![endif]-->

		<script languaje=javascript>

        YAHOO.namespace("example.container");

        function init() {

        	// Define various event handlers for Dialog
        	var handleSubmit = function() {

               document.getElementById('firstBox').style.display='none';
               document.getElementById('secondBox').style.display='none';
               document.getElementById('answerBox').style.display='';
               this.submit();
        	};


           	var handleSubmitRenew = function() {

               document.getElementById('firstBox').style.display='none';
               document.getElementById('secondBox').style.display='none';
               document.getElementById('answerBox').style.display='';
               this.submit();
        	};


          	var handleSubmitReserves = function() {

               document.getElementById('firstBox').style.display='none';
               document.getElementById('answerBox').style.display='';
               this.submit();
        	};



        	var handleCancel = function() {
        		this.cancel();
        	};

        	var handleSuccess = function(o) {
        		var response = o.responseText;
        		document.getElementById("myanswer").innerHTML = response;
        	};


        	var handleFailure = function(o) {
        		alert("Submission failed: " + o.status);
        	};

	    // Instantiate the Dialog
    	YAHOO.example.container.dialog1 = new YAHOO.widget.Dialog("dialog1",
							{ width : "30em",
							  fixedcenter : true,
							  visible : false,
                              modal: true,
							  constraintoviewport : true,
							  buttons : [ { text:"<?php echo $msgstr["sendit"]; ?>", handler:handleSubmit, isDefault:true },
								      { text:"<?php echo $msgstr["cancel"]; ?>", handler:handleCancel } ]
							});


       	YAHOO.example.container.dialog2 = new YAHOO.widget.Dialog("dialog2",
							{ width : "30em",
							  fixedcenter : true,
							  visible : false,
                              modal: true,
							  constraintoviewport : true,
							  buttons : [ { text:"<?php echo $msgstr["confirmrenewal"] ?>", handler:handleSubmitRenew, isDefault:true },
								      { text:"<?php echo $msgstr["cancel"] ?>", handler:handleCancel } ]
							});


       	YAHOO.example.container.dialog3 = new YAHOO.widget.Dialog("dialog3",
							{ width : "30em",
                              height : "10em",
							  fixedcenter : true,
							  visible : false,
                              modal: true,
							  constraintoviewport : true,
							  buttons : [ { text:"<?php echo $msgstr["makereservation"] ?>", handler:handleSubmitReserves, isDefault:true },
								      { text:"<?php echo $msgstr["cancel"] ?>", handler:handleCancel } ]
							});


          	// Validate the entries in the form to require that both first and last name are entered
          	YAHOO.example.container.dialog1.validate = function() {
          		var data = this.getData();
                return true;
          		/*if (data.firstname == "" || data.lastname == "") {
          			alert("Please enter your first and last names.");
          			return false;
          		} else {
          			return true;
          		} */
          	};

          	// Wire up the success and failure handlers
          	YAHOO.example.container.dialog1.callback = { success: handleSuccess,
          						     failure: handleFailure };

          	YAHOO.example.container.dialog2.callback = { success: handleSuccess,
          						     failure: handleFailure };

          	YAHOO.example.container.dialog3.callback = { success: handleSuccess,
          						     failure: handleFailure };

          	// Render the Dialog
          	YAHOO.example.container.dialog1.render();

    	// Render the Dialog
          	YAHOO.example.container.dialog2.render();


    	// Render the Dialog
          	YAHOO.example.container.dialog3.render();

          	//YAHOO.util.Event.addListener("show", "click", YAHOO.example.container.dialog1.show, YAHOO.example.container.dialog1, true);
          	//YAHOO.util.Event.addListener("hide", "click", YAHOO.example.container.dialog1.hide, YAHOO.example.container.dialog1, true);
          }

          YAHOO.util.Event.onDOMReady(init);




		function CambiarLenguaje(){
			if (document.cambiolang.lenguaje.selectedIndex>0){
                lang=document.cambiolang.lenguaje.options[document.cambiolang.lenguaje.selectedIndex].value
                self.location.href="iniciomysite.php?reinicio=s&lang="+lang
			}
		}


        function CancelReservation(idTrans)
        {
          document.forms["formreservation"].waitid.value=idTrans;
          YAHOO.example.container.dialog1.show();

        }


        function LoanRenovation(idTrans,library)
        {
          document.forms["formrenovation"].copyId.value=idTrans;
          document.forms["formrenovation"].userId.value="<?php echo $_SESSION["userid"]; ?>";
          document.forms["formrenovation"].db.value="<?php echo $_SESSION["db"]; ?>";
          document.forms["formrenovation"].library.value=library;
          YAHOO.example.container.dialog2.show();

        }


        function PlaceReserve(recordId,objectType,library)
        {


          document.forms["formreserves"].userId.value="<?php echo $_SESSION["userid"]; ?>";
          document.forms["formreserves"].db.value="<?php echo $_SESSION["db"]; ?>";
          document.forms["formreserves"].recordId.value=recordId;
          if (typeof(document.forms["formshow"].volumeId)!='undefined')
          {
            document.forms["formreserves"].volumeId.value=document.forms["formshow"].volumeId.value;
          }
          document.forms["formreserves"].objectCategory.value=document.forms["formshow"].objectType.value;
          document.forms["formreserves"].library.value=library;

          YAHOO.example.container.dialog3.show();

        }



        function ReloadSite()
        {
            document.location.reload(true);
        }


        function clearOperation()
        {
              document.location.href="iniciomysite.php?action=clear";
        }
       </script>

       <!-- AJAX para consulta de registro -->
       <script>


         function ajaxPublication(id,recordId,database)
         {
           div = document.getElementById(id);
           postData = "id="+recordId+"&database="+database;
           makeRequest();

         }

          var div = document.getElementById('container');

          var handleSuccess = function(o){
          	//YAHOO.log("The success handler was called.  tId: " + o.tId + ".", "info", "example");
          	if(o.responseText !== undefined){
          		div.innerHTML = "<li>" + o.responseText + "</li>";
          		//div.innerHTML += "<li>Argument object: Array ([0] => " + o.argument[0] +
          		//				 " [1] => " + o.argument[1] + " )</li>";
          	}
          };

          var handleFailure = function(o){
          		YAHOO.log("The failure handler was called.  tId: " + o.tId + ".", "info", "example");

          	if(o.responseText !== undefined){
          		div.innerHTML = "<li>Transaction id: " + o.tId + "</li>";
          		div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
          		div.innerHTML += "<li>Status code message: " + o.statusText + "</li>";
          	}
          };

          var callback =
          {
            success:handleSuccess,
            failure:handleFailure
          };

          var sUrl = "mysite/queryobjectservice.php";


          function makeRequest(){

          	var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);

          	//YAHOO.log("Initiating request; tId: " + request.tId + ".", "info", "example");

          }

          //YAHOO.log("As you interact with this example, relevant steps in the process will be logged here.", "info", "example");

       </script>

      <style>
      #example {height:30em;}
      label { display:block;float:left;width:45%;clear:left; }
      .clear { clear:both; }
      #resp { margin:10px;padding:5px;border:1px solid #ccc;background:#fff;}
      #resp li { font-family:monospace }
      </style>





	</head>
	<body class="yui-skin-sam">
		<div class="headingmysite">
			<div class="institutionalInfo">
				<h1><?php echo $institution_name?></h1>
				<h2>ABCD</h2>
			</div>
			<div class="userInfo">
				<span><?echo $_SESSION["nombre"]?></span>,
				<a href="#"><?echo $msgstr[$_SESSION["permiso"]]?></a> |
				<a href="dataentry/logoutmysite.php" class="button_logout"><span><?echo $msgstr["logout"]?></span></a>
			</div>
			<div class="language"><form name=cambiolang> <?php echo $msgstr["lang"]?>:
			<select name=lenguaje style="width:140;font-size:10pt;font-family:arial narrow" onchange=CambiarLenguaje()>
	<?php
 	$a=$db_path."lang/".$_SESSION["lang"]."/lang.tab";

 	if (!file_exists($a)) $a=$db_path."lang/en/lang.tab";
 	if (file_exists($a)){
		$fp=file($a);
		$selected="";
		foreach ($fp as $value){
			$value=trim($value);
			if ($value!=""){
				$l=explode('=',$value);
				if ($l[0]==$_SESSION["lang"]) $selected=" selected";
				echo "<option value=$l[0] $selected>".$l[1]."</option>";
				$selected="";
			}
		}
	}else{
		echo $msgstr["flang"].$db_path."lang/".$_SESSION["lang"]."/lang.tab";
		die;
	}




?>
		</select></form>

		</div>

		<div class="spacer">&#160;</div>
		</div>

		<div class="sectionInfo">
			<div class="breadcrumb">


            </div>

			<div class="actions">
				&#160;
			</div>
			<div class="spacer">&#160;</div>
		</div>


		<div class="helpermysite">
            <a href=documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/homepage.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
        		<a href=documentacion/edit.php?archivo=<?echo $_SESSION["lang"]?>/homepage.html target=_blank><?echo $msgstr["edhlp"]?></a>

	</div>
		<div class="middle homepage">


                <div id="dialog1">
                <div class="hd"><?php echo $msgstr["reservationcancel"] ?></div>
                <div class="bd">
                <form id="formreservation" method="POST" action="mysite/cancelreservation.php">
                  <label for="observations"><?php echo $msgstr["observations"] ?></label>
                    <textarea name="observations"></textarea>
                    <input type="hidden" id="waitid" name="waitid"/>
                </form>
                </div>
                </div>


                <div id="dialog2">
                <div class="hd"><?php echo $msgstr["loanrenewal"] ?></div>
                <div class="bd">
                <form id="formrenovation" method="POST" action="mysite/loanrenovation.php">
                  <label for="observations"><?php echo $msgstr["renewalconfirm"] ?></label>
                    <input type="hidden" id="copyId" name="copyId"/>
                    <input type="hidden" id="userId" name="userId"/>
                    <input type="hidden" id="db" name="db"/>
                    <input type="hidden" id="library" name="library"/>
                </form>
                </div>
                </div>



                <div id="dialog3">
                <div class="hd"<?php echo $msgstr["makereservation"] ?>></div>
                <div class="bd">
                <form id="formreserves" method="POST" action="mysite/reserve.php">
                  <label for="observations"><?php echo $msgstr["reservationconfirm"] ?></label>

                    <input type="hidden" id="userId" name="userId"/>
                    <input type="hidden" id="db" name="db"/>
                    <input type="hidden" id="recordId" name="recordId"/>
                    <input type="hidden" id="volumeId" name="volumeId"/>
                    <input type="hidden" id="objectCategory" name="objectCategory"/>
                    <input type="hidden" id="library" name="library"/>
                </form>
                </div>
                </div>




<?php

           // Llamado ppal

           if ($_SESSION["action"]=='reserve')
           {
              MenuReserves(getRecordStatus());
           }
           else
           {
             $dataarr = getUserStatus();
              MenuFinalUser();
           }

echo "		</div>
	</div>";
include("common/footermysite.php");
echo "	</body>
</html>";


function MenuFinalUser(){
global $arrHttp,$msgstr,$db_path,$valortag,$lista_bases,$dataarr;
?>





              <div id="firstBox" class="mainBox" onmouseover="this.className = 'mainBox mainBoxHighlighted';" onmouseout="this.className = 'mainBox';">

				 <div class="boxTop">
					<div class="btLeft">&#160;</div>
					<div class="btRight">&#160;</div>
				 </div>

				 <div class="boxContent loanSection">

                    <div class="sectionIcon">
						&#160;
					</div>

                    <div class="sectionTitle">
						<h4><strong>&#160;<?php echo $msgstr["generaldata"]; ?></strong></h4>
					</div>

					<div class="sectionButtons">
					                          <!-- Aca viene esto robado del empweb --->

                       <table>
                        <tr>
                          <td rowspan="4">
                            <img src="../photoproxy.php?imgid=users/<?php echo $dataarr["photo"] ?>" alt="PICTURE"/>
                          </td>
                          <td rowspan="4">&nbsp;</td>

                          <td><?php echo $msgstr["myuserid"]; ?></td>
                          <td><?php echo $dataarr["id"] ?></td>
                        </tr>

                        <tr>
                          <td><?php echo $msgstr["name"]; ?></td>
                          <td><?php echo $dataarr["name"] ?></td>
                        </tr>

                        <tr>
                          <td><?php echo $msgstr["userclass"]; ?></td>
                          <td><?php echo $dataarr["userClass"] ?></td>
                        </tr>

                        <tr>
                          <td><?php echo $msgstr["datelimit"]; ?></td>
                          <td><?php echo fechaAsString($dataarr["expirationDate"]); ?></td>
                        </tr>
                      </table>

                    <div class="spacer"> </div>

                    </div>
                 </div>
                 <div class="boxBottom">
					<div class="bbLeft">&#160;</div>
					<div class="bbRight">&#160;</div>
				 </div>

               </div>



              <div id="secondBox" class="mainBox" onmouseover="this.className = 'mainBox mainBoxHighlighted';" onmouseout="this.className = 'mainBox';">


                  <div class="boxTop">
					<div class="btLeft">&#160;</div>
					<div class="btRight">&#160;</div>
				 </div>

                   <div class="boxContent toolSection">

                    <div class="sectionIcon">
						&#160;
					</div>

                    <div class="sectionTitle">
						<h4><strong>&#160;<?php echo $msgstr["userstatus"]; ?></strong></h4>
					</div>

                    <div class="sectionButtons">

                        <h3><?php echo $msgstr["activesuspensions"]; ?> <?php echo sizeof($dataarr["suspensions"]); ?></h3>


                         <?
                         if (sizeof($dataarr["suspensions"])>0)
                         {
                         ?>

                         <table width="90%">
                         <tr>
                         <td><?php echo $msgstr["from"]; ?></td>
                         <td><?php echo $msgstr["to"]; ?></td>
                         <td><?php echo $msgstr["duration"]; ?></td>
                         <td><?php echo $msgstr["cause"]; ?></td>
                         <td><?php echo $msgstr["library"]; ?></td>
                         </tr>

                         <?php

                          if (sizeof($dataarr["suspensions"])>0)
                          {
                             foreach ($dataarr["suspensions"] as $suspension)
                            {

                         ?>

                         <tr>
                         <td><? echo fechaAsString($suspension["startDate"],0,8); ?></td>
                         <td><? echo fechaAsString($suspension["endDate"],0,8); ?></td>
                         <td><? echo $suspension["daysSuspended"]; ?></td>
                         <td><? echo $suspension["obs"]; ?></td>
                         <td><? echo $suspension["location"]; ?></td>
                         </tr>

                         <?php

                            }
                          }

                         ?>
                         </table>

                         <? } ?>


                         <span>
                         <h3><?php echo $msgstr["actualloans"]; ?> <?php echo sizeof($dataarr["loans"]); ?></h3>

                         <?php

                          if (sizeof($dataarr["loans"])>0)
                          {

                          ?>

                         <table width="90%">
                         <tr>
                         <td><?php echo $msgstr["inventory"]; ?></td>
                         <td><?php echo $msgstr["from"]; ?></td>
                         <td><?php echo $msgstr["to"]; ?></td>
                         <td><?php echo $msgstr["publication"]; ?></td>
                         <td><?php echo $msgstr["library"]; ?></td>
                         <td><?php echo $msgstr["operation"]; ?></td>
                         </tr>
                         <?php
                            foreach ($dataarr["loans"] as $loan)
                            {
                         ?>
                         <tr>
                            <td><?php echo $loan["copyId"];
                                  //print_r($loan["profile"]); ?></td>
                            <td><?php echo fechaAsString($loan["startDate"]) ?></td>
                            <td><?php echo fechaAsString($loan["endDate"]) ?></td>
                            <td><a href="javascript: ajaxPublication('<?php echo "loan-".$loan["recordId"]; ?>','<?php echo $loan["recordId"]; ?>','*');"><?php echo $loan["recordId"]."/".$loan["profile"]["objectCategory"]; ?></a></td>
                            <td><?php echo $loan["location"] ?></td>
                            <td><input type="button" id="renew" value="<?php echo $msgstr["makerenewal"]; ?>" OnClick="javascript:LoanRenovation('<? echo $loan["copyId"] ?>','<?php echo $loan["location"] ?>')"></td>
                         </tr>
                         <tr><td colspan="6"><div id="<?php echo "loan-".$loan["recordId"]; ?>"></div></td></tr>
                         <?php
                            }
                         ?>
                         </table>
                         </span>

                         <?php

                          }

                         ?>

                         <span>
                         <h3><?php echo $msgstr["actualreserves"]; ?> <?php echo sizeof($dataarr["waits"]); ?></h3>


                         <?php

                         if (sizeof($dataarr["waits"])>0)
                         {

                         ?>

                         <table width="90%">
                         <tr>


                         <td><?php echo $msgstr["publication"]; ?></td>
                         <td><?php echo $msgstr["reserveddate"]; ?></td>
                         <td><?php echo $msgstr["avaiblefrom"]; ?></td>
                         <td><?php echo $msgstr["avaibleuntil"]; ?></td>
                         <td><?php echo $msgstr["library"]; ?></td>
                         <td><?php echo $msgstr["operation"]; ?></td>
                         </tr>
                         <?php
                            foreach ($dataarr["waits"] as $reserve)
                            {
                              //print_r($reserve);
                         ?>
                         <tr>
                            <td><a href="javascript: ajaxPublication('<?php echo "wait-".$reserve["recordId"]; ?>','<?php echo $reserve["recordId"]; ?>','*');"><?php echo $reserve["recordId"]."/".$reserve["profile"]["objectCategory"]; ?></a></td>
                            <!--<?php echo $reserve["recordId"] ?></td>-->
                            <td><?php echo fechaAsString($reserve["date"]) ?></td>
                            <td><?php echo fechaAsString($reserve["confirmedDate"]) ?></td>
                            <td><?php echo fechaAsString($reserve["expirationDate"]) ?></td>
                            <td><?php echo $reserve["location"] ?></td>
                            <td>
                             <input type="button" value="<?php echo $msgstr["cancel"]; ?>" OnClick="javascript:CancelReservation('<?php echo $reserve["!id"] ?>')"/>
                            </td>
                         </tr>
                         <tr><td colspan="6"><div id="<?php echo "wait-".$reserve["recordId"]; ?>"></div></td></tr>

                         <?php
                            }
                         ?>
                         </table>
                         </span>

                        <?php } ?>


                         <span>
                         <h3><?php echo $msgstr["fines"]; ?> <?php echo sizeof($dataarr["fines"]); ?></h3>
                         <?php

                         if (sizeof($dataarr["fines"])>0)
                         {

                         ?>

                             <table width="90%">
                             <tr>
                             <td><?php echo $msgstr["from"]; ?></td>
                             <td><?php echo $msgstr["amount"]; ?></td>
                             <td><?php echo $msgstr["type"]; ?></td>
                             <td><?php echo $msgstr["observations"]; ?></td>

                             </tr>
                             <?php
                                foreach ($dataarr["fines"] as $fine)
                                {
                                  //print_r($reserve);
                             ?>
                             <tr>
                                <td><?php echo fechaAsString($fine["date"]) ?></td>
                                <td><?php echo $fine["amount"] ?></td>
                                <td><?php echo $fine["type"] ?></td>
                                <td><?php echo $fine["obs"] ?></td>

                             </tr>
                             <?php
                                }
                             ?>
                             </table>
                             </span>

                        <?php } ?>
                        <div class="spacer"> </div>

                       </div>

                    </div>

                    <div class="boxBottom">
					<div class="bbLeft">&#160;</div>
					<div class="bbRight">&#160;</div>
				    </div>

                 </div>



                 <div id="answerBox" class="mainBox" onmouseover="this.className = 'mainBox mainBoxHighlighted';" onmouseout="this.className = 'mainBox';">

				 <div class="boxTop">
					<div class="btLeft">&#160;</div>
					<div class="btRight">&#160;</div>
				 </div>

				 <div class="boxContent helpSection">

                    <div class="sectionIcon">
						&#160;
					</div>

                    <div class="sectionTitle">
						<h4><strong>&#160;<?php echo $msgstr["result"]; ?></strong></h4>
					</div>

					<div class="sectionButtons">
                        <div id="myanswer">
                            <img src="images/loading.gif" />
                        </div>
                        <input type="button" value="<?php echo $msgstr["gomysite"]; ?>" OnClick="javascript:ReloadSite();" />
                    </div>

                 </div>

                <div class="boxBottom">
			    <div class="bbLeft">&#160;</div>
			    <div class="bbRight">&#160;</div>
		        </div>
                </div>

                <script>
                    document.getElementById('answerBox').style.display='none';
                </script>








<?php }

function MenuReserves($vector)
{
global $arrHttp,$msgstr,$db_path,$valortag,$lista_bases,$dataarr;
?>






              <div id="firstBox" class="mainBox" onmouseover="this.className = 'mainBox mainBoxHighlighted';" onmouseout="this.className = 'mainBox';">

				 <div class="boxTop">
					<div class="btLeft">&#160;</div>
					<div class="btRight">&#160;</div>
				 </div>

				 <div class="boxContent loanSection">

                    <div class="sectionIcon">
						&#160;
					</div>

                    <div class="sectionTitle">
						<h4><strong>&#160;<?php echo $msgstr["publicationdata"]; ?></strong></h4>
					</div>

					<div class="sectionButtons">
                    <form name="formshow">
                        <h3>
                        <table>
                            <tr>
                                <td><?php echo $msgstr["recordid"]; ?></td>
                                <td><?php echo $vector["id"] ?></td>
                            </tr>

                            <tr>
                                <td><?php echo $msgstr["title"]; ?></td>
                                <td><?php echo $vector["title"] ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $msgstr["authors"]; ?></td>
                                <td><?php echo $vector["authors"] ?></td>
                             </tr>
                             <tr>
                                <td><?php echo $msgstr["editor"]; ?></td>
                                <td><?php echo $vector["publisher"] ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $msgstr["year"]; ?></td>
                                <td><?php echo $vector["year"] ?></td>
                            </tr>

                             <tr>
                                <td><?php echo $msgstr["categrecord"]; ?></td>
                                <td>
                                    <select name="objectType">
                                        <?php
                                            foreach ($vector["objectType"] as $elemento)
                                            {
                                              echo "<option value='$elemento'>$elemento</option>";
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>


                             <tr>
                                <td><?php echo $msgstr["numberofcopies"]; ?></td>
                                <td><?php echo $vector["copies"]["info"] ?></td>
                            </tr>

                            <?php

                                if ($vector["copies"]["options"])
                                {
                            ?>
                             <tr>
                                <td><font color="red"><?php echo $msgstr["selectthevolume"]; ?></font></td>
                                <td>
                                    <select name="volumeId">
                                        <?php
                                            foreach ($vector["copies"]["options"] as $elemento)
                                            {
                                              echo "<option value='$elemento'>$elemento</option>";
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>

                            <?php

                                }
                             ?>

                            <tr>
                                <td><?php echo $msgstr["librarylegend"]; ?></td>
                                <td><?php echo $vector["library"] ?></td>
                            </tr>


                        </table>
                        </h3>

                     <?php
                        if ($vector["objectType"]!="" && $vector["objectType"]!="")
                        {
                      ?>
                     <input type="button" value="<?php echo $msgstr["makereservation"]; ?>" OnClick="javascript:PlaceReserve(<?php echo "'".$vector["id"]."','".$vector["objectType"]."','".$vector["library"]."'" ?>);" />
                     <?php
                        }
                        else
                        { ?>
                        <div class="inputAlert"><?php echo $msgstr["alertnocateg"]; ?></div>
                        <br />
                       <?php
                        } ?>

                     <input type="button" value="<?php echo $msgstr["gomysite"]; ?>" OnClick="javascript:clearOperation();" />

                    <div class="spacer"> </div>


                    </form>
                    </div>
                 </div>
                 <div class="boxBottom">
					<div class="bbLeft">&#160;</div>
					<div class="bbRight">&#160;</div>
				 </div>

               </div>



                 <div id="answerBox" class="mainBox" onmouseover="this.className = 'mainBox mainBoxHighlighted';" onmouseout="this.className = 'mainBox';">

				 <div class="boxTop">
					<div class="btLeft">&#160;</div>
					<div class="btRight">&#160;</div>
				 </div>

				 <div class="boxContent helpSection">

                    <div class="sectionIcon">
						&#160;
					</div>

                    <div class="sectionTitle">
						<h4><strong>&#160;<?php echo $msgstr["result"]; ?></strong></h4>
					</div>

					<div class="sectionButtons">
                        <div id="myanswer">
                            <img src="images/loading.gif" />
                        </div>
                        <input type="button" value="<?php echo $msgstr["gomysite"]; ?>" OnClick="javascript:clearOperation();" />
                    </div>

                 </div>

                <div class="boxBottom">
			    <div class="bbLeft">&#160;</div>
			    <div class="bbRight">&#160;</div>
		        </div>
                </div>

                <script>
                    document.getElementById('answerBox').style.display='none';
                </script>








<?php }?>
