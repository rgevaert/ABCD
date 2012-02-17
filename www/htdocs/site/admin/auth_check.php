<?
function auth_check_login()
{
	session_start();
	if ( $_SESSION["auth_id"] != "BVS@BIREME"  ) {
		header("Location: /admin/index.php?timeout=session");
		exit;
	}
}
?>