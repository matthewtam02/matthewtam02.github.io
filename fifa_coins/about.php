<!DOCTYPE html>
<html lang="en">
<?php include'inc/head.php';?>
<body>
	<div id="wrapper">
		<?php include'inc/header.php';?>
		<div id="content">
			<div class="centered">
				<h1 class="title mar">Koop je <span class="orange">FIFA</span> coins nu voordelig!</h1>
				<h1 class="title" style="font-size:2em;">Alleen deze week <span class="orange">10%</span> korting!</h1>
				<div class="section white">
					<h1 class="title">Populaire pakketten</h1>
					<?php
						if(isset($_POST['submit'])){
							$errors = '';
							$myemail = 'no-reply@koopfifacoins.nl';
							if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])){
								$errors .= "\n Error: all fields are required";
							}
							$name = $_POST['name']; 
							$email_address = $_POST['email']; 
							$message = $_POST['message']; 
							if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email_address)){
								$errors .= "\n Error: Invalid email address";
							}
							if(empty($errors)){
								$to = $myemail;
								$email_subject = "Contact form submission: $name";
								$email_body = "You have received a new message. ".
								" Here are the details:\n Name: $name \n ".
								"Email: $email_address\n Message \n $message";
								$headers = "From: $myemail\n";
								$headers .= "Reply-To: $email_address";
								mail($to,$email_subject,$email_body,$headers);
								header('Location: index.php');
							}
						}
					?>
					<form action="" method="POST">
						<table>
							<tr>
								<td>Name:</td>
								<td><input type="text" class="input" name="name"></td>
							</tr>
							<tr>
								<td>Email:</td>
								<td><input type="text" class="input" name="email"></td>
							</tr>
							<tr>
								<td>Subject:</td>
								<td><input type="text" class="input" name="subject"></td>
							</tr>
							<tr>
								<td>Message:</td>
								<td><textarea type="text" class="input textarea" name="message"></textarea></td>
							</tr>
							<tr>
								<td><input type="reset" value="Reset" class="input button"></td>
								<td><input type="submit" name="submit" value="Submit" class="input button"></td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
		<?php include'inc/footer.php';?>
	</div>
</body>
</html>