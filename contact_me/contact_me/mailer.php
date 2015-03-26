<?php session_start();
if(isset($_POST['Submit'])) {
$youremail = 'matthewtam02@gmail.com';
$fromsubject = 'matthewtam02.github.io';
$fname = $_POST['fname'];
$mail = $_POST['mail'];
$gender = $_POST['gender']; 
$subject = $_POST['subject']; 
$message = $_POST['message']; 
	$to = $youremail; 
	$mailsubject = 'Masage recived from'.$fromsubject.' Contact Page';
	$body = $fromsubject.'
	
	The person that contacted you is  '.$fname.'
	 Gender: '.$gender.'
	 E-mail: '.$mail.'
	 Subject: '.$subject.'
	
	 Message: 
	 '.$message.'