<?php 
@set_time_limit(3600);
if ((isset($_GET['s']))&&(!empty($_GET['s']))) $step=$_GET['s']; else $step=0;
//==================Class Import=========================
class vnT_Import {
var $dbName;
var $con;
var $query_id;
var $charset	=	"utf8" ;
	function sqlConnect($host, $uid, $pwd,$create=0) {
    global $con, $conf;
			$con=@mysql_connect($host,$uid, $pwd);
			if (!$con) {
				return "Cannot connect to mySQL database . Check your config again !<br><a href='javascript:history.go(-1);'> Back </a>";
			}
			
			if (function_exists('mysql_set_charset')){
				mysql_set_charset($this->charset,$con);
			}else{
				mysql_query( "SET NAMES ".$this->charset);
			}	

			if ($create){
				mysql_query("CREATE DATABASE `".$this->dbName."`");
				if (mysql_errno() >0) // co loi mysql
				{
					if (mysql_errno() == 1007)
					{
						return "CSDL <b>".$this->dbName."</b> đã tồn tại trong data MYSQL <br><a href='javascript:history.go(-1);'> Back </a>" ;
					}
					else
					{
						return mysql_errno()." : ".mysql_error();
					}
				}
			}
			
			if (!@mysql_select_db($this->dbName, $con) ) {
								return "Couldn't select database <b>".$this->dbName."</b> .<br><a href='javascript:history.go(-1);'> Back </a>";
			}

			return "";
    }

	function query($the_query) {
		global $con, $conf;
        $this->query_id = @mysql_query($the_query, $con);
        return $this->query_id;
    }
	
	function fetch_row($query_id = "") {
    
    	if ($query_id == "")
    	{
    		$query_id = $this->query_id;
    	}
    	
        $record_row = @mysql_fetch_array($query_id, MYSQL_ASSOC);
        
        return $record_row;
   	}
		
	function compile_db_insert_string($data) {
    
        $field_names  = "";
        $field_values = "";
        
        foreach ($data as $k => $v)
        {
            $v = preg_replace( "/'/", "\\'", $v );
            $field_names  .= "$k,";
            $field_values .= "'$v',";
        }
        
        $field_names  = preg_replace( "/,$/" , "" , $field_names  );
        $field_values = preg_replace( "/,$/" , "" , $field_values );
        
        return array( 'FIELD_NAMES'  => $field_names,
                      'FIELD_VALUES' => $field_values,
                    );
    }
    
    function compile_db_update_string($data) {
        
        $return_string = "";
        
        foreach ($data as $k => $v)
        {
            $v = preg_replace( "/'/", "\\'", $v );
            $return_string .= $k . "='".$v."',";
        }
        $return_string = preg_replace( "/,$/" , "" , $return_string );
        return $return_string;
    }
    
    function do_update( $tbl, $arr, $where="" )
    {
        $dba = $this->compile_db_update_string( $arr );
        $query = "UPDATE $tbl SET $dba";
        if ( $where )
        {
            $query .= " WHERE ".$where;
        }
        $ci = $this->query( $query );
        return $ci;
    }
    
   function do_insert( $tbl, $arr )
    {
        $dba = $this->compile_db_insert_string( $arr );
        $sql_insert = "INSERT INTO $tbl ({$dba['FIELD_NAMES']}) VALUES({$dba['FIELD_VALUES']})";
		//print "sql_insert= ".$sql_insert."<br>";
		$ci = $this->query($sql_insert);
        return $ci;
    }
    ////////
	
	function fetchDbConfig($confName,$arr_old="") {
	global $conf;
	$sql ="SELECT array FROM config WHERE name ='{$confName}' " ;
	$result = $this->query($sql);
	if($row = $this->fetch_row($result)){
		if ($row['array']!=""){
			$base64Encoded = unserialize($row['array']);
			foreach($base64Encoded as $key => $value){
				$array_conf[base64_decode($key)] = stripslashes(base64_decode($value));
			}
		}else{
			$array_conf = array();
		}
	} 
	if ($arr_old){
		foreach($arr_old as $key => $value){
			$array_conf[$key] = stripslashes($value);
		}
	}
	return $array_conf ;

}


/******************************* write config ************************************
	$configName  : ten  config  name
	$new : mang lang moi
	$prevArray : mang lang cu
	
	tra ve : true neu update thanh cong
			 false neu update that bai	
*************************************************/

function writeDbConfig($configName, $new = "", $prevArray) {
	global $conf;
		if (!is_array($new)){
			exit();
		}

		// add old config vars not in $new array
		if(is_array($prevArray)){
			foreach($prevArray as $key => $value) {
				if($new[$key]!==$prevArray[$key]){
					$newConfig[$key] = $value;
				}
			}
		}

		// build new config vars from $new array
		if(is_array($new)){
			foreach($new as $key => $value) {			
				$newConfig[$key] = $value;
			}
		}
	//	print_r($newConfig);
		foreach($newConfig as $key => $value) {
			$value = str_replace(array("\'","'"),"&#39;",$value);
			
			$newConfigBase64[base64_encode($key)] = base64_encode($value);
		}
		
		$configText = serialize($newConfigBase64);

   		// update into databse
		$array['array'] = $configText;
		$ok = $this->do_update("config",$array,"name='{$configName}'");

		return $ok;

}


