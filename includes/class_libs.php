<?php

/*================================================================================*\
|| 							Name code : class_libs.php 		 		 											  			# ||
||  				Copyright © 2009 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 3.0
 * @date upgrade : 21/02/2009 by Thai Son
 **/
class Lib
{
  function getMenus(){
    global $DB, $input, $vnT, $conf;
    // cache
    //$param_cache = array() ;
    //$cache = $vnT->Cache->read_cache("menu_horizontal",$param_cache);
    //if ($cache != _NOC) return $cache;
    $mod = $input['mod'];
    $act = $vnT->setting['menu_active'];
    $out = array();
    $arr_menu = array();
    $arr_menu_pos = array();
    $result = $vnT->DB->query("SELECT * FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id   and display=1 and lang='$vnT->lang_name'  ORDER BY  pos ASC,  parentid ASC , menu_order ASC , n.menu_id ASC ");
    while ($row = $vnT->DB->fetch_row($result)) {
      if ($row['parentid'] == 0) {
        $arr_menu_pos[$row['pos']][$row['menu_id']] = $row;
      } else {
        $arr_menu[$row['parentid']][$row['menu_id']] = $row;
      }
    }
    $vnT->DB->free_result($result);


    // header
    $num = count($arr_menu_pos['header']);
    if ($num > 0) {

      $out['header'] = '<ul>';
      $i = 0;


      foreach ($arr_menu_pos['header'] as $menu_id => $row) {
        $i++;

        $menu_id = $row['menu_id'];
        $menu_link = $vnT->func->HTML($row['menu_link']);
        if ($menu_link == '') {
          $link = "javascript:void(0)";
        } else {
          $link = (!strstr($menu_link, "http://") && !strstr($menu_link, "https://")) ? $vnT->link_root . $menu_link : $menu_link;
        }


        $title = $vnT->func->HTML($row['title']);
        $target = $row["target"];
        $current = (($act == $row['name']) && ($row['name'])) ? "class='current'" : "";


        $icon = '';
        if($row['picture']){
          $icon = '<img src="'.ROOT_URI.'vnt_upload/weblink/'.$row['picture'].'" alt="'.$title.'" /> ';
        }

        $text = '<li class="'.$row['name'].'" ><a href="'.$link.'" '.$target.' '.$current.' ">'.$icon.$title.'</a></li>';

        $out['header'] .= $text;

      }
      $out['header'] .= '</ul>';
    }


    // menutop
    $num = count($arr_menu_pos['horizontal']);
    if ($num > 0) {

      $out['menutop'] = '' ;
      $out['mobile'] = '';
      $i = 0;
      foreach ($arr_menu_pos['horizontal'] as $menu_id => $row) {
        $i++;

        $menu_id = $row['menu_id'];
        $menu_link = $vnT->func->HTML($row['menu_link']);
        if ($menu_link == '') {
          $link = "javascript:void(0)";
        } else {
          $link = (!strstr($menu_link, "http://") && !strstr($menu_link, "https://")) ? $vnT->link_root . $menu_link : $menu_link;
        }

        $title = $vnT->func->HTML($row['title']);
        $target = $row["target"];
        $current = (($act == $row['name']) && ($row['name'])) ? " class='current'" : "";

        $text_sub = $this->add_submenus($menu_id, $arr_menu);

        $text = "<li class='menu'  ><a  href='".$link."' target='".$target."'  ".$current."  >".$title."</a>";
        $text .= $text_sub;
        $text .= '</li>';
        $out['menutop'] .= $text;

        $text_mobile = "<li  " . $current . "><a  href='".$link."' target='".$target."'  ".$current."  >".$title."</a>";
        $text_mobile .= $text_sub;
        $text_mobile .= '</li>';

        $out['mobile'] .= $text_mobile;


      }

    }




    // footer
    $num = count($arr_menu_pos['footer']);
    if ($num > 0) {

      $out['footer'] = '<ul>';
      $i = 0;

      foreach ($arr_menu_pos['footer'] as $menu_id => $row) {
        $i++;

        $menu_id = $row['menu_id'];
        $menu_link = $vnT->func->HTML($row['menu_link']);
        if ($menu_link == '') {
          $link = "javascript:void(0)";
        } else {
          $link = (!strstr($menu_link, "http://") && !strstr($menu_link, "https://")) ? $vnT->link_root . $menu_link : $menu_link;
        }
        $title = $vnT->func->HTML($row['title']);
        $target = $row["target"];
        $current = (($act == $row['name']) && ($row['name'])) ? " class='current'" : "";
        $text_sub = $this->add_submenus($menu_id, $arr_menu);
        $text= "<li ".$current."><a href='".$link."' target='".$target."' class='".$row['picture']."'>".$title."</a>";
        $text .= $text_sub;
        $text .= '</li>';
        $out['footer'] .= $text;
      }
      $out['footer'] .= '</ul>';
    }
    // cache
    //$vnT->Cache->save_cache("menu_horizontal", $text,$param_cache);
    return $out;
  }

