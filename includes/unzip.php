<?

function unzip ($zip_path)
{
  $ERROR_MSGS[0] = "Unzip th&#224;nh c&#244;ng !";
  $ERROR_MSGS[1] = "Kh&#244;ng t&#236;m th&#7845;y file $zip_path.";
  $ERROR_MSGS[2] = "L&#7895;i khi &#273;&#7885;c file $zip_path .";
  $ERROR = 0;
  if (file_exists($zip_path)) {
    if (($link = zip_open($zip_path))) {
      while (($zip_entry = zip_read($link)) && (! $ERROR)) {
        if (zip_entry_open($link, $zip_entry, "r")) {
          $data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
          $name = zip_entry_name($zip_entry);
          zip_entry_close($zip_entry);
          $file_name = "data/$name";
          $stream = fopen($file_name, "w");
          fwrite($stream, $data);
        } else
          $ERROR = 4;
      }
      zip_close($link);
    } else
      $ERROR = 2;
  } else
    $ERROR = 1;
  return $file_name;
}
?>
