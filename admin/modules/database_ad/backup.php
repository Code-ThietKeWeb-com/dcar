<?php
/*================================================================================*\
|| 							Name code : database.php 		 			                    			# ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
$act = new sMain($sub);

class sMain
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";

  function sMain ($sub)
  {
    global $Template, $vnT, $func, $DB;
    require_once ("function_database.php");
    //load skins	
    $this->skin = new XiTemplate(DIR_MODULE . DS . "database_ad" . DS . "html" . DS . "database.tpl");
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign('IMAGE_MOD', "modules/database_ad/images");
    $this->linkUrl = "?mod=database&act=backup";
    switch ($vnT->input['sub']) {
      case 'import_sql':
        $nd['content'] = $this->Do_Import_Sql();
      break;
      case 'import':
        $nd['content'] = $this->Do_Import();
      break;
      case 'download':
        $this->Do_Download();
      break;
      case 'del':
        $nd['content'] = $this->Do_Del();
      break;
      case 'backup':
        $nd['content'] = $this->Do_Backup();
      break;
      case 'backup_table':
        $nd['content'] = $this->Do_Backup_Table();
      break;
      case 'delall':
        $nd['content'] = $this->Do_Delall();
      break;
      case 'backup_img':
        $nd['content'] = $this->Do_Backup_Img();
      break;
      default:
        $nd['f_title'] = $vnT->lang['manage_database'];
        $nd['content'] = $this->do_Manage();
      break;
    }
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }

  //=================Functions===============
  function do_Manage ()
  {
    global $func, $DB, $conf, $vnT;
    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }

    $default_tables = array(
      'admin' , 
      'config');
    $table_select = "<select name=\"db_tables[]\" size=\"10\" multiple class='select' style='width:90%'>\n";
    $result = $DB->query("SHOW tables");
    while ($row = $DB->fetch_array($result)) {
      $table_select .= "<option value=\"" . $row[0] . "\"";
      if (in_array($row[0], $default_tables)) {
        $table_select .= " selected";
      }
      $table_select .= ">" . $row[0] . "</option>\n";
    }
    $table_select .= "</select>\n";
    $data['table_select'] = $table_select;
    $list_arr = array();
    $totals = 0;
    $handle = opendir(PATH_ROOT . '/db_backup/exports');
    while (false !== ($file = readdir($handle))) {
      if ($file != "." && $file != "..") {
        //check if it is a sql file, and it is in the correct format
        $file_info_arr = explode(".", $file);
        $filtype = $file_info_arr[2];
        if ($filtype == "sql") {
          $fileinfo = $file_info_arr[0] . "." . $file_info_arr[1] . ".info";
          $list_arr[$totals]['file'] = DIR_BACKUP . "/" . $file;
          $list_arr[$totals]['info'] = DIR_BACKUP . "/" . $fileinfo;
          $totals ++;
        }
      }
    }
    rsort($list_arr);
    for ($i = 0; $i < $totals; $i ++) {
      $file_info_arr = explode(".", $list_arr[$i]['file']);
      $fileinfo = $list_arr[$i]['info'];
      $f = fopen($fileinfo, "r");
      $fullinfo = fgets($f, 4096);
      fclose($f);
      $info_arr = explode("|", $fullinfo);
      $size = $info_arr[2];
      $s1 = ($size - ($size % 1024)) / 1024;
      $s2 = (($size * 10 - (($size * 10) % 1024)) / 1024) % 10;
      $size = $s1 . "." . $s2;
      $d = getdate($info_arr[0]);
      $time = "{$d['hours']}h{$d['minutes']}' , {$d['mday']}/{$d['mon']}/{$d['year']}";
      $list .= "<tr class='row0'><td >{$time}</td><td >{$info_arr[1]}</td>
				<td align=center>{$size} KB</td>
				<td align=center><a href=\"" . $this->linkUrl . "&sub=import&csrf_token=".$_SESSION['vnt_csrf_token']."&f={$info_arr[0]}\" onClick=\"return confirm(' Are your sure ? ');\"><img src=\"" . IMAGE_MOD . "/import.gif\" alt=\"Import to database\"></a>&nbsp;&nbsp;&nbsp; </a>				&nbsp;&nbsp;&nbsp;<a href=\"" . $this->linkUrl . "&sub=del&csrf_token=".$_SESSION['vnt_csrf_token']."&f={$info_arr[0]}\"><img src=\"" . IMAGE_MOD . "/delete.gif\" alt=\"Download file sql\"></a></td></tr>";
    }
    $data['file_backup'] = $list;

    $data['link_backup_table'] = $this->linkUrl . "&sub=backup_table";
    $data['link_import_sql'] = $this->linkUrl . "&sub=import_sql";
    $data['link_backup_all'] = $this->linkUrl . "&sub=backup&csrf_token=".$_SESSION['vnt_csrf_token'];
    $data['link_del_all'] = $this->linkUrl . "&sub=delall&csrf_token=".$_SESSION['vnt_csrf_token'] ;
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("backup");
    return $this->skin->text("backup");
  }

  /**
   * function Do_Backup 
   *  
   **/
  function Do_Backup ($auto=0)
  {
    global $conf, $DB, $vnT, $func;

    $time = time();
    $now = mktime();

    if($auto==1){
      $file_sql = DIR_BACKUP . "/" . $conf['dbname'] . "." . $time . ".sql";
      $file_info = DIR_BACKUP . "/" . $conf['dbname'] . "." . $time . ".info";
      do_backup($file_sql);
      $backup_size = @filesize($file_sql);
      // Write Info to file
      $fp = fopen($file_info, "w");
      fwrite($fp, "$time|{$conf['dbname']}|$backup_size");
      fclose($fp);
      // Write  to file last.dat
      $fp = @fopen("../db_backup/last.dat", "w");
      fwrite($fp, $now);
      fclose($fp);
      chmod($file_sql, 0777);
      chmod($file_info, 0777);

      return true ;
    }else{

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $mess =  $vnT->lang['err_csrf_token'] ;
      }else{

        $file_sql = DIR_BACKUP . "/" . $conf['dbname'] . "." . $time . ".sql";
        $file_info = DIR_BACKUP . "/" . $conf['dbname'] . "." . $time . ".info";
        do_backup($file_sql);
        $backup_size = @filesize($file_sql);
        // Write Info to file
        $fp = fopen($file_info, "w");
        fwrite($fp, "$time|{$conf['dbname']}|$backup_size");
        fclose($fp);
        // Write  to file last.dat
        $fp = @fopen("../db_backup/last.dat", "w");
        fwrite($fp, $now);
        fclose($fp);
        chmod($file_sql, 0777);
        chmod($file_info, 0777);

        unset($_SESSION['vnt_csrf_token']);
        //insert adminlog
        $func->insertlog("Backup", $_GET['act'], $id);

        // End write
        $mess = "Backup Successfull !";
      }

      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }

  }

  /**
   * function Do_Backup_Table 
   *  
   **/
  function Do_Backup_Table ()
  {
    global $conf, $DB, $vnT, $func;

    if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
      $mess =  $vnT->lang['err_csrf_token'] ;
    }else{
      $time = time();
      $now = mktime();
      $a_table = $_POST["db_tables"];
      $file_sql = DIR_BACKUP . "/" . $conf['dbname'] . "." . $time . ".sql";
      $file_info = DIR_BACKUP . "/" . $conf['dbname'] . "." . $time . ".info";
      do_backup($file_sql, $a_table);
      $backup_size = @filesize($file_sql);
      // Write Info to file
      $fp = fopen($file_info, "w");
      fwrite($fp, "$time|{$conf['dbname']}|$backup_size");
      fclose($fp);
      // Write  to file last.dat
      $fp = @fopen("../db_backup/last.dat", "w");
      fwrite($fp, $now);
      fclose($fp);
      chmod($file_sql, 0777);
      chmod($file_info, 0777);

      unset($_SESSION['vnt_csrf_token']);
      //insert adminlog
      $func->insertlog("Backup", $_GET['act'], $id);

      $mess = "Backup Successfull";
    }

    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  /**
   * function Do_Delall 
   *  
   **/
  function Do_Del ()
  {
    global $conf, $DB, $vnT, $func;
    if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
      $mess =  $vnT->lang['err_csrf_token'] ;
    }else{
      unset($_SESSION['vnt_csrf_token']);

      $f = $vnT->input['f'];
      if ($f != 0) {
        $err = 0;
        $filenamesql = DIR_BACKUP . "/" . $conf['dbname'] . "." . $f . ".sql";
        $fnamesql = $conf['dbname'] . "." . $f . ".sql";
        $filenameinfo = DIR_BACKUP . "/" . $conf['dbname'] . "." . $f . ".info";
        $fnameinfo = $conf['dbname'] . "." . $f . ".info";
        if (! @unlink($filenamesql))
          $err = 1;
        if (! @unlink($filenameinfo))
          $err = 1;
        //@unlink("db_backup/exports/".$file_info_arr[0].".".$file_info_arr[1].".info");
        if ($err == 1) {
          $mess = "Fille Not Found !!!";
        } else {
          $mess = "Deltele Backup Successfull";
        }

      }
    }

    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  /**
   * function Do_Delall 
   *  
   **/
  function Do_Delall ()
  {
    global $conf, $DB, $vnT, $func;
    if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
      $mess =  $vnT->lang['err_csrf_token'] ;
    }else{
      $handle = @opendir(DIR_BACKUP);
      $i = 0;
      while (false !== ($file = @readdir($handle))) {
        if ($file != "." && $file != "..") {
          //check if it is a sql file, and it is in the correct format
          $i ++;
          $filename = DIR_BACKUP . "/" . $file;
          @unlink($filename);
        }
      }
      $num = ($i - 1) / 2;
      // Write  to file last.dat
      $fp = @fopen("../db_backup/last.dat", "w");
      fwrite($fp, "0");
      fclose($fp);
      $mess = "Deltele All Backup Successfull";
    }

    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  //==============================
  function Do_Import ()
  {
    global $conf, $DB, $vnT, $func;

    if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
      $mess =  $vnT->lang['err_csrf_token'] ;
    }else{
      unset($_SESSION['vnt_csrf_token']);

      //backup 1 ban truoc khi import
      $this->Do_Backup(1);

      $f = $vnT->input['f'];
      $filename = DIR_BACKUP . "/" . $conf['dbname'] . "." . $f . ".sql";
      if (! file_exists($filename))
        $err = "$filename File not found!";
      if (substr($filename, - 4) == '.sql') {
        $sql_query = @fread(@fopen($filename, 'rb'), @filesize($filename));
      } else {
        $sql_query = @gzread(@gzopen($filename, 'rb'), MAXGZIP);
      }
      if (PARSESQL) {
        $sql_query = remove_remarks($sql_query);
        $sql_query = split_sql_file($sql_query, ';');
      } else {
        $sql_query = explode("\n", $sql_query);
      }
      $n = count($sql_query);
      $j = 0;
      for ($i = 0; $i < $n; $i ++) {
        $tmp = trim($sql_query[$i]);
        if (substr($tmp, 0, 1) != '#' && ! empty($tmp))
          if (! $DB->query($tmp)) {
            break;
          }
        if ($i % 100 == 0) {
          echo ++ $j % 10;
          flush();
        }
      }

      //insert adminlog
      $func->insertlog("Import", $_GET['act'], $id);

      $mess = "Import Successfull !";
    }


    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  //==============================
  function Do_Import_Sql ()
  {
    global $conf, $DB, $vnT, $func;





    require_once (PATH_INCLUDE . DS . 'unzip.php');
    if (! empty($_FILES['filesql']) && ($_FILES['filesql']['name'] != "")) {

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $mess =  $vnT->lang['err_csrf_token'] ;
      }else{

        //backup 1 ban truoc khi import
        $this->Do_Backup(1);
        $filesql_name = $_FILES['filesql']['name'];
        $filesql_tmp = $_FILES['filesql']['tmp_name'];
        $filesql_type = $_FILES['filesql']['type'];
        $type_file = strtolower(substr($filesql_name, strrpos($filesql_name, '.') + 1));
        if ($type_file == "gz") {
          $sql_query = @gzread(@gzopen($filesql_tmp, 'rb'), MAXGZIP);
        } else {
          $sql_query = @fread(@fopen($filesql_tmp, 'rb'), @filesize($filesql_tmp));
        }
        if (PARSESQL) {
          $sql_query = remove_remarks($sql_query);
          $sql_query = split_sql_file($sql_query, ';');
        } else {
          $sql_query = explode("\n", $sql_query);
        }
        $n = count($sql_query);
        $j = 0;
        //echo "Running (".ceil($n/100)." blocks of queries): ";
        for ($i = 0; $i < $n; $i ++) {
          $tmp = trim($sql_query[$i]);
          if (substr($tmp, 0, 1) != '#' && ! empty($tmp))
            //echo "<div style='font:10px Tahoma;color:red'>".htmlentities($tmp)."</div>";
            if (! $DB->query($tmp)) {
              //echo "<div style='font:10px Tahoma;color:red'>".htmlentities($tmp)."</div>";
              //echo $DB->debug();
              break;
            }
          if ($i % 100 == 0) {
            echo ++ $j % 10;
            flush();
          }
        }

        //insert adminlog
        $func->insertlog("Import", $_GET['act'], 1);
        $mess = "Import Successfull !";


      }


      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }
  }

  //==============================
  function Do_Download ()
  {
    global $conf, $DB, $vnT, $func;
    require_once (PATH_INCLUDE . DS . 'zip.php');
    $f = $vnT->input['f'];
    if ($f != 0) {
      $filename = DIR_BACKUP . "/" . $conf['dbname'] . "." . $f . ".sql";
      $fname = $conf['dbname'] . "." . $f . ".sql";
      $file_data = get_file_data($filename);
      $zipfile = new zipfile();
      $zipfile->add_file($file_data, $fname);
      $file_data = $zipfile->file();
      $fname_new = $conf['dbname'] . "." . $f . ".zip";
      $file_size = strlen($file_data);
      if (! empty($file_data)) {
        if (get_user_os() == "MAC") {
          @header("Content-Type: application/x-unknown\n");
          @header("Content-Disposition: attachment; filename=\"" . $fname_new . "\"\n");
        } elseif (get_browser_info() == "MSIE") {
          @header("Content-Disposition: attachment; filename=\"" . $fname_new . "\"\n");
          @header("Content-Type: application/x-ms-download\n");
        } elseif (get_browser_info() == "OPERA") {
          @header("Content-Disposition: attachment; filename=\"" . $fname_new . "\"\n");
          @header("Content-Type: application/octetstream\n");
        } else {
          @header("Content-Disposition: attachment; filename=\"" . $fname_new . "\"\n");
          @header("Content-Type: application/octet-stream\n");
        }
        @header("Content-Length: " . $file_size . "\n\n");
        flush();
        echo $file_data;
        exit();
      }
    }
  }
  // end class
}
?>