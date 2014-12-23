<?
/*
$dbconfig_file = "db_select.php";
if (file_exists("$g4[path]/$dbconfig_file"))
{
	include_once("$g4[path]/$dbconfig_file");
}
else
{
	jsAlert("DB 설정 파일이 존재하지 않습니다.\\n\\n 확인후 이용바랍니다.", "");
}
*/
//==============================================================================
// 권한관련 변수 설정  -- 시작
//==============================================================================
define("ACT_NONE", 0x0);

define("ACT_READ", 0x1);
define("ACT_ADD", 0x2);
define("ACT_DEL", 0x4);
define("ACT_EDIT", 0x8);

define("ACT_LIST", 0x10);
define("ACT_LOCK", 0x20);
define("ACT_UNLOCK", 0x40);
define("EXEC_START", 0x80);

define("EXEC_STOP", 0x100);
define("EXEC_RELOAD", 0x200);
define("EXEC_STATUS", 0x400);
define("SYSTEM", 0x800);

define("ACT_USER", 0x78f);
define("ACT_AUDIT", 0x411);
define("ACT_MGMT", 0x7ff);
define("ACT_ALL", 0x1fff);
// 권한관련 변수 설정  -- 끝



function ping($host)
{
        exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
        return $rval === 0;
}

/* Test Class Function */
function debugEcho($str){
    global $on_debug;
    echo nl2br($on_debug?ereg_replace("\t" , "　　　" , "$str\n"):'');
}
function decho($str, $file='', $line=''){
    global $on_debug;
	if(!$on_debug) return ;

	switch(php_sapi_name()){
	case 'cli':
	if($file && $line){ 
	$file=basename($file);
	echo "[$file:$line]$str\n";
	}
    else echo "$str\n";

	break;
	default:
	if($file && $line){ 
	$file=basename($file);
	echo nl2br("[$file:$line]$str\n");
	}
    else echo nl2br("$str\n");
	break;
	}

}

function showValue($arr){
    global $on_debug;
    debugEcho(nl2br( __FILE__ . " : " . __LINE__ ));
    if(is_array($arr) ==false){
    //    debugEcho(nl2br("showValue No Data"));
    //    debugEcho(nl2br( __FILE__ . " : " . __LINE__ ));
        return false;
    }
   while (list($prop, $val) = each($arr)){
    if(is_array($val) == true){
        debugEcho($on_debug?"\t\t$prop is array":'');
        showValue($val);
    }else debugEcho("\t\t$prop = $val");
   }
//    debugEcho(nl2br( __FILE__ . " : " . __LINE__ ));
}
function showClassInfo($obj, $ClassName=''){
    global $on_debug;
   debugEcho("- $ClassName");
    debugEcho("\tProperty");
    $arr = get_object_vars($obj);
    showValue($arr);

    debugEcho("\tMethod");
    $arr = get_class_methods(get_class($obj));
    foreach ($arr as $method)
    debugEcho("\t\tfunction $method()");

}

function myErrorHandler ($errno, $errstr, $errfile, $errline) {
  switch ($errno) {
  case FATAL:
    echo "<b>FATAL</b> [$errno] $errstr<br>\n";
    echo "  Fatal error in line ".$errline." of file ".$errfile;
    echo ", PHP ".PHP_VERSION." (".PHP_OS.")<br>\n";
    echo "Aborting...<br>\n";
    exit(1);
    break;
  case ERROR:
    echo "<b>ERROR</b> [$errno] $errstr $errfile/$errline<br>\n";
    break;
  case WARNING:
    echo "<b>WARNING</b> [$errno] $errstr $errfile/$errline <br>\n";
    break;
    default:
    echo "Unkown error type: [$errno] $errstr $errfile/$errline<br>\n";
    break;
  }
}
if($on_debug == -1 ) $old_error_handler = set_error_handler("myErrorHandler");


function myMail($to, $to_name, $from, $from_name, $subject, $body,$return_path, $cc="", $bcc="") { 
        global $on_debug;
        $recipient = "$to_name <$to>"; 
        $headers = "From: $from_name <$from>\n"; 
        $headers .= "X-Sender: <$from>\n"; 
        $headers .= "X-Mailer: PHP ".phpversion()."\n"; 
        $headers .= "X-Priority: 1\n"; 
        $headers .= "Return-Path: <$return_path>\n"; 

#        $boundary = "--------" . uniqid("part"); 

#        $headers .= "MIME-Version: 1.0\n"; 
#        $headers .= "Content-Type: text/html; boundary=\"$boundary\""; 
        $headers .="Content-Type: text/html;charset=EUC-KR\n";
        $bodytext = stripslashes($body); 

        if($cc) $headers .= "cc: $cc\n"; 
        if($bcc) $headers .= "bcc: $bcc"; 

        if($on_debug == true){
                if($return_path) decho("mail($recipient,$subject,\$bodytext,$headers , \"-f$return_path\"); ");
                else decho("mail($recipient,$subject,\$bodytext,$headers ); ");
        }else{
                if($return_path) mail($recipient,$subject,$bodytext,$headers , "-f$return_path"); 
                else        mail($recipient,$subject,$bodytext,$headers ); 
        }
} 


function isIp($ip) {
   $ip = trim($ip);
   if (strlen($ip) < 7) return $status = false;
   if (!ereg("\.",$ip)) return $status = false;
   if (!ereg("[0-9.]{" . strlen($ip) . "}",$ip)) return $status = false;
   $ip_arr = split("\.",$ip);
   if (count($ip_arr) != 4) return $status = false;
   for ($i=0;$i<count($ip_arr);$i++) {
       if ((!is_numeric($ip_arr[$i])) || (($ip_arr[$i] < 0) || ($ip_arr[$i] > 255))) return $status = false;
   }
   if (!$status) $status = true;
   return $status;
} 

