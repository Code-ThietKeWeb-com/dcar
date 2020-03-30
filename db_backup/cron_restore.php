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
   * function do_restore 
   *  
   **/
	function do_restore($filename)
	{
		global $conf;
		
			if (!file_exists($filename)) die("$filename File not found!");
			if (BIGDATA)
			{
				$fp = @gzopen($filename, 'rb');
			}
			elseif (substr($filename, -4)=='.sql')
			{
				$sql_query = @fread(@fopen($filename, 'rb'), @filesize($filename));
			}
			else
			{
				$sql_query = @gzread(@gzopen($filename, 'rb'), MAXGZIP);
			}
			mysql_select_db($conf['dbname']);
			//echo "Selected database: ".$conf['dbname']."<br>";
	
			if (BIGDATA)
			{
				//echo "BIGDATA mode. It will be slower, but safer.<br>";
				//echo "Running (each dot is a block of 100 queries): ";
				$i=0;
				while (!gzeof($fp))
				{
					$tmp = trim(gzgets($fp));
					$i++;
					if (substr($tmp,0,1)!='#' && !empty($tmp))
						if (!mysql_query($tmp))
						{
							echo "<div style='font:10px Tahoma;color:red'>".htmlentities($tmp)."</div>";
							echo mysql_error();
							break;
						}
					if ($i%100==0) {echo '. ';flush();}
				}
			}
			else
			{
				if (PARSESQL)
				{
					$sql_query = remove_remarks($sql_query);
					$sql_query = split_sql_file($sql_query, ';');
				}
				else
					$sql_query = explode("\n", $sql_query);
			
				$n = count($sql_query);$j=0;
				//echo "Running (".ceil($n/100)." blocks of queries): ";
				for ($i=0; $i<$n; $i++)
				{
					$tmp = trim($sql_query[$i]);
					if (substr($tmp,0,1)!='#' && !empty($tmp))
						if (!mysql_query($tmp))
						{
							echo "<div style='font:10px Tahoma;color:red'>".htmlentities($tmp)."</div>";
							echo mysql_error();
							break;
						}
					if ($i%100==0) {echo ++$j%10;flush();}
				}
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
					if(filemtime($files)>$time_start && filemtime($files)<$time_end)
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
	
	if (isset ($_GET['cmd']) ) $cmd = $_GET['cmd'];
		$code = NDK_decode ($cmd);
		$cmd_arr = explode("|",$code);
		foreach($cmd_arr as $value) {
			if (!empty($value)) {
				$k = trim(substr($value,0,strpos($value,":")));
				$v = trim(substr($value,strpos($value,":")+1));
				$input[$k] = $v;
			}
		}
		
	//lay file backup
	 $day = ($input['day']) ? $input['day'] : date("d_m_Y");	 
	 $name_file = ($input['name_file']) ? $input['name_file'] : str_replace("www.","",$_SERVER['HTTP_HOST']);
	 $filename = $name_file."_".$day; 
	/**
   * Connect Host
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
			$file = DIR_BACKUP ."/" . $filename . ".zip";
			$ok = $myFTP->get($file,$foder_desc) ;
			if($ok) {				
				
				//unzip
				$archive = new PclZip($file);
        //extracts the archive, calling filterFileNames prior to extracting any file
        $success = $archive->extract();
        if ($success)
        {
					/**
					 * Restore database
					 *  
					 **/
				  $file_database = '../vnt_upload/database_'.$day.'.sql.gz';	 
				  do_restore($file_database);
					
					if (file_exists($file_database)) @unlink ($file_database)	 ;
					$myFTP->delete($foder_desc);
						
          $err = "OK" ;
        }
									
			}else{
				$err = "Ko lay file duoc" ;
			}
		
		}else{    	
			$err =  "Khong connect duoc FTP";
		}  
		
		echo  $err;
?>