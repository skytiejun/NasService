<?

include "_common.php";
$sshclient = new sshclient();
$AnySql = new AnySql($db_info);

$Date =date("Ymd");
$Time = date("His");



if($_POST['mode'] == "add" || $_POST['mode'] == "up"){
	$user_name				= stripslashes(trim($_POST['username']));
	$user_passwd			= stripslashes(trim($_POST['password']));
	$group						= $_POST['check_group'][0];
	$permission			= $_POST['check_directory'];
	$user_directory_name	= $_POST['directory_name'];

	$arry_group			= explode("|",$group);
	$user_group			= $arry_group[1];
	$user_group_name	= $arry_group[0];
}




if($_POST['mode'] == "add"){//사용자 생성
/*echo "<pre>";
print_r($_REQUEST);

exit;*/



//print_r($user_group);
//exit;
//공유폴더연결
$connect_directory = "mount --bind /home/Storage/company_name/commom_connnect_directory/ /home/Storage/company_name/".$user_name;
//폴더권한증여
$remount				= "mount -o remount,".$permission.",bind /home/Storage/company_name/".$user_name;


$sshclient->exec("useradd ".$user_name); //사용자 생성


$shellscript = "sudo /home/www/hyuk/chpasswd";
 
 // try to change the password
 $callshell=true;
 // get username and password

// if user skip our javascript ...
// make sure we can only change password if we have both username and password
 if(empty($user_name)) {
   $callshell=false;
 }
 if(empty($user_passwd)) {
   $callshell=false;
 }
 if ( $callshell == true ) {
  // command to change password 
  $cmd="$shellscript " . $user_name . " " . $user_passwd;
  // call command
  // $cmd - command, $output - output of $cmd, $status - useful to find if command failed or not
   exec($cmd,$output,$status);

$sshclient->exec("usermod -g ".$user_group." ".$user_name); //사용자 GID수정
$sshclient->exec("usermod -aG ".$user_group." ".$user_name); // 선택한 그룹에 추가

//사용자 공유폴더 설정
if($permission && $permission != "non"){

	$sshclient->exec($connect_directory); //계정 디렉토리를 공유폴더와 연결.

	$sshclient->exec($remount); //연결된 사용자 디렉토리 계정에대해 권한 증여.

}
   
	//사용자 DB저장
	$setUS = "insert into NS_USER set US_ID = '".$user_name."',US_PASSWORD = '".$user_passwd."',US_EMAIL = '".$_POST[email]."',US_INTRO = '".$_POST[userintro]."',US_GROUP = '".$user_group."',US_GROUP_NAME='".$user_group_name."',US_ADD_DATE = '".$Date."',US_ADD_TIME = '".$Time."' ";

	$AnySql->setQuery($setUS);

	//디렉토리정보 저장
	$setDT = "insert into US_DIRECTORY set US_ID = '".$user_name."',DT_NAME = '".$user_directory_name."',DT_PERMISSION = '".$permission."',DT_ADD_DATE = '".$Date."',DT_ADD_TIME = '".$Time."' ";
	$AnySql->setQuery($setDT);

	goto_url("usermanage.html");

   if ( $status == 0 ) { // Success - password changed
   writeHead("Password changed");
   echo '<h3>Password changed</h3>Setup a new password';
   }
   else { // Password failed 
      writeHead("Password change failed");
      echo '<h3>Password change failed</h3>';
      echo '<p>System returned following information:</p>';
      print_r($output);
      echo '<p><em>Please contact tech-support for more info! Or try <a href='.$_SERVER['PHP_SELF'].'again</a></em></p>';

   }
 }
 else {
   writeHead("Something was wrong -- Please try again");
   echo 'Error - Please enter username and password';

 }

	
}


if($_POST['mode'] == "up"){//사용자 수정

	$connect_directory = "mount --bind /home/Storage/company_name/commom_connnect_directory/ /home/Storage/company_name/".$user_name;

	$remount	= "mount -o remount,".$permission.",bind /home/Storage/company_name/".$user_name;

	//사용자 공유폴더 설정
	if($permission && $permission != "non"){

		//$sshclient->exec($connect_directory); //계정 디렉토리를 공유폴더와 연결.

		$sshclient->exec($remount); //연결된 사용자 디렉토리 계정에대해 권한 수정.
	}

	$setUser = "UPDATE NS_USER SET US_EMAIL = '".$_POST[email]."',US_INTRO = '".$_POST[userintro]."' WHERE US_ID = '".$user_name."' ";
	$AnySql->setQuery($setUser);

	$setDirectory = "UPDATE US_DIRECTORY SET DT_PERMISSION = '".$permission."',DT_CHANGE_DATE = '".$Date."',DT_CHANGE_TIME = '".$Time."' WHERE US_ID = '".$user_name."' ";
	$AnySql->setQuery($setDirectory);

	goto_url("usermanage.html");

}

if($_POST['mode'] == "del"){//사용자 삭제


	$user_delete = "DELETE FROM NS_USER WHERE US_ID = '".$_POST['userlist'][0]."' ";

	$res = $AnySql->setQuery($user_delete);

	if($res == true){
		$sshclient->exec("userdel -r ".$_POST['userlist'][0]); //사용자 생성

		goto_url("usermanage.html");
	}

}
?>