function jsAlert($msg='', $url=''){

	if(headers_sent() == false){
	  $url=$HTTP_HOST . $url;
	  $html .="<html>";
	  $html .="<head>";
	  $html .="<title></title>";
	  $html .="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=euc-kr\">";
	  if($url) 
	    $html .="<meta http-equiv=\"refresh\" content=\"0;url=$url\"> ";
	}

    $html.="<script>";
    if($msg){
        $msg=ereg_replace("'", "\'", $msg);
        $html.="alert('$msg');\n";
		$html.="if (opener){opener.document.location.reload();}\n";
//        $html.="location.replace('$url');";
    }
    if($url =='')
		$html.="history.back();";

    else
	    $html.="location.replace('$url');";
    
//    $html.="opener.document.location.reload();\n";  //수정후 리로드 되게하기위해서 추가해줌 (2010.04.12)

    $html .="</script>";
    if(headers_sent() == false){
        $html .="</head>";
        $html .="<body> </body> </html>";
    }
    echo $html;
	exit;

}

function selfClose(){   //오픈한 팝업창 수정후 확인 누르면 닫히는 함수 (2010.03.22 )

	if(headers_sent() == false){
		$html .="<html>";
		$html .="<head>";
		$html .="<title></title>";
		$html .="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=euc-kr\">";

	}

    $html.="<script>";
         $html.="self.close();\n";

    $html .="</script>";
    if(headers_sent()==false){
        $html .="</head>";
        $html .="<body> </body> </html>";
    }
    echo $html;
	exit;

}

function jsMsgRedirect($msg='', $url="/action/close.php" ){  

	if(headers_sent() == false){
		$html .="<html>";
		$html .="<head>";
		$html .="<title>$msg_group</title>";
		$html .="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=euc-kr\">";
		$html .="<meta http-equiv=\"refresh\" content=\"3;url=$url\" />";


	}

    $html.="<script>";
    if($msg){
        $msg=ereg_replace("'", "\'", $msg);
        $html.="opener.document.location.reload();\n";
//        $html.="opener.document.reload();\n";
//        $html.="location.replace('$url');";
    }

    $html .="</script>";
    if(headers_sent()==false){
        $html .="</head>";
        $html .="<body> <center>$msg <input type=button onClick='self.close();' value='Close'> <center></body> </html>";
    }
    echo $html;
	exit;

}

function jsAlertClose($msg=''){   //오픈한 팝업창 수정후 확인 누르면 닫히는 함수 (2010.03.22 )

	if(headers_sent() == false){
		$html .="<html>";
		$html .="<head>";
		$html .="<title></title>";
		$html .="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=euc-kr\">";
	}

    $html.="<script>";
    if($msg){
        $msg=ereg_replace("'", "\'", $msg);
        $html.="alert('$msg');\n";
        $html.="opener.document.location.reload();\n";
//        $html.="opener.document.reload();\n";
        $html.="self.close();\n";
//        $html.="location.replace('$url');";
    }

    $html .="</script>";
    if(headers_sent()==false){
        $html .="</head>";
        $html .="<body> </body> </html>";
    }
    echo $html;
	exit;

}



class Base
{ 
   function newClass($parent, $seq='') 
   { 
       return new $parent($seq); 
   } 

	function getValueFormat($value){
		$value="1.1.1.1:80";
		
		if ( preg_match('/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*$/',$value) ) return 'Email';
		if ( preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}:([0-9]+)$/',$value) ) return 'IpAddrPort';
		if ( preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/',$value) ) return 'IpAddr';

		return 'unknown';	
	}

}

/* UnitTest Class
 * test for testing and save result
 * result shows the result 
 */
class UnitTest
{
	
	function test($expr1 ,$expr2, $msg)
	{
		$cnt = $this->cnt ;
		$cnt ++;
		$this->map[$cnt]['expr1'] = $expr1;
		$this->map[$cnt]['expr2'] = $expr2;
		$this->map[$cnt]['msg'] = $msg;
		if($expr1 == $expr2){
			$this->sucess ++ ;
		}else{
			$this->failure ++;
		}
		
	}
	
	function result()
	{
		
		printf("Total: %d\tSucess: %d\tFailure:%d\n", $this->total,$this->sucess, $this->failure);
		foreach($this->map as $cnt=>$el){
			if($el['expr1'] != $el['expr2']){
				printrf("Test Case %d: ", $cnt);
				echo $el['expr1'];
				echo ":";
				echo $el['$expr2'];
				echo "\n";
				
			}
		}
		
	}
}



/* Class of Db Connection */
class AnySql {
	var $link = FALSE;
	var $sql='';
    var $result;
    var $db_nm='';
    var $db_host;
    var $db_id;
    var $n_row=-1;
    var $n_rows=-1;
    var $n_field=0;
    var $tb_nm="";
    var $flds=array();
    var $on_setdata=true;
        

        function AnySql($db_info){

            if($db_info[db_nm] && $db_info[db_host] && $db_info[db_id] && $db_info[db_pw])
				$this->connect($db_info[db_nm],$db_info[db_host],$db_info[db_id],$db_info[db_pw]);
            else
				echo "DB정보가 없습니다.";	

            if($sql !='')
                $this->set($sql);

            return $this->link;
        }
       
 
        function connect($db_nm, $db_host, $db_id, $db_pw) {
            $this->db_nm=$db_nm;
            $this->db_host=$db_host;
            $this->db_id=$db_id;
            $this->link = mysql_connect($db_host,$db_id,$db_pw) or die("$db_host DB에 접속할수 없습니다.");
            mysql_select_db($db_nm,$this->link);
            return $this->link;

        }
    
