<?php
	require_once("config.php");
	require_once("functions.php");
	require_once("includes/header.html");
?>		
	<div id="main">			
		<h1>Bugreport filing</h1>
		
		
		<?php
			if (isset($_POST['bugreport_submit'])) {
				$headers = $_POST["bugreport_headers"];
				$remarks = $_POST["bugreport_remarks"];
				$email = $_POST["bugreport_email"];
				
				// Start mail
				$txtMail = "Email: ". $email ."\n";
				$txtMail .= "IP: ". $_SERVER['REMOTE_ADDR'] ."\n\n";
				$txtMail .= "=================================================\n";
				$txtMail .= "Remarks:\n";
				$txtMail .= $remarks ."\n\n";
				$txtMail .= "=================================================\n";
				$txtMail .= "Headers:\n";
				$txtMail .= $headers ."\n";
				$txtMail .= "=================================================\n";
				
				mail("mattias.geniar@gmail.com", "MailHeader - Bugreport", $txtMail, "From: Mailheader Bugreport <bugreport@mailheader.mattiasgeniar.be>");
			?>
			Thank you for filing in this bugreport. I will review and make changes accordingly.
				
			<?php
			} else {
				echo "Sorry, I detect invalid input.";
			}
		?>
		<br /><a href="<?php echo CP_PRODUCT_URL; ?>">Input new headers</a>.
	</div>
<?php
	require_once("includes/footer.html");
?>