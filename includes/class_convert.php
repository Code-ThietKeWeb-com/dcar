<?php

class classConvert
{

  /**
   *=============================================================
   * Group Upload and Convert File
   *=============================================================
   */
  function UploadFile ($data, $id = 0)
  {
    // Upload
    global $handle, $vnT, $conf;
    include ($conf['rootpath'] . 'includes/class.upload.php');
    $path = $data['path'] . $data['dir'];
    // ---------- FILE UPLOAD ----------	
    $handle = new upload($data['file']);
    //check type file add folder
    $ID = $this->getID($id);
    $handle->file_dst_name = $ID . "|" . $data['file_name'] . "." . $handle->file_src_name_ext;
    $handle->file_src_name_body = $ID . "_" . $data['file_name'];
    if (in_array($handle->file_src_name_ext, $vnT->arr["Videos"])) {
      $dir_upload = $this->createFolder($path . "Videos");
      $path_upload = $path . "Videos/" . $dir_upload;
    } else 
      if (in_array($handle->file_src_name_ext, $vnT->arr["Audios"])) {
        $dir_upload = $this->createFolder($path . "Audios");
        $path_upload = $path . "Audios/" . $dir_upload;
      } else 
        if (in_array($handle->file_src_name_ext, $vnT->arr["Images"])) {
          $dir_upload = $this->createFolder($path . "Images");
          $path_upload = $path . "Images/" . $dir_upload;
          $handle->image_resize = true;
          $handle->image_ratio_y = true;
          $handle->image_x = 600;
        } else {
          $err = "Can not find type to upload";
        }
    if (empty($err)) {
      $handle->process($path_upload);
      if ($handle->processed) {
        $re['dir_upload'] = $dir_upload;
        $re['path_upload'] = $path_upload;
        $re['file_name'] = $handle->file_dst_name_body;
        $re['link'] = $handle->file_dst_name;
        $re['size'] = $handle->file_src_size;
        $re['extension'] = $handle->file_dst_name_ext;
      }
      //	echo($handle->log);
    }
    $re['err'] = $err;
    return $re;
  }

  function doConvertURL ($url, $file_name, $id = 0)
  {
    global $vnT, $conf;
    //get type file
    $url = str_replace(" ", "%20", $url);
    $lastdot = strrpos($url, ".");
    $extension = trim(strtolower(substr($url, $lastdot + 1)));
    if (in_array($type, $vnT->arr["Audios"])) {
      $path_source = PATHUPLOAD . "Audios/";
      $file_name = $this->getID($id) . "_" . $file_name . ".{$type}";
      $file_source = $this->doGetFile($path_source, $url, $file_name);
      if ($file_source) {
        $source = $path_source . $file_source;
        $destination = $path_source . str_replace($type, "wav", $file_source);
        $destination_mp3 = $path_source . str_replace($type, "mp3", $file_source);
        $audio = new ffmpeg_movie($source);
        $bitrate = $audio->getBitRate();
        $this->convert_audio($source, $destination, $destination_mp3, $bitrate);
        $re["file"] = str_replace($type, "mp3", $file_source);
        $re["file_source"] = $file_source;
        $re["extension"] = $extension;
        $re["format"] = "Audios";
      } else {
        $re["err"] = "Link to file wrong, false to upload item.";
      }
    } else 
      if (in_array($type, $vnT->arr["Videos"])) {
        $path_source = PATHUPLOAD . "Videos/";
        $file_name = $this->getID($id) . "_" . $file_name . ".{$type}";
        $file_source = $this->doGetFile($path_source, $url, $file_name);
        if ($file_source) {
          $source = $path_source . $file_source;
          $dir = explode("/", $file_source);
          $destination = $path_source . $dir[0];
          $file_converted = $this->convert($source, $destination, str_replace("." . $type, "", $file_name));
          $re["file"] = $dir[0] . "/" . $file_converted["file_name"];
          $re["picture"] = $dir[0] . "/thumbs/" . $file_converted["picture"];
          $re["file_source"] = $file_source;
          $re["extension"] = "flv";
          $re["format"] = "Videos";
        } else {
          $re["err"] = "Link to file wrong, false to upload item.";
        }
      } else 
        if (in_array($type, $vnT->arr["Images"])) {
          $path_source = PATHUPLOAD . "Images/";
          $file_name = $this->getID($id) . "_" . $file_name . ".{$type}";
          $file_source = $this->doGetFile($path_source, $url, $file_name);
          if ($file_source) {
            $re["file"] = $file_source;
            $re["file_source"] = $file_source;
            $re["extension"] = $extension;
            $re["format"] = $format;
          } else {
            $re["err"] = "Link to file wrong, false to upload item.";
          }
        } else {
          $re["err"] = "Link to file wrong, false to upload item.";
        }
    return $re;
  }