  function add_submenus($cid, $arr_menu = array()){
    global $DB, $conf, $func, $vnT, $input;
    $text = "";
    $num = count($arr_menu[$cid]);
    if ($num > 0) {
      $text = '<ul>';
      foreach ($arr_menu[$cid] as $menu_id => $row) {
        $title = $vnT->func->HTML($row['title']);
        $menu_link = $vnT->func->HTML($row['menu_link']);
        $link = (!strstr($menu_link, "http://") && !strstr($menu_link, "https://")) ? $vnT->link_root . $menu_link : $menu_link;
        $target = $row["target"];

        $text_sub = $this->add_submenus($row['menu_id'], $arr_menu);

        $text .= "<li ><a href='".$link."' target='".$target."'>{$title}</a>";
        $text .= $text_sub ;
        $text .= "</li>" . "\n";

      } //end while
      $text .= '</ul>';
    }
    return $text;
  }

  /**
   * function box_lang ()
   *
   **/

  function box_lang(){
    global $vnT, $DB, $input;
    $result = $DB->query("select * from language ");
    if ($num = $DB->num_rows($result)) {
      $i = 0;
      $list = "";
      $list_mobile = '';
      while ($row = $DB->fetch_row($result)) {
        $i++;

        if ($vnT->link_lang[$row['name']]) {
          $link = $vnT->link_lang[$row['name']];
        } else {
          //$link = ROOT_URI.$row['name']."/";
          if ($input['mod'] == "" || $input['mod'] == "main") {
            $link = ROOT_URI . $row['name'] . "/" . $vnT->setting['seo_name'][$row['name']][$input['mod']] . ".html";
          } else {
            $link_old = str_replace($vnT->conf['rooturl'] . $vnT->lang_name . "/" . $vnT->setting['seo_name'][$vnT->lang_name][$input['mod']], "", $vnT->seo_url);
            $link = ROOT_URI . $row['name'] . "/" . $vnT->setting['seo_name'][$row['name']][$input['mod']] . $link_old;
          }
        }

        $current = ($vnT->lang_name==$row['name']) ? 'class="current"' : '';
        $src = ROOT_URL . "vnt_upload/lang/" . $row['picture'];
        $list .= '<a href="'.$link.'" '.$current.'  ><img src="'.$src.'" alt=""><span>'.$row['title'].'</span></a>';
        if ($row['name'] != $vnT->lang_name) {
          $list_mobile .= '<a href="'.$link.'"  ><img src="'.$src.'" alt=""><span>'.strtoupper($row['name']).'</span></a>';
        }

      }

      $out = $list;
    }
    return $out;
  }

  /**
   * function getBanners ()
   *
   **/

