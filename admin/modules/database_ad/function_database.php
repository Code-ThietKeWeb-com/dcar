<?php
/*================================================================================*\
|| 							Name code : funtions_about.php 		 			      	         			  # ||
||  				Copyright © 2008 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
define('DIR_BACKUP', '../db_backup/exports');
define('IMAGE_MOD', 'modules/database_ad/images');
define('MAXLENGTH', 100000);
define('REMARK', '#');
define('TIMEOUT', 1200);
define('MAXGZIP', 60000000);
define('PARSESQL', true);
define('BIGDATA', true);

function getToolbar ($act = "database", $lang = "vn")
{
  global $func, $DB, $conf, $vnT;
  $menu = array(
    "add" => array(
      'icon' => "i_add" , 
      'title' => "Add" , 
      'link' => "?mod=about&act=$act&sub=add&lang=" . $lang) , 
    "edit" => array(
      'icon' => "i_edit" , 
      'title' => "Edit" , 
      'link' => "javascript:alert('" . $vnT->lang['action_no_active'] . "')") , 
    "manage" => array(
      'icon' => "i_manage" , 
      'title' => "Manage" , 
      'link' => "?mod=database&act=$act&lang=" . $lang) , 
    "help" => array(
      'icon' => "i_help" , 
      'title' => "Help" , 
      'link' => "'help/index.php?mod=database&act=$act','AdminCPHelp',1000, 600, 'yes','center'" , 
      'newwin' => 1));
  return $func->getMenu($menu);
}

function get_file_data ($file_path)
{
  ob_start();
  @ob_implicit_flush(0);
  @readfile($file_path);
  $file_data = ob_get_contents();
  ob_end_clean();
  if (! empty($file_data)) {
    return $file_data;
  } else {
    return 0;
  }
}

function get_user_os ()
{
  global $global_info, $HTTP_USER_AGENT, $HTTP_SERVER_VARS;
  if (! empty($global_info['user_os'])) {
    return $global_info['user_os'];
  }
  if (! empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
    $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
  } elseif (getenv("HTTP_USER_AGENT")) {
    $HTTP_USER_AGENT = getenv("HTTP_USER_AGENT");
  } elseif (empty($HTTP_USER_AGENT)) {
    $HTTP_USER_AGENT = "";
  }
  if (eregi("Win", $HTTP_USER_AGENT)) {
    $global_info['user_os'] = "WIN";
  } elseif (eregi("Mac", $HTTP_USER_AGENT)) {
    $global_info['user_os'] = "MAC";
  } else {
    $global_info['user_os'] = "OTHER";
  }
  return $global_info['user_os'];
}

function get_browser_info ()
{
  global $global_info, $HTTP_USER_AGENT, $HTTP_SERVER_VARS;
  if (! empty($global_info['browser_agent'])) {
    return $global_info['browser_agent'];
  }
  if (! empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
    $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
  } elseif (getenv("HTTP_USER_AGENT")) {
    $HTTP_USER_AGENT = getenv("HTTP_USER_AGENT");
  } elseif (empty($HTTP_USER_AGENT)) {
    $HTTP_USER_AGENT = "";
  }
  if (eregi("MSIE ([0-9].[0-9]{1,2})", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "MSIE";
    $global_info['browser_version'] = $regs[1];
  } elseif (eregi("Mozilla/([0-9].[0-9]{1,2})", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "MOZILLA";
    $global_info['browser_version'] = $regs[1];
  } elseif (eregi("Opera(/| )([0-9].[0-9]{1,2})", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "OPERA";
    $global_info['browser_version'] = $regs[2];
  } else {
    $global_info['browser_agent'] = "OTHER";
    $global_info['browser_version'] = 0;
  }
  return $global_info['browser_agent'];
}

function is_remote ($file_name)
{
  return strpos($file_name, '://') > 0 ? 1 : 0;
}

function is_remote_file ($file_name)
{
  return is_remote($file_name) && preg_match("#\.[a-zA-Z0-9]{1,4}$#", $file_name) ? 1 : 0;
}

function get_remote_file ($url)
{
  $file_data = "";
  $url = @parse_url($url);
  if (isset($url['path']) && isset($url['scheme']) && eregi("http", $url['scheme'])) {
    $url['port'] = (! isset($url['port'])) ? 80 : $url['port'];
    if ($fsock = @fsockopen($url['host'], $url['port'], $errno, $errstr)) {
      @fputs($fsock, "GET " . $url['path'] . " HTTP/1.1\r\n");
      @fputs($fsock, "HOST: " . $url['host'] . "\r\n");
      @fputs($fsock, "Connection: close\r\n\r\n");
      $file_data = "";
      while (! @feof($fsock)) {
        $file_data .= @fread($fsock, 1000);
      }
      @fclose($fsock);
      if (preg_match("/Content-Length\: ([0-9]+)[^\/ \n]/i", $file_data, $regs)) {
        $file_data = substr($file_data, strlen($file_data) - $regs[1], $regs[1]);
      }
    }
  }
  return (! empty($file_data)) ? $file_data : 0;
}

function get_document_root ()
{
  global $global_info, $DOCUMENT_ROOT, $HTTP_SERVER_VARS;
  if (! empty($global_info['document_root'])) {
    return $global_info['document_root'];
  }
  if (! empty($HTTP_SERVER_VARS['DOCUMENT_ROOT'])) {
    $DOCUMENT_ROOT = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
  } elseif (getenv("DOCUMENT_ROOT")) {
    $DOCUMENT_ROOT = getenv("DOCUMENT_ROOT");
  } elseif (empty($DOCUMENT_ROOT)) {
    $DOCUMENT_ROOT = "";
  }
  return $global_info['document_root'] = $DOCUMENT_ROOT;
}

function structure ()
{
  global $vnT, $DB, $conf;
  $out = "";
  $res = $DB->list_tables($conf['dbname']);
  $nt = $DB->num_rows($res);
  for ($a = 0; $a < $nt; $a ++) {
    $row = $DB->fetch_rows($res);
    $tablename = $row[0];
    // Table		
    $out .= "DROP TABLE IF EXISTS `" . $tablename . "`;\n";
    $res2 = $DB->query('SHOW CREATE TABLE ' . $conf['dbname'] . '.' . $tablename);
    $tmpres = $DB->fetch_array($res2);
    $out .= $tmpres[1];
    $out .= ";\n\n";
    // Data
    $res2 = $DB->query("select * from `$tablename`");
    $nf = $DB->num_fields($res2);
    $nr = $DB->num_rows($res2);
    for ($c = 0; $c < $nr; $c ++) {
      $out .= "insert into `$tablename` values (";
      $row = $DB->fetch_rows($res2);
      for ($d = 0; $d < $nf; $d ++) {
        $data = strval($row[$d]);
        if (is_null($row[$d])) {
          $out .= "NULL";
        } else {
          $out .= "'" . mysql_escape_string($data) . "'";
        }
        if ($d < ($nf - 1)) {
          $out .= ", ";
        }
      }
      $out .= ");\n";
    }
  }
  $p_ver = $_SERVER['SERVER_SOFTWARE'];
  $p_ser = substr($p_ver, 0, strpos($p_ver, " "));
  $p_php = substr($p_ver, strrpos($p_ver, " ") + 1);
  $p_date = date("M j, Y") . " at " . date("H:i");
  $copyright = "-------------------------------------------------
-- Auto Backup Web Database 
-- version 2.0 by vnTRUST  
-- http://vntrust.com 
-- 
-- Database: {$conf['dbname']}  
-- Generation Time: {$p_date} 
-- Server version: {$p_ser} 
-- PHP Version: {$p_php} 
-------------------------------------------------
\n";
  $out = $copyright . $out;
  echo $DB->error();
  return $out;
}

//===========================================================
//===========================================================
function structure_table ($a_table)
{
  global $vnT, $DB, $conf;
  $out = "";
  $nt = count($a_table);
  for ($i = 0; $i < $nt; $i ++) {
    //$row=mysql_fetch_row($res);
    $tablename = $a_table[$i];
    // Table		
    $out .= "DROP TABLE IF EXISTS `" . $tablename . "`;\n";
    $res2 = $DB->query('SHOW CREATE TABLE ' . $conf['dbname'] . '.' . $tablename);
    $tmpres = $DB->fetch_array($res2);
    $out .= $tmpres[1];
    $out .= ";\n\n";
    // Data
    $res2 = $DB->query("select * from `$tablename`");
    $nf = $DB->num_fields($res2);
    $nr = $DB->num_rows($res2);
    for ($c = 0; $c < $nr; $c ++) {
      $out .= "insert into `$tablename` values (";
      $row = $DB->fetch_rows($res2);
      for ($d = 0; $d < $nf; $d ++) {
        $data = strval($row[$d]);
        if (is_null($row[$d])) {
          $out .= "NULL";
        } else {
          $out .= "'" . mysql_escape_string($data) . "'";
        }
        if ($d < ($nf - 1)) {
          $out .= ", ";
        }
      }
      $out .= ");\n";
    }
  }
  $p_ver = $_SERVER['SERVER_SOFTWARE'];
  $p_ser = substr($p_ver, 0, strpos($p_ver, " "));
  $p_php = substr($p_ver, strrpos($p_ver, " ") + 1);
  $p_date = date("M j, Y") . " at " . date("H:i");
  $copyright = "-------------------------------------------------
-- Auto Backup Web Database 
-- version 2.0 by vnTRUST 
-- http://vntrust.com 
-- 
-- Database: {$conf['dbname']} 
-- Generation Time: {$p_date} 
-- Server version: {$p_ser} 
-- PHP Version: {$p_php} 
-------------------------------------------------
\n";
  $out = $copyright . $out;
  echo $DB->error();
  return $out;
}

//===========================================================
function delOldBackups ()
{
  global $conf;
  $handle = @opendir(DIR_BACKUP);
  $remove_backups_older_than = time() - ($conf['del_after_days_local'] * 86400);
  while (false !== ($file = @readdir($handle))) {
    if ($file != "." && $file != "..") {
      //check if it is a sql file, and it is in the correct format
      $file_info_arr = explode(".", $file);
      $filtype = $file_info_arr[2];
      if ($filtype == "sql") {
        if (($file_info_arr[1] < $remove_backups_older_than) && ($conf['del_after_days_local'] != 0)) {
          @unlink(DIR_BACKUP . "/" . $file_info_arr[0] . "." . $file_info_arr[1] . ".sql");
          @unlink(DIR_BACKUP . "/" . $file_info_arr[0] . "." . $file_info_arr[1] . ".info");
        }
      }
    }
  }
}

/**
 * function do_dump 
 *  
 **/
