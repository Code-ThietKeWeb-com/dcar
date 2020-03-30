<?php
/*================================================================================*\
|| 							Name code : backup_file.php 		 		            	  ||
||  				Copyright @2008 by Thai Son - CMS vnTRUST                     ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
$vntModule = new vntModule();

class vntModule
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = "backup";
  var $action = "backup_file";

  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_" . $this->module . ".php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . $this->module . "_ad" . DS . "html" . DS . $this->action . ".tpl");
    $this->skin->assign('LANG', $vnT->lang);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;
    //load pclzip
    $vnT->func->include_libraries('pclzip.pclzip');
    switch ($vnT->input['sub']) {
      case 'backup':
        $this->do_Backup($lang);
      break;
      case 'backup_folder':
        $this->do_Backup_Folder($lang);
      break;
      case 'upload':
        $this->do_Upload($lang);
      break;
      case 'import':
        $this->do_Import($lang);
      break;
      case 'del':
        $this->do_Del($lang);
      break;
      default:
        $nd['f_title'] = $vnT->lang['backup_file'];
        $nd['content'] = $this->do_Manage($lang);
      break;
    }
    $nd['menu'] = $func->getToolbar_Small($this->module, $this->action, $lang);
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }

  /**
   * function do_Backup 
   * Them gioi thieu moi 
   **/
  function do_Backup ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $filename = trim($vnT->input['filename']);
    $file = "../db_backup/data/" . $filename . ".zip";
    $archive = new PclZip($file);
    if ($vnT->input['ck_backup_time']) {
      $dirarray = array();
      $tmp = explode("/", $vnT->input['date_start']);
      $time_start = mktime(0, 0, 0, $tmp[1], $tmp[0], $tmp[2]);
      $tmp1 = explode("/", $vnT->input['date_end']);
      $time_end = mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
      $dirarray = get_folder_backup(DIR_BACKUP, $time_start, $time_end);
      /*echo "<pre>";
			print_r($dirarray);
			echo "</pre>";
			*/
      $v_list = $archive->create($dirarray);
    } else {
      $v_list = $archive->create(DIR_BACKUP);
    }
    if ($v_list == 0) {
      die("Error : " . $archive->errorInfo(true));
    }
    $mess = "Backup file " . $filename . ".zip Success !!!!";
    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  /**
   * function do_Backup 
   *
   **/
  function do_Backup_Folder ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $filename = trim($vnT->input['filename']);
    $file = "../db_backup/data/" . $filename . ".zip";
    $archive = new PclZip($file);
    if (isset($_POST["del_id"]))
      $key = $_POST["del_id"];
 		
		if ($vnT->input['ck_backup_time']) {
      $dirarray = array();
      $tmp = explode("/", $vnT->input['date_start']);
      $time_start = mktime(0, 0, 0, $tmp[1], $tmp[0], $tmp[2]);
      $tmp1 = explode("/", $vnT->input['date_end']);
      $time_end = mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
      
			if (is_array($key)) {
      $list_folder = '';
				foreach ($key as $folder) {
 					$dirarray = get_folder_backup( DIR_BACKUP . "/" . $folder, $time_start, $time_end); 
				} 
			}
		 /*
      echo "<pre>";
			print_r($dirarray);
			echo "</pre>"; 
			*/
      $v_list = $archive->create($dirarray);
    }else{
			if (is_array($key)) {
				$list_folder = '';
				foreach ($key as $folder) {
					$list_folder .= DIR_BACKUP . "/" . $folder . ",";
				}
				$list_folder = substr($list_folder, 0, - 1);
			}
    	$v_list = $archive->create($list_folder);
		}
    if ($v_list == 0) {
      die("Error : " . $archive->errorInfo(true));
    }
    $mess = "Backup file " . $filename . ".zip Success !!!!";
    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  /**
   * function do_Upload 
   * Cap nhat admin
   **/
  function do_Upload ($lang)
  {
    global $vnT, $func, $DB, $conf;
    if (! empty($_FILES['uploadFile']) && ($_FILES['uploadFile']['name'] != "")) {
      $file_name = $_FILES['uploadFile']['name'];
      $file_tmp = $_FILES['uploadFile']['tmp_name'];
      $archive = new PclZip(realpath($file_tmp));
      //extracts the archive, calling filterFileNames prior to extracting any file
      $success = $archive->extract();
      if ($success) {
        $mess = "Retore file " . $filename . " Success !!!!";
      } else {
        $mess = "Retore file " . $filename . " Failt !!!!";
      }
    }
    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  /**
   * function do_Import
   * 
   **/
  function do_Import ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $filename = $vnT->input['f'];
    $file = "../db_backup/data/" . $filename;
    $lastdot = strrpos($file, ".");
    $file_type = chop(strtolower(substr($file, $lastdot + 1)));
    if (file_exists($file)) {
      if ($file_type == "zip") {
        $archive = new PclZip($file);
        //extracts the archive, calling filterFileNames prior to extracting any file
        $success = $archive->extract();
        if ($success) {
          $mess = "Retore file " . $filename . " Success !!!!";
        } else {
          $mess = "Retore file " . $filename . " Failt !!!!";
        }
      } else {
        $mess = "File " . $filename . " Khong hop le !!!!";
      }
    } else {
      $mess = "File " . $filename . " Khong ton tai !!!!";
    }
    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  /**
   * function do_Del 
   * Xoa 1 ... n  gioi thieu 
   **/
  function do_Del ($lang)
  {
    global $func, $DB, $conf, $vnT;
    $filename = $vnT->input['f'];
    $file = "../db_backup/data/" . $filename;
    if (file_exists($file)) {
      @unlink($file);
      $mess = "Xóa File " . $filename . " thành công !!!!";
    } else {
      $mess = "File " . $filename . " Khong ton tai !!!!";
    }
    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  /**
   * function do_Manage() 
   * Quan ly cac gioi thieu
   **/
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $vnT->html->addStyleSheet($vnT->dir_js . "/jquery/css/ui.all.css");
    $vnT->html->addScript($vnT->dir_js . "/jquery/ui.core.js");
    $vnT->html->addScript($vnT->dir_js . "/jquery/ui.datepicker.js");
    $vnT->html->addScriptDeclaration("
	 		$(function() {
				$('#date_start').datepicker({
					changeMonth: true,
					changeYear: true
				});
				
				$('#date_end').datepicker({
					changeMonth: true,
					changeYear: true
				});
			});
		
		");
    $data['date_start'] = date("d/m/Y", (time() - (30 * 24 * 3600)));
    $data['date_end'] = date("d/m/Y");
    $tmpFiles = array();
    $dir = PATH_ROOT . '/db_backup/data';
    if ($handle = opendir($dir)) {
      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && $file != "thumb.db" && $file != "index.html") {
          $lastupdate = filemtime($dir . "/" . $file);
          $tmpFiles[] = array(
            $file , 
            $lastupdate);
        }
      }
    }
    usort($tmpFiles, 'DateCmp');
    foreach ($tmpFiles as $key) {
      $arr_file[] = $key[0];
    }
    for ($i = 0; $i < count($arr_file); $i ++) {
      $entry = $arr_file[$i];
      if (($entry != ".") && ($entry != "..") && ($entry != "index.html")) {
        $datebackup = date("h:i - d/m/Y", filemtime($dir . "/" . $entry));
        if ($f_size = @filesize("{$dir}/{$entry}"))
          $size = $func->format_size($f_size);
        else
          $size = 0;
        $list .= "<tr class='row0'><td >{$datebackup}</td>
					<td >{$entry}</td>
					<td align=center>{$size}</td>
					<td align=center><a href=\"" . $this->linkUrl . "&sub=import&f={$entry}\" onClick=\"return confirm(' Are your sure ? ');\"><img src=\"" . IMAGE_MOD . "/import.gif\" alt=\"Import to database\"></a>&nbsp;&nbsp;&nbsp;<a href=\"../db_backup/data/$entry\"  ><img src=\"" . IMAGE_MOD . "/download.gif\" alt=\"Download file \"></a> </a>				&nbsp;&nbsp;&nbsp;<a href=\"" . $this->linkUrl . "&sub=del&f={$entry}\" onClick=\"return confirm(' Are your sure Del ? ');\" ><img src=\"" . IMAGE_MOD . "/delete.gif\" alt=\"Download file sql\"></a></td></tr>";
      }
    }
    $data['file_backup'] = $list;
    $data['file_name'] = "Backup-" . date("d-m-Y");
    $data['link_backup_folder'] = $this->linkUrl . "&sub=backup_folder";
    $data['link_backup'] = $this->linkUrl . "&sub=backup";
    $data['link_import'] = $this->linkUrl . "&sub=upload";
    //==== list 
    $vnT->html->addStyleSheet("modules/media_ad/css/media.css");
    $cur_folder = ($_GET['folder']) ? $_GET['folder'] : '';
    if ($_GET['folder']) {
      $tmp = array_reverse(explode("/", $_GET['folder']));
      $link_up = ($tmp[1]) ? $this->linkUrl . "&folder=" . $tmp[1] : $this->linkUrl;
    } else {
      $link_up = $this->linkUrl;
    }
    $list_folder = '<tr class="row0">
										<td align="center"> &nbsp; </td>
										<td align="center"><a href="' . $link_up . '"><img width=16  alt="Up" src="' . DIR_IMAGE_MEDIA . '/folderup_32.png"/></a></td>
										<td> <strong>Up</strong> </td>
										<td align="center"> --- </td>
										<td align="center"> --- </td>
									 </tr>';
    $arr_media = getList($cur_folder);
    //folder
    $row = $arr_media['folders'];
    /*echo "<pre>";
				print_r($row);
				echo "</pre>";
		*/
    for ($i = 0; $i < count($row); $i ++) {
      $link_folder = ($_GET['folder']) ? $this->linkUrl . "&folder=" . $_GET['folder'] . "/" . $row[$i]['name'] : $this->linkUrl . "&folder=" . $row[$i]['name'];
      $c_id = ($_GET['folder']) ? $_GET['folder'] . "/" . $row[$i]['name'] : $row[$i]['name'];
      $list_folder .= '<tr class="row0" id="row_' . $c_id . '">
										<td align="center"> <input type="checkbox" name="del_id[]" value="' . $c_id . '" class="checkbox" > </td>
										<td align="center"><a href="' . $link_folder . '"><img  alt="' . $row[$i]['name'] . '" src="' . DIR_IMAGE_MEDIA . '/folder_sm.png"/></a></td>
										<td> <strong>' . $row[$i]['name'] . '</strong> </td>
										<td align="center"> ' . $row[$i]['files'] . ' </td>
										<td align="center"> ' . $row[$i]['size'] . ' </td>
									 </tr>';
    }
    $data['list_folder'] = $list_folder;
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }
  // end class
}
?>