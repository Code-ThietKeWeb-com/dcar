<?php
/*================================================================================*\
|| 							Name code : main.php 		 		            	  ||
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
  var $module = "main";

  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
		require_once ("function_" . $this->module . ".php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . "main_ad" . DS . "main.tpl");
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign("DIR_IMAGE", $vnT->dir_images);
    $this->skin->assign("DIR_STYLE", $vnT->dir_style);
    $this->skin->assign("DIR_JS", $vnT->dir_js);
    $this->skin->assign("LANG", $vnT->lang);
    $this->skin->assign("CONF", $conf);
		
		
		
		$vnT->html->addStyleSheet( $vnT->dir_js . "/jquery_ui/themes/base/ui.all.css");
		$vnT->html->addStyleSheet( $vnT->dir_js . "/jquery_ui/custom.css");
		$vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.core.js");		
		$vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.sortable.js");
		
		
 		$vnT->html->addScript("modules/" . $this->module."_ad/js/".$this->module.".js");		
		$vnT->html->addStyleSheet("modules/" . $this->module."_ad/css/".$this->module.".css");
 		$vnT->html->addScript("modules/" . $this->module."_ad/js/dashboard.js");		
 		
    
    //Xoa adminlog 1thang
    $day_del_adminlog = ((int) $conf['day_del_adminlog']) ? (int) $conf['day_del_adminlog'] : 30;
    $thoihan = @time() - ($day_del_adminlog * 24 * 3600);
    $DB->query("DELETE from adminlogs where time < $thoihan ");
    //xoa counter_detail 2 thang
    $day_del_counter = ((int) $conf['day_del_counter']) ? (int) $conf['day_del_counter'] : 60;
    $time_del = @time() - ($day_del_counter * 24 * 3600);
    $DB->query("DELETE FROM counter_detail where date_time <{$time_del} ");


    //xoa thung rac
    $day_del_recycle_bin = ((int) $conf['day_del_recycle_bin']) ? (int) $conf['day_del_recycle_bin'] : 30;
    $time_del = @time() - ($day_del_counter * 24 * 3600);

    $res_ck = $DB->query("SELECT * FROM recycle_bin WHERE datesubmit<".$time_del);
    if($DB->num_rows($res_ck))
    {
      while ($row_ck = $DB->fetch_row($res_ck))
      {
        if($row_ck['lang']){
          if (@strstr($row_ck['tbl_data'],"_desc"))
          {
            $res_d = $DB->query("SELECT id FROM ".$row_ck['tbl_data']." WHERE ".$row_ck['name_id']." = ".$row_ck['item_id']."  AND lang<>'".$row_ck['lang']."' ");
            if(!$DB->num_rows($res_d)){
              $DB->query("DELETE FROM ".@str_replace("_desc","",$row_ck['tbl_data'])." WHERE  ".$row_ck['name_id']." = ".$row_ck['item_id'] );
            }
          }
          $DB->query("DELETE FROM ".$row_ck['tbl_data']."  WHERE ".$row_ck['name_id']." = ".$row_ck['item_id']."  AND lang='".$row_ck['lang']."' ");
        }else{
          $DB->query("DELETE FROM ".$row_ck['tbl_data']."  WHERE ".$row_ck['name_id']." = ".$row_ck['item_id'] );
        }
      }

      $DB->query("DELETE FROM recycle_bin WHERE datesubmit<".$time_del);
    }


    $data['wellcome'] = str_replace(array(
      '<username>' , 
      '<date>'), array(
      "<font color=\"#FF0000\" size=\"+1\">" . $vnT->admininfo['username'] . "</font>" , 
      "<font color=\"#FF0000\" >" . @date("h:m , d/m/Y", $vnT->admininfo["lastlogin"]) . "</font>"), $vnT->lang['welcome']);
    
		$data['box_system'] = Box_System();
		if($vnT->admininfo['adminid']==1)
			$data['box_contact'] = Box_Contact();
		  //======
		$data['box_statistics'] = Box_Statistics();
		
		if($vnT->admininfo['adminid']==1)
			$data['box_adminlog'] = Box_AdminLog();
 		
		
		//box_mod_widget
		/*$res_mod = $vnT->DB->query("SELECT * FROM modules ORDER BY ordering ASC ");
		if($num_mod = $vnT->DB->num_rows($res_mod))
		{
			$box_mod_widget = "";
			while ($row_mod = $vnT->DB->fetch_row($res_mod))
			{
				$file_widget = "modules/".$row_mod['mod_name']."_ad/widget.php";
				
				if(file_exists($file_widget))
				{
					ob_start();
					include $file_widget;
					$content_widget = ob_get_contents();
					ob_end_clean();
					$box_mod_widget .= $content_widget;
				}
				
			}
		}
		if($vnT->admininfo['adminid']==1)
			$data['box_mod_widget']  =   $box_mod_widget ;  
   	*/

    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("main");
    $nd['content'] = $this->skin->text("main");
    $nd['f_title'] = '<span class="icon-home">'.$vnT->lang['f_statistics'].'</span>';
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }
  // end class
}
?>