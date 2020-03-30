<?
session_start();
if (!session_is_registered("vnTRUST")) {
//Login Form
function Login_Form($err) {
return<<<EOF
<br><br><br><br><br><br><br><br><br><br>
<form action="" method="post" name="Login">
<table width="350" bgcolor="#B84120" align=center border=0 cellspacing=1 cellpadding=1>
		<tr><td colspan=2 align=center class="ftittle"><b>Login</b></td></tr>
		<tr><td colspan=2 align=center bgcolor="#FFFFFF"><font color="#FF3600"><b>{$err}</b></font></td></tr>
		<tr>
			<td align=left bgcolor="#FFFFFF" width="30%">&nbsp;&nbsp;Username</td>
			<td align=left bgcolor="#FFFFFF" width="70%"><input name="user" type="text" size="30" maxlength="250"></td>
		</tr>
		<tr>
			<td align=left bgcolor="#FFFFFF" width="30%">&nbsp;&nbsp;Password</td>
			<td align=left bgcolor="#FFFFFF" width="70%"><input name="pass" type="password" size="32" maxlength="250"></td>
		</tr>
		<tr><td colspan=2 align=center class="ftittle"><input type="submit" name="Submit" value="Login"></td></tr>
</table>
</form>
EOF;
}
// End form
	$logged = 0;
	if ( (!empty($_POST['user'])) && (!empty($_POST['pass'])) ) {
		$user = $_POST['user'];
		$pass = md5($_POST['pass']);
		if ( ($user=="vntrust") && ($pass=="48ab0f5ed61ba146451db9acf5f3e658") ) {
			$logged=1;
			session_register("vnTRUST");
		} else $err="The Username or Password you entered is not correct";
	}
	if ($logged==0)	$output = Login_Form($err);
} else $logged=1;



