<?php
/*================================================================================*\
|| 							Name code : tour.php 		 		            	  ||
||  				Copyright @2008 by Thai Son - CMS vnTRUST                     ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (! defined('IN_vnT'))
{
  die('Hacking attempt!');
}

//load Model
include_once dirname( __FILE__ ) . '/includes/Model.php';

class vntModule extends Model
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = MOD_NAME;
  var $action = MOD_NAME;

  /**
   * function vntModule ()
   * Khoi tao
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    //load skin
    $this->loadSkinModule($this->action);

    $vnT->html->addStyleSheet("modules/" . $this->module . "_ad/css/" . $this->module . ".css");
    $vnT->html->addScript("modules/" . $this->module . "_ad" . "/js/" . $this->module . ".js");

    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $pos = ($vnT->input['pos']) ? $vnT->input['pos'] : $vnT->setting['pos'] ;
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action."&pos=".$pos . "&lang=" . $lang;

    switch ($vnT->input['sub']) {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_lang'];
        $nd['content'] = $this->do_Add($lang);
        break;
      case 'edit':
        $nd['f_title'] = $vnT->lang["edit_lang"];
        $nd['content'] = $this->do_Edit($lang);
        break;
      case 'edit_lang':
        $nd['f_title'] = $vnT->lang['edit_pharse_lang'];
        $nd['content'] = $this->do_Edit_Lang($lang);
        break;
      case 'del':
        $this->do_Del($lang);
        break;
      default:
        $nd['f_title'] = $vnT->lang['manage_lang'];
        $nd['content'] = $this->do_Manage($lang);
        break;
    }

    $nd['menu'] = $func->getToolbar($this->module, $this->action."&pos=".$pos, $lang);
    $nd['row_lang'] = $func->html_lang("?mod=" . $this->module . "&act=" . $this->action."&pos=".$pos, $lang);

    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }

  /**
   * function do_Add
   * Them lang moi
   **/
  function do_Add ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $err = "";
    $data['charset'] = "UTF-8";
    $data['date_format'] = "d/m/Y";
    $data['time_format'] = "H:i A";
    $data['num_format'] = "";
    if ($vnT->input['do_submit']) {
      $data = $_POST;
      $name = $vnT->input['name'];
      $title = $vnT->input['title'];
      $picture =  $vnT->input['picture'];
      $charset = $vnT->input['charset'];
      $date_format = $vnT->input["date_format"];
      $time_format = $vnT->input["time_format"];
      $unit = $vnT->input['unit'];
      $num_format = $vnT->input['num_format'];
      $is_default = $vnT->input['is_default'];

      // Check for Error
      $query = $DB->query("SELECT * FROM language WHERE name='{$name}' ");
      if ($check = $DB->fetch_row($query))
        $err = str_replace("<title>", "Code Name", $vnT->lang['title_exist']);
      // End check

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if (empty($err)) {
        // set default
        if ($is_default == 1) {
          $DB->query("update  language set is_default=0 ");
        }
        $cot['name'] = $name;
        $cot['title'] = $title;
        $cot['picture'] = $picture;
        $cot['charset'] = $charset;
        $cot['date_format'] = $date_format;
        $cot['time_format'] = $time_format;
        $cot['unit'] = $unit;
        $cot['num_format'] = $num_format;
        $cot['is_default'] = $is_default;
        $ok = $DB->do_insert("language", $cot);
        if ($ok) {

          unset($_SESSION['vnt_csrf_token']);
          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $DB->insertid());
          $err = $vnT->lang["add_success"];
          $url = $this->linkUrl;
          $func->html_redirect($url, $err);
        } else {
          $err = $func->html_err($vnT->lang["add_failt"] . $DB->debug());
        }
      }
    }
    $data['link_action'] = $this->linkUrl . "&sub=add";

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data['err'] = $err;
    $data['list_default'] = vnT_HTML::list_yesno("is_default", $data['is_default']);
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
  }

  /**
   * function do_Edit
   * Cap nhat gioi thieu
   **/
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $id = (int) $vnT->input['id'];
    if ($vnT->input['do_submit']) {
      $data = $_POST;
      $name = $vnT->input['name'];
      $picture = $vnT->input['picture'];
      $title = $vnT->input['title'];
      $charset = $vnT->input['charset'];
      $date_format = $vnT->input["date_format"];
      $time_format = $vnT->input["time_format"];
      $unit = $vnT->input['unit'];
      $num_format = $vnT->input['num_format'];
      $is_default = $vnT->input['is_default'];
      // Check for Error
      $query = $DB->query("SELECT * FROM language WHERE name='{$name}' and  lang_id<>$id ");
      if ($check = $DB->fetch_row($query))
        $err = str_replace("<title>", "Code Name", $vnT->lang['title_exist']);
      // End check

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if (empty($err)) {
        // set default
        if ($is_default == 1) {
          $DB->query("update  language set is_default=0 ");
        }
        $cot['name'] = $name;
        $cot['title'] = $title;
        $cot['charset'] = $charset;
        $cot['date_format'] = $date_format;
        $cot['time_format'] = $time_format;
        $cot['unit'] = $unit;
        $cot['num_format'] = $num_format;
        $cot['is_default'] = $is_default;
        $cot['picture'] = $picture;
        $ok = $DB->do_update("language", $cot, "lang_id=$id");
        if ($ok) {

          unset($_SESSION['vnt_csrf_token']);

          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Edit", $_GET['act'], $id);
          $err = $vnT->lang["edit_success"];
          $url = $this->linkUrl;
          $func->html_redirect($url, $err);
        } else {
          $err = $func->html_err($vnT->lang["add_failt"] . $DB->debug());
        }
      }
    }
    $sql = "select * from language where lang_id=$id";
    $result = $DB->query($sql);
    if ($data = $DB->fetch_row($result)) {
      $data['list_default'] = vnT_HTML::list_yesno("is_default", $data['is_default']);


      if ($data['picture']) {
        $data['pic'] = "<img src=\"" . DIR_UPLOAD . "/" . $data['picture'] . "\"  /> <a href=\"javascript:del_picture('picture')\" class=\"del\">Xóa</a>";
        $data['style_upload'] = "style='display:none' ";
      } else {
        $data['pic'] = "";
      }
    }

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data['link_action'] = $this->linkUrl . "&sub=edit&id=$id";
    $data['err'] = $err;
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
  }

  /**
   * function do_Edit_Lang
   *
   **/
  function do_Edit_Lang ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $err = "";
    $name = ($vnT->input['name']) ? $vnT->input['name'] : "vn";
    $type = ($vnT->input['type']) ? $vnT->input['type'] : "global";
    $phrase = ($vnT->input['phrase']) ? $vnT->input['phrase'] : "global";
    if ($type == "global") {
      $FILE_NAME = PATH_ROOT . DS . "language" . DS . $name . DS . $phrase . ".php";
    } else {
      $FILE_NAME = PATH_ROOT . DS . "language" . DS . $name . DS . $type . DS . $phrase . ".php";
    }
    //set default
    if ($vnT->input['btn_SetDefault']) {
      $DB->query("update  language set is_default=0 ");
      $ok = $DB->query("update  language set is_default=1 WHERE name='{$name}' ");
      if ($ok) {
        //xoa cache
        $func->clear_cache();

        //insert adminlog
        $func->insertlog("Set Default", $_GET['act'], $name);
        $err = $vnT->lang["set_default_success"];
        $url = $this->linkUrl . "&sub=edit_lang&name=$name&type=$type&phrase=$phrase";
        $func->html_redirect($url, $err);
      } else {
        $err = $func->html_err($vnT->lang["set_default_failt"]);
      }
    }

    // btnEdit
    if ($vnT->input['btnUpdate'])
    {
      $err ='';
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if(empty($err))
      {
        $file_string = "<?php \n";
        $file_string .= $vnT->input['header'];
        $file_string .= "\nif ( !defined('IN_vnT') )	{ die('Access denied');	} \n";
        $file_string .= "$" . "lang = array ( \n";
        $cot = $_POST['cot'];
        ksort($cot);
        foreach ($cot as $key => $value) {

          $value = str_replace("\r\n", "<br>", $value);
          $value = preg_replace("/'/", "&#39;", $value);
          if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
          }
          $file_string .= "\t'$key'";
          $file_string .= "\t => \t";
          $file_string .= "'$value' ,\n";
          //	echo "key = $key <br>";
          //	echo "value = $value <br>";
        }
        $file_string .= "); \n?>\n";
        if ($FH = @fopen($FILE_NAME, 'w')) {
          fwrite($FH, $file_string, strlen($file_string));
          fclose($FH);

          unset($_SESSION['vnt_csrf_token']);
          $mess = "Update  Successfull !!!";
          //insert adminlog
          $func->insertlog("Edit Phrase", $_GET['act'], $phrase);
        } else {
          $mess = "Cannot write into file <strong>$phrase.php</strong> , please check cmod this file !!!";
        }
        $url = $this->linkUrl . "&sub=edit_lang&name=$name&type=$type&phrase=$phrase";
        flush();
        echo $func->html_redirect($url, $mess);
        exit();

      }
    }
    //echo $FILE_NAME;
    if (file_exists($FILE_NAME)) {
      if (! is_writable($FILE_NAME)) {
        @chmod($FILE_NAME, 0777);
      }
      if ($FH = @fopen($FILE_NAME, 'rb')) {
        $content = @fread($FH, filesize($FILE_NAME));
        @fclose($FH);
      }
      //
      $s_header = strpos($content, "/*");
      $e_header = strpos($content, "*/", $s_header) + strlen("*/");
      $len_header = $e_header - $s_header;
      $header = trim(substr($content, $s_header, $len_header));
      //
      $s_text = strpos($content, "array (") + strlen("array (");
      $e_text = strpos($content, ");", $s_text);
      $len_text = $e_text - $s_text;
      $text = trim(substr($content, $s_text, $len_text));
      //xoa cac comment

      //$text = preg_replace('#(//)(.*?)(\n)#si', '', $text);
      $text = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $text);
      $arr_text = explode("\n", $text);
      asort($arr_text);
      foreach ($arr_text as $value) {
        $arr = explode("=>", $value);
        //nhay cuoi
        $arr[0] = trim($arr[0]);
        $arr[1] = trim($arr[1]);
        $pos_end = strrpos($arr[1], "'");
        $text_value = substr($arr[1], 1, $pos_end - 1);
        $str_find = array(
          "\'" ,
          "<br>");
        $str_replace = array(
          "&#39;" ,
          "\n");
        $var_key  = str_replace("'", "", trim($arr[0]));
        $var_text = str_replace($str_find, $str_replace, trim($text_value));


        if (! empty($arr[0]) ) {
          $row['varname'] = trim($var_key);
          $row['text'] = trim($var_text);
          $this->skin->assign('row', $row);
          $this->skin->parse("edit_phrase.row_lang");
        }
      }
    }
    $data['header'] = $header;
    $data['list_lang'] = $this->List_Lang($name);
    $data['list_type'] = $this->List_Type($type);
    $data['list_pharse'] = $this->List_Phrase($type, $phrase);
    $data['phrase'] = $phrase;


    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;


    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=edit_lang&name=$name";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit_phrase");
    return $this->skin->text("edit_phrase");
  }

  /**
   * function do_Del 
   * Xoa 1 ... n  gioi thieu 
   **/
  function do_Del ($pos, $lang)
  {
    global $func, $DB, $conf, $vnT;
    $id = (int) $vnT->input['id'];
    $ext = $vnT->input["ext"];

    if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
      $mess =  $vnT->lang['err_csrf_token'] ;
    }else{
      unset($_SESSION['vnt_csrf_token']);
      $del = 0;
      $qr = "";
      if ($id != 0) {
        $ids = $id;
      }
      if (isset($vnT->input["del_id"])) {
        $ids = implode(',', $vnT->input["del_id"]);
      }
      $query = "DELETE FROM language WHERE lang_id IN (" . $ids . ") ";
      if ($ok = $DB->query($query)) {
        $mess = $vnT->lang["del_success"];
        $DB->query("DELETE FROM language_content WHERE lang_id IN (" . $ids . ")" );

        //xoa cache
        $func->clear_cache();
      } else
        $mess = $vnT->lang["del_failt"];
    }

    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  /**
   * function render_row
   * list cac record
   **/
  function render_row ($row_info, $lang)
  {
    global $func, $DB, $conf, $vnT;
    $row = $row_info;
    // Xu ly tung ROW
    $id = $row['lang_id'];
    $row_id = "row_" . $id;
    $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id;
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&csrf_token=".$_SESSION['vnt_csrf_token']."&id=" . $id . "')";
    if (empty($row['picture']))
      $picture = "<i>No Image</i>";
    else {
      $picture = '<img src="' . DIR_UPLOAD . '/' . $row['picture'] . '" height=20 />';
    }
    $output['picture'] = $picture;
    $name = $row['name'];
    $output['name'] = "<a href=\"{$link_edit}\"><strong>" . $row['name'] . "</strong></a>";
    $output['title'] = $row['title'];
    $output['edit_block'] = "<a href=\"" . $this->linkUrl . "&sub=edit_lang&name={$name}\">Edit / Translate {$row['title']}</a>";
    if ($row['is_default']) {
      $default = "<img src=\"{$vnT->dir_images}/dispay.gif\"  width=30 />";
    } else {
      $default = "<a href=\"" . $this->linkUrl . "&default_id={$id}\"><img src=\"{$vnT->dir_images}/nodispay.gif\" width=30 /></a>";
    }

    $output['default'] = $default;

    $link_display = $this->linkUrl . $row['ext_link']."&csrf_token=".$_SESSION['vnt_csrf_token'];
    if ($row['display'] == 1) {
      $display = "<a  class='i-display'  href='" . $link_display . "&do_hidden=$id' data-toggle='tooltip' data-placement='top'  title='" . $vnT->lang['click_do_hidden'] . "' ><i class='fa fa-eye' ></i></a>";
    } else {
      $display = "<a class='i-display'  href='" . $link_display . "&do_display=$id'  data-toggle='tooltip' data-placement='top'  title='" . $vnT->lang['click_do_display'] . "' ><i class='fa fa-eye-slash' ></i></a>";
    }


    $output['action'] = '<div class="action-buttons"><input name=h_id[]" type="hidden" value="' . $id . '" />';
    $output['action'] .= '<a href="' . $link_edit . '" class="i-edit" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
    $output['action'] .= $display;
    $output['action'] .= '<a href="' . $link_del . '" class="i-del" ><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
    $output['action'] .= '</div>';

    return $output;
  }

  /**
   * function do_Manage()
   * Quan ly cac gioi thieu
   **/
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;



    //update
    $rs_up = $this->do_ProcessUpdate($lang);
    $err = $rs_up['err'];

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }

    $table['link_action'] = $this->linkUrl;
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" ,
      'name' => "Code name|10%|center" ,
      'picture' => $vnT->lang['picture'] . "|10%|center" ,
      'title' => $vnT->lang['title'] . "|15%|center" ,
      'edit_block' => $vnT->lang['edit_pharse_lang'] . " ||center" ,
      'default' => $vnT->lang['default'] . "|15%|center" ,
      'action' => "Action|10%|center");
    $sql = "SELECT * FROM language ORDER BY lang_id DESC ";
    //print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result)) {
      $row = $DB->get_array($result);
      for ($i = 0; $i < count($row); $i ++) {
        $row_info = $this->render_row($row[$i], $lang);
        $row_field[$i] = $row_info;
        $row_field[$i]['stt'] = ($i + 1);
        $row_field[$i]['row_id'] = "row_" . $row[$i]['lang_id'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_lang'] . "</div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&csrf_token='.$_SESSION['vnt_csrf_token'].'\')">';
    $table['csrf_token'] = $_SESSION['vnt_csrf_token'] ;
    $data['table_list'] = $func->ShowTable($table);

    $data['err'] = $err;
    if (isset($_SESSION['mess']) && $_SESSION['mess'] != '') {
      $data['err'] = $_SESSION['mess'];
      unset($_SESSION['mess']);
    }

    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }

  // end class
}

$vntModule = new vntModule();
?>