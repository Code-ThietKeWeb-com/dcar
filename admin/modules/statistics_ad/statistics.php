<?php
/*================================================================================*\
|| 							Name code : modname.php 		 		            	  ||
||  				Copyright @2008 by Thai Son - CMS vnTRUST                     ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
$vntModule = new vntModule();

class vntModule
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = "statistics";
  var $action = "statistics";

  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_" . $this->module . ".php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . $this->module . "_ad" . DS . "html" . DS . $this->action . ".tpl");
    $this->skin->assign('LANG', $vnT->lang);

    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action ;

    $this->linkUrl = "?mod=statistics&act=statistics";
    switch ($vnT->input['sub']) {
      case 'search':
        $nd['f_title'] = $vnT->lang['detail_statistics'];
        $nd['content'] = $this->do_Search();
      break;
			case 'web_referer':
        $nd['f_title'] = "Thống kê chi tiết website truy cập tới";
        $nd['content'] = $this->do_WebReferer();
      break;
      default:
        $nd['f_title'] = $vnT->lang['manage_statistics'];
        $nd['content'] = $this->do_Manage();
      break;
    }
		
    $nd['menu'] = $this->form_Search();
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }
	
	/**
   * function do_Manage() 
   * Quan ly option
   **/	 
	function form_Search ()
	{
		global $func, $DB, $conf, $vnT;
		if ($_GET['sub'] == "search") 
		{
			if (isset($_GET['day']))	$day = $_GET['day'];
			if (isset($_POST['day']))	$day = $_POST['day'];
			if (isset($_GET['month']))	$month = $_GET['month'];
			if (isset($_POST['month']))	$month = $_POST['month'];
			if (isset($_GET['year']))	$year = $_GET['year'];
			if (isset($_POST['year'])) $year = $_POST['year'];
			
			$data['day'] = $day;
			$data['list_thang'] = List_Thang($month);
			$data['list_nam'] = List_Nam($year);
			$data['back'] = "<div align=center class=font_err><a href='?mod=statistics&act=statistics'>[" . $vnT->lang['back_to_statistics'] . "]</a></div>";
			$data['link_action'] = "?mod=statistics&act=statistics&sub=search";
		} else {
			$data['day'] = date("d");
			$data['list_thang'] = List_Thang(date("m"));
			$data['list_nam'] = List_Nam(date("Y"));
			$data['back'] = "<div align=center class=font_err>" . $vnT->lang['note_search'] . "</div>";
			$data['link_action'] = "?mod=statistics&act=statistics&sub=search";
		}
		
		/*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("html_form_search");
    return $this->skin->text("html_form_search");
		
 	}


  //================= do_Search ===============
  function do_Search ()
  {
    global $vnT, $func, $DB, $conf, $lang_acp;
		$day = $vnT->input['day'];
		$month = $vnT->input['month'];
		$year = $vnT->input['year'];

    if (! empty($month) && ! empty($year)) {
      if (empty($day)) {
        $content = $this->Result_Month($month, $year);
      } else {
        $content = $this->Result_Day($day, $month, $year);
      }
    } else {
      $err = "Vui lòng điền ngày tháng năm cần xem thống kê";
      $url = $this->linkUrl;
      $func->html_redirect($url, $err);
    }
    return $content;
  }

  //== Result_Month 
  function Result_Month ($month, $year)
  {
    global $vnT, $func, $DB, $conf, $lang_acp;
    $where = $month . "/" . $year;
    $query = "select max(count) as max_count from counter  where substring(date_log,4,7)='$where'";
    $data_arr = $DB->query($query);
    if ($r = $DB->fetch_row($data_arr)) {
      $max_count = $r['max_count'];
    }
    $sql = "select * from counter where substring(date_log,4,7)='$where' order by date_log  ";
    //	echo "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    $i = 0;
    
		$textout = '<table width="100%"  border="0" cellspacing="2" cellpadding="2">';
		
    while ($row = $DB->fetch_row($result)) {
      $i ++;
      //$rate =$this->get_prozent($row['count'],$max_count)."%";
      $rate = number_format(($row["count"] / $max_count) * 400, 1);
      if ($rate < 1)
        $width = 1;
      else
        $width = $rate;
      $tmp = explode("/", $row['date_log']);
      $day = $tmp[0];
      $textout .= "<tr>
				<td width=20%>Ngày&nbsp;&nbsp; <strong>{$row['date_log']}</strong></td>
				<td width=60% align=left><img src=\"{$vnT->dir_images}/title_bg.gif\" width=\"$width\" height=\"16\" align=\"absmiddle\" ></td>
				<td><strong>{$row['count']}</strong>&nbsp;visits</td>
				<td width=50 align=center><a href='" . $this->linkUrl . "&sub=search&day=$day&month=$month&year=$year'><img src=\"images/but_view.gif\" width=20 /></a></td>
			  </tr>";
    }
		
		$textout .= '</table>';
		
    return $textout;
  }

  //== Result_Day 
  function Result_Day ($day, $month, $year)
  {
    global $vnT, $func, $DB, $conf, $lang_acp;
    if ((isset($_GET['p'])) && (is_numeric($_GET['p'])))
      $p = $_GET['p'];
    else
      $p = 1;
    $where = $day . "/" . $month . "/" . $year;
    $num_vister = 0;
    $res_num = $DB->query("select * from counter where date_log='$where' ");
    if ($r = $DB->fetch_row($res_num)) {
      $num_vister = $r['count'];
    }
    $n = 100;
    $num_pages = ceil($num_vister / $n);
    if ($p > $num_pages)
      $p = $num_pages;
    if ($p < 1)
      $p = 1;
    $start = ($p - 1) * $n;
    $ext = "&day=$day&month=$month&year=$year";
    $nav = $func->paginate($num_vister, $n, $ext, $p);
    $data['text_total'] = str_replace(array(
      '{date}' , 
      '{number}'), array(
      $where , 
      $num_vister), $vnT->lang['totals_visited']);
    $sql = "select  *  from counter_detail where date_log='$where' order by id DESC  LIMIT $start,$n";
    $result = $DB->query($sql);
    $i = 0;
    $html_row = "";
    while ($row = $DB->fetch_row($result)) {
      $html_row .= '<tr align="center">
					<td>' . date("h:i:s A", $row['date_time']) . '&nbsp;</td>
					<td>' . $row['ip'] . '&nbsp;</td>
					<td>' . $row['os'] . '&nbsp;</td>
					<td>' . $row['browser'] . '&nbsp;</td>
				  </tr>';
    }
    $data['html_row'] = $html_row;
    $data['nav'] = $nav;
    
		
		/*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("html_result_day");
    return $this->skin->text("html_result_day");
  }


	
	/**
   * function do_WebReferer() 
   * 
   **/	 
	function do_WebReferer ()
	{
		global $func, $DB, $conf, $vnT;
		$p = ((int)$_GET['p']) ? $_GET['p'] : 1 ;
    $n = 30;
		
    $res_num = $DB->query("select  id  from counter_website ");
		$totals = $DB->num_rows($res_num);
		
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages)   $p = $num_pages;
    if ($p < 1)   $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    
    $sql = "select  *  from counter_website order by num_click DESC,id DESC  LIMIT $start,$n";
    $result = $DB->query($sql);
    $i = 0;
    $html_row = "";
    while ($row = $DB->fetch_row($result)) {
			$i++;
      $html_row .= '<tr >
					<td align="center">' . $i . '&nbsp;</td>
					<td> <a href="http://'.$row['domain'].'" target="_blank" >' . $row['domain'] . '&nbsp;</td>
					<td align="center"><strong>' . $row['num_click'] . '</strong>&nbsp;</td>
					<td align="center">' . @date("H:i, d/m/Y",$row['date_click']) . '&nbsp;</td>
				  </tr>';
    }
    $data['html_row'] = $html_row;
    $data['nav'] = $nav;
		
		$data['back'] = "<p align=center class=font_err ><a href='?mod=statistics&act=statistics'>[" . $vnT->lang['back_to_statistics'] . "]</a></p>";
		/*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("html_web_referer");
    return $this->skin->text("html_web_referer");
		
 	}

  /**
   * function do_Manage() 
   * Quan ly option
   **/
  function do_Manage ()
  {
    global $vnT, $func, $DB, $conf;
		
    $type = (isset($_POST["selType"])) ? $_POST["selType"] : "day";
		
    //so do truy cap
		$sodo = '<table width="100%"  border="0" cellspacing="2" cellpadding="2">';
    $sodo .= "<tr>";
    switch ($type) {
      case "day":        
          $query = "select max(count) as max_count from counter";
          $data_arr = $DB->query($query);
          if ($row = $DB->fetch_row($data_arr)) {
            $total_num += $row['max_count'];
          }
          $sql = "select * from counter order by id DESC LIMIT 0,10";
          $result = $DB->query($sql);
          while ($row = $DB->fetch_row($result)) {
            $rate = number_format(($row["count"] / $total_num) * 200, 1);
            $height = $rate;
            $sodo .= "<td align=center valign=bottom>({$row['count']}&nbsp;visits)<br><img src=\"{$vnT->dir_images}/redline.gif\" width=\"20\" height=\"{$height}\" align=\"absmiddle\" ><br>{$row['date_log']}</td>";
          }        
      break;
      case "month":      
          $query = "select sum(count) as sum_month, substring(date_log,4,7) as thang
			from counter group by thang order by sum_month desc LIMIT 0,1";
          $data_arr = $DB->query($query);
          if ($row = $DB->fetch_row($data_arr)) {
            $total_num = $row['sum_month'];
          }
          $sql = "select sum(count) as sum_month, substring(date_log,4,7) as thang
			from counter group by thang order by id desc LIMIT 0,12 ";
          $result = $DB->query($sql);
          while ($row = $DB->fetch_row($result)) {
            $rate = number_format(($row["sum_month"] / $total_num) * 200, 1);
            $height = $rate;
            $sodo .= "<td align=center valign=bottom>({$row['sum_month']}&nbsp;visits)<br><img src=\"{$vnT->dir_images}/redline.gif\" width=\"20\" height=\"{$height}\" align=\"absmiddle\" ><br>{$row['thang']}</td>";
          }       
      break;
      case "year":     
          $query = "select sum(count) as sum_year, substring(date_log,7,4) as nam
			from counter group by nam order by sum_year desc LIMIT 0,1";
          $data_arr = $DB->query($query);
          if ($row = $DB->fetch_row($data_arr)) {
            $total_num = $row['sum_year'];
          }
          $sql = "select sum(count) as sum_year, substring(date_log,7,4) as nam
			from counter group by nam order by substring(date_log,7,4) desc LIMIT 0,10 ";
          $result = $DB->query($sql);
          $i = 0;
          while ($row = $DB->fetch_row($result)) {
            $rate = number_format(($row["sum_year"] / $total_num) * 200, 1);
            $height = $rate;
            $sodo .= "<td align=center valign=bottom>({$row['sum_year']}&nbsp;visits)<br><img src=\"{$vnT->dir_images}/redline.gif\" width=\"20\" height=\"{$height}\" align=\"absmiddle\" ><br>{$row['nam']}</td>";
          }      
      break;
    }
    $sodo .= "</tr>";
    $sodo .= '</table>';
		
		
    // chitiet
    $chitiet = '<table width="100%"  border="0" cellspacing="2" cellpadding="2">';
    switch ($type) {
      case "day":
        {
          $sql = "select * from counter order by id DESC LIMIT 0,30";
          $result = $DB->query($sql);
          $i = 0;
          while ($row = $DB->fetch_row($result)) {
            $i ++;
            $chitiet .= "<tr>
								<td>{$i}.&nbsp;&nbsp; {$row['date_log']}</td>
								<td><strong>{$row['count']}</strong>&nbsp;visits</td>
							  </tr>";
          }
        }
      break;
      case "month":
        {
          $sql = "select id, sum(count) as sum_month, substring(date_log,4,7) as thang
			from counter group by thang order by id desc LIMIT 0,12 ";
          $result = $DB->query($sql);
          $i = 0;
          while ($row = $DB->fetch_row($result)) {
            $i ++;
            $chitiet .= "<tr>
								<td>{$i}.&nbsp;&nbsp; {$row['thang']}</td>
								<td><strong>{$row['sum_month']}</strong>&nbsp;visits</td>
							  </tr>";
          }
        }
      break;
      case "year":
        {
          $sql = "select sum(count) as sum_year, substring(date_log,7,4) as nam
			from counter group by nam order by substring(date_log,7,4) desc LIMIT 0,10 ";
          $result = $DB->query($sql);
          $i = 0;
          while ($row = $DB->fetch_row($result)) {
            $i ++;
            $chitiet .= "<tr>
								<td>{$i}.&nbsp;&nbsp; {$row['nam']}</td>
								<td><strong>{$row['sum_year']}</strong>&nbsp;visits</td>
							  </tr>";
          }
        }
      break;
    }
    $chitiet .= '</table>';
   
	  //thong ke
    $thoihan = time() - 1800;
    $get_online = $DB->query("SELECT * FROM sessions WHERE time >= {$thoihan} ");
    $now = $DB->num_rows($get_online);
    $query = "select count from counter";
    $data_arr = $DB->query($query);
    $totals = 0;
    while ($row = $DB->fetch_row($data_arr)) {
      $totals += $row['count'];
    }
    $thongke = "<table width=\"100%\"  border=\"0\" cellspacing=\"2\" cellpadding=\"2\">
		<tr>
			<td>Tổng số người truy cập : </td>
			<td><strong>{$totals}</strong></td>
		</tr>
		<tr>
			<td>Số người đang online :</td>
			<td><strong>{$now}</strong></td>
		</tr>
	</table>";

    // top 10 truy cap
    $top10 = '<table width="100%"  border="0" cellspacing="2" cellpadding="2">';
    $sql = "select * from counter order by count DESC LIMIT 0,10";
    $result = $DB->query($sql);
    $i = 0;
    while ($row = $DB->fetch_row($result)) {
      $i ++;
      $top10 .= "<tr>
						<td>{$i}.&nbsp;&nbsp; {$row['date_log']}</td>
						<td><strong>{$row['count']}</strong>&nbsp;visits</td>
					  </tr>";
    }
    $top10 .= '</table>';
		
    $total_visiter = $DB->do_get_num("counter_detail");
   
	  // thong ke browser
    $browser = '<table width="100%"  border="0" cellspacing="2" cellpadding="2">';

    $sql = "select  browser, count(id) as num_browser  from counter_detail group by browser order by num_browser desc";
    $result = $DB->query($sql);
    $i = 0;
    while ($row = $DB->fetch_row($result)) {
      $rate_browser = get_prozent($row['num_browser'], $total_visiter) . "%";
      $i ++;
      $browser .= "<tr>
						<td>{$i}.&nbsp;&nbsp; {$row['browser']}</td>
						<td align='right'>{$rate_browser}</td>
					  </tr>";
    }
    $browser .= '</table>';
    
		// thong ke os
    $os = '<table width="100%"  border="0" cellspacing="2" cellpadding="2">';
    $sql = "select  os, count(id) as num_os  from counter_detail group by os order by num_os desc";
    $result = $DB->query($sql);
    $i = 0;
    while ($row = $DB->fetch_row($result)) {
      $rate_os = get_prozent($row['num_os'], $total_visiter) . "%";
      $i ++;
      $os .= "<tr>
						<td>{$i}.&nbsp;&nbsp; {$row['os']}</td>
						<td align='right'>{$rate_os}</td>
					  </tr>";
    }
    $os .= '</table>';
		
		//web_referer 
		$web_referer = '<table width="100%"  border="0" cellspacing="2" cellpadding="2">';
    $sql = "select * from counter_website  order by num_click desc , id DESC LIMIT 0,10 ";
    $result = $DB->query($sql);
    $i = 0;
    while ($row = $DB->fetch_row($result)) {
      $i ++;
      $web_referer .= "<tr>
						<td>{$i}.&nbsp;&nbsp; {$row['domain']}</td>
						<td align='right'><strong>{$row['num_click']}</strong> &nbsp;</td>
					  </tr>";
    }
    $web_referer .= '</table>';
		
		
    // thong ke last ip
    $last_ip = '<table width="100%"  border="0" cellspacing="2" cellpadding="2">';
    $sql = "select  ip, date_time, date_log  from counter_detail order by id desc LIMIT 0,30";
    $result = $DB->query($sql);
    $i = 0;
    while ($row = $DB->fetch_row($result)) {
      $i ++;
      $last_ip .= "<tr>
						<td >{$row['ip']}</td>
						<td align='center' > " . $row['date_log'] . "</td>
						<td align='right' >" . date("H:i:s", $row['date_time']) . "</td>						
					  </tr>";
    }
    $last_ip .= '</table>';	
		
		
		
		
    $data['sodo'] = $sodo;
    $data['thongke'] = $thongke;
    $data['chitiet'] = $chitiet;
    $data['top10'] = $top10;
    $data['browser'] = $browser;
    $data['os'] = $os;
		$data['web_referer'] = $web_referer;		
    $data['last_ip'] = $last_ip;
    $data['list_type'] = List_Type($type);
		
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("html_manage");
    return $this->skin->text("html_manage");
  }
  // end class
}
?>