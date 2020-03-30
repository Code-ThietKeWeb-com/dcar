<?php
/*================================================================================*\
|| 							Name code : funtions_config.php 		 			                      # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
define('DIR_MOD', DIR_MODULE . '/config_ad');

 

//==== List_Backup
function List_Backup ($did)
{
  global $func, $DB, $conf, $vnT;
  $text = "";
  if ($did == "1") {
    $text = "<input name=\"cot[auto_backup]\" type=\"radio\" value=\"1\" checked> {$vnT->lang['yes']} 
				<input name=\"cot[auto_backup]\" type=\"radio\" value=\"0\">{$vnT->lang['no']}";
  } else {
    $text = "<input name=\"cot[auto_backup]\" type=\"radio\" value=\"1\" > {$vnT->lang['yes']} 
				<input name=\"cot[auto_backup]\" type=\"radio\" value=\"0\" checked>{$vnT->lang['no']}";
  }
  return $text;
}

//==== List_Backup_Email
function List_Backup_Email ($did)
{
  global $func, $DB, $conf, $vnT;
  $text = "";
  if ($did == "1") {
    $text = "<input name=\"cot[backup_email]\" type=\"radio\" value=\"1\" checked> {$vnT->lang['yes']} 
				<input name=\"cot[backup_email]\" type=\"radio\" value=\"0\">{$vnT->lang['no']}";
  } else {
    $text = "<input name=\"cot[backup_email]\" type=\"radio\" value=\"1\" > {$vnT->lang['yes']} 
				<input name=\"cot[backup_email]\" type=\"radio\" value=\"0\" checked>{$vnT->lang['no']}";
  }
  return $text;
}

//==== List_Cache
function List_Cache ($did)
{
  global $func, $DB, $conf, $vnT;
  $text = "";
  if ($did == "1") {
    $text = "<input name=\"cot[cache]\" type=\"radio\" value=\"1\" checked> {$vnT->lang['yes']} 
				<input name=\"cot[cache]\" type=\"radio\" value=\"0\">{$vnT->lang['no']}";
  } else {
    $text = "<input name=\"cot[cache]\" type=\"radio\" value=\"1\" > {$vnT->lang['yes']} 
					<input name=\"cot[cache]\" type=\"radio\" value=\"0\" checked>{$vnT->lang['no']}";
  }
  return $text;
}

//==== List_Counter
function List_Counter ($did)
{
  global $func, $DB, $conf, $vnT;
  $text = "";
  if ($did == "1") {
    $text = "<input name=\"cot[counter]\" type=\"radio\" value=\"1\" checked> {$vnT->lang['counter_on']} 
				<input name=\"cot[counter]\" type=\"radio\" value=\"0\"> {$vnT->lang['counter_off']}";
  } else {
    $text = "<input name=\"cot[counter]\" type=\"radio\" value=\"1\" > {$vnT->lang['counter_on']} 
					<input name=\"cot[counter]\" type=\"radio\" value=\"0\" checked> {$vnT->lang['counter_off']}";
  }
  return $text;
}

//==== List_Method_Email
function List_Method_Email ($did)
{
  global $func, $DB, $conf, $vnT;
  $text = "<select name = \"cot[method_email]\" class='select' id='method_email' >";
  if ($did == "mail") {
    $text .= "<option value=\"mail\" selected > Mail()</option>";
  } else
    $text .= "<option value=\"mail\" > Mail()</option>";
  if ($did == "smtp") {
    $text .= "<option value=\"smtp\" selected > SMTP </option>";
  } else
    $text .= "<option value=\"smtp\" > SMTP </option>";
  if ($did == "gmail") {
    $text .= "<option value=\"gmail\" selected > Gmail </option>";
  } else
    $text .= "<option value=\"gmail\" > Gmail </option>";
  $text .= "</select>";
  return $text;
}
 

//==== List_Smtp_Type_Encryption
function List_Smtp_Type_Encryption ($did)
{
  global $func, $DB, $conf, $vnT;
	$arr_type = array("none"	=>	"None" , "ssl"	=>	"SSL" , "tls"	=>	"TLS" );
  $text = "<select name = \"cot[smtp_type_encryption]\" id='smtp_type_encryption' class='select' >";
	foreach ($arr_type as $key => $val)
	{
		$selected = ($key==$did) ? " selected" : "" ;
		$text .= "<option value=\"{$key}\" {$selected} >".$val."</option>";	
	}	
  $text .= "</select>";
  return $text;
}



//==== List_Skin
function List_Skin ($did)
{
  global $vnT, $func, $conf, $DB;
  $text = "";
  $path = $conf['rootpath'] . "skins";
  if ($dir = opendir($path)) {
    $text .= "<select name=\"cot[skin]\" class='select' >";
    while (false !== ($file = readdir($dir))) {
		 if ( $file != "index.html" && $file != "." && $file != "..") {
        if ($did == $file)
          $text .= "<option value=\"{$file}\" selected=\"selected\" >($file)</option>";
        else
          $text .= "<option value=\"{$file}\"  >($file)</option>";
      }
    }
    $text .= "</select>";
  }
  return $text;
}

//==== List_Editor
function List_Editor ($did)
{
  global $vnT, $func, $conf, $DB;
  $text = "";
  $path = PATH_PLUGINS . DS . "editors";
  if ($dir = opendir($path)) {
    $text .= "<select name=\"cot[editor]\" class='select' >";
    while (false !== ($file = readdir($dir))) {
      if ( $file != "index.html" && $file != "." && $file != "..") {
				if ($did == $file)
					$text .= "<option value=\"{$file}\" selected=\"selected\" >" . $file . "</option>";
				else
					$text .= "<option value=\"{$file}\" >" . $file . "</option>";
			}
    }
    $text .= "</select>";
  }
  return $text;
}

//==========
function List_Module_Show ($did = "")
{
  global $func, $DB, $conf, $vnT;
  $text = "<select name=\"cot[module]\" class='select' >";
  if ($did == "intro") {
    $text .= "<option value='intro' selected  > Intro </option>";
  } else {
    $text .= "<option value='intro'  > Intro </option>";
  }
  if ($did == "main") {
    $text .= "<option value='main' selected  > " . $vnT->lang['home'] . "</option>";
  } else {
    $text .= "<option value='main'  > " . $vnT->lang['home'] . " </option>";
  }
  if ($did == "about") {
    $text .= "<option value='about' selected  >" . $vnT->lang['about'] . " </option>";
  } else {
    $text .= "<option value='about'  > " . $vnT->lang['about'] . " </option>";
  }
  if ($did == "contact") {
    $text .= "<option value='contact' selected  > " . $vnT->lang['contact'] . " </option>";
  } else {
    $text .= "<option value='contact'  > " . $vnT->lang['contact'] . " </option>";
  }
  if ($did == "page") {
    $text .= "<option value='page' selected  >Trang tĩnh </option>";
  } else {
    $text .= "<option value='page'  > Trang tĩnh </option>";
  }
  $sql = "select * from modules order by id DESC";
  $result = $DB->query($sql);
  while ($row = $DB->fetch_row($result)) {
    $text .= "<option value=\"" . $row['mod_name'] . "\"";
    if ($did == $row['mod_name']) {
      $text .= " selected";
    }
    $text .= ">" . $row['name'] . "</option>\n";
  }
  $text .= "</select>";
  return $text;
}

//==== List_Web_Close
function List_Web_Close ($did)
{
  global $func, $DB, $conf, $vnT;
  $text = "";
  if ($did == "1") {
    $text = "<input name=\"cot[web_close]\" type=\"radio\" value=\"1\" checked> " . $vnT->lang['close_web'] . "
				<input name=\"cot[web_close]\" type=\"radio\" value=\"0\">" . $vnT->lang['open_web'] . "";
  } else {
    $text = "<input name=\"cot[web_close]\" type=\"radio\" value=\"1\" > " . $vnT->lang['close_web'] . " 
				<input name=\"cot[web_close]\" type=\"radio\" value=\"0\" checked>" . $vnT->lang['open_web'] . "";
  }
  return $text;
}
?>