        function close() {
            return mysql_close($this->link);
        }
		/*2014/12/5 추가 고철군*/
		function getUser($userID = "",$group_name=""){
			$where = "";

			if($userID){
				$where  = " AND US_ID =  '".$userID."' ";
			}
			if($group_name){
				$where  = " AND US_GROUP_NAME =  '".$group_name."' ";
			}
			$sql = "SELECT * FROM NS_USER WHERE 1=1 ".$where." ORDER BY US_NO DESC";

			$this->result = mysql_query($sql);


			if($userID){
				//한개 라인 출력시 바로 리턴
				return $row = mysql_fetch_assoc($this->result);
			}else{

				while($row = mysql_fetch_assoc($this->result)){
					$user[] = $row;
				}
				return $user;
			}
		}
		function getDirectoryInfo(){
			$sql = "SELECT * FROM DIRECTORY_INFO";
			$this->result = mysql_query($sql);

			while($row = mysql_fetch_assoc($this->result)){
					$DirectoryInfo[] = $row;
				}

			return $DirectoryInfo;
		}
		function getUserDirectory($userID){

			$sql = "SELECT * FROM US_DIRECTORY WHERE US_ID = '".$userID."' ";
			$this->result = mysql_query($sql);

			while($row = mysql_fetch_assoc($this->result)){
					$Directory[$row[DT_NAME]] = $row;
				}

			return $Directory;
		}
		function getQuery($result)
		{
			return $this->result = mysql_query($result);
			//$row = mysql_fetch_array($this->result);
			//return $row;
		}
		function setQuery($result)
		{
			$status = 0;
			$this->result = mysql_query($result);
			//$row = mysql_fetch_array($this->result);

			if(mysql_affected_rows() >0)
			{
				$status = 1;
			}
			return $status;
		}
		/*2014/12/5 추가 고철군*/
        function set($sql) {

            global $on_debug, $on_trace,  $on_setdata, $REMOTE_ADDR, $COMMON_DIR;

            if($sql ){
				$this->sql=$sql;
				$today  = date("Y-m-d");
				$c_date = date("Y-m-d H:i:s");
				$file_nm= $COMMON_DIR."/logs/sql_log/".$today;	
				$fp     = @fopen( $file_nm, 'a+');
				chown($file_nm, "nobody");
				chgrp($file_nm, "nobody");
				if($fp){
					$log_msg="[$c_date]		$sql\n";
					fwrite($fp, $log_msg, strlen($log_msg));
					fclose($fp);
				}

			} else {
				 return false;
			}

            if($on_debug ){

                decho($sql );
                if(ereg("SELECT ", $sql) || ereg("select ", $sql)  || $on_setdata==true){
                    $this->result= mysql_query($sql,$this->link) 
                    or die("Class Mysql:Method COMMIT SQL ERROR  \n\n$sql\n\n" . mysql_error());
                }

            }else{
                $this->result= mysql_query($sql,$this->link);

            }

            
            if(ereg("^SELECT ", trim($sql)) || ereg("^select ", trim($sql)) ){
                if($on_debug){
                    $this->n_field	= mysql_num_fields($this->result);
                    $this->n_row	= -1;
                    $this->n_rows	= mysql_num_rows($this->result);

                }else{
                    $this->n_field	= @mysql_num_fields($this->result);
                    $this->n_row	= -1;
                    $this->n_rows	= @mysql_num_rows($this->result);
                }
            }

            return true;
        }

        function showtables(){
            $this->result = mysql_query("show tables ");
            while($data=mysql_fetch_array($this->result)){
                $rows[] = $data[0];
            }
            return $rows;
        }
        function describe ( $table ){
            $this->result = mysql_query("describe $table ");
            while($data=mysql_fetch_assoc($this->result)){
                $rows[] = $data;
            }
            return $rows;
        }
        

        function getFlds($tb_nm){
            $this->result = mysql_query("DESCRIBE $tb_nm", $this->link);
            decho(" DESCRIBE $tb_nm , resource id : $this->link, db_host : $this->db_host ");

             $this->fld==array();
             if($this->result){
              while($row = mysql_fetch_array($this->result))
              {

                $field = $row['Field'];
                $fld[$field] = $field;
              }
              $this->tb_nm=$tb_nm;
              return $this->fld=$fld;
             }

             return false;
        }    
        function showInput($tb_nm, $vtype='text', $vname='data', $vval='$data'){
            global $on_debug;
            if($on_debug){
            $fld=$this->getFlds($tb_nm);
            foreach($fld as $idx => $val){
                ?>
                <input type="<?=$vtype?>" name="<?=$vname?>[<?=$idx?>]" value="<?='<?='?><?=$vval?>['<?=$idx?>']<?='?>'?>">
                <?
            }
            }else return false;
        }


        function setItems($tb_nm, $pk, $fnth, $vdata){
            // $vdata[0][fnm1] 
            // $vdata[0][fnm2] 
            // $vdata[0][fnm3] 
            showValue($vdata);
            if(is_array($vdata) == true){
                foreach($vdata as $idx =>$val){
                    if(is_array($val) && array_key_exists($fnth, $val) == true)  $pk[$fnth]=$val[$fnth]?$val[$fnth]:$idx;
                    else $pk[$fnth]=$idx+1;

                    if($this->isNullData($tb_nm, $pk, $val) )
                        $this->delete($tb_nm, $pk);
                    else
                        $this->setData($tb_nm, $pk, $val);
                }
            }
        }

        function getItems($tb_nm, $pk, $fnth){
                switch($this->count($tb_nm, $pk) ){
                    case 0:
                        return false;
                        break;
                    case 1:
                    $item[0]=$this->getData($tb_nm, $pk);
                        break;
                    default:
                    $item=$this->getData($tb_nm, $pk, true, $fnth);
                        break;
                }
                return $item;
        }