	function close() {
        global $con, $conf;
        @mysql_close($con);
    }


		
	function import() 
	{
		global $con, $conf;

		$file="database.sql";
	
		$sql_query = @fread(@fopen($file, 'rb'), @filesize($file));
		$sql_query = $this->remove_remarks($sql_query);
		$sql_query = $this->split_sql_file($sql_query, ';');

		$n = count($sql_query);$j=0;
		for ($i=0; $i<$n; $i++)
		{
			$tmp = trim($sql_query[$i]);
			if (substr($tmp,0,1)!='#' && !empty($tmp))
				if (!$this->query($tmp))
				{
					$sql_error =1;
				}
			if ($i%100==0) {flush();}
		}
		
		if($sql_error==1) {
			return "Error : ".mysql_error($con);
		} else {
			return "Installed Database successful";
		}
	}
			
	// ------------------------------------
	// These functions are from phpBB 2.0
	function remove_comments(&$output)
	{
		$lines = $output;
		$output = "";
	
		// try to keep mem. use down
		$linecount = count($lines);
	
		$in_comment = false;
		for($i = 0; $i < $linecount; $i++)
		{
			if( preg_match("/^\/\*/", preg_quote($lines[$i])) )
			{
				$in_comment = true;
			}
	
			if( !$in_comment )
			{
				$output .= $lines[$i] . "\n";
			}
	
			if( preg_match("/\*\/$/", preg_quote($lines[$i])) )
			{
				$in_comment = false;
			}
		}
	
		unset($lines);
		return $output;
	}
	
	function remove_remarks($sql)
	{
		$lines = explode("\n", $sql);
		
		// try to keep mem. use down
		$sql = "";
		
		$linecount = count($lines);
		$output = "";
	
		for ($i = 0; $i < $linecount; $i++)
		{
			if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0))
			{
				if ($lines[$i][0] != REMARK)
				{
					$output .= $lines[$i] . "\n";
				}
				else
				{
					$output .= "\n";
				}
				// Trading a bit of speed for lower mem. use here.
				$lines[$i] = "";
			}
		}
		
		return $output;
	}
	
	function split_sql_file($sql, $delimiter)
	{
		// Split up our string into "possible" SQL statements.
		$tokens = explode($delimiter, $sql);
	
		// try to save mem.
		$sql = "";
		$output = array();
		
		// we don't actually care about the matches preg gives us.
		$matches = array();
		
		// this is faster than calling count($oktens) every time thru the loop.
		$token_count = count($tokens);
		for ($i = 0; $i < $token_count; $i++)
		{
			// Don't wanna add an empty string as the last thing in the array.
			if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
			{
				// This is the total number of single quotes in the token.
				$total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
				// Counts single quotes that are preceded by an odd number of backslashes, 
				// which means they're escaped quotes.
				$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
				
				$unescaped_quotes = $total_quotes - $escaped_quotes;
				
				// If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
				if (($unescaped_quotes % 2) == 0)
				{
					// It's a complete sql statement.
					$output[] = $tokens[$i];
					// save memory.
					$tokens[$i] = "";
				}
				else
				{
					// incomplete sql statement. keep adding tokens until we have a complete one.
					// $temp will hold what we have so far.
					$temp = $tokens[$i] . $delimiter;
					// save memory..
					$tokens[$i] = "";
					
					// Do we have a complete statement yet? 
					$complete_stmt = false;
					
					for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
					{
						// This is the total number of single quotes in the token.
						$total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
						// Counts single quotes that are preceded by an odd number of backslashes, 
						// which means they're escaped quotes.
						$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
				
						$unescaped_quotes = $total_quotes - $escaped_quotes;
						
						if (($unescaped_quotes % 2) == 1)
						{
							// odd number of unescaped quotes. In combination with the previous incomplete
							// statement(s), we now have a complete statement. (2 odds always make an even)
							$output[] = $temp . $tokens[$j];
	
							// save memory.
							$tokens[$j] = "";
							$temp = "";
							
							// exit the loop.
							$complete_stmt = true;
							// make sure the outer loop continues at the right point.
							$i = $j;
						}
						else
						{
							// even number of unescaped quotes. We still don't have a complete statement. 
							// (1 odd and 1 even always make an odd)
							$temp .= $tokens[$j] . $delimiter;
							// save memory.
							$tokens[$j] = "";
						}
						
					} // for..
				} // else
			}
		}
	
		return $output;
	}
	
}
//============Main=============
$output = "";
$fileconfig="../_config.php";
$show=0;
$conf=array();

