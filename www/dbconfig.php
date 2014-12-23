<?php
//include "./class/common.php";

$db_info[db_nm]		= "nas_service";
$db_info[db_host]	= "115.68.24.163";
$db_info[db_id]		= "root";
$db_info[db_pw]	= "qhdkscjfwj@#";

$AnySql = new AnySql($db_info);

/*
$sql = "SELECT * FROM NS_USER";
$rs = $AnySql->getQuery($sql);
while($row = mysql_fetch_assoc($rs)){
	echo $row[US_ID];
}
	/*	function sql_fetch_array($result)
		{
			 // $this->result = mysql_query(
			$row = @mysql_fetch_array($result);
			return $row;
		}*/
/*echo $set = "insert into NS_USER set US_ID = 'gaotiejun',US_PASSWORD = 'aaa' ";
$AnySql->setQuery($set);
*/




?>