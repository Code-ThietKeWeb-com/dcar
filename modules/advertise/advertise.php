<?php
/*================================================================================*\
|| 							Name code : advertise.php 		 		 														  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Access denied');
}
$nts = new sMain();

class sMain
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";

  // Start func
  function sMain ()
  {
    global $vnT, $conf, $input, $DB, $func;
    $id = (int) $input['lid'];
    switch ($input['type']) {
      case "news":
        $select = $vnT->DB->select("select * from news_advertise where id='{$id}'");
        $link = (! strstr($select[0]["link"], "http")) ? $vnT->link_root .$select[0]["link"] : $select[0]["link"] ;				
        if ($link == "http://" || empty($link))  $link = $vnT->conf['rooturl'];
        $num = $select[0]["num_click"] + 1;
        $vnT->DB->query("update news_advertise set num_click={$num} where id='{$id}'");
      break;
			case "product":
        $select = $vnT->DB->select("select * from product_advertise where id='{$id}'");
        $link = (! strstr($select[0]["link"], "http")) ? $vnT->link_root .$select[0]["link"] : $select[0]["link"] ;				
        if ($link == "http://" || empty($link))  $link = $vnT->conf['rooturl'];
        $num = $select[0]["num_click"] + 1;
        $vnT->DB->query("update product_advertise set num_click={$num} where id='{$id}'");
      break;
      default:
        $select = $vnT->DB->select("select l_id,link,num_click from advertise where l_id='{$id}'"); 
				$link = (! strstr($select[0]["link"], "http")) ? $vnT->link_root .$select[0]["link"] : $select[0]["link"] ;				
        if ($link == "http://" || empty($link))  $link = $vnT->conf['rooturl'];
        $num = $select[0]["num_click"] + 1;
        $vnT->DB->query("update advertise set num_click={$num} where l_id='{$id}'");
      break;
    }
    @header("Location: {$link}");
    echo "<meta http-equiv='refresh' content='0; url={$link}' />";
  }
  // end class
}
?>