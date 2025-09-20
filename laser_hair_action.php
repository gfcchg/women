<?php	

	session_start();

	require_once("./phpincludes/commonfunctions.php");
	//require_once("./phpincludes/connection.php");
	include("./phpincludes/smtp-details.php");
	include("./phpincludes/class.smtp.php");
	include("./phpincludes/PHPMailer.class.php");

	date_default_timezone_set('asia/kolkata');
	
	$type = $_POST['type'];
	$REFERER_URL = $_POST['REFERER_URL'];
	$REFERER_UR_arr = explode("/",$REFERER_URL);
	if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['g-recaptcha-response'])) 

	{
		
		$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

		$recaptcha_secret = '6LdcLv0pAAAAAIGtDnfiqz4xj7h66AS2fAfqlRn-';

		$recaptcha_response = $_POST['g-recaptcha-response'];

		// Make and decode POST request:

		$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);

		$recaptcha = json_decode($recaptcha);


		// Take action based on the score returned:
		
		if ($recaptcha->success) 
		{
			
			if(isset($_POST['name']) && $_POST['name'] != "")

			{	

				
				if(!preg_match("/[A-Za-z]/", $_POST['name']))

				{

					$_SESSION['Error'] = "Invalid Data";

					$_SESSION['Post'] = $_POST;

					header("Location:".$_SERVER['HTTP_REFERER']);

					exit();

				}

				if(!preg_match("/^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$/", $_POST['email']))

				{

					$_SESSION['Error'] = "Invalid Data";

					$_SESSION['Post'] = $_POST;

					header("Location:".$_SERVER['HTTP_REFERER']);

					exit();

				}

				if(!preg_match("/^[0-9]*$/", $_POST['mobile']))

				{

					$_SESSION['Error'] = "Invalid Data";

					$_SESSION['Post'] = $_POST;

					header("Location:".$_SERVER['HTTP_REFERER']);

					exit();

				}
				
				if(!preg_match("/[A-Za-z]/", $_POST['city']))

				{

					$_SESSION['Error'] = "Invalid Data";

					$_SESSION['Post'] = $_POST;

					header("Location:".$_SERVER['HTTP_REFERER']);

					exit();

				}

				$fp=fopen("./mailer/hifu_contact_mailer.html","r");

				$message= fread($fp,filesize("./mailer/hifu_contact_mailer.html"));

				

				$DateTime = convertDBDateTime(date("Y-m-d H:i:s"));
				$Appointment = str_replace('T',' ',$_POST['Appointment']);
				
				$Subject = "Enquiry Form posted for Laser Hair Removal Treatment on ".convertDBDateTime($Appointment).", at ".$_POST['location'].", ".$_POST['city']."";
				
				$_POST['RegTime']=date("Y-m-d H:i:s");

						



				$message=str_replace('$Name', $_POST['name'],$message);
				$message=str_replace('$Email',$_POST['email'],$message);
				$message=str_replace('$PhoneNo',$_POST['mobile'],$message);
				$message=str_replace('$City',$_POST['city'],$message);
				$message=str_replace('$location',$_POST['location'],$message);
				
				$message=str_replace('$Appointment',convertDBDateTime($Appointment),$message);
				$message=str_replace('$REFERER_URL',$_POST['REFERER_URL'],$message);
				
				#echo $message; exit();

				$mail             = new PHPMailer();

				$body             = $message;

				$mail->IsSMTP(); // telling the class to use SMTP

				$mail->Host       = $SmtpHost; // SMTP server

				$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)

														// 1 = errors and messages

														// 2 = messages only

				$mail->SMTPAuth   = true;                  // enable SMTP authentication

				$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier

				$mail->Port       = $SmtpPort;                 // set the SMTP port for the server

				$mail->Username   = $SmtpUserName;  // username

				$mail->Password   = $SmtpPassword;            // password

						

				$mail->SetFrom($FromEmail, $FromName);

				

				$mail->Subject    = $Subject;

				$mail->MsgHTML($body);


				//$mail->AddAddress('akash@dimakhconsultants.com');
				$mail->AddAddress($RecipientMailId);

				$mail->Addbcc('suhrud@dimakhconsultants.com');
				
				
				if(!$mail->Send()) 

				{
				
					//echo "Mailer Error: " . $mail->ErrorInfo; exit;

				} 

				else 

				{

					//echo "Message sent!"; exit;
					if($_POST['email'] !=''){
				
						$mail1             = new PHPMailer();
						$Subject1 = "Appointment Set for Laser Hair Removal Treatment on ".convertDBDateTime($Appointment).", at ".$_POST['location'].", ".$_POST['city']."";
						$body             = $message;
						$mail1->IsSMTP(); // telling the class to use SMTP
						$mail1->Host       = $SmtpHost; // SMTP server
						$mail1->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)// 1 = errors and messages// 2 = messages only

						$mail1->SMTPAuth   = true;                  // enable SMTP authentication
						$mail1->SMTPSecure = "ssl";                 // sets the prefix to the servier
						$mail1->Port       = $SmtpPort;                 // set the SMTP port for the server
						$mail1->Username   = $SmtpUserName;  // username
						$mail1->Password   = $SmtpPassword;            // password						
						$mail1->SetFrom('rebornpune@gmail.com', $FromName);					
						$mail1->Subject    = $Subject1;
						$mail1->MsgHTML($body);
						$mail1->AddAddress($_POST['email']);
						if(!$mail1->Send()) 
						{
							//echo "Mailer Error: " . $mail1->ErrorInfo; exit;
						}else{
							//echo "Message sent!"; exit;
						}
					}

				}

				header("location:laser-hair-thank-you.php");

				exit();

			} // End of if(isset($_POST['form_name']) && $_POST['form_name'] != "")

			else
			{

				echo ("<script LANGUAGE='JavaScript'>

				window.alert('Error Occurred, Please try again');

				window.location.href='$REFERER_UR_arr[2]';

				</script>");	

			}
		}
		else
		{

			echo ("<script LANGUAGE='JavaScript'>

			window.alert('Captcha verification failed, Please try again');

			window.location.href='$REFERER_UR_arr[2]';

			</script>");

		}
				
	}else{

		echo ("<script LANGUAGE='JavaScript'>

		window.alert('Error Occurred, Please try again');

		window.location.href='$REFERER_UR_arr[2]';

		</script>");

	}

?>