        function setData($tb_nm, $pk, $vdata){
            // $pk[$pkey]=$pkeyvalue
            // $vdata[$fldkey]= $fldvalue;
//print_r2($pk);
//print_r2($vdata);
            if(is_array($vdata) == false ){

                return false;
            }
            if(is_array($pk) == false ){
                echo "$tb_nm :: PK_ERR";exit;
                return false;
            }
            

            if($this->tb_nm == $tb_nm){
                $fld=$this->fld;
            }else{  
                $fld=$this->getFlds($tb_nm);
            }
            if(is_array($fld) == true){
                foreach($fld as $idx => $val){
                    if(array_key_exists($idx, $pk)==false && array_key_exists($idx, $vdata) == true ){
                        $data_stmt .= ($data_stmt ? " , " : "" ) .    "$idx= '" . trim($vdata[$idx]) ."'";
                    }
                }
            }else{
                return false;
            }


            foreach($pk as $idx => $val){
                if($idx !=''){
                $pk_stmt .=  ( $pk_stmt ? " AND " : "") .  "$idx= '" . trim($val) ."'";
                }
            }

            if($this->get("SELECT count(*) as flag FROM $tb_nm WHERE $pk_stmt ")  > 0) {
                $sql="UPDATE  $tb_nm SET $data_stmt WHERE $pk_stmt ";
            }else{
				$pk_stmt="";
				$data_stmt="";
				foreach($fld as $idx => $val){
					if(array_key_exists($idx, $pk)==true  ) $pk_stmt .= ($pk_stmt ? " , " : "" ) .    "$idx= '" . trim($pk[$val]) ."'";
					else if(array_key_exists($idx, $vdata)==true  ) $data_stmt .= ($data_stmt ? " , " : "" ) .    "$idx= '" . trim($vdata[$val]) ."'";
				}

				if($data_stmt && $pk_stmt)
	                $sql="INSERT INTO  $tb_nm SET $data_stmt , $pk_stmt ";

				else if(!$data_stmt && $pk_stmt)
	                $sql="INSERT INTO  $tb_nm SET $pk_stmt ";

				else if($data_stmt && !$pk_stmt)
	                $sql="INSERT INTO  $tb_nm SET $data_stmt ";

				else if(!$data_stmt && !$pk_stmt)
					return false;
				
            }
            $this->set($sql);
        }

        function isNullData($tb_nm, $pk, $vdata){
			if(is_array($vdata)==false) return true;

            if($this->tb_nm == $tb_nm){
                $fld=$this->fld;
            }else{  
                $fld=$this->getFlds($tb_nm);
            }

			foreach($fld as $idx => $val){
				if(array_key_exists($idx, $pk)==false && array_key_exists($idx, $vdata) == true ){
					$data_concat .=  trim($vdata[$idx]) ;
				}
			}

			if($data_concat)
				return false;
			else
				return true;
		}


        function getData($tb_nm, $pk='', $bAll=true, $od_stmt=''){
            // $pk[$pkey]=$pkeyvalue
            // $vdata[$fldkey]= $fldvalue;

            if($this->tb_nm == $tb_nm){
                $fld=$this->fld;
            }else{  
                $fld=$this->getFlds($tb_nm);
            }

            if(is_array($pk)== true){
                foreach($pk as $idx => $val) $pk_stmt .=  ( $pk_stmt ? " AND " : "") .  "$idx= '" . trim($val) ."'";
                    $total =$this->get("SELECT count(*) FROM $tb_nm WHERE $pk_stmt ");
                    $sql="SELECT * FROM $tb_nm  WHERE $pk_stmt " . ($od_stmt ? " ORDER BY $od_stmt ": "");
            }else{
                $total =$this->get("SELECT count(*) FROM $tb_nm " . ($od_stmt ? " ORDER BY $od_stmt ": "") );
//              $sql="SELECT * FROM $tb_nm  "; //기존
                $sql="SELECT * FROM $tb_nm  " . ($od_stmt ? " ORDER BY $od_stmt ": "");	//기존에 있는거 잘못되어있어서 수정 2010.10.15 
            }

            switch($total){

                case 0:
                    return false;
				break;

                case 1:
                    return $this->get($sql);
				break;

                default:
                    if($bAll==false){
                        return $this->get($sql);
                    }else{
                        $this->set($sql);
                        $i=0;
                        while($r=$this->get()){
                            $vdata[$i++]=$r;
                        }
                        return $vdata;
                    }
            }
        }


		function filter($tb_nm, $data)
		{
            if($this->tb_nm == $tb_nm){
                $fld=$this->fld;
            }else{  
                $fld=$this->getFlds($tb_nm);
            }
			foreach($fld as $k=>$v) 
					if(isset($data[$k])) $retData[$k]=$data[$k];
			return $retData;	
		}

        function count($tb_nm, $pk=''){
            // $pk[$pkey]=$pkeyvalue
            // $vdata[$fldkey]= $fldvalue;

				
            if(is_array($pk)== true){
				$pk=$this->filter($tb_nm, $pk);
                foreach($pk as $idx => $val) $pk_stmt .=  ( $pk_stmt ? " AND " : "") .  "$idx= '" . trim($val) ."'";
                    $total =$this->get("SELECT count(*) FROM $tb_nm WHERE $pk_stmt ");

            }else{
                $total =$this->get("SELECT count(*) FROM $tb_nm ");
            }
            return $total;
        }

        function delete($tb_nm, $pk=''){
            // $pk[$pkey]=$pkeyvalue
            // $vdata[$fldkey]= $fldvalue;

            if($this->tb_nm == $tb_nm){
                $fld=$this->fld;
            }else{  
                $fld=$this->getFlds($tb_nm);
            }

            if(is_array($pk)== true){
                foreach($pk as $idx => $val) $pk_stmt .=  ( $pk_stmt ? " AND " : "") .  "$idx= '" . trim($val) ."'";
                    $total =$this->set("DELETE FROM $tb_nm WHERE $pk_stmt ");
            }
        }

