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
    //load skins	
    $this->skin = new XiTemplate(DIR_MODULE . DS . "database_ad" . DS . "html" . DS . "database.tpl");
    $this->skin->assign('LANG', $vnT->lang);
    $this->linkUrl = "?mod=database&act=database";
    $nd['f_title'] = $vnT->lang['manage_database'];
    $nd['content'] = $this->do_Manage();
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }

  //=================Functions===============
  function do_Manage ()
  {
    global $func, $DB, $conf, $vnT;
    $vnT->html->addStyleSheet($vnT->dir_js . "/jquery_tab/ui.tabs.css");
    $vnT->html->addScript($vnT->dir_js . "/jquery_tab/ui.core.js");
    $vnT->html->addScript($vnT->dir_js . "/jquery_tab/ui.tabs.js");
    $vnT->html->addScriptDeclaration("$(function() {	 
		$('#container-1 > ul').tabs();
	});");
    if (isset($_GET['do']) && $_GET['do'] == "submit") {
    }
    $data['info_database'] = $this->infoDatabase();
    $data['optimize_db'] = $this->optimizeDatabase();
    $data['repair_database'] = $this->repairDatabase();
    $data['analyze_database'] = $this->analyzeDatabase();
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }

  //=== infoDatabase
  function infoDatabase ()
  {
    global $func, $DB, $conf, $vnT;
    $text .= "<table border=0 cellpadding=1 cellspacing=1 width=100% class='adminlist' ><tbody>\n";
    $text .= "<tr class=row_title ><td style=\"text-align:left\" ><b>Table</b></td>";
    $text .= "<td style=\"text-align:center\" ><b>Row</b></td>";
    $text .= "<td style=\"text-align:center\" ><b>Data size</b></td>";
    $text .= "<td style=\"text-align:center\" ><b>Index Size</b></td>";
    $text .= "<td style=\"text-align:center\" ><b>Max Data Size</b></td>";
    $text .= "<td style=\"text-align:center\" ><b>Data free</b></td>";
    $text .= "<td style=\"text-align:center\" ><b>Create Time</b></td>";
    $text .= "<td style=\"text-align:center\" ><b>Update Time</b></td></tr>";
    $i = 0;
    $total_size = 0;
    $result = $DB->query("SHOW TABLE STATUS FROM {$conf['dbname']}");
    while ($row = $DB->fetch_row($result)) {
      $total_data = $func->format_size($row['Data_length']);
      $total_idx = $func->format_size($row['Index_length']);
      $max_data = $func->format_size($row['Max_data_length']);
      $data_free = $func->format_size($row['Data_free']);
      $total_size += $row['Data_length'];
      $class = ($i % 2 == 0) ? "class='row1'" : "class='row0'";
      $text .= "<tr {$class}><td style=\"text-align:left\" >&nbsp;<strong>" . $row['Name'] . "</strong></td>";
      $text .= "<td style=\"text-align:center\" >" . $row['Rows'] . "</td>";
      $text .= "<td style=\"text-align:center\" >" . $total_data . "</td>";
      $text .= "<td style=\"text-align:center\" >" . $total_idx . "</td>";
      $text .= "<td style=\"text-align:center\" >" . $max_data . "</td>";
      $text .= "<td style=\"text-align:center\" >" . $data_free . "</td>";
      $text .= "<td style=\"text-align:center\" >" . $row['Create_time'] . "</td>";
      $text .= "<td style=\"text-align:center\" >" . $row['Update_time'] . "</td></tr>";
      $i ++;
    }
    $text .= "</tbody></table></center>";
    $text .= "<b class=font_err>Total Data Size: " . $func->format_size($total_size) . "</b>";
    return $text;
  }

  //=== optimizeDatabase
  function optimizeDatabase ()
  {
    global $func, $DB, $conf, $vnT;
    if (isset($_GET['do']) && $_GET['do'] == "optimize") {
      $total_data = 0;
      $total_idx = 0;
      $total_all = 0;
      $text .= "<center><table width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class='adminlist'><tbody>
		<tr class='row_title'><td width='50%' align='left' >Table</td><td >Trạng thái tối ưu</td></tr>";
      $result = $DB->query("SHOW TABLE STATUS FROM {$conf['dbname']}");
      while ($row = $DB->fetch_row($result)) {
        $total_data = $row['Data_length'];
        $total_idx = $row['Index_length'];
        $total = $total_data + $total_idx;
        $total = $total / 1024;
        $total = round($total, 3);
        $gain = $row['Data_free'];
        $gain = $gain / 1024;
        $gain = round($gain, 3);
        $total_all += $gain;
        if ($gain != 0) {
          $result1 = $DB->query("OPTIMIZE TABLE {$row['Name']}");
          $text .= "<tr class='row1'><td align='left' >Table &nbsp;" . "<b>" . $row['Name'] . "</b></td><td class='row'><span class='font_err'>Đã được tối ưu</span></td></tr>";
        } else {
          $text .= "<tr class='row0'><td align='left' >Table &nbsp;" . "<b>" . $row['Name'] . "</b></td><td class='row'>Không cần tối ưu</td></tr>";
        }
      }
      $text .= "</tbody></table></center>";
      $text .= "<br><center><b>Việc tối ưu dữ liệu đã hòan tất - Giả phóng được <span class='font_err'>" . $total_all . "KB</span></b></center><br>";
    } else {
      $text .= "<table border=0 cellpadding=1 cellspacing=1 width=100% class='adminlist' ><tbody>\n";
      $text .= "<tr class=row_title><td style=\"text-align:left\" ><b>Table</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>" . $vnT->lang['date_length'] . "</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>" . $vnT->lang['status'] . "</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>" . $vnT->lang['free'] . "</b></td></tr>";
      $i = 0;
      $result = $DB->query("SHOW TABLE STATUS FROM {$conf['dbname']}");
      while ($row = $DB->fetch_row($result)) {
        $total_data = $row['Data_length'];
        $total_idx = $row['Index_length'];
        $total = $total_data + $total_idx;
        $total = $total / 1024;
        $total = round($total, 3);
        $gain = $row['Data_free'];
        $gain = $gain / 1024;
        $gain = round($gain, 3);
        $total_all += $gain;
        if ($gain == 0) {
          $text .= "<tr class=\"row0\" ><td style=\"text-align:left\" >&nbsp;<strong>" . $row['Name'] . "</strong></td>";
          $text .= "<td style=\"text-align:center\" >" . $total . "KB</td>";
          $text .= "<td style=\"text-align:center\" >" . $vnT->lang['not_optimize'] . "</td>";
          $text .= "<td style=\"text-align:center\" >0" . "KB</td></tr>";
        } else {
          $text .= "<tr class=\"row1\"><td style=\"text-align:left\" >&nbsp;<strong>" . $row['Name'] . "</strong></td>";
          $text .= "<td style=\"text-align:center\" >" . $total . "KB</td>";
          $text .= "<td style=\"text-align:center\" ><span class='font_err'>" . $vnT->lang['need_optimize'] . "</span></td>";
          $text .= "<td style=\"text-align:center\" ><span class='font_err'>" . $gain . "KB</span></td></tr>";
        }
        $i ++;
      }
      $text .= "</tbody></table></center>";
      if ($total_all == 0) {
        $text .= "<br><div align='center'><b>Không cần tối ưu</b></div>\n";
      } else {
        $text .= "<br><div align='center'><b><a href='" . $this->linkUrl . "&do=optimize#optimize_db'>Thực hiện tối ưu hóa</a></b></div>\n";
      }
    }
    return $text;
  }

  //=== repairDatabase
  function repairDatabase ()
  {
    global $func, $DB, $conf, $vnT;
    if (isset($_GET['do']) && $_GET['do'] == "repair") {
      $text .= "<table border=0 cellpadding=1 cellspacing=1 width=100% class='adminlist'><tbody>\n";
      $text .= "<tr class=row_title ><td style=\"text-align:left\" ><b>Table</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>Op</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>Msg_type</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>Msg_text</b></td></tr>";
      $i = 0;
      $result = $DB->query("SHOW TABLE STATUS FROM {$conf['dbname']}");
      while ($row = $DB->fetch_row($result)) {
        $result1 = $DB->query("REPAIR TABLE {$row['Name']}");
        $r = $DB->fetch_row($result1);
        $class = ($i % 2 == 0) ? "class='row1'" : "class='row0'";
        $text .= "<tr {$class}><td style=\"text-align:left\" >&nbsp;<strong>" . $row['Name'] . "</strong></td>";
        $text .= "<td style=\"text-align:center\" >" . $r['Op'] . "</td>";
        $text .= "<td style=\"text-align:center\" >" . $r['Msg_type'] . "</td>";
        $text .= "<td style=\"text-align:center\" >" . $r['Msg_text'] . "</td></tr>";
        $i ++;
      }
      $text .= "</tbody></table></center>";
      $text .= "<br><center><b>Việc repair dữ liệu đã hòan tất </b></center><br>";
    } else {
      $text .= "<table border=0 cellpadding=1 cellspacing=1 width=100% class='adminlist'><tbody>\n";
      $text .= "<tr class=row_title><td style=\"text-align:left\"><b>Table</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>Row</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>Data size</b></td>";
      $i = 0;
      $result = $DB->query("SHOW TABLE STATUS FROM {$conf['dbname']}");
      while ($row = $DB->fetch_row($result)) {
        $total_data = $func->format_size($row['Data_length']);
        $total_idx = $func->format_size($row['Index_length']);
        $max_data = $func->format_size($row['Max_data_length']);
        $data_free = $func->format_size($row['Data_free']);
        $class = ($i % 2 == 0) ? "class='row1'" : "class='row0'";
        $text .= "<tr {$class}><td style=\"text-align:left\" >&nbsp;<strong>" . $row['Name'] . "</strong></td>";
        $text .= "<td style=\"text-align:center\" >" . $row['Rows'] . "</td>";
        $text .= "<td style=\"text-align:center\" >" . $total_data . "</td></tr>";
        $i ++;
      }
      $text .= "</tbody></table></center>";
      $text .= "<br><div align='center'><b><a href='" . $this->linkUrl . "&do=repair#repair_database'>Repair database</a></b></div>\n";
    }
    return $text;
  }

  //=== analyzeDatabase
  function analyzeDatabase ()
  {
    global $func, $DB, $conf, $vnT;
    if (isset($_GET['do']) && $_GET['do'] == "analyze") {
      $text .= "<table border=0 cellpadding=1 cellspacing=1 width=100% class='adminlist'><tbody>\n";
      $text .= "<tr class=row_title ><td style=\"text-align:left\" ><b>Table</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>Op</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>Msg_type</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>Msg_text</b></td></tr>";
      $i = 0;
      $result = $DB->query("SHOW TABLE STATUS FROM {$conf['dbname']}");
      while ($row = $DB->fetch_row($result)) {
        $result1 = $DB->query("ANALYZE TABLE {$row['Name']}");
        $r = $DB->fetch_row($result1);
        $class = ($i % 2 == 0) ? "class='row1'" : "class='row0'";
        $text .= "<tr {$class}><td style=\"text-align:left\" >&nbsp;<strong>" . $row['Name'] . "</strong></td>";
        $text .= "<td style=\"text-align:center\" >" . $r['Op'] . "</td>";
        $text .= "<td style=\"text-align:center\" >" . $r['Msg_type'] . "</td>";
        $text .= "<td style=\"text-align:center\" >" . $r['Msg_text'] . "</td></tr>";
        $i ++;
      }
      $text .= "</tbody></table></center>";
      $text .= "<br><center><b>Việc analyze dữ liệu đã hòan tất </b></center><br>";
    } else {
      $text .= "<table border=0 cellpadding=1 cellspacing=1 width=100% class='adminlist'><tbody>\n";
      $text .= "<tr class=row_title ><td style=\"text-align:left\" ><b>Table</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>Row</b></td>";
      $text .= "<td style=\"text-align:center\" ><b>Data size</b></td>";
      $i = 0;
      $result = $DB->query("SHOW TABLE STATUS FROM {$conf['dbname']}");
      while ($row = $DB->fetch_row($result)) {
        $total_data = $func->format_size($row['Data_length']);
        $total_idx = $func->format_size($row['Index_length']);
        $max_data = $func->format_size($row['Max_data_length']);
        $data_free = $func->format_size($row['Data_free']);
        $class = ($i % 2 == 0) ? "class='row1'" : "class='row0'";
        $text .= "<tr {$class}><td style=\"text-align:left\" >&nbsp;<strong>" . $row['Name'] . "</strong></td>";
        $text .= "<td style=\"text-align:center\" >" . $row['Rows'] . "</td>";
        $text .= "<td style=\"text-align:center\" >" . $total_data . "</td></tr>";
        $i ++;
      }
      $text .= "</tbody></table></center>";
      $text .= "<br><div align='center'><b><a href='" . $this->linkUrl . "&do=analyze#analyze_database'>Analyze database</a></b></div>\n";
    }
    return $text;
  }
  // end class
}
?>