  function box_statistics(){
    global $vnT, $DB, $input;
    $output = $vnT->lang['global']['vistited'] . " : <b id='stats_totals' >&nbsp;</b> - " . $vnT->lang['global']['online'] . " : <b id='stats_online' >&nbsp;</b>";
    if ($vnT->conf['cache']) {
      $output = "<!-- Start_box_statistics -->" . $output . "<!-- End_box_statistics -->";
    }
    return $output;
  }


  /**
   * function getBanners ()
   *
   **/
  function getBanners($pos, $banner_num = 0, $tpl_skin = 'advertise')
  {
    global $vnT, $input;
    $textout = '';

    if(@is_array($vnT->advertise[$pos]) && count($vnT->advertise[$pos]) >0)
    {
      if($banner_num==1){
        $lid = @array_rand($vnT->advertise[$pos],1);
        $arr_banners[$lid] = $vnT->advertise[$pos][$lid];
      }elseif ($banner_num>1){
        @shuffle($vnT->advertise[$pos]);
        $arr_banners = array_slice($vnT->advertise[$pos], 0, $banner_num) ;
      }else{
        $arr_banners = $vnT->advertise[$pos] ;
      }

      $num = @count($arr_banners);
      if($num>0)
      {

        foreach ($arr_banners as $key => $row)
        {

          $vnT->skin_box->assign("row", $row);
          $vnT->skin_box->parse($tpl_skin . ".html_item");
        }

        $data['class'] = ($num > 1) ? " muti" : "";
        $vnT->skin_box->reset($tpl_skin);
        $vnT->skin_box->assign("data", $data);
        $vnT->skin_box->parse($tpl_skin);
        $textout = $vnT->skin_box->text($tpl_skin);
      }

    }


    return $textout;
  }

  /**
   * function get_advertise ()
   *
   **/
  function get_advertise($pos, $banner_num = 0, $tpl_skin = 'advertise',$class_item="advertise")
  {
    global $vnT, $input;
    $textout = '';

    if(@is_array($vnT->advertise[$pos]) && count($vnT->advertise[$pos]) >0)
    {
      //xu ly so luong
      if($banner_num==1){
        $lid = @array_rand($vnT->advertise[$pos],1);
        $arr_banners[$lid] = $vnT->advertise[$pos][$lid];
      }elseif ($banner_num>1){
        @shuffle($vnT->advertise[$pos]);
        $arr_banners = array_slice($vnT->advertise[$pos], 0, $banner_num) ;
      }else{
        $arr_banners = $vnT->advertise[$pos] ;
      }


      $num = @count($arr_banners);
      if($num>0)
      {
        $list_item ='<div class="'.$class_item.'">';
        foreach ($arr_banners as $key => $row)
        {

          switch ($row["type_ad"]) {
            case 1:
              $list_item .= $row['img'] ;
              break;
            case 2:
              $list_item .= $vnT->func->txt_unHTML($row['img']);
              break;
            default:
              $list_item .='<a onmousedown="return rwt(this,\'advertise\',' . $row['l_id'] . ')" href="' . $row['link'] . '" target="' .  $row['target'] . '"  ><img src="' .  $row['src'] . '" alt="' .  $row['title'] . '"  /></a>';
              break;

          } //end switch
          $list_item .= '</div>';

        }

        $data['list_item'] = $list_item ;
        $data['class'] = ($num > 1) ? " muti" : "";
        $vnT->skin_box->reset($tpl_skin);
        $vnT->skin_box->assign("data", $data);
        $vnT->skin_box->parse($tpl_skin);
        $textout = $vnT->skin_box->text($tpl_skin);
      }

    }


    return $textout;
  }