        function getNext($tb_nm, $fn, $pk=''){
            // $pk[$pkey]=$pkeyvalue
            // $vdata[$fldkey]= $fldvalue;

            if($this->tb_nm == $tb_nm){
                $fld=$this->fld;
            }else{  
                $fld=$this->getFlds($tb_nm);
            }

            if(is_array($pk)== true){
                foreach($pk as $idx => $val) $pk_stmt .=  ( $pk_stmt ? " AND " : "") .  "$idx= '" . trim($val) ."'";
                    $next =$this->get("SELECT MAX($fn)+1 FROM $tb_nm WHERE $pk_stmt ");

            }else{
                $next =$this->get("SELECT MAX($fn)+1 FROM $tb_nm ");
            }
            return $next?$next:1;
        }

        function commit($sql) {
            global $on_debug;

            $this->set($sql);
        }
    
        function get($sql='') {

            if($sql !=''){
                if( $this->set($sql)  == false ) return true;
            }

            switch($this->n_field ){

                case 1:
                    
                    $this->n_row = $this->n_row + 1;
                    if($this->n_row == $this->n_rows) return false;
                    return mysql_result($this->result, $this->n_row,0);

                break;

                case 0:
                    return false;
                break;

                default:
                    $this->n_row = $this->n_row + 1;
                    if( $this->n_row == $this->n_rows){
                            $this->n_row = -1;
                            return false;
                    }else{

                         return  mysql_fetch_assoc($this->result) ;
                    }
                break;
            }
        }
       


} 


/////////////////////////////////////////////////////////////////////////////////////////////
//
//   Class  CListPage
//
/////////////////////////////////////////////////////////////////////////////////////////////

class CListPage extends Base {
    var $sql_list;
    var $sql_total;
    var $pageIdx=1;
    var $per_list=10;
    var $per_pageIdx=10;
    var $max_pageIdx=1;
    var $anysql;
    var $no;
    var $ind=0;     
    var $cond=array(); 
    var $tables=" TB_CUST ";
    var $bShowAll=false;
    var $bEnd=false;
    var $total_data;

    function CListPage( $db_info, $sql_list='', $sql_total='',$pageIdx='', $per_list =10, $bShowAll=false){
        $this->anysql= parent::newClass("AnySql", $db_info);
//        $this->anysql= $anysql;
        decho(__FILE__ .":". __LINE__);
        if($sql_list !='' && $sql_total !=''){ 
        $this->sql_list=$sql_list;
        $this->sql_total=$sql_total;

        if( ! $pageIdx ) $this->pageIdx=1;
        else $this->pageIdx=$pageIdx;
        $this->bShowAll=$bShowAll;
        if($per_list ) $this->per_list=$per_list;
        $this->setList($sql_total, $sql_list);
        }    
    }

/*
    function connect($dbnm, $host, $dbid, $dbpw){
        $this->anysql->connect($dbnm, $host, $dbid, $dbpw);
    }
*/
    function setPageIdx($pageIdx){
        $this->pageIdx=$pageIdx?$pageIdx:1    ;
    }

    function setStmt($fn_stmt, $tb_stmt, $co_stmt, $od_stmt, $pageIdx){
        if($tb_stmt =='') return false;
        else $tb_stmt =" FROM $tb_stmt ";

        if($fn_stmt =='') $fn_stmt =" * ";

        if($co_stmt) $co_stmt =" WHERE $co_stmt ";
        if($od_stmt) $od_stmt ="  ORDER BY $od_stmt ";

       $sql_list=" SELECT $fn_stmt $tb_stmt $co_stmt $od_stmt ";
       $sql_total = " SELECT count(*) $tb_stmt $co_stmt ";
       if(!$pageIdx) $this->pageIdx=1;
       else $this->pageIdx=$pageIdx;

       $this->setList($sql_total, $sql_list);

    }


        function setList($sql_total, $sql_list, $sql_total_top='', $sql_list_top=''){
         global $on_debug;
         $per_list=$this->per_list;
         $sql_total_top=$sql_total_top?$sql_total_top:$this->sql_total_top;
          $sql_list_top=$sql_list_top?$sql_listl_top:$this->sql_list_top;

          if($this->bShowAll == false){
                        if($sql_list_top){
                            $total_data_top=$this->anysql->get($sql_total_top)  ;                
                            $this->total_data_top=$total_data_top;
                        }                    
                         $total_data=$this->anysql->get($sql_total) + ($total_data_top?$total_data_top:0) ;

                        $pageIdx =$this->pageIdx;

                        if($per_list != 0) $max_pageIdx = ceil($total_data /$per_list);
                        else $max_pageIdx=0;

                        if($max_pageIdx ==0) $max_pageIdx=1;

                        $this->max_pageIdx=$max_pageIdx;
                        $pageIdx=$pageIdx > $max_pageIdx ? $max_pageIdx: $pageIdx;

                        $per_pageIdx=$this->per_pageIdx;
                        $start_pageIdx= (ceil($pageIdx/$per_pageIdx) -1) * $per_pageIdx + 1;

                        if($max_pageIdx >= $start_pageIdx + $per_pageIdx) $end_pageIdx =($start_pageIdx-1)+$per_pageIdx ;
                        else $end_pageIdx=$max_pageIdx ;

                        if($pageIdx == $end_pageIdx) $this->bEnd=true;
                        else $this->bEnd=false;


                        $start_list=($pageIdx-1)* $per_list;



                        if($total_data_top > 0 ){
                            if($start_list  < $total_data_top){

                                $start_list_top=($pageIdx-1)* $per_list;
    
                                if($total_data_top  < $start_list + $per_list){
                                    $per_limit_top=$total_data_top - $start_list_top;
                                    $start_list=0;
                                    $per_limit=$per_list - $per_limit_top  ;
                                }else{
                                    $per_limit_top=$per_list;
                                    $per_limit=0;
                                    $start_list=0;
                                }
                                $sql_pageIdx_top=" limit $start_list_top, $per_limit_top";    

                            }else{
                                $start_list_top=0;
                                $per_limit_top=0;
                                $start_list=$start_list - $total_data_top;
                            }
                        }

        
                        if($total_data  < $total_data_top + $start_list + $per_list) $per_limit=$total_data - $start_list - $total_data_top;
                        else  $per_limit=$per_list-$per_limit_top;

                        $this->no= $total_data - ($pageIdx -1) * $per_list +1;
                        

                        if($per_limit > 0){
                            $sql_pageIdx=" limit $start_list, $per_limit";
                        }else{
                            $sql_pageIdx=" limit $start_list, 1";
                        }



         }else{
             $sql_pageIdx="";
              $this->no=$total_data +1; 
         }

        decho ("start_list $start_list" );
        $this->start_list=$start_list;
        $this->start_list_top=$start_list_top;
        $this->per_limit_top=$per_limit_top;
        $this->per_limit=$per_limit;
        $this->total_data=$total_data;
        $this->total_data_top=$total_data_top?$total_data_top:0;
        $this->sql_pageIdx_top=$sql_pageIdx_top;
        $this->sql_pageIdx=$sql_pageIdx;
        $this->sql_list=$sql_list;
        $this->sql_list_top=$sql_list_top;

        if($per_limit  < 0 || $per_limit_top < 0 ) return false;    
        return true;
    }

