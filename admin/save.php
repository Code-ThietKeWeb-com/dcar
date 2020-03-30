<?php
sleep(0);
define('IN_vnT', 1);
require_once ('../_config.php');
require "../includes/JSON.php";
require_once ('../includes/class_db.php');
$DB = new DB();

//Functions
include ($conf['rootpath'] . 'includes/class_functions.php');
include($conf['rootpath'] . 'includes/admin.class.php');
$func  = new Func_Admin;
 
$url        = $_POST['url'];
$form_type    = $_POST['form_type'];
$id            = $_POST['id'];
$orig_value    = $_POST['orig_value'];
$new_value    = $_POST['new_value'];
$data =  $_POST['data'];

if( $form_type == 'select' ) {
    $orig_option_text    = $_POST['orig_option_text'];
    $new_option_text    = $_POST['new_option_text'];

    $new_value            = $new_option_text;
}

//$text_out = "url = $url | form_type =$form_type | id =$id | orig_value =$orig_value | new_value =$new_value | data=$data  ";


//code here
$arr_data = explode("|",$data);
$table = $arr_data[0];
$field = $arr_data[1];
$where = $arr_data[2];
$lang = $arr_data[3];

if($lang)
{
	$value = $func->update_content($table,$field,$where,$lang,$new_value);
}else{
	$value = $new_value;
}

$sql_update = "UPDATE $table SET $field='$value' WHERE $where ";
$ok = $DB->query($sql_update);
if($ok){
	$text_out = $new_value;
}else{
	$text_out = "Update Error !!!";
}

$json = new Services_JSON( );

print $json->encode( array(
    "is_error"        => false,
    "error_text"    => "Ack!  Something broke!",
    "html"            => $text_out
) );
?>