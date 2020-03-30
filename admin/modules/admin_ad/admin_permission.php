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
  var $action = "admin_permission";

  /**
   * function vntModule ()
   * Khoi tao
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;

    //load skin
    $this->loadSkinModule($this->action);

    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;


    switch ($_GET['sub']) {
      case 'add':
        $nd['f_title'] = "Them";
        $nd['content'] = $this->do_Add($lang);
      break;
      case 'edit':
        $nd['f_title'] = "Edit";
        $nd['content'] = $this->do_Edit($lang);
      break;
      case 'del':
        $this->do_Del($lang);
      break;
      default:
        $nd['f_title'] = "Manage";
        $nd['content'] = $this->do_Manage($lang);
      break;
    }

    $nd['menu'] = $func->getToolbar($this->module, $this->action, $lang);
    $nd['row_lang'] = $func->html_lang("?mod=" . $this->module . "&act=" . $this->action, $lang);
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }
 

  // Ham List_Cat
  function List_Cat ($did = -1, $lang = "vn")
  {
    global $func, $DB, $conf;
    $str_title = "title_" . $lang;
    $text = "<select size=1 id=\"g_name\" name=\"g_name\" >";
    $query = $DB->query("SELECT * FROM admin_menu WHERE parentid=0 order by displayorder");
    while ($row = $DB->fetch_row($query)) {
      $title = $func->HTML($row[$str_title]);
      if ($row['g_name'] == $did)
        $text .= "<option value=\"{$row['g_name']}\" selected>{$title}</option>";
      else
        $text .= "<option value=\"{$row['g_name']}\" >{$title}</option>";
    }
    $text .= "</select>";
    return $text;
  }

  //================ do_Add
  function do_Add ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $target = '_self';
    if (isset($_POST['do_submit'])) {
      $data = $_POST;
      $title = $func->txt_HTML($data['title']);
      // End check
      if (empty($err)) {
        $row['g_name'] = $_POST['g_name'];
        $row['title_vn'] = $title;
        $row['title_en'] = $title;
        $row['module'] = $_POST['module'];
        $row['block'] = $_POST['block'];
        $row['act'] = $_POST['act'];
        $row['text_option'] = $_POST['text_option'];
        $kq = $DB->do_insert("admin_permission", $row);
        if ($kq) {
          //xoa cache
          $func->clear_cache();
          $err = $vnT->lang["add_success"];
          $url = $this->linkUrl . "&sub=add";
          $func->html_redirect($url, $err);
        } else {
          echo $DB->debug();
          $err = $vnT->lang["add_failt"];
        }
      }
    }
    $data['list_cat'] = $this->List_Cat($_POST['g_name']);
    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=add";
    return $this->html_add($data);
  }

  //================ do_Edit
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf;
    if ((isset($_GET['id'])) && (is_numeric($_GET['id'])))
      $id = $_GET['id'];
    else
      $id = 0;
    $str_title = "title_" . $lang;
    $err = "";
    if (isset($_POST['do_submit'])) {
      $data = $_POST;
      $title = $func->txt_HTML($data['title']);
      if (empty($err)) {
        $row['g_name'] = $_POST['g_name'];
        $row[$str_title] = $title;
        $row['module'] = $_POST['module'];
        $row['block'] = $_POST['block'];
        $row['act'] = $_POST['act'];
        $row['text_option'] = $_POST['text_option'];
        $kq = $DB->do_update("admin_permission", $row, "id=$id");
        if ($kq) {
          //xoa cache
          $func->clear_cache();
          $err = $vnT->lang["edit_success"];
          $url = $this->linkUrl;
          $func->html_redirect($url, $err);
        } else {
          $err = $vnT->lang["edit_failt"];
        }
      }
    }
    $query = $DB->query("SELECT * FROM admin_permission WHERE id=$id ");
    if ($data = $DB->fetch_row($query)) {
      $data['title'] = $data[$str_title];
    }
    $data['list_cat'] = $this->List_Cat($data['g_name']);
    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=edit&id={$id}";
    return $this->html_add($data);
  }

  //================
  function do_Del ($lang)
  {
    global $func, $DB, $conf, $vnT;
    if ((isset($_GET['id'])) && (is_numeric($_GET['id'])))
      $id = $_GET['id'];
    else
      $id = 0;
    $del = 0;
    $qr = "";
    if ($id != 0) {
      $del = 1;
      $qr = " OR id='{$id}' ";
    }
    if (isset($_POST["del_id"]))
      $key = $_POST["del_id"];
    for ($i = 0; $i < count($key); $i ++) {
      $del = 1;
      $qr .= " OR id='{$key[$i]}' ";
    }
    if ($del) {
      $query = "DELETE FROM admin_permission WHERE id=-1" . $qr;
      if ($ok = $DB->query($query)) {
        //xoa cache
        $func->clear_cache();
        $mess = $vnT->lang["del_success"];
      } else
        $mess = $vnT->lang["del_failt"];
      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    } else
      $this->do_Manage($lang);
  }

  //================
  function render_row ($row_info, $lang)
  {
    global $func, $DB, $conf, $vnT;
    $str_title = "title_" . $lang;
    $row = $row_info;
    // Xu ly tung ROW
    $id = $row['id'];
    $row_id = "row_" . $id;
    $output['row_id'] = $row_id;
    $link_edit = $this->linkUrl . "&sub=edit&id={$id}";
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id={$id}')";
    if ($row['is_sub']) {
      $output['check_box'] = "<input type=\"checkbox\" name=\"del_id[]\" value=\"{$id}\" class=\"checkbox\" >";
      $row['ext'] = "&nbsp;<img src=\"{$vnT->dir_images}/line3.gif\" align=\"absmiddle\"/>&nbsp;";
      $delete = "<a href=\"{$link_del}\"><img src=\"{$vnT->dir_images}/delete.gif\"  alt=\"Delete \"></a>";
    } else {
      $output['check_box'] = "";
      $row['ext'] = "";
      $delete = "";
    }
    $output['order'] = $row['ext'] . "<input name=\"txt_Order[{$id}]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['displayorder']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";
    $output['g_name'] = $row['g_name'];
    $output['title'] = $row['ext'] . "<strong><a href=\"{$link_edit}\">" . $func->HTML($row[$str_title]) . "</a></strong>";
    $output['mod'] = (! empty($row['block'])) ? $row['block'] : $row['module'];
    $output['act'] = $row['act'];
    $output['text_option'] = $row['text_option'];
    if ($row['display'] == 1) {
      $display = "<img src=\"{$vnT->dir_images}/dispay.gif\" width=15  />";
    } else {
      $display = "<img src=\"{$vnT->dir_images}/nodispay.gif\"  width=15 />";
    }
    $output['action'] = "
		<input name=\"h_id[]\" type=\"hidden\" value=\"{$id}\" />
		<a href=\"{$link_edit}\"><img src=\"{$vnT->dir_images}/edit.gif\"  alt=\"Edit \"></a>&nbsp;	
		{$display} &nbsp;
		{$delete}";
    return $output;
  }

  //============
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;
    if ((isset($_GET['p'])) && (is_numeric($_GET['p'])))
      $p = $_GET['p'];
    else
      $p = 1;
      //update
    if (isset($_POST["do_action"])) {
      //xoa cache
      $func->clear_cache();
      if (isset($_POST["del_id"]))
        $h_id = $_POST["del_id"];
      switch ($_POST["do_action"]) {
        case "do_edit":
          {
            $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
            $str_mess = "";
            if (isset($_POST["txt_Order"]))
              $arr_order = $_POST["txt_Order"];
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['displayorder'] = $arr_order[$h_id[$i]];
              $ok = $DB->do_update("admin_permission", $dup, "id={$h_id[$i]}");
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
          }
          ;
        break;
        case "do_hidden":
          {
            $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['display'] = 0;
              $ok = $DB->do_update("admin_permission", $dup, "id={$h_id[$i]}");
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_err($mess);
          }
          ;
        break;
        case "do_display":
          {
            $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['display'] = 1;
              $ok = $DB->do_update("admin_permission", $dup, "id={$h_id[$i]}");
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_err($mess);
          }
          ;
        break;
      }
    }
    $table['link_action'] = $this->linkUrl . "&sub=manage&p=" . $p;
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"all\" class=\"checkbox\" onclick=\"javascript:checkall();\" />|5%|center" , 
      'order' => "Thứ tự |10%|center" , 
      'g_name' => "G name |10%|center" , 
      'title' => "title|20%|left" , 
      'mod' => "mod,Block|10%|center" , 
      'act' => "Act|10%|center" , 
      'text_option' => "Option|15%|center" , 
      'action' => "Action|15%|center");
    $sql = "SELECT * FROM admin_menu where parentid=0  ORDER BY  displayorder ";
    //	print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result)) {
      $row = $DB->get_array($result);
      $stt = 0;
      for ($i = 0; $i < count($row); $i ++) {
        $row_info = $this->render_row($row[$i], $lang);
        $row_field[$stt] = $row_info;
        $row_field[$stt]['stt'] = ($i + 1);
        $row_field[$stt]['row_id'] = "row_" . $row[$i]['id'];
        $row_field[$stt]['ext'] = "";
        //check sub
        $sql_sub = "SELECT * FROM admin_permission where g_name='" . $row[$i]['g_name'] . "'  ORDER BY displayorder ";
        //print "sql_sub = ".$sql_sub."<br>";
        $res_sub = $DB->query($sql_sub);
        if ($DB->num_rows($res_sub)) {
          $row_sub = $DB->get_array($res_sub);
          for ($j = 0; $j < count($row_sub); $j ++) {
            $row_sub[$j]['is_sub'] = 1;
            $stt ++;
            $row_info = $this->render_row($row_sub[$j], $lang);
            $row_field[$stt] = $row_info;
            $row_field[$stt]['stt'] = ($j + 1);
            $row_field[$stt]['row_id'] = "row_" . $row_sub[$j]['id'];
            $row_field[$stt]['ext'] = "";
          }
        }
        $stt ++;
      }
      $table['row'] = $row_field;
    } else {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >Chưa có </div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&ext=' . $ext_page . '\')">';
    $data['table_list'] = $func->ShowTable($table);
    $data['totals'] = $totals;
    $data['err'] = $err;
    $data['nav'] = $nav;
    return $this->html_manage($data);
  }

  //===========List sub=========
  function Row_Sub ($cid, $n, $lang)
  {
    global $func, $DB, $conf;
    $textout = "";
    $space = "&nbsp;&nbsp;&nbsp;&nbsp;";
    $n1 = $n;
    $sql = "SELECT * FROM admin_permission WHERE parentid='{$cid}' ORDER BY displayorder ";
    //	print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    while ($row_sub = $DB->fetch_row($result)) {
      $row_sub['ext'] = "&nbsp;<img src=\"{$vnT->dir_images}/line3.gif\" align=\"absmiddle\"/>";
      for ($k = 1; $k < $n1; $k ++) {
        $width = $space;
        $row_sub['ext'] = $width . "&nbsp;<img src=\"{$vnT->dir_images}/line3.gif\" align=\"absmiddle\"/>";
      }
      $row_info = $this->render_row($row_sub, $lang);
      $textout .= $this->html_row($row_info);
      $n = $n1 + 1;
      $textout .= $this->Row_Sub($row_sub['id'], $n, $lang);
    }
    return $textout;
  }

  //=================Skin===================
  function html_add ($data)
  {
    global $func, $vnT, $conf;
    return <<<EOF
<script language=javascript>
	function checkform(f) {			
		
		var title = f.title.value;
		if (title == '') {
			alert('Plz enter Title');
			f.title.focus();
			return false;
		}
		var menu_link = f.menu_link.value;
		if (menu_link == '') {
			alert('Plz enter menu_link');
			f.menu_link.focus();
			return false;
		}		
		return true;
	}
</script>

      <form action="{$data['link_action']}" method="post" name="news"  onSubmit="return checkform(this);">
        <table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center>
          <tr>
            <td colspan=2 align="center" class="font_err">{$data['err']}</td>
          </tr>	
					 <tr>
            <td width="21%" align="right">Group name : </td>
            <td width="79%" align="left">{$data['list_cat']} 		</td>
          </tr>
          <tr>
            <td width="21%" align="right">Title : </td>
            <td width="79%" align="left"><input name="title" type="text" size="50" maxlength="250" value="{$data['title']}"  >
			</td>
          </tr>
		  
		  <tr>
            <td align="right">Module : </td>
            <td  align="left"><input name="module" type="text" size="50" maxlength="250" value="{$data['module']}" ></td>
          </tr>
		  <tr>
			<tr>
            <td align="right">Block : </td>
            <td  align="left"><input name="block" type="text" size="50" maxlength="250" value="{$data['block']}" ></td>
          </tr>
		  <tr>
            <td  align="right">Act  : </td>
            <td  align="left"><input name="act" type="text" size="20" maxlength="250" value="{$data['act']}"  >
			</td>
          </tr>
				
				<tr>
            <td  align="right">Option  : </td>
            <td  align="left"><textarea name="text_option" cols="50" rows="2">{$data['text_option']}</textarea>
				</td>
          </tr>
	
          <tr align="center">
            <td colspan="2">
			
					<input type="hidden" name="do_submit" value="1" />
					<input type="submit" name="btnSubmit" value="Submit" class="button">
            <input type="reset" name="Submit2" value="Reset" class="button">            
			</td></tr>
        </table>
    </form>
<br>
EOF;
  }

  //=====
  //=====html_manage
  function html_manage ($data)
  {
    return <<<EOF

{$data['err']}
<br />
{$data['table_list']}

<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1">
  <tr>
    <td  height="30">{$data['nav']}</td>
  </tr>
</table>
<br />
EOF;
  }
  // end class
}
$vntModule = new vntModule();
?>