    function getList(){
        $this->no--;
        if($this->total_data_top < 1){
            if( $this->no  == $this->total_data - $this->start_list ){
                $this->anysql->set($this->sql_list . $this->sql_pageIdx);
            }
        }else{
                if($this->per_limit_top > 0){
                    if( $this->no == $this->total_data  - $this->start_list_top){
                    $this->anysql->set($this->sql_list_top . $this->sql_pageIdx_top);
                    }else if( $this->no == $this->total_data  - $this->start_list_top - $this->per_limit_top){
                            $this->anysql->set($this->sql_list . $this->sql_pageIdx);
                    }
                }else {
                    if( $this->no  == $this->total_data - $this->total_data_top - $this->start_list ){
                                                    $this->anysql->set($this->sql_list . $this->sql_pageIdx);
                    }
                }
        }
        return $this->anysql->get();
    }
  function getRownum(){
      return $this->no;
  }
    function pageIdxTable(){

        $pageIdx=$this->pageIdx;
        $per_pageIdx=$this->per_pageIdx;
        $per_list=$this->per_list;
        $max_pageIdx=$this->max_pageIdx;


        $start_pageIdx= (ceil($pageIdx/$per_pageIdx) -1) * $per_pageIdx + 1;
        if($max_pageIdx >= $start_pageIdx + $per_pageIdx) $end_pageIdx =($start_pageIdx-1)+$per_pageIdx ;
        else $end_pageIdx=$max_pageIdx ;

        $table_index="";

        for($pindex=$start_pageIdx;$pindex <= $end_pageIdx; $pindex++)
        {
            if($pageIdx != $pindex)
                $table_index .="<a href='javascript:gopageIdx($pindex)'>$pindex</a>";
            else
                $table_index .="<span>$pindex</span>";
        }

        if($start_pageIdx > 1)
			$prev_10pageIdx="<a href='javascript:gopageIdx($start_pageIdx - 1)'><strong>이전 10개</strong></a>";
        else
			$prev_10pageIdx="";

        if($end_pageIdx != $max_pageIdx)
			$next_10pageIdx="<a href='javascript:gopageIdx($end_pageIdx + 1)'><strong>다음 10개</strong></a>";
        else
			$next_10pageIdx="";

        if( $pageIdx > 1)
			$prev_pageIdx="<a href='javascript:gopageIdx($pageIdx - 1)' title='이전 페이지로'>  ◀  </a>";
        else
			$prev_pageIdx="<span><strong>◀</strong></span>";


        if($pageIdx != $max_pageIdx)
			$next_pageIdx="<a href='javascript:gopageIdx($pageIdx + 1)' title='다음 페이지로'>  ▶   </a>";
        else{
			$next_pageIdx="<span><strong>▶</strong></span>";
	        $this->bEnd=true;
        }

		if($end_pageIdx > 10)
        	$first_page="<a href='javascript:gopageIdx(1)' title='맨처음 페이지로 '>1</a><strong>..</strong>";
		else
			$first_page="";

   		if( ($max_pageIdx - $start_pageIdx) > 10)
        	$last_page="<strong>..</strong><a href='javascript:gopageIdx($max_pageIdx)' title='맨 나중에 페이지로' >$max_pageIdx</a>";
		else
			$last_page="";    

$html=<<<_HTML
<script>
function gopageIdx(pageIdx){
		if(document.forms[1])	//이부분은 페이지중에서 왼쪽메뉴  실행부분 있는 페이지는 form 태그가 하나더 있기때문에 넣어줬음(예. inc/waf_left.htm)
		    var f=document.forms[1];
		else
		    var f=document.forms[0];

        f.action='$PHP_SELF';
        f.pageIdx.value=pageIdx;
        f.submit();
}
</script>
<input type=hidden name=pageIdx value='$pageIdx'>
<div class='pagination'><p>
	$prev_10pageIdx
	$first_page
	$table_index
	$last_page
	$next_10pageIdx
</p></div>
_HTML;
            return $html;
    }



}

 
// end of classCPAGELIST;






/////////////////////////////////////////////////////////////////////////////////////////////
//
//   Class  CSession
//
/////////////////////////////////////////////////////////////////////////////////////////////


class CSession extends Base
{

var $anysql;
var $sessionLifetime;
var $user_id;
var $remote_ip;
var $mbrship=array();
var $user_acl; // absolute privilges ;