if ($logged) {



if (isset($_GET['vnTRUST'])) $vnTRUST=$_GET['vnTRUST']; else $vnTRUST='';
if (isset($_GET['f'])) $f=$_GET['f']; else $f=0;

require_once("../_config.php"); 
require_once("../includes/class_db.php"); 
$DB = new DB;

include ("_backupfuncs.php");
include ("zip.php");
include ("unzip.php");
$NDKBackup=new NDK_Backup;
$output = "";

$default_tables = array(
  'admin',
  'config',
  'setting'
);

// Get list
function Get_List() {
global $conf,$DB,$NDKBackup,$default_tables;
$table_select = "<select name=\"db_tables[]\" size=\"10\" multiple>\n";
  $result = $DB->query("SHOW tables");
  while ($row = $DB->fetch_array($result)) {
    $table_select .= "<option value=\"".$row[0]."\"";
    if (in_array($row[0], $default_tables)) {
      $table_select .= " selected";
    }
    $table_select .= ">".$row[0]."</option>\n";
  }
$table_select .= "</select>\n";

$list ="<br> <table width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" align=\"center\" bgcolor=\"#B84120\" >
  <form action=\"?vnTRUST=backup_table\" method=\"post\">
  <tr height=20>
    <td colspan=\"2\" class=\"ctittle\">Backup list table</td>
  </tr>
  <tr>
    <td width=\"60%\" bgcolor=\"#FFFFFF\" valign=top>Sao l&#432;u d&#7919; li&#7879;u.<br />
          Ch&#7885;n b&#7843;ng d&#7919; li&#7879;u   &#273;&#432;&#7907;c c&#7853;p nh&#7853;t. B&#7855;t bu&#7897;c c&aacute;c b&#7843;ng ph&#7843;i &#273;&#432;&#7907;c ch&#7885;n tr&#432;&#7899;c.</td>
    <td width=\"40%\" bgcolor=\"#FFFFFF\">{$table_select}</td>
  </tr>
  <tr>
    <td colspan=\"2\" align=\"center\" class=\"tittle\"><input name=\"btnBackupTable\" type=\"submit\" value =\"Backup List Table\">&nbsp;<input name=\"reset\" type=\"reset\" value=\"Reset\"></td>
  </tr>
  </form>
</table>"; 
$list .="<br> <table width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#B84120\" align=\"center\">
<form action=\"backup.php?vnTRUST=import_sql\" method=\"post\" enctype=\"multipart/form-data\" name=\"f_import\"> 
  <tr height=20>
    <td colspan=\"2\" class=\"ctittle\">IMPORT DATABASE </td>
  </tr>
  <tr bgcolor=\"#FFFFFF\">
    <td width=\"20%\" ><strong>File (.sql or .zip) :</strong></td>
    <td width=\"80%\"><input type=\"file\" name=\"filesql\" id=\"filesql\" size=\"30\"></td>
  </tr>
  <tr bgcolor=\"#FFFFFF\">
    <td >&nbsp;</td>
    <td><input type=\"submit\" name=\"btnImport\" value=\"Import\"></td>
  </tr>
  </form>
</table>"; 
$list .= "<br><table width=\"80%\" bgcolor=\"#B84120\" align=center border=0 cellspacing=1 cellpadding=1>
		<tr height=20><td colspan=4 align=center class=\"ctittle\"><b>List of Backup file</b></td></tr>
		<tr>
			<td align=center class=\"tittle\" width=\"30%\">Time</td>
			<td align=center class=\"tittle\" width=\"35%\">Database Name</td>
			<td align=center class=\"tittle\" width=\"12%\">Size</td>
			<td align=center class=\"tittle\" width=\"13%\">Action</td>
		</tr>";
	$list_arr = array();
	$totals = 0;
	$handle=opendir('exports');
	while (false !== ($file = readdir($handle))) {
	    if ($file != "." && $file != "..") {
        //check if it is a sql file, and it is in the correct format
	        $file_info_arr=explode(".",$file);
    	    $filtype=$file_info_arr[2];
	        if($filtype=="sql") {
				$fileinfo = $file_info_arr[0].".".$file_info_arr[1].".info";
				$list_arr[$totals]['file'] = "exports/".$file;
				$list_arr[$totals]['info'] = "exports/".$fileinfo;
				$totals++;
	       }
    	}
	}
	rsort($list_arr);
	for ($i=0;$i<$totals;$i++) {
		$file_info_arr=explode(".",$list_arr[$i]['file']);
		$fileinfo = $list_arr[$i]['info'];
		$f = fopen($fileinfo, "r");
		$fullinfo = fgets($f, 4096);
		fclose($f);
		$info_arr = explode("|",$fullinfo);
		$size = $info_arr[2] ;
		$s1= ($size-($size%1024))/1024 ;
		$s2= (($size*10-(($size*10)%1024))/1024)%10 ;
		$size=$s1.".".$s2;
		$d = getdate($info_arr[0]);
		$time = "{$d['hours']}h{$d['minutes']}' {$d['month']} {$d['mday']}, {$d['year']}";
		$list .= "<tr><td align=center bgcolor=\"#FFFFFF\">{$time}</td><td bgcolor=\"#FFFFFF\">{$info_arr[1]}</td>
				<td bgcolor=\"#FFFFFF\" align=center>{$size} KB</td>
				<td bgcolor=\"#FFFFFF\" align=center><a href=\"?vnTRUST=import&f={$info_arr[0]}\"><img src=\"import.gif\" alt=\"Import to database\"></a>&nbsp;&nbsp;&nbsp;<a href=\"?vnTRUST=download&f={$info_arr[0]}\"><img src=\"download.gif\" alt=\"Download file sql\"></a> </a>				&nbsp;&nbsp;&nbsp;<a href=\"?vnTRUST=del&f={$info_arr[0]}\"><img src=\"delete.gif\" alt=\"Download file sql\"></a></td></tr>";
	
	}
	$list .= "</table>";
	
	

	$list .="<table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"2\">
  <tr>
    <td align=\"center\">
      <a href=\"?vnTRUST=backup\"><img src=\"backup.gif\"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"?vnTRUST=delall\"><img src=\"del.gif\"></a>
    </td>
  </tr>
</table>";

	
	return $list;
}
//==============================
function Do_Import($f) {
global $conf,$DB,$NDKBackup;
if ($f!=0) {
	$filename = "exports/".$conf['dbname'].".".$f.".sql";
	$ndfile = file($filename);
$type="NONE";
$drop_a=array(); 
$table_q=array(); 
$data_q=array();
$tables_q=0;

foreach($ndfile as $line) {
       $line=chop($line);
        if ($type=="NONE") {
            if(strtolower(substr($line,0,6))=="insert") {
                $data_q[]=substr($line,0,strlen($line)-1);
            } elseif(strtolower(substr($line,0,6))=="create") {
                $type="TABLE";
                $table_q[$tables_q]=$line."\n";
           	  } elseif(strtolower(substr($line,0,4))=="drop") {
                $type="NONE";
	    		array_push($drop_a, $line);
                  }
        } elseif ($type=="TABLE") {
            if(strtolower(substr($line,0,1))==")") {
                $type="NONE";
                $table_q[$tables_q] .= substr($line,0,strlen($line)-1)."\n";
                $tables_q++;
            } else {
                $table_q[$tables_q] .= $line."\n";
                     }
          }
}

   $sql_error=0;
    foreach($drop_a as $q_data) {
		if($q_data != "") {
    		$q=mysql_query($q_data);
        		if($q == 0) $sql_error=1; 
        		}
    }
    $table=0;
    foreach($table_q as $q_data) {
		if($q_data != "") {
    		$q=mysql_query($q_data);
        		if($q == 0) $sql_error=1;
				$table++;
        }
    }
    $data=0;
    foreach($data_q as $q_data) {
		if($q_data != "") {
	            $q=mysql_query($q_data);
      	             if($q == 0) $sql_error=1;
					$data++;
		}
    }

    if($sql_error==1) {
        $out = "ERROR : ".mysql_error();
    } else {
        $out = "<center><br><br><br><br><br><br><br><br><br><br><br><br><br>
	<table width=\"50%\" bgcolor=\"#B84120\" align=center border=0 cellspacing=1 cellpadding=1>
	<tr><td>
		<table width=\"100%\" bgcolor=\"#FFFFFF\" align=center border=0 cellspacing=2 cellpadding=2>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr>
			<td align=center colspan=2><font size=2 color=\"#FF3C00\"><b>Import Successfull !</b></font></td>
		</tr>
		<tr>
			<td align=center><font size=2>Tables : {$table}</font></td>
			<td align=center><font size=2>Data : {$data}</font></td>
		</tr>
		<tr><td colspan=2><a href=\"backup.php\">Back to Main List</a></td></tr>
		</table>
	</td></tr>
	</table>
	</center>";
             }
	return $out;
} else header("Location: backup.php");
}
//==============================
function get_file_data($file_path) {
  ob_start();
  @ob_implicit_flush(0);
  @readfile($file_path);
  $file_data = ob_get_contents();
  ob_end_clean();
  if (!empty($file_data)) {
    return $file_data;
  }else{
	  return 0;
  }
}

function get_user_os() {
  global $global_info, $HTTP_USER_AGENT, $HTTP_SERVER_VARS;
  if (!empty($global_info['user_os'])) {
    return $global_info['user_os'];
  }
  if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
    $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
  }
  elseif (getenv("HTTP_USER_AGENT")) {
    $HTTP_USER_AGENT = getenv("HTTP_USER_AGENT");
  }
  elseif (empty($HTTP_USER_AGENT)) {
    $HTTP_USER_AGENT = "";
  }
  if (eregi("Win", $HTTP_USER_AGENT)) {
    $global_info['user_os'] = "WIN";
  }
  elseif (eregi("Mac", $HTTP_USER_AGENT)) {
    $global_info['user_os'] = "MAC";
  }
  else {
    $global_info['user_os'] = "OTHER";
  }
  return $global_info['user_os'];
}

