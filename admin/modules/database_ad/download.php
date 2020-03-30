<?php
define('IN_vnT', 1);
require_once ('../../../_config.php');
$f = $_GET['f'];
$filename = $conf['rootpath'] . "db_backup/exports/" . $conf['dbname'] . "." . $f . ".sql";
if (file_exists($filename)) {
  $fname = $conf['dbname'] . "." . $f;
  @ob_start();
  @ob_implicit_flush(0);
  header('Content-Type: text/x-delimtext; name="' . $fname . '.sql.gz"');
  header('Content-disposition: attachment; filename=' . $fname . '.sql.gz');
  header("Pragma: no-cache");
  header("Expires: 0");
  echo @readfile($filename);
  $gzip_contents = ob_get_contents();
  ob_end_clean();
  $gzip_size = strlen($gzip_contents);
  $gzip_crc = crc32($gzip_contents);
  $gzip_contents = gzcompress($gzip_contents, 9);
  $gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);
  echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
  echo $gzip_contents;
  echo pack('V', $gzip_crc);
  echo pack('V', $gzip_size);
} else {
  die("Not found !!! ");
}
?>