  function doConvert ($data)
  {
    global $vnT;
    $mess = "";
    $dir_uploaded = $data["dir_upload"];
    $path_upload = $data["path_upload"];
    $file_name = $data["file_name"];
    $file_uploaded = $data["link"];
    $type_uploaded = $data["extension"];
    if (in_array($type_uploaded, $vnT->arr["Videos"])) {
      $source = $path_upload . "/{$file_uploaded}";
      $destination = $path_upload;
      //convert file
      $file_converted = $this->convert($source, $destination, $file_name);
      $file_source = $dir_uploaded . "/" . $file_uploaded;
      $file = $dir_uploaded . "/" . $file_converted["file_name"];
      $picture = $dir_uploaded . "/thumbs/" . $file_converted["picture"];
      $extension = "flv";
      $format = "Videos";
    } else 
      if (in_array($type_uploaded, $vnT->arr["Audios"])) {
        $source = $path_upload . "/{$file_uploaded}";
        $destination = $path_upload . "/{$file_name}.wav";
        $destination_mp3 = $path_upload . "/{$file_name}.mp3";
        $audio = new ffmpeg_movie($source);
        $bitrate = $audio->getBitRate();
        $jf = $this->convert_audio($source, $destination, $destination_mp3, $bitrate);
        $file = $dir_uploaded . "/{$file_name}.mp3";
        $file_source = $dir_uploaded . "/" . $file_uploaded;
        $extension = "mp3";
        $format = "Audios";
      } else 
        if (in_array($type_uploaded, $vnT->arr["Images"])) {
          $file = $dir_uploaded . "/" . $file_uploaded;
          $file_source = $dir_uploaded . "/" . $file_uploaded;
          $extension = $type_uploaded;
          $format = "Images";
        } else {
          $mess = "Upload have been problem, please try again.";
        }
    if (empty($mess)) {
      $re["file"] = $file;
      $re["picture"] = $picture;
      $re["file_source"] = $file_source;
      $re["extension"] = $extension;
      $re["format"] = $format;
    } else {
      $re["err"] = $mess;
    }
    return $re;
  }

  function convert ($source, $destination, $file_name)
  {
    $cmd = "which mencoder";
    $ok_fail = $ok;
    $font = $green;
    $des_thumb = $destination . "/thumbs";
    //create folder thumbs
    if (! is_dir($des_thumb)) {
      @mkdir($des_thumb, 0777);
      @exec("chmod 777 {$des_thumb}");
    }
    $file_name = $file_name . time();
    $des_file = $destination . "/" . $file_name . ".flv";
    exec("$cmd 2>&1", $output);
    foreach ($output as $outputline) {}
    exec("" . $outputline . " {$source} -o {$des_file} -of lavf -oac mp3lame -lameopts abr:br=56 -ovc lavc -lavcopts vcodec=flv:vbitrate=300:mbd=2:mv0:trell:v4mv:cbp:last_pred=3 -lavfopts i_certify_that_my_video_stream_does_not_use_b_frames -srate 22050");
    //create thumb
    $mov = new ffmpeg_movie($source);
    $finalDir = $destination;
    $finalFile = $finalDir . "/thumbs/" . $file_name . ".jpg";
    $frame = "20";
    $ff_frame = @$mov->getFrame($frame);
    if ($ff_frame) {
      $gd_image = $ff_frame->toGDImage();
      if ($gd_image) {
        imagejpeg($gd_image, $finalFile, 20);
        imagedestroy($gd_image);
      }
    }
    $re["file_name"] = $file_name . ".flv";
    $re["picture"] = $file_name . ".jpg";
    return $re;
  }

