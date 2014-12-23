<?
/////////////////////////////////////////////////////////////////////////////////////////////
//
//   Class  localhost
//
/////////////////////////////////////////////////////////////////////////////////////////////



class localhost {

	var $target;
	var $logfile = "/usr/local/L4-service/logs/sshcmd_logs/sshcmd.log";
	var $options;
	var $stdout;
	var $stderr;
	var $exit;
	var $query;
	var $job; 

	function localhost()
	{
		$this->options=array();
	}
	function log( )
	{
	$contents= implode("\n", array("cmd: ", $this->query , "stdout", $this->stdout, "stderr", $this->stderr) );
	if($this->job) $this->job->log("ssh 실행이력", $contents);
	}

	function exec($cmd )
	{
			$this->query=$cmd;
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

		if($on_debug) echo "localhost:$cmd\n" ; 

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
		
        if ($exit)
        {
            return false;
        }
        return true;
		
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

    function  icmpAlive($host){

            $cmd = '(ping  -c 1 -w 1 ' .$host. ' | grep icmp_seq=1 |grep -c ms$ )';
            $this->exec($cmd);
            switch($this->stdout){
            case 1:
                return true;
            break;
            case 0:
                return false;
            break;
            default:
                echo "Error: $cmd ";

            }
            return $this->stdout ;

     }

     function tcpPortAlive($host, $port){

            $cmd = " nmap  -p $port  $host  |grep $port/tcp | awk '{print \$2}' |grep -c open ";
            $this->exec($cmd);
            switch($this->stdout){
            case 1:
                return true;
            break;
            case 0:
                return false;
            break;
            default:
                echo "Error: $cmd ";

            }
            return $this->stdout ;

     }

	 function isdir($file){
	 	return isdir($file);
	 }
	 
	 function mount($opt){
		$this->exec("mount $opt");
	 }
	 function umount($opt){
		$this->exec("umount $opt");
	 }	
	 
	 
}

// class localhost end


?>