function do_dump ($table, $fp = 0)
{
  global $vnT, $DB, $conf;
  //		if (in_array(substr($table, strlen($conf['prefix'])), $GLOBALS['exclude'])) return;
  $tabledump = "\n";
  $tabledump .= "DROP TABLE IF EXISTS $table;\n";
  $rows = $DB->query("SHOW CREATE TABLE $table");
  $data = $DB->fetch_array($rows);
  //$tabledump .= preg_replace('/\r|\n|\t/', '', $data[1]).";\n";
  $tabledump .= preg_replace('/\r|\n|\t/', '', $data[1]) . ";\n";
  if ($fp)
    fwrite($fp, $tabledump);
  else
    echo $tabledump;
  $rows = $DB->query("SELECT * FROM $table");
  $numfields = $DB->num_fields($rows);
  $dump = array();
  $length = 0;
  while ($row = $DB->fetch_array($rows)) {
    $data = '(';
    for ($i = 0; $i < $numfields; $i ++) {
      if ($i != 0)
        $data .= ',';
      $data .= isset($row[$i]) ? "'" . mysql_escape_string($row[$i]) . "'" : 'NULL';
    }
    $dump[] = $data . ')';
    $length += strlen($data) + 1;
    if ($length > MAXLENGTH) {
      $tabledump = "INSERT INTO $table VALUES " . implode(', ', $dump) . ";\n";
      $dump = array();
      $length = 0;
      if ($fp)
        fwrite($fp, $tabledump);
      else
        echo $tabledump;
    }
  }
  mysql_free_result($rows);
  if ($length > 0) {
    $tabledump = "INSERT INTO $table VALUES " . implode(', ', $dump) . ";\n";
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
function do_backup ($filename = "databse.sql", $a_table = "")
{
  global $vnT, $DB, $conf;
  $fp = fopen($filename, "w");
  // Header
  $header = "#----------------------------------------\n";
  $header .= "# Backup Web Database \n";
  $header .= "# Version 2.0 by vnTRUST  \n";
  $header .= "# http://trust.vn  \n";
  $header .= "# DATABASE:  " . $conf['dbname'] . "\n";
  $header .= "# Date/Time:  " . date("l dS  F Y H:i:s") . "\n";
  $header .= "#----------------------------------------\n";
  fwrite($fp, $header);
  $tablesbackup = $DB->query("SHOW tables LIKE '" . $conf['prefix'] . "%'");
  $nums = $DB->num_rows($tablesbackup);
  //echo "Dumping ($nums tables): ";flush();$i=0;
  while ($tablebackup = $DB->fetch_array($tablesbackup)) {
    if (is_array($a_table)) {
      if (in_array($tablebackup[0], $a_table)) {
        do_dump($tablebackup[0], $fp);
      }
    } else {
      do_dump($tablebackup[0], $fp);
    }
    //echo ++$i%10;
    flush();
  }
  fclose($fp);
  /*	
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
		*/
}

// These functions are from phpBB 2.0
function remove_comments (&$output)
{
  $lines = $output;
  $output = "";
  // try to keep mem. use down
  $linecount = count($lines);
  $in_comment = false;
  for ($i = 0; $i < $linecount; $i ++) {
    if (preg_match("/^\/\*/", preg_quote($lines[$i]))) {
      $in_comment = true;
    }
    if (! $in_comment) {
      $output .= $lines[$i] . "\n";
    }
    if (preg_match("/\*\/$/", preg_quote($lines[$i]))) {
      $in_comment = false;
    }
  }
  unset($lines);
  return $output;
}

function remove_remarks ($sql)
{
  $lines = explode("\n", $sql);
  // try to keep mem. use down
  $sql = "";
  $linecount = count($lines);
  $output = "";
  for ($i = 0; $i < $linecount; $i ++) {
    if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0)) {
      if ($lines[$i][0] != REMARK) {
        $output .= $lines[$i] . "\n";
      } else {
        $output .= "\n";
      }
      // Trading a bit of speed for lower mem. use here.
      $lines[$i] = "";
    }
  }
  return $output;
}