  /**
   * function get_logos ()
   *
   **/
  function get_logos($pos = 'logo')
  {
    global $input, $vnT;
    $logo = '';
    if(@is_array($vnT->advertise[$pos]) && count($vnT->advertise[$pos]) >0)
    {
      $arr_logos = @array_slice($vnT->advertise[$pos], 0, 1) ;
      foreach ($arr_logos as $key => $row)
      {
        $logo = "<a  onmousedown=\"return rwt(this,'advertise'," . $row['l_id'] . ")\" href='".$row['link']."' target='".$row['target']."' title='" . $row['title'] . "'  ><img  src='".$row['src']."'  alt='" . $row['title'] . "'   /></a>";
        if ($input['mod'] == "main") {
          $logo .= '<h1 style="display:none">' . $row['title'] . '</h1>';
        }

        if($pos=="logo") {
          $vnT->setting['src_logo'] = $row['src'];
        }
      }
    }
    return $logo;
  }



  /**
   * function List_Country ()
   *
   **/
  function List_Country($did = "", $ext = "")
  {
    global $vnT, $conf, $DB, $func;
    $text = "<select name=\"country\" id=\"country\" class='select form-control'  {$ext}   >";
    $sql = "SELECT iso,name FROM iso_countries where display=1 order by name ASC ";
    $result = $vnT->DB->query($sql);
    while ($row = $vnT->DB->fetch_row($result)) {
      $selected = ($row['iso'] == $did) ? " selected" : "";
      $text .= "<option value=\"{$row['iso']}\" " . $selected . ">" . $func->HTML($row['name']) . "</option>";
    }
    $vnT->DB->free_result($result);
    $text .= "</select>";
    return $text;
  }

  /**
   * function get_country_name ()
   *
   **/
  function get_country_name($code)
  {
    global $func, $DB, $conf, $vnT;
    $text = $code;
    $result = $vnT->DB->query("SELECT name FROM iso_countries WHERE iso='$code' ");
    if ($row = $vnT->DB->fetch_row($result)) {
      $text = $vnT->func->HTML($row['name']);
    }
    return $text;
  }

  /**
   * function List_City ()
   *
   **/
  function List_City($country = "VN", $did = "", $default = "", $type_show = "list", $ext = "")
  {
    global $vnT, $conf, $DB, $func;

    $text = "<option value=\"\" >" . $default . "</option>";
    $sql = "SELECT id,name FROM iso_cities where country='" . $country . "' AND display=1  order by c_order ASC , name ASC  ";
    $result = $vnT->DB->query($sql);
    while ($row = $vnT->DB->fetch_row($result)) {
      $selected = ($row['id'] == $did) ? "selected" : "";
      $text .= "<option value=\"{$row['id']}\" {$selected} >" . $vnT->func->HTML($row['name']) . "</option>";
    }
    $vnT->DB->free_result($result);


    if ($type_show == "option") {
      $textout = $text;
    } else {
      $textout = "<select name=\"city\" id=\"city\" class='form-control'  {$ext}   >";
      $textout .= $text;
      $textout .= "</select>";
    }
    return $textout;
  }

  /**
   * function get_city_name ()
   *
   **/
  function get_city_name($id)
  {
    global $func, $DB, $conf, $vnT;
    $text = $id;
    $result = $vnT->DB->query("SELECT name FROM iso_cities WHERE id=" . $id);
    if ($row = $vnT->DB->fetch_row($result)) {
      $text = $vnT->func->HTML($row['name']);
    }
    return $text;
  }

  /**
   * function List_State ()
   *
   **/
  function List_State($city, $did = "", $default = "", $type_show = "list", $ext = "")
  {
    global $vnT, $conf, $DB, $func;

    $text = "<option value=\"\" >" . $default . "</option>";
    $sql = "SELECT id,name FROM iso_states where display=1 and city='$city'  order by s_order ASC , name ASC  ";
    $result = $vnT->DB->query($sql);
    while ($row = $vnT->DB->fetch_row($result)) {
      $selected = ($row['id'] == $did) ? "selected" : "";
      $text .= "<option value=\"{$row['id']}\" {$selected} >" . $vnT->func->HTML($row['name']) . "</option>";
    }
    $vnT->DB->free_result($result);
    if ($type_show == "option") {
      $textout = $text;
    } else {
      $textout = "<select name=\"state\" id=\"state\" class='form-control'  {$ext}   >";
      $textout .= $text;
      $textout .= "</select>";
    }
    return $textout;
  }