if ($step==0) {
	if (!is_writable($fileconfig)) {
		if (!@chmod($fileconfig,0777))
			$output="<div style='padding:5px;color:#FF1111;background-color:#FFFFFF' align=center>Cannot write to Config file . Please CHMOD file _config.php to 0777</div>";
	} else {
		$output="<div style='padding:5px;color:#1111FF;background-color:#FFFFFF' align=center>Please enter your infomation to install <b>CMS-vnTRUST&trade;</b></div>";
	}
}

switch ($step) {
	case '1' : $output .= do_step1(); break;
	case '2' : $output .= do_step2(); break;
	case '3' : $output .= do_step3(); break;
	case '4' : $output .= do_step4(); break;
}
//=======Function========
function do_step1() {
global $fileconfig,$show;
require_once $fileconfig;
$confnew=array(
	'host' => $_POST['host'],
	'dbuser' => $_POST['dbuser'],
	'dbpass' => $_POST['dbpass'],
	'dbname' => $_POST['dbname'],
	//'dbprefix' =>$_POST['dbprefix'],
	'rooturl' => $_POST['rooturl'],
	'rootpath' => $_POST['rootpath'],
	//'cmd' => "vnTRUST",
	//'customer_code' => $_POST['customer_code'],
	//'lisence' => $_POST['lisence'],
	//'path_wyswyg' => $_POST['path_wyswyg'],
);

while( list($k, $v) = each($confnew) ) {
	$conf[$k]=$v;
}
	
// Test connect
	$vnT=new vnT_Import;

	$vnT->dbName=$conf['dbname'];	
	$res=$vnT->sqlConnect($conf['host'],$conf['dbuser'],$conf['dbpass'],$_POST['DBcreate']);
if (!empty($res)) {
	$output="<div style='padding:5px;color:#1111FF;background-color:#FFFFFF' align=center>{$res}</div>";
} else {
	
	
	//echo "ok = ".$ok;
	// write to file
	$f = fopen($fileconfig,"w");
	fwrite($f,"<?php\r\n");
	while( list($k, $v) = each($conf) ) {
		
		if($k== "rooturl" || $k== "rootpath")
		{
			if($k == "rooturl" ){
				$value ="'http://'.".'$_SERVER[\'HTTP_HOST\']'.".'".substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-17)."'";
				fwrite($f,"$"."conf['{$k}']=".$value.";\r\n");
			}
						
			if($k == "rootpath" ){
				$value = '$_SERVER[\'DOCUMENT_ROOT\']'.".'".substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-17)."'";
				fwrite($f,"$"."conf['{$k}']=".$value.";\r\n");			
			}
			
		}else{
			fwrite($f,"$"."conf['{$k}']='{$v}';\r\n");
		}
		
		
	}
	
	fwrite($f,"?>");
	fclose($f);
	// end
	// Import database
	$output=$vnT->import();
	// End
	
	// update database config
	$arr_old = $vnT->fetchDbConfig("config");
	$cot['cmd'] = $_POST['cmd'];
	$cot['customer_code'] = $_POST['customer_code'];
	$cot['lisence'] =$_POST['lisence'];
	
	$ok = $vnT->writeDbConfig("config",$cot,$arr_old);
	
	$show=1;
}
return $output;

}
//=========================
function do_step2() {
global $fileconfig,$show;
require_once $fileconfig;
	$vnT=new vnT_Import;
	$vnT->dbName=$conf['dbname'];	
	$res=$vnT->sqlConnect($conf['host'],$conf['dbuser'],$conf['dbpass']);
	include "functions.php";
	$func = new func;
	
if (!empty($res)) {
	$output="<div style='padding:5px;color:#1111FF;background-color:#FFFFFF' align=center>{$res}</div>";
} else {
	
	$email = $_POST['email'];
	$admin_user = str_replace("\'","",$_POST['username']);
	$admin_pass = str_replace("\'","",$_POST['password']);
	if(stristr($_SERVER['HTTP_REFERER'], $conf['rooturl']) === FALSE) {
		$output="<div style='padding:5px;color:#1111FF;background-color:#FFFFFF' align=center>Get Out !!!</div>";
	} else {
		$admin_pass=$func->md10($admin_pass);
		$sql_update = "UPDATE admin set username='{$admin_user}', password= '{$admin_pass}' , email='{$email}' where adminid=1";
		//print "sql_update = ".$sql_update;
		$res = $vnT->query($sql_update);
		if ($res){
			// update database config
			$arr_old = $vnT->fetchDbConfig("config");
			$cot['indextitle'] = $_POST['indextitle'];
			$cot['indextitle_vn'] = $_POST['indextitle'];
			$cot['rooturl'] = $conf['rooturl'];
			$cot['rootpath'] = $conf['rootpath'];
			$cot['email'] = $_POST['email'];
			
			$ok = $vnT->writeDbConfig("config",$cot,$arr_old);
			$output="<div style='padding:5px;color:#1111FF;background-color:#FFFFFF' align=center>Install CMS-vnTRUST Successful !<br>Please rename or delete directory <b>install</b> for security reason.<br><a href='setup.php'><strong>[Install Modules - Block]</strong></a>&nbsp;|&nbsp;<a href='../'><strong>[Main page]</strong></a>&nbsp;|&nbsp;<a href='../admin'><strong>[Administrator page]</strong></a></div>";
		} else{
			$output="<div style='padding:5px;color:#1111FF;background-color:#FFFFFF' align=center>Cannot Insert Admin Infomations .<br><a href='javascript:history.go(-1);'> Back </a></div>";
		}
	}
}

return $output;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>CMS vnTRUST Installer 2.0</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
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
	line-height:18px;
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
	border: 1px solid #344559;
}
.ctittle {
	font-size: 14px;
	font-weight: bold;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#0063B0;
}
.mtittle {
	font-size: 16px;
	font-weight: bold;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#2B6082;
	height:40px;
}
.ftittle {
	font-size: 12px;
	font-weight: bold;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#3A74AB;
	height:25px;
}
.tittle {
	font-size: 12px;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#3F668A;
}
.br_sp {
	border-top-width: 1px;
	border-top-style: dotted;
	border-top-color: #003366;
	color: #003333;
	text-decoration: none;
	font-size: 11px;
}
.btitle {
	color: #FFFFFF;
	font-weight: bold;
	padding:5px;
}
.rowdir {
	background-color:#2B6082;
	color:#FFFFFF;
	font-weight:bold;
}
.rowfile {
	background-color:#FFFFFF;
}
.rowbut {
	background-color:#FFFFCC;
}
.des {
	font-size:9px;
	text-align:left;
	color:#666666;	
}
-->
</style>
</head>
<body>
<table width="100%" bgcolor="#1E476A" align=center border=0 cellspacing=1 cellpadding=1>
	<tr>
	<td align=center class="ftittle" width="200"><div style="font-size:16px; font-weight:bold"> Step: <?=$step+1?></div></td>
	<td align=center class="mtittle" valign="middle"><b>CÀI ĐẶT CMS VNTRUST v6.0</b></td>
	</tr>
