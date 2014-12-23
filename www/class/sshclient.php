<?
/////////////////////////////////////////////////////////////////////////////////////////////
//
//   Class  sshclient
//
/////////////////////////////////////////////////////////////////////////////////////////////



class sshclient {
	var $user='root';
//	var $host='115.68.24.163';
	var $host='127.0.0.1';
	var $port=5135;
	var $target;
	//var $base='/usr/local/L4-service/key';
	var $base='/home/www/key';
	var $IdentityFile='id_rsa';
	//var $logfile = "/usr/local/L4-service/logs/sshcmd_logs/sshcmd.log";
	var $logfile = "/var/log/httpd/sshcmd.log";
	var $options;
	var $stdout;
	var $stderr;
	var $exit;
	var $KnownHostsFile = "known_hosts";
	var $ssh_config_authkey;
	var $ssh_config_default;
	var $query;
	var $job;

	function sshclient($host='127.0.0.1', $port=5135, $identity='id_rsa', $user='root')
	{
		global $ssh_debug_on;
		
		$this->options=array();
		if(is_array($host))
		{
			$ssh_conf=$host;
			unset($host);

			$host=$ssh_conf[host];
			$port=$ssh_conf[sshPort] ? $ssh_conf[sshPort] : $ssh_conf[port] ;
			$identity=$ssh_conf[identity];
			$user=$ssh_conf[user];

		}
		
		$this->host = escapeshellarg($host);
		$this->port = escapeshellarg($port);
		$this->user = escapeshellarg($user);
		$this->target = $this->user .'@'. $this->host;
		$this->IdentityFile = escapeshellarg($identity);
		if($ssh_debug_on==true) {
			$this->ssh_config  = "-o ConnectTimeOut=10  -vv -o Port=$this->port -o IdentityFile=$this->base/$this->IdentityFile ";
			//$this->ssh_config  = " -vv -o Port=$this->port -o IdentityFile=$this->base/$this->IdentityFile -o UserKnownHostsFile=$this->base/$this->KnownHostsFile ";
			// 아래쪽에 -o UserKnownHostsFile=/dev/null 옵션은 known_hosts파일을 사용않하는것이기때문에 -o UserKnownHostsFile=$this->base/$this->KnownHostsFile 옵션을 빼야한다
		} else {
			$this->ssh_config  = "-o ConnectTimeOut=10 -o Port=$this->port -o IdentityFile=$this->base/$this->IdentityFile ";
			//$this->ssh_config  = " -o Port=$this->port -o IdentityFile=$this->base/$this->IdentityFile -o UserKnownHostsFile=$this->base/$this->KnownHostsFile ";
		}

		$this->ssh_config_authkey  = $this->ssh_config .  " -o GSSAPIAuthentication=no -o PasswordAuthentication=no -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR";
		//UserKnownHostsFile=/dev/null 이렇게하면 경고메시지가 뜬다. 따라서 경고메세지를 출력하지 않고 에러일경우에만 출력하려면 로그레벨을 에러로 해주면된다. LogLevel=ERROR
	}
	function log( )
	{
	$contents= implode("\n", array("cmd: ", $this->query , "stdout", $this->stdout, "stderr", $this->stderr) );
	if($this->job) $this->job->log("ssh 실행이력", $contents);
	}

	function is_online()
	{
		return ereg( "OK",  $this->check_connectivity() ) ? true : false ;
	}
	function check_connectivity()
	{
		$cmd = "echo OK";
		$this->exec($cmd);
		$ret = trim($this->stdout);

		return $ret;
	}

	function setOptions($options)
	{
		foreach($options as $key=>$val)
			$this->options[$key]=$val;

	}

	function getOptions()
	{
		$opts=array();
		foreach($this->options as $k=>$v)
			$opts[] =" -o $k=$v ";

		return implode( $opts) ;
	}

	function exec($cmd, $options = '')
	{
		global $ssh_debug_on;

		if(is_array($options)){
			$this->setOptions($options);
			$options_txt = $this->getOptions();
		}else{
			$options_txt = $this->getOptions() . $options ;
		}

		if($ssh_debug_on==true) {

			echo $this->cmd=$cmd;
			echo "\n";

		}else{

			$this->cmd=$cmd;
			//$this->query="ssh $this->ssh_config_authkey $options_txt  $this->target \"$cmd\" ";		
			$this->query="ssh $this->ssh_config_authkey $options_txt  $this->target $cmd ";		
			$this->exit=$this->raw_exec($this->query, $this->stdout, $this->stderr);
		}
	}

