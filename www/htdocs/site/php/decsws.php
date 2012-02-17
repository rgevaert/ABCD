<?php
session_start();
$DirNameLocal=dirname(__FILE__).'/';
include_once($DirNameLocal . "./include.php");
include_once($DirNameLocal . "./common.php");

$page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : 'default');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<? include($DirNameLocal . "./head.php"); ?>
	</head>
	<body>
		<div class="container">
			<div class="level2">
				<? 
					if ($page == 'default'){
						include($localPath['html'] . "/bvs.html");
					}
				?>
				<div class="middle">
					<?php
					
					$tree_id = $_REQUEST["tree_id"];
					$VARS["expression"] = $_SESSION["expression"];
					$VARS["source"] = "decs";
					$VARS["lang"] = $checked["lang"];

					$xml = getDeCSTreeVmx($tree_id);

					if ($page == 'info'){
						$xsl = "xsl/public/components/page_decs_info.xsl";
					}elseif ($page == 'qualifier'){
						$xsl = "xsl/public/components/page_decs_qualifier.xsl";
					}elseif ($page == 'explode'){
						$xsl = "xsl/public/components/page_decs_explode.xsl";
					}else{
						$xsl = "xsl/public/components/page_decs.xsl";
					}	
					
					require($DirNameLocal . "./xmlRoot.php");
					
					?>
				</div>			
				<div class="bottom">	
					<? 
						if ($page == 'default'){ 
							include($localPath['html'] . "/responsable.html"); 
						}	
					?>
				</div>	
			</div>
		</div>
		<? include($DirNameLocal. "./foot.php");  ?>
	</body>
</html>