  /**
   * function get_state_name ()
   *
   **/
  function get_state_name($id)
  {
    global $func, $DB, $conf, $vnT;
    $text = $id;
    $result = $vnT->DB->query("SELECT name FROM iso_states WHERE id=" . $id);
    if ($row = $vnT->DB->fetch_row($result)) {
      $text = $vnT->func->HTML($row['name']);
    }
    return $text;
  }

  /**
   * function List_Ward ()
   *
   **/
  function List_Ward($state, $did = "", $default = "", $type_show = "list", $ext = "")
  {
    global $vnT, $conf, $DB, $func;

    $text = "<option value=\"\" selected>" . $default . "</option>";
    $sql = "SELECT id,name FROM iso_wards where display=1 and state='$state'  order by   w_order ASC , name ASC  ";
    $result = $vnT->DB->query($sql);
    while ($row = $vnT->DB->fetch_row($result)) {
      $selected = ($row['code'] == $did) ? "selected" : "";
      $text .= "<option value=\"{$row['id']}\" {$selected} >" . $vnT->func->HTML($row['name']) . "</option>";
    }
    $vnT->DB->free_result($result);
    if ($type_show == "option") {
      $textout = $text;
    } else {
      $textout = "<select name=\"ward\" id=\"ward\" class='form-control'  {$ext}   >";
      $textout .= $text;
      $textout .= "</select>";
    }
    return $textout;
  }

  /**
   * function get_ward_name ()
   *
   **/
  function get_ward_name($id)
  {
    global $func, $DB, $conf, $vnT;
    $text = $id;
    $result = $vnT->DB->query("SELECT name FROM iso_wards WHERE id=" . $id);
    if ($row = $vnT->DB->fetch_row($result)) {
      $text = $vnT->func->HTML($row['name']);
    }
    return $text;
  }



  /**
   * function box_search ()
   *
   **/
  function box_search()
  {
    global $vnT, $input;
    $textout = "";

    $data['link_search'] = $vnT->link_root . $vnT->setting['seo_name'][$vnT->lang_name]['search'] . ".html";
    $textout = $vnT->skin_box->parse_box("box_search", $data);
    return $textout;
  }


  /**
   * function get_social_network ()
   *
   **/
  function get_social_network(){
    global $vnT, $input;
    $arr_out = array();
    $res_icon = $vnT->DB->query("SELECT * FROM social_network_icon  WHERE display=1 ORDER BY display_order ASC , id ASC ");
    while ($row_icon = $vnT->DB->fetch_row($res_icon)) {
      $arr_out['icon'] .= '<li><a style="color: #' . $row_icon['picture'] . '; " href="' . $row_icon['link'] . '" title=""><i class="fa fa-' . $row_icon['title'] . '"></i></a></li>';
      $arr_out['icon_img'] .= "<li><a href='" . $row_icon['link'] . "' target='_blank' ><img src='" . ROOT_URI . "vnt_upload/weblink/" . $row_icon['picture'] . "' alt='" . $row_icon['title'] . "'></a></li>";
    }
    $res_share = $vnT->DB->query("SELECT * FROM social_network_share WHERE display=1 ORDER BY display_order ASC , id ASC ");
    while ($row_share = $vnT->DB->fetch_row($res_share)) {
      if ($row_share['type'] == 2) {
        $arr_out['share'] .= $vnT->func->txt_unHTML($row_share['picture']) . ' &nbsp; ';
      } else {
        $arr_out['share'] .= "<a href='" . $row_share['link'] . "' target='_blank' ><img src='" . ROOT_URI . "vnt_upload/weblink/" . $row_share['picture'] . "' /></a> &nbsp; ";
      }

    }

    $res_like = $vnT->DB->query("SELECT * FROM social_network_like WHERE display=1 ORDER BY display_order ASC , id ASC ");
    while ($row_like = $vnT->DB->fetch_row($res_like)) {
      $arr_out['like'] .= $vnT->func->txt_unHTML($row_like['html_code']) . ' &nbsp; ';
    }

    return $arr_out;
  }