  function convert_audio ($source, $destination, $destination_mp3, $bitrate)
  {
    $cmd = "which mencoder";
    $ok_fail = $ok;
    $font = $green;
    exec("$cmd 2>&1", $output);
    foreach ($output as $outputline) {}
    exec("ffmpeg -i {$source} {$destination}");
    exec("ffmpeg -i {$destination} {$destination_mp3} -ab 64k");
    @unlink($destination);
  }

  function getYouTube ($dir, $file_name, $id = 0)
  {
    global $conf;
    $path = PATHUPLOAD . "Videos/";
    $file_name = $this->getID($id) . "_" . $this->make_url($file_name);
    $dir_path = $this->createFolder($path);
    $destination = $path . $dir_path;
    $data = file_get_contents($dir);
    preg_match('#/watch_fullscreen\?video_id=([a-z0-9-_]+)&l=([0-9]+)&t=([a-z0-9-_]+)#i', $data, $matches);
    if (ereg($matches[1], $dir)) {
      $download = "http://www.youtube.com/get_video?video_id=$matches[1]&l=$matches[2]&t=$matches[3]";
    }
    copy($download, $destination . "/" . $file_name . ".flv");
    $mov = @new ffmpeg_movie($destination . "/" . $file_name . ".flv");
    $finalDir = $destination . "/thumbs/";
    $finalFile = str_replace(" ", "", $finalDir . $file_name . ".jpg");
    $frame = "20";
    $ff_frame = @$mov->getFrame($frame);
    if ($ff_frame) {
      $gd_image = $ff_frame->toGDImage();
      if ($gd_image) {
        imagejpeg($gd_image, $finalFile, 20);
        imagedestroy($gd_image);
      }
    }
    $re["picture"] = $dir_path . "/thumbs/" . $file_name . ".jpg";
    $re["file_name"] = $dir_path . "/" . $file_name . ".flv";
    return $re;
  }

  function doGetFile ($path_source, $url, $file_name)
  {
    $ok = 0;
    if ($f = fopen($url, "r")) {
      //check folder
      $dir_copy = $this->createFolder($path_source);
      $dst_file = $path_source . $dir_copy . "/" . $file_name;
      @copy($url, $dst_file);
      $ok = 1;
    }
    if ($ok) {
      return $dir_copy . "/" . $file_name;
    } else {
      return false;
    }
  }

  function createFolder ($path)
  {
    //create folder
    $dir1 = date("m_Y", time());
    if (! is_dir($path . "/" . $dir1)) {
      mkdir($path . "/" . $dir1, 0777);
      mkdir($path . "/" . $dir1 . "/thumbs", 0777);
      $ndir = $path . "/" . $dir1;
      $ndir_thumb = $path . "/" . $dir1 . "/thumbs";
      exec("chmod 777 {$ndir}");
      exec("chmod 777 {$ndir_thumb}");
    }
    return $dir1;
  }

  function getID ($id = 0, $name = "")
  {
    global $DB, $func, $conf;
    if ($name == "")
      $name = "exam";
    if ($id) {
      return $id;
    } else {
      $query = $DB->query("SHOW TABLE STATUS LIKE '{$name}'");
      $row = $DB->fetch_row($query);
      if (empty($row["Auto_increment"]))
        $ID = 1;
      else
        $ID = $row["Auto_increment"] + 1;
        //	return $ID;
      return time();
    }
  }

