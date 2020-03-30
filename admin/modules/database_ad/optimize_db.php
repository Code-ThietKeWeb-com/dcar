<?php
/*================================================================================*\
|| 							Name code : optimize_db.php 		 			                    			# ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
define('IN_vnT', 1);
require_once ("../../../_config.php");
require_once ("../../../includes/class_db.php");
$DB = new DB();
require_once ("../../includes/admin.class.php");
$func = new Func_Admin();
$conf = $func->fetchDbConfig("config", $conf);
$func->load_language("database");
if (isset($_GET['do']) && $_GET['do'] == "optimize") {
  $total_data = 0;
  $total_idx = 0;
  $total_all = 0;
  $text .= "<center><table width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class='bg_tbl'>
	<tr><td width='50%' align='left' class='row_title1'>Table</td><td class='row_title'>Trạng thái tối ưu</td></tr>";
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
      $text .= "<tr><td align='left' class='row1'>Table &nbsp;" . "<b>" . $row['Name'] . "</b></td><td class='row'><span class='font_err'>Đã được tối ưu</span></td></tr>";
    } else {
      $text .= "<tr><td align='left' class='row1'>Table &nbsp;" . "<b>" . $row['Name'] . "</b></td><td class='row'>Không cần tối ưu</td></tr>";
    }
  }
  $text .= "</table></center>";
  $text .= "<br><center><b>Việc tối ưu dữ liệu đã hòan tất - Giả phóng được <span class='font_err'>" . $total_all . "KB</span></b></center><br>";
} else {
  $text .= "<table border=0 cellpadding=1 cellspacing=1 width=100% class='bg_tbl' >\n";
  $text .= "<tr><td style=\"text-align:left\" class=row_title1><b>Table</b></td>";
  $text .= "<td style=\"text-align:center\" class=row_title><b>" . $vnT->lang['date_length'] . "</b></td>";
  $text .= "<td style=\"text-align:center\" class=row_title><b>" . $vnT->lang['status'] . "</b></td>";
  $text .= "<td style=\"text-align:center\" class=row_title><b>" . $vnT->lang['free'] . "</b></td></tr>";
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
      $text .= "<tr bgcolor=White><td style=\"text-align:left\" class=\"row1\">&nbsp;<strong>" . $row['Name'] . "</strong></td>";
      $text .= "<td style=\"text-align:center\" class=\"row\">" . $total . "KB</td>";
      $text .= "<td style=\"text-align:center\" class=\"row\">" . $vnT->lang['not_optimize'] . "</td>";
      $text .= "<td style=\"text-align:center\" class=\"row\">0" . "KB</td></tr>";
    } else {
      $text .= "<tr><td style=\"text-align:left\" class=\"row1\">&nbsp;<strong>" . $row['Name'] . "</strong></td>";
      $text .= "<td style=\"text-align:center\" class=\"row\">" . $total . "KB</td>";
      $text .= "<td style=\"text-align:center\" class=\"row\"><span class='font_err'>" . $vnT->lang['need_optimize'] . "</span></td>";
      $text .= "<td style=\"text-align:center\" class=\"row\"><span class='font_err'>" . $gain . "KB</span></td></tr>";
    }
    $i ++;
  }
  $text .= "</table></center>";
  if ($total_all == 0) {
    $text .= "<br><div align='center'><b>Không cần tối ưu</b></div>\n";
  } else {
    $text .= "<br><div align='center'><b><a href='?mod=database&act=database&do=optimize#remote-tab-2'>Th&#7921;c hi&#7879;n t&#7889;i &#432;u h&#243;a</a></b></div>\n";
  }
}
echo $text;
?>