    function CSession($db_info)
    {
		global $PHP_SELF;
		global $_SESSION;

	    $this->anysql= parent::newClass("AnySql", $db_info);
//	    $this->anysql= $anysql;
	
	        // get session lifetime
	//        $this->sessionLifetime = get_cfg_var("session.gc_maxlifetime");
	        $this->sessionLifetime = 36000;
//	        $this->sessionLifetime = 10;
	        // register the new handler
	
	        session_set_save_handler(
	            array(&$this, 'open'),
	            array(&$this, 'close'),
	            array(&$this, 'read'),
	            array(&$this, 'write'),
	            array(&$this, 'destroy'),
	            array(&$this, 'gc')
	        );
	       // register_shutdown_function('session_write_close');
	
	        // start the session
	    session_set_cookie_params( 0, "/");
	
		if(! session_id() )
			session_start();
//		echo session_id();	
//		echo basename($PHP_SELF);
		$menu_no = ereg('.htm$', $PHP_SELF ) ? basename($PHP_SELF, ".htm") : basename($PHP_SELF , '.php')  ;
		switch( $menu_no ){

			//로그인 않하고 사용하는 페이지는 여기에 등록해준다.
			case 'L4':		// 첫메인 페이지
			case 'logon':		// 첫메인 페이지에서 받은 아이디, 패스워드 처리하는 페이지
			case 'user_join':	// 회원가입 페이지
			case 'check_id':	// 아이디 체크 페이지
			case 'user_post':	// 회원가입 페이지, 아이디와비밀번호 찾기 페이지는 user_post.php 에서 처리해야되므로 여기넣어줘야 로그인않하고 처리할수 있다.
			case 'mon_ip_list_admin_view':	// 스마일서브 관리자 페이지에서 링크걸어서 보기위함
			break;
		
//			case 'logout.php':		// 로그아웃 처리하는 페이지
//			    $this->logoff();
//			break;

			// 위 페이지 외에는 모두 로그인 여부 체크해야함
			default:

				if(!$_SESSION[user_id])
				{
					jsAlert("로그인 후 사용바랍니다." , '../L4.htm');
				}
				else
				{
					$this->user_id = $_SESSION['user_id'];
					$this->remote_ip = $_SESSION['remote_ip'];
					$this->user_acl = $_SESSION['user_acl'];
				}

			break;
		}

    }


	function duplicatedIdCheck($user_id)
	{
		$sdata=$this->anysql->getData("TB_SESSION");

        if(!$sdata)
            return false;

        if(is_array($sdata[0]))
            $data= $sdata;
        else
            $data[0]=$sdata; //2차원 배열이 아닐경우 2차원 배열로 만듬

        foreach($data as $list)
        {
            foreach($list as $fn => $val)
            {
                if($fn == "session_data" & $val != NULL)
                {
                    $arr=explode("\"", $val);   //예) user_id|s:4:"admin";user_acl|s:1:"0";remote_ip|s:15:"218.236.115.201";
                    if( $arr[1] == $user_id )   // 접속한 아이디중에 중복되는 아이디가있는지 검사
                        return $arr[5]; //중복된 아이디가 있으면 로그인 중단
                }

            }
        }

		return false;
	}

    function validUser($user_id, $user_pw){

		global $SALT;
		$user_pw = md5($SALT.$user_pw.$SALT);
	    return $this->anysql->get("SELECT count(*) as flag from TB_USER where user_id='$user_id' and user_pw='$user_pw'");

    }

    function logon($user_id, $user_pw,$acl=0)
    {
//		$ret=$this->duplicatedIdCheck($user_id);
//		if($ret)
//			return $ret;
	    if($this->validUser($user_id, $user_pw) )
		{
		    $_SESSION['user_id']	= $user_id ;
		    $_SESSION['user_acl']	= $this->getAcl($user_id, $user_pw);
		    $_SESSION['remote_ip']	= $_SERVER[REMOTE_ADDR] ;
			return true;
    	}
    	return false;
    }

    function getAcl($user_id, $user_pw){
        $data=$this->anysql->getData("TB_USER", array(user_id=>$user_id));
        return $data[user_acl];
    }


	function logoff()
	{
		global $_SESSION;
		$_SESSION=array();
		$this->regenerateId();
	}

    function regenerateId()
    {

        $oldSessionID = session_id();
        session_regenerate_Id();

        $this->destroy($oldSessionID);

    }

    function getUonline()
    {

        return $this->anysql->get(" SELECT COUNT(session_id) as count FROM TB_SESSION ");

    }

    function open($save_path, $session_name)
    {

        return true;

    }

    function close()
    {
//		echo "sessionLifetime : ".$this->sessionLifetime. "<br>";
//		$this->gc($this->sessionLifetime); 
        return true;
    }

    function read($session_id)
    {
        $sql = " SELECT session_data FROM TB_SESSION
            WHERE session_id = '".$session_id."' AND session_expire > '".time()."' ";
          return $this->anysql->get($sql);
    }

    function write($session_id, $session_data)
    {
	    if($session_data){
	    $data[session_data] = $session_data;
	    }
	    $data[session_expire] = time() + $this->sessionLifetime;
	    $pk[session_id]=$session_id;
	
	
	    return    $this->anysql->setData("TB_SESSION", $pk, $data);

    }

    function destroy($session_id)
    {

	    $pk[session_id] = $session_id;
	    return    $this->anysql->delete("TB_SESSION", $pk);
    }
	
	function gc($maxlifetime)
	{
//		echo "garbage collect 함수<br>";
	    $this->anysql->set(" DELETE FROM TB_SESSION WHERE session_expire < '".time() ."'");
	//		echo  "<script>location.href='./user_login.htm'</script>";

    }