    function exec_vnc($cmd, $cust_id, $suffic, $REMOTEIP, $out_msg, $err_msg, $options = '')
    {
        global $ssh_debug_on;

        if(is_array($options)){
            $this->setOptions($options);
            $options_txt = $this->getOptions();
        }else{
            $options_txt = $this->getOptions() . $options ;
        }

        if($ssh_debug_on==true) {

            echo $this->cmd=$cmd;
            echo "\n";

        }else{

            $this->cmd=$cmd;
            //$this->query="ssh $this->ssh_config_authkey $options_txt  $this->target \"$cmd\" ";
            //$this->query="ssh $this->ssh_config_authkey $options_txt  $this->target $cmd ";
            //$this->exit=$this->raw_exec($this->query, $this->stdout, $this->stderr);

            // 로그 생성 추가사항
			$out_msg = str_replace("", " ", $out_msg);
            $out_msg = str_replace("\n", "", $out_msg);
            $out_msg = str_replace("(", "\(", $out_msg);
            $out_msg = str_replace(")", "\)", $out_msg);
            $out_msg = str_replace("'", "", $out_msg);
            $out_msg = str_replace(";", "", $out_msg);

			$err_msg = str_replace("", " ", $err_msg);
            $err_msg = str_replace("\n", "", $err_msg);
            $err_msg = str_replace("(", "\(", $err_msg);
            $err_msg = str_replace(")", "\)", $err_msg);
            $err_msg = str_replace("'", "", $err_msg);
            $err_msg = str_replace(";", "", $err_msg);

            $curTime    = time();
            $curDate    = date("Y/m/d H:i:s", $curTime);
            $append     = $curDate . " " . "[" . $cust_id . ", " . $suffic . "]" . " REMOTEIP:" . $REMOTEIP;
            $cmd        = "'".$cmd."'";
            $cmdTmp     = str_replace("|", "\|", $cmd);
            $cmd2       = $append." ".$cmdTmp." {-SERVERMSG- stdout:".$out_msg." , stderr:".$err_msg." }";
            $this->query="ssh $this->ssh_config_authkey $options_txt  $this->target \"echo $cmd2 >> /var/log/qemu-do/qemu-do.log\" 2>&1  ";
            $this->exit=$this->raw_exec($this->query, $this->stdout, $this->stderr);


        }
    }


	function put_txtdata($txtdata, $dst){
		$src = tempnam("../tmp", "txtdata");
		$fp=@fopen($src, "a+");
		
		if ($fp===false) {
			// error reading or opening file
			return true;
		}
		
		
		fwrite($fp,$txtdata);
		fclose($fp);
		$this->put($src,$dst);
	//	unlink($src);

	}

	function put($src, $dst)
	{
		if(is_array($options)){
			$this->setOptions($options);
			$options_txt = $this->getOptions();
		}else{
			$options_txt = $this->getOptions() . $options ;
		}

		$this->query = "scp $this->ssh_config_authkey $options_txt $src $this->target:$dst   ";
		$this->exit=$this->raw_exec($this->query, $this->stdout, $this->stderr);
	}

	function get($src, $dst)
	{
		if(is_array($options)){
			$this->setOptions($options);
			$options_txt = $this->getOptions();
		}else{
			$options_txt = $this->getOptions() . $options ;
		}
		$this->query = "scp $this->ssh_config_authkey $options_txt $this->target:$src  $dst ";
		$this->exit=$this->raw_exec($this->query, $this->stdout, $this->stderr);
	}


	function raw_exec($cmd, &$stdout, &$stderr)
	{
		global $on_debug;
		$outfile = tempnam("../tmp", "cmd");
		$errfile = tempnam("../tmp", "cmd");
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("file", $outfile, "w"),
			2 => array("file", $errfile, "w")
		);

		if($on_debug) echo "sshclient:$cmd\n" ; 

		$proc = proc_open($cmd, $descriptorspec, $pipes);

		if (!is_resource($proc)) return 255;
		
		fclose($pipes[0]);    //Don't really want to give any input
		
		$exit = proc_close($proc);

		$this->stdout = implode("\n", file($outfile) );
		$this->stderr = implode("\n", file($errfile) );
		
		unlink($outfile);
		unlink($errfile);
		
		$logsmsg= "COMMAND = $cmd \n\n";
		$logsmsg.= "STDOUT = $this->stdout \n\n";
		$logsmsg.= "STDERR = $this->stderr \n";

		if($on_debug)	echo $logmsg;
	
		$this->set_log($logsmsg);
		return $exit;
	}


	function set_log($logmsg)
	{
		$logfile = $this->logfile . "_" . date("Y-m-d");
		$fp=@fopen($logfile, "a+");
		chown($logfile, "nobody");
		chgrp($logfile, "nobody");
		
		if ($fp===false) {
			// error reading or opening file
			return true;
		}
		
		$log_data="";
		$log_data.="==========";
		$log_data.= date("Y-m-d H:i:s");
		$log_data.="========== \n";
		$log_data.=$logmsg;
		$log_data.="\n\n\n";
		
		fwrite($fp,$log_data);
		fclose($fp);
	}

	function keygen()
	{
		if( ! file_exists("$this->base/$this->IdentityFile") ){
		$cmds[] = "ssh-keygen -t rsa -f $this->base/$this->IdentityFile "; 
		$cmds[] = "chmod nobody:root $this->base/* ";	
		foreach($cmds as $cmd ) $this->raw_exec($cmd);


		return true;


		}else{
			echo "$this->base : auth key exists!!"; 
			return false;
		}
	
	}


	function publishAuthKey($saveas, $password)
	{
		switch($this->user)
		{
		case 'root':
			$saveas = "/root/.ssh/.ssh/authorized_keys";
		break;
		default:
			$saveas = "/home/$this->user/.ssh/.ssh/authorized_keys";
		break;

		}

		$cmd="sshpass -p '$password' scp $this->base/$this->IdentityFile.pub  $this->target:$saveas ";
		$this->raw_exec($cmd);

	}

	function isdir($file){
		
		$cmd = "if [ -d $file ] ; then echo 1; else 0; fi" ;
		
		$this->exec($cmd);
		
		switch($this->stdout){
			case 0:
				return false;
				break;
			case 1:
				return true;
				break;
			}
	}
	
	function mount($opt){
		$this->exec("mount $opt");
	}
	function umount($opt){
		$this->exec("umount $opt");
	}	
			
	
}

// class sshclient end


?>
