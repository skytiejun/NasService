<?php
// change .. me! - shell script name
$shellscript = "sudo /home/www/hyuk/chpasswd";
 
// Make sure form is submitted by user
if(!(isset($_POST['pwdchange']))) {
 // if not display them form
 writeHead("Change password");
 writeForm();
 writeFoot();
}
else {
 // try to change the password
 $callshell=true;
 // get username and password
 $_POST['username'] = stripslashes(trim($_POST['username']));
 $_POST['passwd'] = stripslashes(trim($_POST['passwd']));
 
// if user skip our javascript ...
// make sure we can only change password if we have both username and password
 if(empty($_POST['username'])) {
   $callshell=false;
 }
 if(empty($_POST['passwd'])) {
   $callshell=false;
 }
 if ( $callshell == true ) {
  // command to change password 
  $cmd="$shellscript " . $_POST['username'] . " " . $_POST['passwd'];
  // call command
  // $cmd - command, $output - output of $cmd, $status - useful to find if command failed or not
   exec($cmd,$output,$status);
   if ( $status == 0 ) { // Success - password changed
   writeHead("Password changed");
   echo '<h3>Password changed</h3>Setup a new password';
   writeFoot();
   }
   else { // Password failed 
      writeHead("Password change failed");
      echo '<h3>Password change failed</h3>';
      echo '<p>System returned following information:</p>';
      print_r($output);
      echo '<p><em>Please contact tech-support for more info! Or try <a href='.$_SERVER['PHP_SELF'].'again</a></em></p>';
      writeFoot();
   }
 }
 else {
   writeHead("Something was wrong -- Please try again");
   echo 'Error - Please enter username and password';
   writeForm();
   writeFoot();
 }
}
 
// display html head
function writeHead($title) {
echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> ' .$title. '</title>
<style type="text/css" media="screen">
.passwdform {
	position: static;
	overflow: hidden;
}
 
.passwdleft {
	width: 25%;
	text-align: right;
	clear: both;
	float: left;
	display: inline;
	padding: 4px;
	margin: 5px 0;
}
 
.passwdright {
	width: 70%;
	text-align: left;
	float: right;
	display: inline;
	padding: 4px;
	margin: 5px 0;
}
 
.passwderror {
	border: 1px solid #ff0000;
}
 
.passwdsubmit {
}
</style>
 
</head>
 
<body>';
 
}
// display html form
function writeForm() {
echo '
<h3>Use following form to change password:</h3>
 
<script>
function checkForm() {
if (document.forms.changepassword.elements[\'username\'].value.length == 0) {
    alert(\'Please enter a value for the "User name" field\');
    return false;
}
if (document.forms.changepassword.elements[\'passwd\'].value.length == 0) {
    alert(\'Please enter a value for the "Password" field\');
    return false;
}
  return true;
}
</script>
 
<div class="contactform">
<form action="' . $_SERVER[PHP_SELF]. '" method="post" onSubmit="return checkForm()" name="changepassword">
<div class="passwdleft"><label for="lblusername">User Name: </label></div>
<div class="passwdright"><input type="text" name="username" id="lblusername" size="30" maxlength="50" value="" /> (required)</div>
<div class="passwdleft"><label for="lblpasswd">Password: </label></div>
<div class="passwdright"><input type="password" name="passwd" id="lblpasswd" size="30" maxlength="50" value="" /> (required)</div>
<div class="passwdright"><input type="submit" name="Submit" value="Change password" id="passwdsubmit" />
<input type="hidden" name="pwdchange" value="process" /></div>
</form>
</div>
';
 
}
// display footer 
function writeFoot(){
echo '</body>
</html>
';
}
?>