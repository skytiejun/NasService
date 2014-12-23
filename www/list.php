<?PHP

//include "./ui/sample/6/cls/sshclient.php";
include "./class/localhost.php";

//$sshclient = new sshclient();
$local = new localhost();
echo 1111111111;
$local->exec("cat /etc/passwd");

//$aa = "cat /etc/passwd|grep -v nologin|grep -v halt|grep -v shutdown|awk -F":" '{ print $1"|"$3"|"$4 }'|more";

//$local->exec("$aa");
echo 2222222222;
echo "<pre>";
print_r($local);

?>
