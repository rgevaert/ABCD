<?
require_once('../bvs-mod/minixml/minixml.inc.php');

ob_start();

$lang = $_POST["lang"];

$messageArray["pt"]["invalid"] = "Usuário e/ou senha inválidos. Por favor verifique.";
$messageArray["pt"]["back"] = "voltar";
$messageArray["es"]["invalid"] = "Usuario y/o contraseña inválidos. Por favor verifique.";
$messageArray["es"]["back"] = "volver";
$messageArray["en"]["invalid"] = "Invalid user or password. Please verify.";
$messageArray["en"]["back"] = "back";

$message = $messageArray[$lang];

if ( isset($_POST["auth_submit"]) ) {

		include "auth_config.php";
		$filename = $database_name;
		
	if (!file_exists($filename)) {
			
			echo "Invalid user file. Please check DATABASE_PATH in \"bvs-site-config.php\" file.";
			
	} else {
		
		$logged_in = 0;
		$xmlDoc = new MiniXMLDoc();

		$xmlDoc->fromFile($filename);
		
		$usersXml = $xmlDoc->toArray();
		foreach( $usersXml['users']['user'] as $user){
			if ( isset($user['_attributes']) ){
				$username = $user['_attributes']['name'];
				$password = $user['_attributes']['password'];
				$level = $user['_attributes']['type'];
			}else{
				$username = $user['name'];
				$password = $user['password'];
				$level = $user['type'];
			}			

			if ($_POST["auth_usr"] == trim($username) && md5($_POST["auth_pwd"]) == trim($password)) {	

				$logged_in = 1;
				break;
				
			}
		}
		if ($logged_in != 1) { // IF USER IS NOT LOGGED IN
			
			echo $message['invalid'] . "<br><a href=\"".$_SERVER['HTTP_REFERER']."\">[" . $message['back'] ."]</a>";
		
		} else { // ELSE LOGGED IN			
					
			session_start();
			session_regenerate_id();
			$_SESSION['auth_id'] = "BVS@BIREME";
			$_SESSION['auth_username'] = $_POST["auth_usr"];
			$_SESSION['auth_level'] = $level;

			if (strlen($_POST['auth_pwd']) < 6){
				$_SESSION['auth_pwd_change'] = 'true';
			}
				
			if ($_POST["auth_rm"] == 1) {
				setcookie("phpAuth_username",$_POST["phpAuth_usr"],time()+3600);
			}

			header('Location: '.$login_redirect.'');
		}	
	
	} // END IF FILE EXISTS

} 
ob_end_flush();
?>