  /**
   * function get_icon_share ()
   *
   **/
  function get_icon_share($type, $link_share)
  {
    global $vnT, $input;
    $textout = '';
    switch ($type) {
      case "google"   :
        $textout = '<a title="Chia sẻ qua Google Plus." href="https://plus.google.com/share?url=' . $link_share . '" rel="nofollow"  target="_blank"><img src="' . $vnT->dir_images . '/icon_google_share.png" /></a>';
        break;
      case "facebook"   :
        $textout = '<a title="Chia sẻ qua Facebook." href="https://www.facebook.com/sharer.php?u=' . $link_share . '" rel="nofollow"   target="_blank"><img src="' . $vnT->dir_images . '/icon_facebook_share.png" /></a>';
        break;
      case "twitter"   :
        $textout = '<a href="https://twitter.com/share" class="twitter-share-button" data-size="large">Tweet</a>';
        break;
    }

    return $textout;
  }

  /**
   * function get_icon_share ()
   *
   **/
  function get_icon_like($type, $link_share)
  {
    global $vnT, $input;
    $textout = '';
    switch ($type) {
      case "facebook"   :
        $textout = '<iframe src="https://www.facebook.com/plugins/like.php?href=' . $link_share . '&width=85&height=28&layout=button_count&action=like&size=large&show_faces=false&share=false&appId=' . $vnT->setting['social_network_setting']['facebook_appId'] . '"  style="border:none;overflow:hidden"  scrolling="no" frameborder="0" allowTransparency="true" width="85" height="28" ></iframe>';
        break;
      case "google"   :
        $textout = '<g:plusone href="' . $link_share . '" size="medium" ></g:plusone>';
        break;
    }

    return $textout;
  }

  

  /**
   * function do_Comment ()
   *
   **/
  function do_Comment($info)
  {
    global $vnT, $input;


    $data = $info;


    $facebook_comment='';
    if ($info['facebook_comment']) {
      $facebook_comment = '<div class="facebook-comment"  ><div class="fb-comments" data-href="' . $info['item_link'] . '" data-width="100%" data-num-posts="5" data-colorscheme="light"></div><div id="fb-root"></div><script>(function(d, s, id) {  var js, fjs = d.getElementsByTagName(s)[0];  if (d.getElementById(id)) return;  js = d.createElement(s); js.id = id;  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=' . $vnT->setting['social_network_setting']['facebook_appId'] . '";  fjs.parentNode.insertBefore(js, fjs);}(document, "script", "facebook-jssdk"));</script></div>';
    }

    $data['facebook_comment'] = $facebook_comment;
    $vnT->skin_box->reset("html_comment");
    $vnT->skin_box->assign("data", $data);
    $vnT->skin_box->parse("html_comment");
    $textout = $vnT->skin_box->text("html_comment");

    return $textout;
  }


  /*** Ham List_Option_Array ****/
  function List_Option_Array($arr_item , $did = 0 , $default = "", $default_val="")
  {
    global $vnT ;
    if ($did)
      $arr_selected = explode(",",$did);
    else{
      $arr_selected = array();
    }

    $text = "";
    if($default){
      $text = "<option value='".$default_val."'>".$default."</option>";
    }

    if(is_array($arr_item) && count($arr_item)>0){
      foreach ($arr_item as $key => $val) {
        $title = is_array($val) ? $val['title'] : $val ;
        $selected = in_array($key, $arr_selected)? " selected" : "";
        $text .= "<option value='".$key."' " . $selected . ">".$title."</option>";
      }
    }


    return $text;
  }
  