	function allowed( $method , $owner_id="" )
	{
		global $on_debug;
		if($owner_id)
		{
			if($owner_id == $_SESSION[user_id])	//객체의 소유가 자신이라면 권한획득
			{
				if($on_debug == true)
					decho( "소유자 ID랑 같음" );
				
				return true;
			}
		}

		$user_acl=$this->anysql->get("SELECT user_acl from TB_USER where user_id='$_SESSION[user_id]'");

		//현재 접속한 유저의 권한과 객체에서 요구하는 권한을 비트연산해서 객체에서 요구하는 권한이 나오면 권한 획득
		if( ( $method[acl][uid] & $user_acl ) == $method[acl][uid] )	
		{
			if($on_debug == true)
				decho( "유저 ACL 성공" );
			return true;
		}

		//위에 유저의 권한이 실패하면 유저가 속해있는 권한을 체크해본다
		$gdata=$this->anysql->getData("TB_GROUP_MEMBER", array(user_id=>$_SESSION[user_id])); //유저가 속해 있는 그룹 리스트를 가져온다.
		if(!$gdata)  //유저가 어떤그룹에도 속하지 않았을경우는 그냥 false 반환
			return false;

		//유저가 속해 있는 그룹은 하나일수도 있고 여러개일수도 있다.
        if(is_array($gdata[0]))
            $data= $gdata;
        else
            $data[0]=$gdata; //2차원 배열이 아닐경우 2차원 배열로 만듬

		foreach($data as $group_list)
		{
			foreach($group_list as $fn => $val)
			{
				if($fn == "group_id")
				{
					$group_acl=$this->anysql->get("SELECT group_acl from TB_GROUP where group_id='$val'");
					if( ( $method[acl][gid] & $group_acl ) == $method[acl][gid] )
					{
						if($on_debug == true)
							decho( "$val 그룹 ACL 성공" );
						return true;
					}
				}
			}
		}

		if($on_debug == true)
			decho( "모든 권한없음" );

		return false;
    }


    function methodAcl($method)
    {

        switch($method){	// 체크할 권한을 선택


            case 'add':	// 추가할수 있는 권한
                $this->mbrship = array(

                                acl => array( uid=>(ACT_ADD), gid=>(ACT_ADD) )

                             );
                return $this->mbrship;
            break;

            case 'delete':	// 삭제할수 있는 권한
                $this->mbrship = array(

                                acl => array( uid=>ACT_DEL, gid=>ACT_DEL )

                             );
                return $this->mbrship;
            break;

            case 'edit':	// 수정할수 있는 권한
                $this->mbrship = array(

                                acl => array( uid=>ACT_EDIT, gid=>ACT_EDIT )

                             );
                return $this->mbrship;
            break;

            case 'start':	// 시작할수 있는권한(방화벽 시작..)
                $this->mbrship = array(

                                acl => array( uid=>EXEC_START, gid=>EXEC_START )

                             );
                return $this->mbrship;
            break;

            case 'stop':	// 스탑할수 있는 권한
                $this->mbrship = array(

                                acl => array( uid=>EXEC_STOP, gid=>EXEC_STOP )

                             );
                return $this->mbrship;
            break;

            case 'mgmt':	//관리자 권한
                $this->mbrship = array(

                                acl => array( uid=>(ACT_MGMT), gid=>(ACT_MGMT) )

                             );
                return $this->mbrship;
            break;

            case 'print_list':	// 모든사용자의 출력된 리스트를 볼수 있는권한
                $this->mbrship = array(

                                acl => array( uid=>ACT_LIST, gid=>ACT_LIST )

                             );
                return $this->mbrship;
            break;

            case 'page_access_check':	//페이지 접근권한
                $this->mbrship = array(

                                acl => array( uid=>(ACT_AUDIT), gid=>(ACT_AUDIT) )

                             );
                return $this->mbrship;
            break;

            case 'lock_unlock':
                $this->mbrship = array(

                                acl => array( uid=>(ACT_LOCK | ACT_UNLOCK), gid=>(ACT_LOCK | ACT_UNLOCK) )

                             );
                return $this->mbrship;
            break;

/*
            case 'add':
                $this->mbrship = array(

                                owner => array( uid=>"admin", gid=>"admin" ),

                                acl => array( uid=>ACT_EDIT, gid=>ACT_READ )

                             );
                return $this->mbrship;
            break;
*/
        }
    }


//moduleAcl start - 각 페이지에 대한 권한 설정.
	function moduleAcl($page)
	{
		switch($page)
		{
			case 'user_list':
			case 'group_list':
			case 'group_mb_list':

				$this->mbrship = array(
									acl => array(  uid=>(ACT_AUDIT), gid=>(ACT_AUDIT) )
								);
				return $this->mbrship;

			break;


			case 'fw':
			case 'fw_policy':
			case 'fw_policy_ip':
			case 'fw_policy_ip_port':
			case 'fw_rule_get':
			case 'fw_scan':
			case 'fw_init':
			case 'fw_other_init':
			case 'p_fw_rule_form':
			case 'p_fw_form':
			case 'p_fw_rule_save_form':

				$this->mbrship = array(
									acl => array(  uid=>(ACT_AUDIT), gid=>(ACT_AUDIT) )
								);
				return $this->mbrship;

			break;

			case 'waf':
			case 'waf_admin_rule':
			case 'waf_rule':
			case 'waf_rule_mgmt':
			case 'waf_exception':
			case 'waf_set':
			case 'waf_report':
			case 'waf_statistics':
			case 'p_waf_report_form':

				$this->mbrship = array(
									acl => array(  uid=>(ACT_AUDIT), gid=>(ACT_AUDIT) )
								);
				return $this->mbrship;

			break;

			case 'mon':
			case 'mon_mgmt_list':
			case 'mon_server_set':
			case 'mon_log_list':
			case 'mon_top_list':
			case 'mon_server_rrd':
			case 'p_mon_mgmt_reg':
			case 'p_mon_list':

				$this->mbrship = array(
									acl => array(  uid=>(ACT_AUDIT), gid=>(ACT_AUDIT) )
								);
				return $this->mbrship;

			break;



		}

	}
//moduleAcl end


}
// class CSession end

function dec2mac($mac) { 
   $mac=preg_split("([.])", $mac, 6); 
   $hexmac=""; 
   foreach ($mac as $part) 
   { 
   $part=dechex($part); 
   strlen($part)<2 ? $hexmac.="0$part" : $hexmac.=$part; 
   } 
   return $hexmac; 
} 




?>