  function make_url ($value)
  {
    $value = str_replace(".", " .", $value);
    $value = str_replace(" ", "_", $value);
    $value = str_replace("'", "", $value);
    $value = str_replace("-", "", $value);
    $value = str_replace("(", "_", $value);
    $value = str_replace(")", "_", $value);
    $value = str_replace("&", "_", $value);
    $value = str_replace("+", "_", $value);
    $value = str_replace("|", "_", $value);
    $value = str_replace("=", "_", $value);
    $value = str_replace("@", "_", $value);
    $value = str_replace("`", "_", $value);
    $value = str_replace("!", "_", $value);
    $value = str_replace("#", "_", $value);
    $value = str_replace("$", "_", $value);
    $value = str_replace("%", "_", $value);
    $value = str_replace("*", "_", $value);
    $value = str_replace("[", "_", $value);
    $value = str_replace("]", "_", $value);
    #---------------------------------a^
    $value = str_replace("ấ", "a", $value);
    $value = str_replace("ầ", "a", $value);
    $value = str_replace("ẩ", "a", $value);
    $value = str_replace("ẫ", "a", $value);
    $value = str_replace("ậ", "a", $value);
    #---------------------------------A^
    $value = str_replace("Ấ", "A", $value);
    $value = str_replace("Ầ", "A", $value);
    $value = str_replace("Ẩ", "A", $value);
    $value = str_replace("Ẫ", "A", $value);
    $value = str_replace("Ậ", "A", $value);
    #---------------------------------a(
    $value = str_replace("ắ", "a", $value);
    $value = str_replace("ằ", "a", $value);
    $value = str_replace("ẳ", "a", $value);
    $value = str_replace("ẵ", "a", $value);
    $value = str_replace("ặ", "a", $value);
    #---------------------------------A(
    $value = str_replace("Ắ", "A", $value);
    $value = str_replace("Ằ", "A", $value);
    $value = str_replace("Ẳ", "A", $value);
    $value = str_replace("Ẵ", "A", $value);
    $value = str_replace("Ặ", "A", $value);
    #---------------------------------a
    $value = str_replace("á", "a", $value);
    $value = str_replace("à", "a", $value);
    $value = str_replace("ả", "a", $value);
    $value = str_replace("ã", "a", $value);
    $value = str_replace("ạ", "a", $value);
    $value = str_replace("â", "a", $value);
    $value = str_replace("ă", "a", $value);
    #---------------------------------A
    $value = str_replace("Á", "A", $value);
    $value = str_replace("À", "A", $value);
    $value = str_replace("Ả", "A", $value);
    $value = str_replace("Ã", "A", $value);
    $value = str_replace("Ạ", "A", $value);
    $value = str_replace("Â", "A", $value);
    $value = str_replace("Ă", "A", $value);
    #---------------------------------e^
    $value = str_replace("ế", "e", $value);
    $value = str_replace("ề", "e", $value);
    $value = str_replace("ể", "e", $value);
    $value = str_replace("ễ", "e", $value);
    $value = str_replace("ệ", "e", $value);
    #---------------------------------E^
    $value = str_replace("Ế", "E", $value);
    $value = str_replace("Ề", "E", $value);
    $value = str_replace("Ể", "E", $value);
    $value = str_replace("Ễ", "E", $value);
    $value = str_replace("Ệ", "E", $value);
    #---------------------------------e
    $value = str_replace("é", "e", $value);
    $value = str_replace("è", "e", $value);
    $value = str_replace("ẻ", "e", $value);
    $value = str_replace("ẽ", "e", $value);
    $value = str_replace("ẹ", "e", $value);
    $value = str_replace("ê", "e", $value);
    #---------------------------------E
    $value = str_replace("É", "E", $value);
    $value = str_replace("È", "E", $value);
    $value = str_replace("Ẻ", "E", $value);
    $value = str_replace("Ẽ", "E", $value);
    $value = str_replace("Ẹ", "E", $value);
    $value = str_replace("Ê", "E", $value);
    #---------------------------------i
    $value = str_replace("í", "i", $value);
    $value = str_replace("ì", "i", $value);
    $value = str_replace("ỉ", "i", $value);
    $value = str_replace("ĩ", "i", $value);
    $value = str_replace("ị", "i", $value);
    #---------------------------------I
    $value = str_replace("Í", "I", $value);
    $value = str_replace("Ì", "I", $value);
    $value = str_replace("Ỉ", "I", $value);
    $value = str_replace("Ĩ", "I", $value);
    $value = str_replace("Ị", "I", $value);
    #---------------------------------o^
    $value = str_replace("ố", "o", $value);
    $value = str_replace("ồ", "o", $value);
    $value = str_replace("ổ", "o", $value);
    $value = str_replace("ỗ", "o", $value);
    $value = str_replace("ộ", "o", $value);
    #---------------------------------O^
    $value = str_replace("Ố", "O", $value);
    $value = str_replace("Ồ", "O", $value);
    $value = str_replace("Ổ", "O", $value);
    $value = str_replace("Ô", "O", $value);
    $value = str_replace("Ộ", "O", $value);
    #---------------------------------o*
    $value = str_replace("ớ", "o", $value);
    $value = str_replace("ờ", "o", $value);
    $value = str_replace("ở", "o", $value);
    $value = str_replace("ỡ", "o", $value);
    $value = str_replace("ợ", "o", $value);
    #---------------------------------O*
    $value = str_replace("Ớ", "O", $value);
    $value = str_replace("Ờ", "O", $value);
    $value = str_replace("Ở", "O", $value);
    $value = str_replace("Ỡ", "O", $value);
    $value = str_replace("Ợ", "O", $value);
    #---------------------------------u*
    $value = str_replace("ứ", "u", $value);
    $value = str_replace("ừ", "u", $value);
    $value = str_replace("ử", "u", $value);
    $value = str_replace("ữ", "u", $value);
    $value = str_replace("ự", "u", $value);
    #---------------------------------U*
    $value = str_replace("Ứ", "U", $value);
    $value = str_replace("Ừ", "U", $value);
    $value = str_replace("Ử", "U", $value);
    $value = str_replace("Ữ", "U", $value);
    $value = str_replace("Ự", "U", $value);
    #---------------------------------y
    $value = str_replace("ý", "y", $value);
    $value = str_replace("ỳ", "y", $value);
    $value = str_replace("ỷ", "y", $value);
    $value = str_replace("ỹ", "y", $value);
    $value = str_replace("ỵ", "y", $value);
    #---------------------------------Y
    $value = str_replace("Ý", "Y", $value);
    $value = str_replace("Ỳ", "Y", $value);
    $value = str_replace("Ỷ", "Y", $value);
    $value = str_replace("Ỹ", "Y", $value);
    $value = str_replace("Ỵ", "Y", $value);
    #---------------------------------DD
    $value = str_replace("Đ", "D", $value);
    $value = str_replace("đ", "d", $value);
    #---------------------------------o
    $value = str_replace("ó", "o", $value);
    $value = str_replace("ò", "o", $value);
    $value = str_replace("ỏ", "o", $value);
    $value = str_replace("õ", "o", $value);
    $value = str_replace("ọ", "o", $value);
    $value = str_replace("ô", "o", $value);
    $value = str_replace("ơ", "o", $value);
    #---------------------------------O
    $value = str_replace("Ó", "O", $value);
    $value = str_replace("Ò", "O", $value);
    $value = str_replace("Ỏ", "O", $value);
    $value = str_replace("Õ", "O", $value);
    $value = str_replace("Ọ", "O", $value);
    $value = str_replace("Ô", "O", $value);
    $value = str_replace("Ơ", "O", $value);
    #---------------------------------u
    $value = str_replace("ú", "u", $value);
    $value = str_replace("ù", "u", $value);
    $value = str_replace("ủ", "u", $value);
    $value = str_replace("ũ", "u", $value);
    $value = str_replace("ụ", "u", $value);
    $value = str_replace("ư", "u", $value);
    #---------------------------------U
    $value = str_replace("Ú", "U", $value);
    $value = str_replace("Ù", "U", $value);
    $value = str_replace("Ủ", "U", $value);
    $value = str_replace("Ũ", "U", $value);
    $value = str_replace("Ụ", "U", $value);
    $value = str_replace("Ư", "U", $value);
    #---------------------------------
    #---------------------------------a^
    $value = str_replace("&#7845;", "a", $value);
    $value = str_replace("&#7847;", "a", $value);
    $value = str_replace("&#7849;", "a", $value);
    $value = str_replace("&#7851;", "a", $value);
    $value = str_replace("&#7853;", "a", $value);
    #---------------------------------A^
    $value = str_replace("&#7844;", "A", $value);
    $value = str_replace("&#7846;", "A", $value);
    $value = str_replace("&#7848;", "A", $value);
    $value = str_replace("&#7850;", "A", $value);
    $value = str_replace("&#7852;", "A", $value);
    $value = str_replace("&#193;", "A", $value);
    #---------------------------------a(
    $value = str_replace("&#7855;", "a", $value);
    $value = str_replace("&#7857;", "a", $value);
    $value = str_replace("&#7859;", "a", $value);
    $value = str_replace("&#7861;", "a", $value);
    $value = str_replace("&#7863;", "a", $value);
    #---------------------------------A(
    $value = str_replace("&#7854;", "A", $value);
    $value = str_replace("&#7856;", "A", $value);
    $value = str_replace("&#7858;", "A", $value);
    $value = str_replace("&#7860;", "A", $value);
    $value = str_replace("&#7862;", "A", $value);
    #---------------------------------a
    $value = str_replace("&#225;", "a", $value);
    $value = str_replace("&#224;", "a", $value);
    $value = str_replace("&#7843;", "a", $value);
    $value = str_replace("&#227;", "a", $value);
    $value = str_replace("&#7841;", "a", $value);
    $value = str_replace("&#226;", "a", $value);
    $value = str_replace("&#259;", "a", $value);
    #---------------------------------A
    $value = str_replace("&#7854;", "A", $value);
    $value = str_replace("&#192;", "A", $value);
    $value = str_replace("&#7842;", "A", $value);
    $value = str_replace("&#195;", "A", $value);
    $value = str_replace("&#7840;", "A", $value);
    $value = str_replace("&#194;", "A", $value);
    $value = str_replace("&#258;", "A", $value);
    #---------------------------------e^
    $value = str_replace("&#7871;", "e", $value);
    $value = str_replace("&#7873;", "e", $value);
    $value = str_replace("&#7875;", "e", $value);
    $value = str_replace("&#7877;", "e", $value);
    $value = str_replace("&#7879;", "e", $value);
    #---------------------------------E^
    $value = str_replace("&#7870;", "E", $value);
    $value = str_replace("&#7872;", "E", $value);
    $value = str_replace("&#7874;", "E", $value);
    $value = str_replace("&#7876;", "E", $value);
    $value = str_replace("&#7878;", "E", $value);
    #---------------------------------e
    $value = str_replace("&#233;", "e", $value);
    $value = str_replace("&#232;", "e", $value);
    $value = str_replace("&#7867;", "e", $value);
    $value = str_replace("&#7869;", "e", $value);
    $value = str_replace("&#7865;", "e", $value);
    $value = str_replace("&#234;", "e", $value);
    #---------------------------------E
    $value = str_replace("&#201;", "E", $value);
    $value = str_replace("&#200;", "E", $value);
    $value = str_replace("&#7866;", "E", $value);
    $value = str_replace("&#7868;", "E", $value);
    $value = str_replace("&#7864;", "E", $value);
    $value = str_replace("&#202;", "E", $value);
    #---------------------------------i
    $value = str_replace("&#237;", "i", $value);
    $value = str_replace("&#236;", "i", $value);
    $value = str_replace("&#7881;", "i", $value);
    $value = str_replace("&#297;", "i", $value);
    $value = str_replace("&#7883;", "i", $value);
    #---------------------------------I
    $value = str_replace("&#205;", "I", $value);
    $value = str_replace("&#204;", "I", $value);
    $value = str_replace("&#7880;", "I", $value);
    $value = str_replace("&#296;", "I", $value);
    $value = str_replace("&#7882;", "I", $value);
    #---------------------------------o^
    $value = str_replace("&#7889;", "o", $value);
    $value = str_replace("&#7891;", "o", $value);
    $value = str_replace("&#7893;", "o", $value);
    $value = str_replace("&#7895;", "o", $value);
    $value = str_replace("&#7897;", "o", $value);
    #---------------------------------O^
    $value = str_replace("&#7888;", "O", $value);
    $value = str_replace("&#7890;", "O", $value);
    $value = str_replace("&#7892;", "O", $value);
    $value = str_replace("&#7894;", "O", $value);
    $value = str_replace("&#212;", "O", $value);
    $value = str_replace("&#7896;", "O", $value);
    #---------------------------------o*
    $value = str_replace("&#7899;", "o", $value);
    $value = str_replace("&#7901;", "o", $value);
    $value = str_replace("&#7903;", "o", $value);
    $value = str_replace("&#7905;", "o", $value);
    $value = str_replace("&#7907;", "o", $value);
    #---------------------------------O*
    $value = str_replace("&#7898;", "O", $value);
    $value = str_replace("&#7900;", "O", $value);
    $value = str_replace("&#7902;", "O", $value);
    $value = str_replace("&#7904;", "O", $value);
    $value = str_replace("&#7906;", "O", $value);
    #---------------------------------u*
    $value = str_replace("&#7913;", "u", $value);
    $value = str_replace("&#7915;", "u", $value);
    $value = str_replace("&#7917;", "u", $value);
    $value = str_replace("&#7919;", "u", $value);
    $value = str_replace("&#7921;", "u", $value);
    #---------------------------------U*
    $value = str_replace("&#7912;", "U", $value);
    $value = str_replace("&#7914;", "U", $value);
    $value = str_replace("&#7916;", "U", $value);
    $value = str_replace("&#7918;", "U", $value);
    $value = str_replace("&#7920;", "U", $value);
    #---------------------------------y
    $value = str_replace("&#253;", "y", $value);
    $value = str_replace("&#7923;", "y", $value);
    $value = str_replace("&#7927;", "y", $value);
    $value = str_replace("&#7929;", "y", $value);
    $value = str_replace("&#7925;", "y", $value);
    #---------------------------------Y
    $value = str_replace("&#221;", "Y", $value);
    $value = str_replace("&#7922;", "Y", $value);
    $value = str_replace("&#7926;", "Y", $value);
    $value = str_replace("&#7928;", "Y", $value);
    $value = str_replace("&#7924;", "Y", $value);
    #---------------------------------DD
    $value = str_replace("&#272;", "D", $value);
    $value = str_replace("&#273;", "d", $value);
    #---------------------------------o
    $value = str_replace("&#243;", "o", $value);
    $value = str_replace("&#242;", "o", $value);
    $value = str_replace("&#7887;", "o", $value);
    $value = str_replace("&#245;", "o", $value);
    $value = str_replace("&#7885;", "o", $value);
    $value = str_replace("&#244;", "o", $value);
    $value = str_replace("&#417;", "o", $value);
    #---------------------------------O
    $value = str_replace("&#211;", "O", $value);
    $value = str_replace("&#210;", "O", $value);
    $value = str_replace("&#7886;", "O", $value);
    $value = str_replace("&#213;", "O", $value);
    $value = str_replace("&#7884;", "O", $value);
    $value = str_replace("&#212;", "O", $value);
    $value = str_replace("&#416;", "O", $value);
    #---------------------------------u
    $value = str_replace("&#250;", "u", $value);
    $value = str_replace("&#249;", "u", $value);
    $value = str_replace("&#7911;", "u", $value);
    $value = str_replace("&#361;", "u", $value);
    $value = str_replace("&#7909;", "u", $value);
    $value = str_replace("&#432;", "u", $value);
    #---------------------------------U
    $value = str_replace("&#218;", "U", $value);
    $value = str_replace("&#217;;", "U", $value);
    $value = str_replace("&#7910;", "U", $value);
    $value = str_replace("&#360;", "U", $value);
    $value = str_replace("&#7908;", "U", $value);
    $value = str_replace("&#431;", "U", $value);
    #---------------------------------
    $value = str_replace("&", "", $value);
    $value = str_replace("%", "", $value);
    $value = str_replace("^", "", $value);
    $value = str_replace("*", "", $value);
    $value = str_replace("@", "", $value);
    $value = str_replace("!", "", $value);
    $value = str_replace(";", "", $value);
    $value = str_replace("`", "", $value);
    $value = str_replace(",", "", $value);
    return $value;
  }
  // end classs
}
?>