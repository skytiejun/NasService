<?PHP

include "./class/sshclient-jaeminj.php";
//exit;
//$ssh_debug_on=true;
$on_debug=true;
$sshclient = new sshclient() ;
$sshclient->base = '/home/www/key';

/*exec("cat /etc/passwd",$array);  print_r($array); exit;
echo 2222222;*/

$sshclient->exec("useradd gao4");
echo "<pre>";
print_r($sshclient);

//echo $text;

echo 333333333;


$sshclient->raw_exec("whoami");

echo "<pre>";
print_r($sshclient);



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
