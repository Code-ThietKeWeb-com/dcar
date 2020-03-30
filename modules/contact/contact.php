<?php
/*================================================================================*\
|| 							Name code : about.php 		 		 																	  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Access denied');
}
//load Model
include_once dirname( __FILE__ ) . '/includes/Model.php';

class Controller extends Model
{

  var $skin = "";
  var $linkUrl = "";
  var $module = MOD_NAME ;
  var $action = MOD_NAME ;


  /**
   *
   * Khoi tao
   **/
  public function __construct()
  {
    global $vnT, $input;

    //load skin
    $this->loadSkinModule($this->module);
    $vnT->html->addStyleSheet(DIR_MOD . "/css/" . $this->module . ".css");
    $vnT->html->addScript(DIR_MOD . "/js/" . $this->module . ".js");

    // menu_active
    $vnT->setting['menu_active'] = $this->module;

    //SEO
    $vnT->conf['indextitle'] =  $vnT->lang['contact']['contact'] ;
    $link_seo = ($vnT->muti_lang) ? ROOT_URL.$vnT->lang_name."/" : ROOT_URL ;
    $link_seo .= $vnT->setting['seo_name'][$vnT->lang_name]['contact'].".html";
    $vnT->conf['meta_extra'] .= "\n".'<link rel="canonical" href="'.$link_seo.'" />';
    $vnT->conf['meta_extra'] .= "\n". '<link rel="alternate" media="handheld" href="'.$link_seo.'"/>';
    $meta_info = array();
    if($vnT->setting['src_logo']){
      $meta_info['image']  = $vnT->setting['src_logo'];
    }
    $vnT->conf['meta_extra'] .= $vnT->lib->build_meta_header($meta_info);



    $data['banner'] = $vnT->lib->getBanners("top",0);
    $data['main'] = $this->do_Contact();

    $arr_navation[] = array("link" => "","title" => $vnT->lang['contact']['f_navation'] );
    $data['navation'] =  $vnT->lib->get_navation($arr_navation);

    $this->skin->assign("data", $data);
    $this->skin->parse("modules");
    $vnT->output .= $this->skin->text("modules");
  }

  // do_Contact
  function do_Contact ()
  {
    global $input, $vnT, $conf, $DB, $func;
    $err ='';
    $mess = "";
    $func->include_libraries('qrcode.qrcode');
    $vnT->qrcode = new QrCodes;

    // Xu ly Input
    if ($input['do_submit'])
    {
      $data = $input;

      $check =  $this->check_submit_form() ;
      if($check['ok'])
      {
        //reset sec_code
        $vnT->func->get_security_code();

        $staff = ($input["staff"]) ? $input["staff"] :  $vnT->conf['email'] ;
        $file_attach = '';
        $cot = array();
        $cot['name'] = $vnT->func->txt_HTML($_POST["name"]);
        $cot['email'] = $input["email"];
        $cot['phone'] = $input["phone"];
        $cot['company'] = $vnT->func->txt_HTML($_POST["company"]);
        $cot['address'] = $vnT->func->txt_HTML($_POST["address"]);
        $cot['staff'] = $staff;

        $cot['subject'] = $vnT->func->txt_HTML($_POST['subject']);
        $cot['content'] = $vnT->func->txt_HTML($_POST['content']);
        $cot['datesubmit'] = time();
        $ok = $vnT->DB->do_insert("contact", $cot);
        if ($ok) {

          $content_email = $vnT->func->load_MailTemp("contact");
          $qu_find = array();
          $qu_replace = array();
          foreach ($cot as $key => $value)
          {
            $qu_find[] = "{".$key."}" ;
            $qu_replace[] = $value ;
          }
          //more
          $qu_find[] = "{domain}" ;
          $qu_replace[] = $_SERVER['HTTP_HOST'] ;
          $qu_find[] = "{date}" ;
          $qu_replace[] = date("d/m/Y") ;

          $message = str_replace($qu_find, $qu_replace, $content_email);
          $subject = str_replace("{host}",$_SERVER['HTTP_HOST'],$vnT->lang['contact']['subject_email']);
          $sent = $vnT->func->doSendMail($staff, $subject, $message, $input["email"], $file_attach);
          //end send

          $mess = $vnT->func->html_mess($vnT->lang['contact']['send_contact_success'],'jAlert');
          $url = LINK_MOD . ".html";
          $vnT->func->header_redirect($url, $mess);
        } else {
          $err = $vnT->func->html_err($vnT->lang['contact']['send_contact_failt']);
        }
      }else{
        $err = $vnT->func->html_err($check['mess']);
      }

    }
    //===========


    $list_tab= '';
    $w_map = 450;
    $h_map = 450;
    $show_map = 0;
    $description =''; $cur_map_title ='';
    $result = $vnT->DB->query("select * from contact_config  WHERE display=1 AND lang='$vnT->lang_name' ORDER BY display_order ASC , id DESC ");
    if ($num = $vnT->DB->num_rows($result))
    {
      $i=0;
      while($row = $vnT->DB->fetch_row($result))
      {
        if($row['map_type']!=0) $show_map = 1 ;

        $class_maps = '';
        if($i==0) {

          switch ($row['map_type'])
          {
            case 1 :
              $data['maps'] = '<script language=javascript>load_maps('.$row['id'].','.$w_map.','.$h_map.'); </script>';
              break ;
            case 2 :
              $data['maps'] = '<div id="Map" class="maps" ><img src="'.$row['map_picture'].'" alt="map_picture" width="'.$w_map.'" /></div>';
              break ;
            case 3 :
              $data['maps'] = '<div id="Map" class="maps" ><div align="center" class="embed-responsive embed-responsive-16by9">'.$vnT->func->txt_unHTML($row['map_embed']).'</div></div>';
              break ;
          }

          $class_maps = 'class="active"';
          $cur_map_title = $row['title'] ;
        }



        $info_contact = '';

        $description .= $row['description'];
        $qrcode = $vnT->qrcode->GetVcard($row['full_name'],$row['company'],"",$row['phone'],$row['fax'],$row['email'],$row['website'],$row['address']);

        $row['company'] = $vnT->func->HTML($row['company']);
        $row['address'] = $vnT->func->HTML($row['address']);


        $row['link_map'] =  ROOT_URI."modules/contact/popup/maps.php?id=".$row['id']."&lang=".$vnT->lang_name;

        $row['info'] = $info_contact;
        $row['qrcode'] = $qrcode;


        $this->skin->assign("row", $row);

        if($row['phone']){
          $this->skin->parse("html_contact.html_item.phone");
        }
        if($row['fax']){
          $this->skin->parse("html_contact.html_item.fax");
        }
        if($row['email']){
          $this->skin->parse("html_contact.html_item.email");
        }
        if($row['website']){
          $this->skin->parse("html_contact.html_item.website");
        }
        $this->skin->parse("html_contact.html_item");

        $list_tab .= '<li '.$class_maps.' ><a id="map'.$row['id'].'" href ="#map'.$row['id'].'" data-id="'.$row['id'].'">'.$vnT->func->HTML($row['title']).'</a></li>' ;
        $i++;
      }
    }

    $data['description'] = $description ;
    if($show_map){
      $row_map['maps'] =  $data['maps'];
      $row_map['list_tab'] = $list_tab;
      $row_map['cur_title'] = $cur_map_title ;
      $this->skin->assign("row", $row_map);
      $this->skin->parse("html_contact.html_map");
    }



    if($vnT->conf['captcha_type']=="reCAPTCHA"){
      $captcha = array();
      $captcha['reCAPTCHA_site_key'] = $vnT->conf['reCAPTCHA_site_key'];
      $this->skin->assign("captcha", $captcha);
      $this->skin->parse("html_contact.html_recaptcha");
    }else{
      $captcha = array();
      $captcha['ver_img'] = ROOT_URL . "includes/captcha.php?w=100&h=40&size=25&nocache=".rand(1000,9999);
      $this->skin->assign("captcha", $captcha);
      $this->skin->parse("html_contact.html_captcha");
    }

    if (! isset($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['csrf_token'] ;

    $data['err'] = $err;
    if (isset($_SESSION['mess']) && $_SESSION['mess'] != '') {
      $data['err'] = $_SESSION['mess'];
      unset($_SESSION['mess']);
    }

    $data['link_action'] = LINK_MOD . ".html";
    $this->skin->assign("data", $data);
    $this->skin->parse("html_contact");
    $nd['content'] = $this->skin->text("html_contact");
    $nd['f_title'] =  $vnT->lang['contact']['contact'];
    $textout = $vnT->skin_box->parse_box("box_middle", $nd);

    return  $textout;

  }
  // end class
}

$controller = new Controller();
?>