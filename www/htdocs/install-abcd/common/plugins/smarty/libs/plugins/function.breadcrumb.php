<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {popup} function plugin
 *
 * Type:     function<br>
 * Name:     breadcrumb<br>
 * Purpose:  create the breadcrumb/navigation site
 * @author   Domingos Teruel <domingos.teruel at bireme dot org>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_breadcrumb($params, &$smarty)
{
	global $BVS_LANG;
	$totalRecords = $params['total'];
	echo "<a href=\"http://{$_SERVER['HTTP_HOST']}{$BVS_LANG['index']}\">{$BVS_LANG['home']}</a>\n\t";

	
if(isset($_SESSION["identified"])) {
	if(is_array($_GET)) {
		if(sizeof($_GET) > 0)
		{
			foreach ($_GET as $key => $value) {
				if($key == 'm'){
        				if(sizeof($_GET) > 1 && !(isset($_GET['searchExpr'])) && !(isset($_GET['indexes'])) && !(isset($_GET['title']))) {
        					echo " / <a href=\"?$key=$value\">{$BVS_LANG[$value]}</a>\n\t";
        					//echo "<sup>($totalRecords {$BVS_LANG['registers']})r</sup>";
        				}elseif(sizeof($_GET) == 2 && isset($_GET['title'])){
        					echo " / <a href=\"?m=title\">{$BVS_LANG['title']}</a>\n\t";
        					$facicTitle = facicTitle($_GET["title"]);
        					echo " / <h3>$facicTitle</h3>\n\t";
        					echo "<sup>($totalRecords {$BVS_LANG['registers']})</sup>";
        				}else {
        					if($_GET['m'] == 'mytitle'){
        						echo " / <a href=\"?m=mytitle\">{$BVS_LANG['myTitle']}</a>\n\t";
        					}elseif($_GET['m'] == 'facic' && isset($_GET['title']) ) {
        						echo " / <a href=\"?m=title\">{$BVS_LANG['title']}</a>\n\t";
        						$facicTitle = facicTitle($_GET["title"]);
        						echo " / <a href=\"?$key=$value&title={$_GET['title']}\">$facicTitle</a>\n\t";
        						if(!(isset($_GET['edit']))) {								
        								echo " / <h3>{$BVS_LANG[$_GET['m']]}</h3>\n\t";								
        							if(!(isset($_GET['action']))) {								
        								echo "<sup>($totalRecords {$BVS_LANG['registers']})</sup>";
        							}
        						}
        					}else{
        						if(sizeof($_GET) > 1 && !(isset($_GET['searchExpr'])) && !(isset($_GET['indexes']))) {
        							echo " / <a href=\"?$key=$value\">{$BVS_LANG[$value]}</a>\n\t";
        						}else {        						    
        							echo " / <h3>{$BVS_LANG[$value]}</h3>\n\t";
        							echo "<sup>($totalRecords {$BVS_LANG['registers']})</sup>";
        						}
        					}
        				}

				}
				if($key == 'action') {
					if($_GET["action"] == "delete" && isset($_GET['id'])) {
						echo " / <h3>{$BVS_LANG["actionDeleteRegister"]}</h3>\n\t";
					}elseif($_GET["action"] == "new") {
						echo " / <h3>{$BVS_LANG['btNewRecord']} "; if($_GET['m'] != 'facic') {echo "{$BVS_LANG[$_GET['m']]}";} echo "</h3>\n\t";
					}
				}
				if($key == 'edit') {
					echo " / <h3>{$BVS_LANG['btEditRecord']} {$BVS_LANG[$_GET['m']]}</h3>\n\t";
				}
	
			}
		}else {
			echo " / <h3>{$BVS_LANG['homepage']}</h3>\n\t";
		}
	}else {
		echo " / <h3>{$BVS_LANG['homepage']}</h3>\n\t";
	}
}else{
	echo " / <h3>{$BVS_LANG["login"]}</h3>\n\t";
}

}

/* vim: set expandtab: */

?>
