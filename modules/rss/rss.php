<?php
/*================================================================================*\
|| 							Name code : rss.php 		 		 																	  # ||
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

  // Start func
  function sMain ()
  {
    global $input, $vnT, $conf, $DB, $func;
    include ("function_rss.php");
    require_once "skin_rss.php";
    $this->skin = new skin_rss();
    $this->linkMod = $vnT->cmd . "=mod:rss";
    $mess = "";
    $row_rss = "";
    $row_rss .= "<tr>
						<td class='font_title'><a href=\"{$conf['rooturl']}rss/last10_news_{$vnT->lang_name}.php\"><img src=\"" . IMAGE_MOD . "/RSS.gif\" align=\"absmiddle\">&nbsp;Tin m&#7899;i nh&#7845;t</a></td>
						<td><a href=\"{$conf['rooturl']}rss/last10_news_{$vnT->lang_name}.php\">" . $conf['rooturl'] . "rss/last10_news_{$vnT->lang_name}.php</a></td>
					  </tr>";
    $sql = "SELECT n.*, nd.cat_name FROM news_category n,news_category_desc nd WHERE n.cat_id=nd.cat_id AND nd.lang='$vnT->lang_name' and parentid=0  order by cat_order";
    //	echo $sql;
    $result = $DB->query($sql);
    if ($num = $DB->num_rows($result)) {
      while ($row = $DB->fetch_row($result)) {
        $file_rss = "last10_cat" . $row['cat_id'] . "_{$vnT->lang_name}.php";
        $link = $conf['rooturl'] . "rss/" . $file_rss;
        $cat_name = $func->HTML($row['cat_name']);
        $row_rss .= "<tr>
						<td class='font_title'><a href=\"{$link}\"><img src=\"" . IMAGE_MOD . "/RSS.gif\" align=\"absmiddle\">&nbsp;" . $cat_name . "</a></td>
						<td><a href=\"{$link}\">" . $conf['rooturl'] . "rss/" . $file_rss . "</a></td>
					  </tr>";
      }
    }
    $data['row_rss'] = $row_rss;
    $vnT->output .= $this->skin->html_main($data);
  }
  // end class
}
?>