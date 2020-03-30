<?php
/*if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
  die('Hacking attempt!');
}*/
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
@ini_set("display_errors", "1");
@header('Access-Control-Allow-Origin: *');
session_start();
define('IN_vnT', 1);
define('PATH_ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
require_once("../../_config.php");
require_once($conf['rootpath'] . "includes/class_db.php");
require_once($conf['rootpath']  ."includes/class.XiTemplate.php");
require_once($conf['rootpath'] . "includes/JSON.php");
// initialize the data registry
class vnT_Registry
{
  var $DB;
  var $conf = array();
  var $lang_name ;
  var $skin;
  /**
   * Constructor - initializes
   */
  function vnT_Registry ()
  {
    global $conf;
    // initialize
    $this->conf =  $conf;
    $this->DB =  new DB();
  }

}


$vnT = new vnT_Registry();
$vnT->lang_name = (isset($_REQUEST['lang'])) ? $_REQUEST['lang']  : "vn" ;

//Template
$vnT->skin = new XiTemplate( $conf['rootpath'] . "modules/advertise/html/vnt_ads.tpl");
$vnT->skin->assign("DIR_MOD", $conf['rooturl']."modules/advertise");
$vnT->skin->assign("CONF", $vnT->conf);

switch ($_GET['do'])
{
  case "news" : $jsout = load_advertise() ; break;
  default :  $jsout = load_advertise() ;
  break;
}


//load_advertise
function load_advertise()
{
  global  $vnT,$conf;
  $ok = 1;
  $html ='';
  $item_id = (int) $_REQUEST['id'];


  $res_ck = $vnT->DB->query("SELECT * FROM news_adv_script WHERE adv_id=".$item_id);
  if($row_ck = $vnT->DB->fetch_row($res_ck))
  {

      $sql = "SELECT n.newsid  , n.picture  ,nd.title ,nd.friendly_url
						FROM  news n, news_desc nd 
						WHERE n.newsid=nd.newsid
						AND lang='" . $vnT->lang_name . "' 
						AND display=1  
						AND n.newsid in (" . $row_ck['list_items'] . ") 
						ORDER BY FIELD(n.newsid," . $row_ck['list_items'] . ") ";

      $result = $vnT->DB->query($sql);
      if($num = $vnT->DB->num_rows($result))
      {
        $html = '<div class="adv-script-box">';
        while ($row = $vnT->DB->fetch_row($result))
        {
          $link = $vnT->conf['rooturl'].$row['friendly_url'].".html";
          $title = $row['title'];

          $_src = ($row['picture']) ? $conf['rooturl']."vnt_upload/news/".$row['picture'] : $conf['rooturl']."vnt_upload/news/nophoto.gif";


          $src =  $conf['rooturl'] . 'image.php?image=' .$_src . '&width=250&cropratio=1.5:1' ;
        
          $html .='<div class="adv-item">
                      <div class="adv-image"><a href="'.$link.'" title="'.$title.'" target="_blank"><img src="'.$src.'" alt="'.$title.'" ></a></div>
                      <div class="adv-info">
                          <div class="adv-name"><a href="'.$link.'" title="'.$title.'" target="_blank" >'.$title.'</a></div> 
                           <div class="adv-qc"><span>QC</span> '.$_SERVER['HTTP_HOST'].'</div> 
                      </div>                                                 
                  </div>';


        }
        $html .='</div>';

        $vnT->DB->free_result($result) ;
    } else {
      $ok=0;
    }
  }else{
    $ok=0;
  }

  if($_REQUEST['type']=="iframe"){
    if($ok){
      $data['id'] = $item_id;
      $data['content'] = $html;
      $vnT->skin->assign("data", $data);
      $vnT->skin->parse("html_iframe");
      $textout = $vnT->skin->text("html_iframe");
    }else{
      $textout = 'Not found';
    }
  }else{
    $arr_json['ok'] = $ok;
    $arr_json['html'] = $html;
    $json = new Services_JSON( );
    $textout = $json->encode($arr_json);
  }
  return $textout;
}


$vnT->DB->close();
flush();
echo $jsout;
exit();
?>