</table>
<br>
<table width="70%" border="0" cellspacing="2" cellpadding="3" align="center" style="border:1px #666666 solid">
  <tr>
    <td align="left" bgcolor="#FFFFFF"><?=$output?></td>
  </tr>
</table>
<br>
<? if ($step==0) { ?>
<form action="?s=1" id="finfo" name="finfo" method="post">
<table width="70%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#FFFFFF" style="border:1px #999999 solid">
  <tr>
    <td colspan="2" align="left" bgcolor="#2B6082" class="btitle">Your Hosting Configuration</td>
  </tr>
  <tr>
    <td width="40%"><b> root URL </b>
	    <div class="des">
	This is the URL (must start with http://) to your main Web directory (ex:http://www.domain.com/)	</div>	</td>
    <td align="left" valign="top"><input name="rooturl" type="text" id="rooturl" size="50" maxlength="250" value="http://<?=$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-17)?>"></td>
  </tr>
  <tr>
    <td ><b> root Path </b>
	    <div class="des">
	This is the Path (not start with http://) to your main Web directory (ex:/home/domain/public_html/)	</div>	</td>
    <td align="left" valign="top"><input name="rootpath" type="text" id="rootpath" size="50" maxlength="250" value="<?=substr($_SERVER['SCRIPT_FILENAME'],0,strlen($_SERVER['SCRIPT_FILENAME'])-17)?>"></td>
  </tr>
  
	
	<tr>
    <td ><strong>Customer Code </strong><div class="des">(This is the customer code support by vnTRUST)</div>
		</td>
    <td align="left" valign="top"><input name="customer_code" type="text" id="customer_code" size="50" maxlength="250" value="KH001"></td>
  </tr>
	<tr>
    <td ><strong>Lisence Key </strong><div class="des">(This is the lisence code used for Domain or Server IP)</div>
		</td>
    <td align="left" valign="top"><input name="lisence" type="text" id="lisence" size="50" maxlength="250" value="2eb0ca794f4cee2a52e739710e585dee"></td>
  </tr>
  <tr>
    <td colspan="2" align="left" bgcolor="#2B6082" class="btitle">Your Database Configuration</td>
  </tr>
  <tr>
    <td align="right">Host Name: </td>
    <td align="left"><input name="host" type="text" id="host" size="50" maxlength="250" value="localhost"></td>
  </tr>
  <tr>
    <td align="right">Username: </td>
    <td align="left"><input name="dbuser" type="text" id="dbuser" size="50" maxlength="250" value="root"></td>
  </tr>
  <tr>
    <td align="right">Password: </td>
    <td align="left"><input name="dbpass" type="text" id="dbpass" size="50" maxlength="250"></td>
  </tr>
  <tr>
    <td align="right">Database Name:</td>
    <td align="left"><input name="dbname" type="text" id="dbname" size="50" maxlength="250">		</td>
  </tr>
	<tr>
    <td align="right">&nbsp;</td>
    <td align="left">
		<input name="DBcreate" type="radio" value="1" checked align="absmiddle" > 
		Tạo database mới &nbsp;&nbsp;&nbsp;&nbsp;<input name="DBcreate" type="radio" value="0"  align="absmiddle" > Database đã có sẵn		</td>
  </tr>
  <tr>
    <td align="right">Database prefix</td>
    <td align="left"><input name="dbprefix" type="text" id="dbprefix" size="50" maxlength="250" value="" readonly="Readonly"></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Submit">&nbsp;&nbsp;&nbsp;<input name="Reset" type="reset" id="Reset" value="Reset"></td>
  </tr>
</table>
</form>
<? } else { 
if ($show) { ?>
<form action="?s=2" id="finfo" name="finfo" method="post">
<table width="70%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#FFFFFF" style="border:1px #999999 solid">
  <tr>
    <td colspan="2" align="left" bgcolor="#2B6082" class="btitle">Root Admin Configuration</td>
  </tr>
	  <tr>
    <td  align="right"><b>Web Title </b>	</td>
    <td align="left"><input name="indextitle" type="text" id="indextitle" size="50" maxlength="250" value=".: CMS - vnTRUST v6.0 :."></td>
  </tr>
  
  <tr>
    <td width="40%" align="right"><b>Username: </b>
	</td>
    <td align="left"><input name="username" type="text" id="username" size="50" maxlength="250" value="admin"></td>
  </tr>
  <tr>
    <td align="right"><b>Password: </b>
	</td>
    <td align="left"><input name="password" type="text" id="password" size="50" maxlength="250" value="kocopass"></td>
  </tr>
	<tr>
    <td align="right" ><b>Admin Email </b>	</td>
    <td align="left"><input name="email" type="text" id="email" size="50" maxlength="250" value="thaison@trust.vn"></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Submit">&nbsp;&nbsp;&nbsp;<input name="Reset" type="reset" id="Reset" value="Reset"></td>
  </tr>
</table>
</form>
<? }
} ?>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" bgcolor="#2B6082" class="btitle">Copyright © 2006 :: <a href="http://vntrust.com" target="_blank">vnTRUST Co., LTD</a> :: All Rights Reserved</td>
	<td width="200" align="center" bgcolor="#3A74AB" class="btitle">Powered by <a href="http://vntrust.com" target="_blank">vnTRUST CMS </a></td>
  </tr>
</table>
</body>
</html>