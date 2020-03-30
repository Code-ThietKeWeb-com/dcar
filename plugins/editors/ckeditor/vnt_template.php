<?php
define('IN_vnT', 1);
define('DS', DIRECTORY_SEPARATOR);
require_once ("../../../_config.php");
require_once ($conf['rootpath'] . "includes/class_db.php");
$DB = new DB();

$sql = "SELECT * FROM template_xml ORDER BY  display_order ASC, tpl_id DESC  ";
//print "sql = ".$sql."<br>";
$result = $DB->query($sql);
if ($DB->num_rows($result)) {
  $rows = $DB->get_array($result);
  foreach ($rows as $row)
  {
    $title = $row['title'];
    $image = ($row['picture']) ? $row['picture'] : 'template1.gif';
    $description = $row['description'];
    $content_html = $row['content_html'];

    $item = array();
    $item['title'] = $title;
    $item['image'] = $image;
    $item['description'] = $description;
    $item['html'] = $content_html;
    $items[] = $item;
  }
}
$DB->close();
$arr_json = $items;
header('Content-Type: application/json');
echo json_encode($arr_json);
?>