function get_browser_info() {
  global $global_info, $HTTP_USER_AGENT, $HTTP_SERVER_VARS;
  if (!empty($global_info['browser_agent'])) {
    return $global_info['browser_agent'];
  }
  if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
    $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
  }
  elseif (getenv("HTTP_USER_AGENT")) {
    $HTTP_USER_AGENT = getenv("HTTP_USER_AGENT");
  }
  elseif (empty($HTTP_USER_AGENT)) {
    $HTTP_USER_AGENT = "";
  }
  if (eregi("MSIE ([0-9].[0-9]{1,2})", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "MSIE";
    $global_info['browser_version'] = $regs[1];
  }
  elseif (eregi("Mozilla/([0-9].[0-9]{1,2})", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "MOZILLA";
    $global_info['browser_version'] = $regs[1];
  }
  elseif (eregi("Opera(/| )([0-9].[0-9]{1,2})", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "OPERA";
    $global_info['browser_version'] = $regs[2];
  }
  else {
    $global_info['browser_agent'] = "OTHER";
    $global_info['browser_version'] = 0;
  }
  return $global_info['browser_agent'];
}

function is_remote($file_name) {
  return strpos($file_name, '://') > 0 ? 1 : 0;
}
function is_remote_file($file_name) {
  return is_remote($file_name) && preg_match("#\.[a-zA-Z0-9]{1,4}$#", $file_name) ? 1 : 0;
}

function get_remote_file($url) {
  $file_data = "";
  $url = @parse_url($url);
  if (isset($url['path']) && isset($url['scheme']) && eregi("http", $url['scheme'])) {
    $url['port'] = (!isset($url['port'])) ? 80 : $url['port'];
    if ($fsock = @fsockopen($url['host'], $url['port'], $errno, $errstr)) {
      @fputs($fsock, "GET ".$url['path']." HTTP/1.1\r\n");
      @fputs($fsock, "HOST: ".$url['host']."\r\n");
      @fputs($fsock, "Connection: close\r\n\r\n");
      $file_data = "";
      while (!@feof($fsock)) {
        $file_data .= @fread($fsock, 1000);
      }
      @fclose($fsock);
      if (preg_match("/Content-Length\: ([0-9]+)[^\/ \n]/i", $file_data, $regs)) {
        $file_data = substr($file_data, strlen($file_data) - $regs[1], $regs[1]);
      }
    }
  }
  return (!empty($file_data)) ? $file_data : 0;
}
function get_document_root() {
  global $global_info, $DOCUMENT_ROOT, $HTTP_SERVER_VARS;
  if (!empty($global_info['document_root'])) {
    return $global_info['document_root'];
  }
  if (!empty($HTTP_SERVER_VARS['DOCUMENT_ROOT'])) {
    $DOCUMENT_ROOT = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
  }
  elseif (getenv("DOCUMENT_ROOT")) {
    $DOCUMENT_ROOT = getenv("DOCUMENT_ROOT");
  }
  elseif (empty($DOCUMENT_ROOT)) {
    $DOCUMENT_ROOT = "";
  }
  
  return $global_info['document_root'] = $DOCUMENT_ROOT;
}

function Do_Download($f) {
global $conf,$DB,$NDKBackup;
if ($f!=0) {
	$filename = "exports/".$conf['dbname'].".".$f.".sql";
	$fname = $conf['dbname'].".".$f.".sql";
	
	$file_data = get_file_data($filename) ; 
	 
	 $zipfile = new zipfile();
	 $zipfile->add_file($file_data, $fname);
	 $file_data = $zipfile->file();
		
	 $fname_new = $conf['dbname'].".".$f.".zip";
	 $file_size= strlen($file_data);
	  
	  if (!empty($file_data)) {
		  if (get_user_os() == "MAC") {
			header("Content-Type: application/x-unknown\n");
			header("Content-Disposition: attachment; filename=\"".$fname_new."\"\n");
		  }
		  elseif (get_browser_info() == "MSIE") {
			header("Content-Disposition: attachment; filename=\"".$fname_new."\"\n");
			header("Content-Type: application/x-ms-download\n");
		  }
		  elseif (get_browser_info() == "OPERA") {
			header("Content-Disposition: attachment; filename=\"".$fname_new."\"\n");
			header("Content-Type: application/octetstream\n");
		  }
		  else {
			header("Content-Disposition: attachment; filename=\"".$fname_new."\"\n");
			header("Content-Type: application/octet-stream\n");
		  }
			header("Content-Length: ".$file_size."\n\n");
		  flush();
		  echo $file_data;
		  exit();
	 }
} else header("Location: backup.php");
}
//==============================
function Do_Del($f) {
global $conf,$DB,$NDKBackup;
if ($f!=0) {
	$err = 0;
	$filenamesql = "exports/".$conf['dbname'].".".$f.".sql";
	$fnamesql = $conf['dbname'].".".$f.".sql";
	$filenameinfo = "exports/".$conf['dbname'].".".$f.".info";
	$fnameinfo = $conf['dbname'].".".$f.".info";
	if( !unlink($filenamesql) )$err =1 ;
	if( !unlink($filenameinfo) )$err =1 ;
	//@unlink("db_backup/exports/".$file_info_arr[0].".".$file_info_arr[1].".info");
	if ($err == 1){
		$out = "
	<center><br><br><br><br><br><br><br><br><br><br><br><br><br>
	<table width=\"50%\" bgcolor=\"#B84120\" align=center border=0 cellspacing=1 cellpadding=1>
	<tr><td>
		<table width=\"100%\" bgcolor=\"#FFFFFF\" align=center border=0 cellspacing=2 cellpadding=2>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr>
			<td align=center colspan=2><font size=2 color=\"#FF3C00\"><b>Delete Backup Error !</b></font></td>
		</tr>
		<tr>
			<td align=><font size=2>File <strong> {$fnamesql}</strong> Not Found </font></td>
		</tr>
		<tr>
		<td align=><font size=2>File <strong> {$fnameinfo} </strong> Not Found</font></td>
		</tr>
		<tr><td colspan=2><a href=\"backup.php\">Back to Main List</a></td></tr>
		</table>
	</td></tr>
	</table>
	</center>
	" ;
	}else{
	$out  ="
	<center><br><br><br><br><br><br><br><br><br><br><br><br><br>
	<table width=\"50%\" bgcolor=\"#B84120\" align=center border=0 cellspacing=1 cellpadding=1>
	<tr><td>
		<table width=\"100%\" bgcolor=\"#FFFFFF\" align=center border=0 cellspacing=2 cellpadding=2>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr>
			<td align=center colspan=2><font size=2 color=\"#FF3C00\"><b>Delete Backup Successfull !</b></font></td>
		</tr>
		<tr>
			<td align=><font size=2><strong>File Backup .sql</strong> : {$fnamesql}</font></td>
		</tr>
		<tr>
		<td align=><font size=2><strong>File Backup .info</strong> : {$fnameinfo}</font></td>
		</tr>
		<tr><td colspan=2><a href=\"backup.php\">Back to Main List</a></td></tr>
		</table>
	</td></tr>
	</table>
	</center>
	" ;
 }
	return $out;
} else header("Location: ndkbackup.php");

}
//==============================
function Do_Backup() {
global $conf,$DB,$NDKBackup;
$NDKBackup->sqlConnect($conf['host'],$conf['dbuser'],$conf['dbpass']);
$NDKBackup->dbName=$conf['dbname'];
$time=time();
$now = mktime();
$fileData=$NDKBackup->structure();
$backup_size=strlen($fileData);
// Write sql to file
	$fp=@fopen("exports/".$NDKBackup->dbName.".".$time.".sql", "w");
    fwrite($fp,$fileData);
    fclose($fp);
    $BackupFileName=$NDKBackup->dbName.".".$time.".sql";
// Write Info to file
	$fp=fopen("exports/".$NDKBackup->dbName.".".$time.".info", "w");
    fwrite($fp,"$time|{$NDKBackup->dbName}|$backup_size");
    fclose($fp);
	$FileInfoName = $NDKBackup->dbName.".".$time.".info";
// Write  to file last.dat
	$fp=@fopen("last.dat", "w");
    fwrite($fp,$now);
    fclose($fp);
	
    chmod("exports/".$BackupFileName,0777);
    chmod("exports/".$FileInfoName,0777);

// End write

$out  ="
	<center><br><br><br><br><br><br><br><br><br><br><br><br><br>
	<table width=\"50%\" bgcolor=\"#B84120\" align=center border=0 cellspacing=1 cellpadding=1>
	<tr><td>
		<table width=\"100%\" bgcolor=\"#FFFFFF\" align=center border=0 cellspacing=2 cellpadding=2>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr>
			<td align=center colspan=2><font size=2 color=\"#FF3C00\"><b>Backup Successfull !</b></font></td>
		</tr>
		<tr>
			<td align=><font size=2><strong>File Backup .sql</strong> : {$BackupFileName}</font></td>
		</tr>
		<tr>
		<td align=><font size=2><strong>File Backup .info</strong> : {$FileInfoName}</font></td>
		</tr>
		<tr><td colspan=2><a href=\"backup.php\">Back to Main List</a></td></tr>
		</table>
	</td></tr>
	</table>
	</center>
	" ;

	return $out;
}
//==============================

function Do_Backup_Table() {
global $conf,$DB,$NDKBackup;
$NDKBackup->sqlConnect($conf['host'],$conf['dbuser'],$conf['dbpass']);
$NDKBackup->dbName=$conf['dbname'];
$time=time();
$now = mktime();
$a_table =$_POST["db_tables"];

$fileData=$NDKBackup->structure_table($a_table);
$backup_size=strlen($fileData);
// Write sql to file
	$fp=@fopen("exports/".$NDKBackup->dbName.".".$time.".sql", "w");
    fwrite($fp,$fileData);
    fclose($fp);
    $BackupFileName=$NDKBackup->dbName.".".$time.".sql";
// Write Info to file
	$fp=fopen("exports/".$NDKBackup->dbName.".".$time.".info", "w");
    fwrite($fp,"$time|{$NDKBackup->dbName}|$backup_size");
    fclose($fp);
	$FileInfoName = $NDKBackup->dbName.".".$time.".info";
// Write  to file last.dat
	$fp=@fopen("last.dat", "w");
    fwrite($fp,$now);
    fclose($fp);
	
    chmod("exports/".$BackupFileName,0777);
    chmod("exports/".$FileInfoName,0777);

// End write

$out  ="
	<center><br><br><br><br><br><br><br><br><br><br><br><br><br>
	<table width=\"50%\" bgcolor=\"#B84120\" align=center border=0 cellspacing=1 cellpadding=1>
	<tr><td>
		<table width=\"100%\" bgcolor=\"#FFFFFF\" align=center border=0 cellspacing=2 cellpadding=2>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr>
			<td align=center colspan=2><font size=2 color=\"#FF3C00\"><b>Backup Successfull !</b></font></td>
		</tr>
		<tr>
			<td align=><font size=2><strong>File Backup .sql</strong> : {$BackupFileName}</font></td>
		</tr>
		<tr>
		<td align=><font size=2><strong>File Backup .info</strong> : {$FileInfoName}</font></td>
		</tr>
		<tr><td colspan=2><a href=\"backup.php\">Back to Main List</a></td></tr>
		</table>
	</td></tr>
	</table>
	</center>
	" ;

	return $out;
}
//==============================

function Do_Delall() {
global $conf,$DB,$NDKBackup;
	$handle=@opendir('exports');
	$i = 0 ;
	while (false !== ($file = @readdir($handle))) {
	    if ($file != "." && $file != "..") {
        //check if it is a sql file, and it is in the correct format
			$i++ ;
			$filename = "exports/".$file;
			@unlink($filename);
		}
	}
	$num = ($i-1)/2 ;
	
// Write  to file last.dat
	$fp=@fopen("last.dat", "w");
    fwrite($fp,"0");
    fclose($fp);
	
$out  ="
	<center><br><br><br><br><br><br><br><br><br><br><br><br><br>
	<table width=\"50%\" bgcolor=\"#B84120\" align=center border=0 cellspacing=1 cellpadding=1>
	<tr><td>
		<table width=\"100%\" bgcolor=\"#FFFFFF\" align=center border=0 cellspacing=2 cellpadding=2>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr>
			<td align=center colspan=2><font size=2 color=\"#FF3C00\"><b>Deltele All Backup Successfull !</b></font></td>
		</tr>
		<tr>
			<td align=><font size=2><strong>Total have </strong> : {$num} File backup was deleted !!!</font></td>
		</tr>
		
		<tr><td colspan=2><a href=\"backup.php\">Back to Main List</a></td></tr>
		</table>
	</td></tr>
	</table>
	</center>
	" ;

	return $out;

}

//==============================

function Do_Backup_Img() {
global $conf,$DB,$NDKBackup;
	$out="";
	ob_start();
				if (file_exists($file_name)) include $file_name;
				else include "img_backup/backup.php";
				$out = ob_get_contents();
			ob_end_clean();

	return $out;

}
//==============================
function Do_Import_Sql() {
global $conf,$DB,$NDKBackup;

	
	if (!empty($_FILES['filesql']) && ($_FILES['filesql']['name']!="") ){
		$filesql_name = $_FILES['filesql']['name'];
		$filesql_tmp = $_FILES['filesql']['tmp_name'];
		$filesql_type = $_FILES['filesql']['type'];
		//print "filesql_tmp = ".$filesql_tmp."<br>";
		//print "filesql_type = ".$filesql_type."<br>";
		
		if ($filesql_type=="application/zip" || $filesql_type=="application/x-zip-compressed"){
			$file_unzip = unzip($filesql_tmp);
			$ndfile = file($file_unzip);
			if (file_exists($file_unzip)) unlink($file_unzip);
		}else{
			$ndfile = file($filesql_tmp);
		}
		//print $ndfile;
		
		$type="NONE";
		$drop_a=array(); 
		$table_q=array(); 
		$data_q=array();
		$tables_q=0;
		
		foreach($ndfile as $line) {
			   $line=chop($line);
				if ($type=="NONE") {
					if(strtolower(substr($line,0,6))=="insert") {
						$data_q[]=substr($line,0,strlen($line)-1);
					} elseif(strtolower(substr($line,0,6))=="create") {
						$type="TABLE";
						$table_q[$tables_q]=$line."\n";
					  } elseif(strtolower(substr($line,0,4))=="drop") {
						$type="NONE";
						array_push($drop_a, $line);
						  }
				} elseif ($type=="TABLE") {
					if(strtolower(substr($line,0,1))==")") {
						$type="NONE";
						$table_q[$tables_q] .= substr($line,0,strlen($line)-1)."\n";
						$tables_q++;
					} else {
						$table_q[$tables_q] .= $line."\n";
							 }
				  }
		}
		
		   $sql_error=0;
			foreach($drop_a as $q_data) {
				if($q_data != "") {
					$q=mysql_query($q_data);
						if($q == 0) $sql_error=1; 
						}
			}
			$table=0;
			foreach($table_q as $q_data) {
				if($q_data != "") {
					$q=mysql_query($q_data);
						if($q == 0) $sql_error=1;
						$table++;
				}
			}
			$data=0;
			foreach($data_q as $q_data) {
				if($q_data != "") {
						$q=mysql_query($q_data);
							 if($q == 0) $sql_error=1;
							$data++;
				}
			}
		
			if($sql_error==1) {
				$out = "ERROR : ".mysql_error();
			} else {
				$out = "<center><br><br><br><br><br><br><br><br><br><br><br><br><br>
			<table width=\"50%\" bgcolor=\"#B84120\" align=center border=0 cellspacing=1 cellpadding=1>
			<tr><td>
				<table width=\"100%\" bgcolor=\"#FFFFFF\" align=center border=0 cellspacing=2 cellpadding=2>
				<tr><td colspan=2>&nbsp;</td></tr>
				<tr>
					<td align=center colspan=2><font size=2 color=\"#FF3C00\"><b>Import Successfull !</b></font></td>
				</tr>
				<tr>
					<td align=center><font size=2>Tables : {$table}</font></td>
					<td align=center><font size=2>Data : {$data}</font></td>
				</tr>
				<tr><td colspan=2><a href=\"backup.php\">Back to Main List</a></td></tr>
				</table>
			</td></tr>
			</table>
			</center>";
		 }
		 
		return $out; 
		
	} else{
		 header("Location: ndkbackup.php");
	}


}
//==============================
switch ($vnTRUST) {
	case 'import_sql' : $output .= Do_Import_Sql(); break;
	case 'import' : $output .= Do_Import($f); break;
	case 'download' : Do_Download($f); break;
	case 'del' : $output .= Do_Del($f); break;
	case 'backup' : $output .= Do_Backup(); break;
	case 'backup_table' : $output .= Do_Backup_Table(); break;
	case 'delall' : $output .= Do_Delall(); break;
	case 'backup_img' : $output .= Do_Backup_Img(); break;
	default : $output .= Get_List(); break;
}


} // If Main
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>-:[ vnTRUST Web Auto Backup Database - ver 2.0 - vnTRUST.com ]:-</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
html {
	overflow-x: auto;
	scrollbar-face-color: #CECECE;
	scrollbar-shadow-color: #6B6B6B;
	scrollbar-highlight-color: #F8F8F8;
	scrollbar-3dlight-color: #8A8A8A;
	scrollbar-darkshadow-color: #8A8A8A;
	scrollbar-track-color: #8A8A8A;
	scrollbar-arrow-color: #215A8C;
}
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #061D36;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color:#DCDCDC;
}
a:link {
	color: #FF8400;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #FF8400;
}
a:hover {
	text-decoration: none;
	color: #FF6600;
}
a:active {
	text-decoration: none;
	color: #FF8400;
}
img { border : 0px; }
.bdr {
	border: 1px solid #EC8500;
}
.ctittle {
	font-size: 12px;
	font-weight: bold;
	color: #fff;
	text-decoration: none;
	background-color:#B84120;
}
.mtittle {
	font-size: 16px;
	font-weight: bold;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#EC8500;
	height:40px;
}
.ftittle {
	font-size: 12px;
	font-weight: bold;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#B84120;
	height:25px;
}
.tittle {
	font-size: 12px;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#EC8500;
}
.br_sp {
	border-top-width: 1px;
	border-top-style: dotted;
	border-top-color: ##EC8500;
	color: #003333;
	text-decoration: none;
	font-size: 11px;
}
-->
</style>
</head>
<body>
<table width="100%" bgcolor="#B84120" align=center border=0 cellspacing=1 cellpadding=1>
	<tr><td align=center class="mtittle"><b>Web  Backup Database - ver 2.0</b></td>
	<td align=center class="ftittle">&copy;Copyright 2007 - Powered by <a href="http://vntrust.com">vnTRUST.com</a></td>
	</tr>
</table>
<br>
<table width="80%" border="0" cellspacing="2" cellpadding="2" align="center" bgcolor="#B84120">
  <tr>
    <td class="ftittle" align="center" width="50%" ><a href="backup.php">Backup database </a></td>
    <td class="ftittle" align="center"><a href="backup_img.php">Backup images</a> </td>
  </tr>
</table>

<?=$output;?>
<br><br><br>
</body>
</html>
