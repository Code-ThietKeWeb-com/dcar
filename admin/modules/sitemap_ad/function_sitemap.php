<?php
/*================================================================================*\
|| 							Name code : funtions_config.php 		 			                      # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (!defined('IN_vnT')) {
  die('Hacking attempt!');
}

function box_menu($lang)
{
  global $func, $DB, $conf, $vnT, $input;
  $output = array();
  // lay ROOT cat
  $sql = "SELECT  *  FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND parentid=0 and display=1 and lang='$lang'  order by menu_order ASC ,n.menu_id ASC";
  $result = $DB->query($sql);
  if ($num = $DB->num_rows($result)) {
    while ($row = $DB->fetch_row($result)) {
      if ($row['menu_link'] != "#" && $row['menu_link'] != "") {
        $link = $row['menu_link'];
        $output[] = $link;
      }
      //check sub
      $sql_sub = "SELECT  *  FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND parentid=" . $row['menu_id'] . " and display=1 and lang='vn'    order by menu_order ASC ,n.menu_id ASC";
      $res_sub = $DB->query($sql_sub);
      if ($num_sub = $DB->num_rows($res_sub)) {
        while ($row_sub = $DB->fetch_row($res_sub)) {
          if ($row_sub['menu_link'] != "#" && $row_sub['menu_link'] != "") {
            $link_sub = $row_sub['menu_link'];
            $output[] = $link_sub;
          }
        }
      }
      $DB->free_result($res_sub);
    }//end while    

  }
  $DB->free_result($result);
  return $output;
}

function box_about($lang)
{
  global $func, $DB, $conf, $vnT, $input;
  $output = array();
  $query = $DB->query("SELECT n.aid,nd.title,nd.friendly_url FROM about n,about_desc nd  WHERE n.aid=nd.aid AND nd.lang='$lang' order by display_order  ASC , date_post DESC");
  if ($num = $DB->num_rows($query)) {
    $list = "";
    while ($row = $DB->fetch_row($query)) {
      //$link = ($lang=="vn") ? "gioi-thieu" : "about";
      //$link .= "/".$row['friendly_url'].".html";
      $link = $row['friendly_url'] . ".html";
      $output[] = $link;
    }
  }
  $DB->free_result($query);
  return $output;
}


function box_product($lang)
{
  global $func, $DB, $conf, $vnT, $input;
  $output = array();
  // lay ROOT cat
  $sql = "select c.cat_id,cd.cat_name , cd.friendly_url
						from product_category c , product_category_desc cd
						where c.cat_id=cd.cat_id 
						AND lang='$lang' 
						AND display=1
						AND parentid=0
						order by cat_order DESC ,date_post DESC  ";
  $result = $DB->query($sql);
  if ($num = $DB->num_rows($result)) {
    while ($row = $DB->fetch_row($result)) {
      //$link = ($lang=="vn") ? "san-pham" : "product";
      //$link .= "/".$row['friendly_url']."-".$row['cat_id'].".html";
      $link = $row['friendly_url'] . ".html";
      $output[] = $link;

      //check sub
      $sql_sub = "select c.cat_id,cd.cat_name , cd.friendly_url
						from product_category c , product_category_desc cd
						where c.cat_id=cd.cat_id 
						AND lang='$lang' 
						AND display=1
						AND parentid=" . $row['cat_id'] . " 
						order by cat_order DESC ,date_post DESC ";
      $res_sub = $DB->query($sql_sub);
      if ($num_sub = $DB->num_rows($res_sub)) {
        while ($row_sub = $DB->fetch_row($res_sub)) {
          //$link_sub = ($lang=="vn") ? "san-pham" : "product";
          //$link_sub .= "/".$row_sub['friendly_url']."-".$row_sub['cat_id'].".html";
          $link_sub = $row_sub['friendly_url'] . ".html";
          $output[] = $link_sub;
          //check sub
          $sql_sub1 = "select c.cat_id,cd.cat_name , cd.friendly_url
						from product_category c , product_category_desc cd
						where c.cat_id=cd.cat_id 
						AND lang='$lang' 
						AND display=1
						AND parentid=" . $row_sub['cat_id'] . " 
						order by  cat_order DESC ,date_post DESC ";
          $res_sub1 = $vnT->DB->query($sql_sub1);
          if ($num_sub1 = $vnT->DB->num_rows($res_sub1)) {
            while ($row_sub1 = $vnT->DB->fetch_row($res_sub1)) {
              //$link_sub1 = ($lang=="vn") ? "san-pham" : "product";
              //$link_sub1 .= "/".$row_sub1['friendly_url']."-".$row_sub1['cat_id'].".html";
              $link_sub1 = $row_sub1['friendly_url'] . ".html";
              $output[] = $link_sub1;
            }
          }

        }
      }
      $DB->free_result($res_sub);
    }//end while    
    $DB->free_result($result);
  }

  //san pham
  $sql = "SELECT p.p_id , pd.p_name , pd.friendly_url
					FROM products p , products_desc pd
					WHERE p.p_id=pd.p_id 
					AND lang='$lang' 
					AND display=1 
					ORDER BY p_order DESC, date_post DESC ";

  $result = $DB->query($sql);
  if ($num = $DB->num_rows($result)) {
    while ($row = $DB->fetch_row($result)) {
      //$link = ($lang=="vn") ? "san-pham" : "product";
      //$link .= "/".$row['p_id']."/".$row['friendly_url'].".html"; 
      $link = $row['friendly_url'] . ".html";
      $output[] = $link;
    }
  }

  $DB->free_result($result);

  return $output;
}


//---------------------
function box_news($lang)
{
  global $func, $DB, $conf, $vnT, $input;
  $output = array();
  // lay ROOT cat
  $sql = "SELECT n.cat_id , nd.friendly_url
				 FROM news_category n , news_category_desc nd
				 WHERE n.cat_id=nd.cat_id
				 AND display=1 
				 AND lang='$lang' 
				 order by cat_order DESC ,date_post DESC ";
  $result = $DB->query($sql);
  if ($num = $DB->num_rows($result)) {
    while ($row = $DB->fetch_row($result)) {
      //$link = ($lang=="vn") ? "tin-tuc" : "news";
      //$link .= "/".$row['friendly_url']."-".$row['cat_id'].".html";			
      $link = $row['friendly_url'] . ".html";
      $output[] = $link;
    }//end while    

  }
  $DB->free_result($result);

  //news
  $sql = "SELECT n.newsid , nd.title , nd.friendly_url
					FROM news n , news_desc nd
					WHERE n.newsid=nd.newsid 
					AND lang='$lang' 
					AND display=1 
					ORDER BY display_order DESC ,date_post DESC ";

  $result = $DB->query($sql);
  if ($num = $DB->num_rows($result)) {
    while ($row = $DB->fetch_row($result)) {
      //$link = ($lang=="vn") ? "tin-tuc" : "news";
      //$link .= "/".$row['newsid']."/".$row['friendly_url'].".html"; 
      $link = $row['friendly_url'] . ".html";
      $output[] = $link;
    }
  }
  $DB->free_result($result);
  return $output;
}


function box_service($lang)
{
  global $func, $DB, $conf, $vnT, $input;
  $output = array();
  $query = $DB->query("SELECT n.service_id,nd.title,nd.friendly_url FROM service n,service_desc nd  WHERE n.service_id=nd.service_id AND nd.lang='$lang' order by s_order  DESC , date_post DESC");
  if ($num = $DB->num_rows($query)) {
    while ($row = $DB->fetch_row($query)) {
      $link = $row['friendly_url'] . ".html";
      $output[] = $link;
    }
  }
  $DB->free_result($query);
  return $output;
}


function box_guide($lang)
{
  global $func, $DB, $conf, $vnT, $input;
  $output = array();
  $query = $DB->query("SELECT n.guide_id,nd.title,nd.friendly_url FROM guide n,guide_desc nd  WHERE n.guide_id=nd.guide_id AND nd.lang='$lang' order by display_order  DESC , date_post DESC");
  if ($num = $DB->num_rows($query)) {
    while ($row = $DB->fetch_row($query)) {
      $link = $row['friendly_url'] . ".html";
      $output[] = $link;
    }
  }
  $DB->free_result($query);
  return $output;
}


?>