  /**
   * function loadSiteDoc ()
   *
   **/
  function loadSiteDoc()
  {
    global $vnT, $input;
    $out = array();

    $result = $vnT->DB->query("SELECT * FROM sitedoc WHERE lang='".$vnT->lang_name."'") ;
    while ($row = $vnT->DB->fetch_row($result))
    {
      $out[$row['doc_name']] = $row['doc_content'];
    }
    $vnT->DB->free_result($result);
    return $out;
  }

  /**
   * function get_navation ()
   *
   **/
  function get_navation ($arr_items){
    global $vnT, $input;
    $textout ='<ul>';
    $textout .='<li><a href="'.$vnT->link_root.'" >'.$vnT->lang['global']['homepage'].'</a></li>';
    foreach ($arr_items as $item){
      if($item['link']){
        $textout .='<li><a href="'.$item['link'].'">'.$item['title']."</a></li>";
      }else{
        $textout .='<li>'.$item['title']."</li>";
      }
    }

    $textout .='</ul>';
    return $textout ;
  }


  /**
   * function build_meta_header ()
   *
   **/
  function build_meta_header ($info){
    global $vnT, $input;
    $textout ='';

    $site_name = ($info['site_name']) ? $info['site_name'] : $_SERVER['HTTP_HOST'];
    $locale = ($vnT->lang_name=="en") ? "en_US" : "vi_VN";
    $url =   ($info['url']) ? $info['url'] : $vnT->seo_url;
    $title = ($info['title']) ? $info['title'] : $vnT->conf['indextitle'];
    $description = ($info['description']) ? $info['description'] : $vnT->conf['meta_description'];

    //Facebook
    $textout .="\n".'<meta property="og:site_name" content="'.$site_name.'"/>';
    $textout .="\n".'<meta property="og:locale" content="'.$locale.'" />';
    $textout .= "\n".'<meta property="og:title" content="'.$title.'"/>';
    if($info['type']){
      $textout .="\n".'<meta property="og:type" content="'.$info['type'].'"/>';
    }
    $textout .="\n".'<meta property="og:url" content="'.$url.'"/>';
    $textout .="\n".'<meta property="og:description" content="'.$description.'"/>';
    if($info['image']){
      $textout .="\n".'<meta property="og:image" content="'.$info['image'].'"/>';
    }

    if($vnT->setting['social_network_setting']['twitter'])
    {
      $textout .= "\n".'<meta name="twitter:card" content="summary" />';
      $textout .= "\n".'<meta name="twitter:url" content="'.$url.'" />';
      $textout .= "\n".'<meta name="twitter:title" content="'.$title.'" />';
      $textout .= "\n".'<meta name="twitter:content" content="'.$description.'" />';
      if($info['image']){
        $textout .="\n".'<meta name="twitter:image" content="'.$info['image'].'"/>';
      }
      $textout .="\n".'<meta name="twitter:site" content="'.$site_name.'"/>';
    }

    return $textout ;
  }


  /**
   * function loadDataGlobal ()
   *
   **/
  function loadDataGlobal (){
    global $vnT, $input;
    $data = array();
    $data['box_lang'] = $this->box_lang();
    $data['banner'] = $this->get_advertise("top",1);
    $data['sitedoc'] = $this->loadSiteDoc();

    $data['link_search'] =  $vnT->link_root . $vnT->setting['seo_name'][$vnT->lang_name]['search'] . ".html";
    $data['scroll_left'] = $vnT->lib->get_advertise("scrollLeft",0);
    $data['scroll_right'] = $vnT->lib->get_advertise("scrollRight",0);

    $data['link_fanpage'] = $vnT->setting['social_network_setting']['facebook_page'] ;
    $data['facebook_appId'] = $vnT->setting['social_network_setting']['facebook_appId'];
    $data['google_appId'] = $vnT->setting['social_network_setting']['google_apikey'];


    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['vnt_csrf_token'] = $_SESSION['vnt_csrf_token'] ;
    $data['ver'] = '1.0';

    return $data;
  }


}

?>