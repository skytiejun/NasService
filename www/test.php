<?PHP

include "./class/sshclient.php";
include "./dbconfig.php";

$sshclient = new sshclient();


//$ssh_debug_on=1;
$sshclient->exec("useradd gao5");
echo "<pre>";
print_r($sshclient);

//echo $text;

echo 333333333;



 

exit;

/*
$rs = exec("useradd spc33");
echo "<pre>



</pre>";
echo $rs;

$cpu = "cat /etc/passwd";
exec($cpu,$array);
echo "<pre>";
print_r($array); 

*/


?>
