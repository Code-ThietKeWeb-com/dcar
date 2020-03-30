<?php 
$conf['host']='localhost';
$conf['dbuser']='dwingule_vntrust';
$conf['dbpass']='MFSf5gvp';
$conf['dbname']='dwingule_web';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
  $conf['rooturl']='https://' . $_SERVER['HTTP_HOST'].'/';
}else{
  $conf['rooturl']='http://' . $_SERVER['HTTP_HOST'].'/';
} 
$conf['rootpath']= $_SERVER['DOCUMENT_ROOT']."/";
$conf['cmd'] = "vnTRUST";

?>