<?php
/*================================================================================*\
|| 							Name code : info_database.php 		 			                    			# ||
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
$text .= "<table border=0 cellpadding=1 cellspacing=1 width=100% class='bg_tbl' >\n";
$text .= "<tr><td style=\"text-align:left\" class=row_title1><b>Table</b></td>";
$text .= "<td style=\"text-align:center\" class=row_title><b>Row</b></td>";
$text .= "<td style=\"text-align:center\" class=row_title><b>Data size</b></td>";
$text .= "<td style=\"text-align:center\" class=row_title><b>Index Size</b></td>";
$text .= "<td style=\"text-align:center\" class=row_title><b>Max Data Size</b></td>";
$text .= "<td style=\"text-align:center\" class=row_title><b>Data free</b></td>";
$text .= "<td style=\"text-align:center\" class=row_title><b>Create Time</b></td>";
$text .= "<td style=\"text-align:center\" class=row_title><b>Update Time</b></td></tr>";
$i = 0;
$result = $DB->query("SHOW TABLE STATUS FROM {$conf['dbname']}");
while ($row = $DB->fetch_row($result)) {
  $total_data = $func->format_size($row['Data_length']);
  $total_idx = $func->format_size($row['Index_length']);
  $max_data = $func->format_size($row['Max_data_length']);
  $data_free = $func->format_size($row['Data_free']);
  $class = ($i % 2 == 0) ? "bgcolor=White" : "";
  $text .= "<tr {$class}><td style=\"text-align:left\" class=\"row1\">&nbsp;<strong>" . $row['Name'] . "</strong></td>";
  $text .= "<td style=\"text-align:center\" class=\"row\">" . $row['Rows'] . "</td>";
  $text .= "<td style=\"text-align:center\" class=\"row\">" . $total_data . "</td>";
  $text .= "<td style=\"text-align:center\" class=\"row\">" . $total_idx . "</td>";
  $text .= "<td style=\"text-align:center\" class=\"row\">" . $max_data . "</td>";
  $text .= "<td style=\"text-align:center\" class=\"row\">" . $data_free . "</td>";
  $text .= "<td style=\"text-align:center\" class=\"row\">" . $row['Create_time'] . "</td>";
  $text .= "<td style=\"text-align:center\" class=\"row\">" . $row['Update_time'] . "</td></tr>";
  $i ++;
}
$text .= "</table></center>";
echo $text;
?>
