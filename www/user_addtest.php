<?
include "./class/sshclient.php";

$sshclient = new sshclient();

$userName = "sky02"; // $userName = "생성ID입력";
$userPasswd = "sky02"; //$userPasswd = "생성PW입력";


//사용자생성 실행
$sshclient->exec("useradd {$userName}"); //사용자 ID생성
$sshclient->exec("echo {$userPasswd} | passwd --stdin {$userName})"; //사용자 PW생성

?>
