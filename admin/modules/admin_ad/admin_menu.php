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
  var $action = "admin_menu";

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
        $nd['f_title'] = "Edi";
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
    $text = "<select size=1 id=\"parentid\" name=\"parentid\" >";
    $text .= "<option value=\"0\" selected>-- ROOT --</option>";
    $query = $DB->query("SELECT * FROM admin_menu WHERE parentid=0 order by displayorder");
    while ($row = $DB->fetch_row($query)) {
      $title = $func->HTML($row[$str_title]);
      if ($row['id'] == $did)
        $text .= "<option value=\"{$row['id']}\" selected>{$title}</option>";
      else
        $text .= "<option value=\"{$row['id']}\" >{$title}</option>";
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
      $description = $func->txt_HTML($data['description']);
      // End check
      if (empty($err)) {
        $row['g_name'] = $_POST['g_name'];
        $row['title_vn'] = $title;
        $row['title_en'] = $title;
        $row['module'] = $_POST['module'];
        $row['block'] = $_POST['block'];
        $row['act'] = $_POST['act'];
        $row['sub'] = $_POST['sub'];
        $row['parentid'] = $_POST['parentid'];
        $row['description_vn'] = $description;
        $row['description_en'] = $description;
        $kq = $DB->do_insert("admin_menu", $row);
        if ($kq) {
          $err = $vnT->lang["add_success"];
          $url = $this->linkUrl . "&sub=add";
          flush();
          echo $func->html_redirect($url, $err);
          exit();
        } else {
          echo $DB->debug();
          $err = $vnT->lang["add_failt"];
        }
      }
    }
    $data['list_cat'] = $this->List_Cat($_POST['parentid']);
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
    $str_desc = "description_" . $lang;
    $err = "";
    if (isset($_POST['do_submit'])) {
      $data = $_POST;
      $title = $func->txt_HTML($data['title']);
      $description = $func->txt_HTML($data['description']);
      if (empty($err)) {
        $row['g_name'] = $_POST['g_name'];
        $row[$str_title] = $title;
        $row['module'] = $_POST['module'];
        $row['block'] = $_POST['block'];
        $row['act'] = $_POST['act'];
        $row['sub'] = $_POST['sub'];
        $row['parentid'] = $_POST['parentid'];
        $row[$str_desc] = $description;
        $kq = $DB->do_update("admin_menu", $row, "id=$id");
        if ($kq) {
          //xoa cache
          $func->clear_cache();
          $err = $vnT->lang["edit_success"];
          $url = $this->linkUrl;
          flush();
          echo $func->html_redirect($url, $err);
          exit();
        } else {
          $err = $DB->debug();
        }
      }
    }
    $query = $DB->query("SELECT * FROM admin_menu WHERE id=$id ");
    if ($data = $DB->fetch_row($query)) {
      $data['title'] = $data[$str_title];
      $data['description'] = $data[$str_desc];
    }
    $data['list_cat'] = $this->List_Cat($data['parentid'], $lang);
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
      $query = "DELETE FROM admin_menu WHERE id=-1" . $qr;
      if ($ok = $DB->query($query)) {
        //xoa cache
        $func->clear_cache();
        $mess = $vnT->lang["del_success"];
      } else
        $mess = $vnT->lang["del_failt"];
      $url = $this->linkUrl;
      flush();
      echo $func->html_redirect($url, $mess);
      exit();
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
    $output['check_box'] = "<input type=\"checkbox\" name=\"del_id[]\" value=\"{$id}\" class=\"checkbox\" onclick=\"select_row('{$row_id}')\">";
    $link_edit = $this->linkUrl . "&sub=edit&id={$id}";
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id={$id}')";
    $output['order'] = $row['ext'] . "<input name=\"txt_Order[{$id}]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['displayorder']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";
    $output['g_name'] = $row['g_name'];
    $output['title'] = $row['ext'] . "<strong><a href=\"{$link_edit}\">" . $func->HTML($row[$str_title]) . "</a></strong>";
    $output['mod'] = (! empty($row['block'])) ? $row['block'] : $row['module'];
    $output['act'] = $row['act'];
    $output['sub'] = $row['sub'];
    if ($row['display'] == 1) {
      $display = "<img src=\"{$vnT->dir_images}/dispay.gif\"   />";
    } else {
      $display = "<img src=\"{$vnT->dir_images}/nodispay.gif\"    />";
    }
    $output['action'] = "
		<input name=\"h_id[]\" type=\"hidden\" value=\"{$id}\" />
		<a href=\"{$link_edit}\"><img src=\"{$vnT->dir_images}/edit.gif\"  alt=\"Edit \"></a>&nbsp;	
		{$display} &nbsp;
	 	<a href=\"{$link_del}\"><img src=\"{$vnT->dir_images}/delete.gif\"  alt=\"Delete \"></a>";
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
              $ok = $DB->do_update("admin_menu", $dup, "id={$h_id[$i]}");
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
              $ok = $DB->do_update("admin_menu", $dup, "id={$h_id[$i]}");
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
              $ok = $DB->do_update("admin_menu", $dup, "id={$h_id[$i]}");
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
    $query = $DB->query("SELECT * FROM admin_menu where parentid=0   ");
    $totals = $DB->num_rows($query);
    $n = 20;
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages)
      $p = $num_pages;
    if ($p < 1)
      $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $sql = "SELECT * FROM admin_menu where parentid=0  ORDER BY  displayorder  LIMIT $start,$n";
    //	print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result)) {
      $table_list .= '';
      while ($row = $DB->fetch_row($result)) {
        $row['ext'] = "";
        $row_info = $this->render_row($row, $lang);
        $table_list .= $this->html_row($row_info);
        $n = 1;
        $table_list .= $this->Row_Sub($row['id'], $n, $lang);
      }
    } else {
      $table_list .= '<tr>
	  				<td  align="center" class="row" colspan=5 ><span class="font_err">' . $vnT->lang['no_have_menu'] . '</span></td>
					</tr>';
    }
    $data['table_list'] = $table_list;
    $data['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $data['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $data['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
    $data['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&ext=' . $ext_page . '\')">';
    $data['totals'] = $totals;
    $data['err'] = $err;
    $data['nav'] = $nav;
    return $this->html_manage($data);
  }

  //===========List sub=========
  function Row_Sub ($cid, $n, $lang)
  {
    global $vnT, $func, $DB, $conf;
    $textout = "";
    $space = "&nbsp;&nbsp;&nbsp;&nbsp;";
    $n1 = $n;
    $sql = "SELECT * FROM admin_menu WHERE parentid='{$cid}'  ORDER BY displayorder ";
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
            <td width="79%" align="left"><input name="g_name" type="text" size="50" maxlength="250" value="{$data['g_name']}"  >	</td>
          </tr>
          <tr>
            <td align="right">Title : </td>
            <td  align="left"><input name="title" type="text" size="50" maxlength="250" value="{$data['title']}"  >
					</td>
          </tr>
					
					<tr>
            <td align="right">Module : </td>
            <td  align="left"><input name="module" type="text" size="50" maxlength="250" value="{$data['module']}"  >
					</td>
          </tr>
					<tr>
            <td align="right">Block : </td>
            <td  align="left"><input name="block" type="text" size="50" maxlength="250" value="{$data['block']}"  >
					</td>
          </tr>
		  
          <tr>
              <td align="right">Act : </td>
              <td  align="left"><input name="act" type="text" size="50" maxlength="250" value="{$data['act']}" ></td>
          </tr>
          <tr>
            <td align="right">Sub : </td>
            <td  align="left"><input name="sub" type="text" size="50" maxlength="250" value="{$data['sub']}" ></td>
         </tr>
					
          <tr>
            <td  align="right">Description: </td>
            <td  align="left">	<textarea name="description" cols="50" rows="3" class="textarea">{$data['description']}</textarea>			</td>
          </tr>
          <tr>
            <td  align="right">Menu Parent : </td>
            <td  align="left">{$data['list_cat']}</td>
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
    global $func, $vnT, $conf;
    return <<<EOF
{$data['err']}
<table width="100%"  border="0" align="center" cellspacing="0" cellpadding="0" class="tableborder">
  <tr>
    <td ><strong>Tổng số  : </strong><span class="font_err"><strong>{$data['totals']}</strong></span></td>
  </tr>
</table>
<br />
<div class="box-manage">
<form id="manage" name="manage" method="post" action="{$data['link_action']}">
<div class="nav-action nav-top">{$data['button']}</div>
<div class="table-list">
<table cellspacing="1" class="table table-sm table-bordered table-hover " id="table_list">
<thead>
<tr>
		<th width="5%" align="center" ><input type="checkbox" value="all" class="checkbox" name="checkall" id="checkall"/></td>
		<th width="10%" align="center" >Thứ tự</th>
		<th width="15%" align="left" >Group name</th>
		<th width="30%" align="center" >Tiêu đề</th>
		<th width="10%" align="center" >mod,Block</th>
    <th width="7%" align="center" >Act</th>
    <th width="8%" align="center" >Sub</th>    
		<th width="10%"  align="center" >Action</th>
		</tr>
</thead>
<tbody>

{$data['table_list']}

</tbody>
</table>
</div>
<div class="nav-action nav-bottom">{$data['button']}</div>
 
<input type="hidden" name="do_action" id="do_action" value="" >
</form>
</div>
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1">
  <tr>
    <td  height="30">{$data['nav']}</td>
  </tr>
</table>
<br />
EOF;
  }

  //=====html_row
  function html_row ($data)
  {
    return <<<EOF
<tr class="row0" id="{$data['row_id']}"> 
	<td align="center" class="row1">{$data['check_box']}</td>
	<td align="center" class="row">{$data['order']}</td>
	<td align="center" class="row">{$data['g_name']}</td>
	<td align="left" class="row">{$data['title']}</td>
	<td align="center" class="row">{$data['mod']}</td>
	<td align="center" class="row">{$data['act']}</td>
	<td align="center" class="row">{$data['sub']}</td>
	<td align="center" class="row">{$data['action']}</td>
</tr>
EOF;
  }
  // end class
}
$vntModule = new vntModule();
?>