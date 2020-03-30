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
  var $action = "adminsession";
  var $month = array(
    1 => "Th&#225;ng 1" ,
    2 => "Th&#225;ng 2" ,
    3 => "Th&#225;ng 3" ,
    4 => "Th&#225;ng 4" ,
    5 => "Th&#225;ng 5" ,
    6 => "Th&#225;ng 6" ,
    7 => "Th&#225;ng 7" ,
    8 => "Th&#225;ng 8" ,
    9 => "Th&#225;ng 9" ,
    10 => "Th&#225;ng 10" ,
    11 => "Th&#225;ng 11" ,
    12 => "Th&#225;ng 12");
  var $time1 = "";
  var $time2 = "";

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


    if ((isset($_GET['id'])) && (is_numeric($_GET['id'])))
      $id = $_GET['id'];
    else
      $id = 0;
    if (! isset($_POST['d2']))
      $_POST['d2'] = date("d");
    if (! isset($_POST['m2']))
      $_POST['m2'] = date("m");
    if (! isset($_POST['y2']))
      $_POST['y2'] = date("Y");
    if (! isset($_POST['d1']))
      $_POST['d1'] = 1;
    if (! isset($_POST['m1']))
      $_POST['m1'] = 1;
    if (! isset($_POST['y1']))
      $_POST['y1'] = 2006;
    $this->time1 = mktime(0, 0, 0, $_POST['m1'], $_POST['d1'], $_POST['y1']);
    $this->time2 = mktime(23, 59, 59, $_POST['m2'], $_POST['d2'], $_POST['y2']);

    $nd['f_title'] = $vnT->lang['manage_adminlog'];
    $nd['content'] .= $this->get_admin_session();
    $nd['content'] .= $this->get_admin_list();

		$nd['icon'] = 'icon-'.$this->module;
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }

  //=================Functions===============
  //============Get date=========
  function Get_ListDate ()
  {
    global $func, $DB, $conf, $vnT;
    $data['d1'] = "";
    for ($k = 1; $k <= 31; $k ++) {
      if ($k == $_POST['d1'])
        $data['d1'] .= "<option value=\"{$k}\" selected>{$k}";
      else
        $data['d1'] .= "<option value=\"{$k}\">{$k}";
    }
    $data['m1'] = "";
    for ($k = 1; $k <= 12; $k ++) {
      if ($k == $_POST['m1'])
        $data['m1'] .= "<option value=\"{$k}\" selected>{$this->month[$k]}";
      else
        $data['m1'] .= "<option value=\"{$k}\">{$this->month[$k]}";
    }
    $data['y1'] = "";
    for ($k = 2010; $k <= @date("Y"); $k ++) {
      if ($k == $_POST['y1'])
        $data['y1'] .= "<option value=\"{$k}\" selected>{$k}";
      else
        $data['y1'] .= "<option value=\"{$k}\">{$k}";
    }
    $data['d2'] = "";
    for ($k = 1; $k <= 31; $k ++) {
      if ($k == $_POST['d2'])
        $data['d2'] .= "<option value=\"{$k}\" selected>{$k}";
      else
        $data['d2'] .= "<option value=\"{$k}\">{$k}";
    }
    $data['m2'] = "";
    for ($k = 1; $k <= 12; $k ++) {
      if ($k == $_POST['m2'])
        $data['m2'] .= "<option value=\"{$k}\" selected>{$this->month[$k]}";
      else
        $data['m2'] .= "<option value=\"{$k}\">{$this->month[$k]}";
    }
    $data['y2'] = "";
    for ($k = 2010; $k <= @date("Y")+1; $k ++) {
      if ($k == $_POST['y2'])
        $data['y2'] .= "<option value=\"{$k}\" selected>{$k}";
      else
        $data['y2'] .= "<option value=\"{$k}\">{$k}";
    }
    $text = $this->form_date($data);
    return $text;
  }

  // Function
  function get_admin_session ()
  {
    global $func, $DB, $conf, $vnT;
    $text = "";
    $thoihan = time() - 1800;
    $query = $DB->query("SELECT s.*,a.username 
										FROM adminsessions s,admin a 
										WHERE a.adminid=s.adminid  AND s.time>={$thoihan} 
										ORDER BY s.time DESC");
    while ($admin = $DB->fetch_row($query)) {
      if ((! empty($admin['time'])) && ($admin['time'] != 0))
        $admin['time'] = date("H:i, d/m/Y", $admin['time']);
      else
        $admin['time'] = "None";

      $this->skin->assign('row', $admin);
      $this->skin->parse("admin_session.html_item");

    }


    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("admin_session");
    return $this->skin->text("admin_session");

  }


  //================
  function render_row ($row_info)
  {
    global $func, $DB, $conf, $vnT;
    $row = $row_info;
    // Xu ly tung ROW
    $id = $row['id'];
    $row_id = "row_" . $id;
    $output['check_box'] = "<input type=\"checkbox\" name=\"del_id[]\" value=\"{$id}\" class=\"checkbox\" onclick=\"select_row('{$row_id}')\">";
    $link_edit = $this->linkUrl . "&id={$row['adminid']}";
    $output['username'] = "<a href=\"{$link_edit}\"><strong>" . $row['username'] . "</strong></a>";
    if ((! empty($row['time'])) && ($row['time'] != 0))
      $time = date("H:i, d/m/Y ", $row['time']);
    else
      $time = "None";
    $output['time'] = $time;
    $output['ip'] = $row['ip'];
    $output['action'] = "<b>" . $row['action'] . "&nbsp;</b>";
    $output['cat'] = $row['cat'] . "&nbsp;";
    $output['pid'] = $row['pid'] . "&nbsp;";
    return $output;
  }

  function get_admin_list ()
  {
    global $func, $DB, $conf, $vnT;
    $table_list_time = "";
    if ((isset($_GET['id'])) && (is_numeric($_GET['id'])))
      $id = $_GET['id'];
    else
      $id = 0;
    $listdate = $this->Get_ListDate();
    $listcat = "<div class=\"messbar\" >View Admin Log : <select size=1 name=\"id\" onChange=\"gotosp(this)\">";
    $listcat .= "<option value=\"0\">--- Select Admin ---</option>";
    $query = $DB->query("SELECT * FROM admin");
    while ($pcat = $DB->fetch_row($query)) {
      if ((isset($_GET['id'])) && ($_GET['id'] == $pcat['adminid'])) {
        $listcat .= "<option value=\"{$pcat['adminid']}\" selected>{$pcat['username']}</option>";
      } else
        $listcat .= "<option value=\"{$pcat['adminid']}\">{$pcat['username']}</option>";
    }
    $listcat .= "</select></div>";
    $table_list_time .= $listcat . $listdate;
    if ($id != 0) {
      $ext_qr = "AND adminid='{$id}'";
      $ext_qr1 = "AND l.adminid='{$id}'";
    }
    if ((isset($_GET['p'])) && (is_numeric($_GET['p'])))
      $p = $_GET['p'];
    else
      $p = 1;
    $query = $DB->query("SELECT l.*,a.username 
										FROM adminlogs l,admin a 
										WHERE a.adminid=l.adminid {$ext_qr1} 
										AND l.time>={$this->time1} 
										AND l.time<={$this->time2}");
    $totals = $DB->num_rows($query);
    $n = 30;
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages)
      $p = $num_pages;
    if ($p < 1)
      $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = "?mod=admin&act=adminsession&p={$i}$ext";
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" , 
      'username' => "Username |15%|center" , 
      'time' => "Time |20%|center" , 
      'ip' => "IP |15%|center" , 
      'action' => "Action|15%|center" , 
      'cat' => "Page|15%|center" , 
      'pid' => "Name/ID|20%|center");
    $sql = "SELECT l.*,a.username 
				FROM adminlogs l,admin a 
				WHERE a.adminid=l.adminid {$ext_qr1} 
				AND l.time>={$this->time1} 
				AND l.time<={$this->time2}
				ORDER BY l.id DESC LIMIT $start,$n";
    //print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result)) {
      $row = $DB->get_array($result);
      for ($i = 0; $i < count($row); $i ++) {
        $row_info = $this->render_row($row[$i], $lang);
        $row_field[$i] = $row_info;
        $row_field[$i]['stt'] = ($i + 1);
        $row_field[$i]['row_id'] = "row_" . $row[$i]['id'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >Chưa có admin log</div>";
    }

    $table['button'] = '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
   // $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&ext=' . $ext_page . '\')">';
    $table_list = $func->ShowTable($table);
    $data['table_list_time'] = $table_list_time;
    $data['table_list'] = $table_list;
    $data['totals'] = $totals;
    $data['nav'] = $nav;

    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");

  }


  function form_date ($data)
  {
    return <<<EOF
<br />
<table width="100%" border="0" cellspacing="2" cellpadding="2" class="bg_tbl" >
<form name="quick" method="post" action="">
  <tr>
    <td ><strong>Custom View</strong></td>
    <td >
	From <select name="d1">
	{$data['d1']}
	</select>
	<select name="m1">
	{$data['m1']}
	</select>
	<select name="y1">
	{$data['y1']}
	</select>
	</td>
	<td class="copyText">
	To <select name="d2">
	{$data['d2']}
	</select>
	<select name="m2">
	{$data['m2']}
	</select>
	<select name="y2">
	{$data['y2']}
	</select>
	</td>
	<td><input name="submit" type="submit" value="View" class="button" /></td>
  </tr>
  </form>
</table>

EOF;
  }
  // end class
}

$vntModule = new vntModule();
?>