function split_sql_file ($sql, $delimiter)
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
  for ($i = 0; $i < $token_count; $i ++) {
    // Don't wanna add an empty string as the last thing in the array.
    if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0))) {
      // This is the total number of single quotes in the token.
      $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
      // Counts single quotes that are preceded by an odd number of backslashes, 
      // which means they're escaped quotes.
      $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
      $unescaped_quotes = $total_quotes - $escaped_quotes;
      // If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
      if (($unescaped_quotes % 2) == 0) {
        // It's a complete sql statement.
        $output[] = $tokens[$i];
        // save memory.
        $tokens[$i] = "";
      } else {
        // incomplete sql statement. keep adding tokens until we have a complete one.
        // $temp will hold what we have so far.
        $temp = $tokens[$i] . $delimiter;
        // save memory..
        $tokens[$i] = "";
        // Do we have a complete statement yet? 
        $complete_stmt = false;
        for ($j = $i + 1; (! $complete_stmt && ($j < $token_count)); $j ++) {
          // This is the total number of single quotes in the token.
          $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
          // Counts single quotes that are preceded by an odd number of backslashes, 
          // which means they're escaped quotes.
          $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
          $unescaped_quotes = $total_quotes - $escaped_quotes;
          if (($unescaped_quotes % 2) == 1) {
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
          } else {
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
?>