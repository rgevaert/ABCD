<?
	require_once("../php/include.php");
	require_once("../php/class.phpmailer.php");

	// use internal php mail function
	function sendByMail()
	{
		$response = $_POST["response"];
		$toPosition = substr($_POST["subject"],strpos($_POST["subject"],"(")+1,strpos($_POST["subject"],")")-1)-1;
		$subject = substr($_POST["subject"],strpos($_POST["subject"],")")+1,strlen($_POST["subject"]));
		$fromName = $_POST["fromName"];
		$from = $_POST["from"];
		$message = str_replace("\r","<br>",$_POST["message"]);

		if(strpos($_POST["account"],",") === false) {
			$account = $_POST["account"];
		} else {
			$account = explode(", ",$_POST["account"]);
		}
		if(strpos($_POST["host"],",") === false) {
			$host = $_POST["host"];
		} else {
			$host = explode(", ",$_POST["host"]);
		}
		$lang = $_POST["lang"];

		if(is_array($account) && is_array($host)) {
			$count = count($account);
			$to = $account[$toPosition]."@".$host[$toPosition];
			/*
			for($i=0;$i<count($account);$i++) {
				if($account[$i] != "" && $host[$i] != "") {
					$to .= $account[$i]."@".$host[$i];
					if($i != count($account)-1) $to .= ", ";
				}
			}
			*/
		} else {
			$to = $account."@".$host;
		}

		$ip = getenv("REMOTE_ADDR");
		$text = "";

		$text .= "<p>".$message."</p>";
		$text .= "<p>".$fromName." &lt;<a href=\"mailto:".$from."\">".$from."</a>&gt;<br>".$ip."</p>";

		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	
		$headers .= "From: ".$from."\r\n";
	
		mail($to,$subject,$text,$headers);
		return;
	}
	
	// use phpmailer class to send by SMTP
	function sendBySMTP()
	{
		global $def;

		$mail = new PHPMailer();

		$mail->IsSMTP();  // set mailer to use SMTP		
		$mail->Host = $def["SMTP_HOST"];  // specify main and backup server
		$mail->SMTPAuth = true;  // turn on SMTP authentication
		$mail->Username = $def["SMTP_USERNAME"];  // SMTP username
		$mail->Password = $def["SMTP_PASSWORD"]; // SMTP password

		$response = $_POST["response"];
		$toPosition = substr($_POST["subject"],strpos($_POST["subject"],"(")+1,strpos($_POST["subject"],")")-1)-1;
		$subject = substr($_POST["subject"],strpos($_POST["subject"],")")+1,strlen($_POST["subject"]));
		$fromName = $_POST["fromName"];
		$from = $_POST["from"];
		$message = str_replace("\r","<br>",$_POST["message"]);

		if(strpos($_POST["account"],",") === false) {
			$account = $_POST["account"];
		} else {
			$account = explode(", ",$_POST["account"]);
		}
		if(strpos($_POST["host"],",") === false) {
			$host = $_POST["host"];
		} else {
			$host = explode(", ",$_POST["host"]);
		}
		$lang = $_POST["lang"];

		if(is_array($account) && is_array($host)) {
			$count = count($account);
			$to = $account[$toPosition]."@".$host[$toPosition];
			/*
			for($i=0;$i<count($account);$i++) {
				if($account[$i] != "" && $host[$i] != "") {
					$to .= $account[$i]."@".$host[$i];
					if($i != count($account)-1) $to .= ", ";
				}
			}
			*/
		} else {
			$to = $account."@".$host;
		}

		$ip = getenv("REMOTE_ADDR");
		$text = "";
	
		$text .= "<p>".$message."</p>";
		$text .= "<p>".$fromName." &lt;<a href=\"mailto:".$from."\">".$from."</a>&gt;<br>".$ip."</p>";
	
		$mail->FromName = $fromName;
		$mail->From     = $from;
		$mail->AddAddress($to);
			
		$mail->IsHTML(true);  // set email format to HTML
			
		$mail->Subject = $subject;
		$mail->Body    = $text;

		if( !$mail->Send() ){
			echo $mail->ErrorInfo;
			die("There was a problem sending this mail!");
		}
		
		$mail->ClearAddresses();
		$mail->ClearAttachments();
		return;
	}

//----------------------------------------------------------------------------------------------------------------------------------

	if ($def['SMTP_HOST'] != ''){
		sendBySMTP();
	}else{
		sendByMail();
	}

	header("Location: $response");

?>
