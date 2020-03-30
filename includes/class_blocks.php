<?php

/*================================================================================*\
|| 							Name code : class_block.php 		 		 										  			# ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
class Blocks
{
  var $title = NULL;
  var $content = NULL;

  /// Class Functions
  //
  function Blocks ()
  {
    $this->title = NULL;
    $this->content = NULL;
  }

  /*** Fake constructor to keep PHP5 happy **/
  function __construct ()
  {
    $this->Blocks();
  }

  //
  function get_content ()
  {
    // This should be implemented by the derived class.
    return $this->content;
  }

  //
  function get_title ()
  {
    // Intentionally doesn't check if a title is set. This is already done in _self_test()
    return $this->title;
  }

  function is_empty ()
  {
    $this->get_content();
    return false;
  }

  /*** Display the one block!  */
  function _print_one_block ($block_name, $align = "")
  {
    global $vnT, $conf, $DB, $input;
    $file_name = "blocks/" . $block_name . "/block_" . $block_name . ".php";
    if (file_exists($file_name)) {
      $class_name = "block_" . $block_name;
      if (! class_exists($class_name)) {
        $vnT->load_language($block_name, "blocks");
        include $file_name;
      }
    }
    $this->title = $vnT_Block->get_title();
    $this->content = $vnT_Block->get_content();
    if ($this->title == NULL) {
      return $this->content;
    } else {
      $nd['f_title'] = $this->title;
      $nd['content'] = $this->content;
      $vnT->skin_box->assign("data", $nd);
      if ($align) {
        $vnT->skin_box->reset("box_" . $align);
        $vnT->skin_box->parse("box_" . $align);
        return $vnT->skin_box->text("box_" . $align);
      } else {
        $vnT->skin_box->reset("box");
        $vnT->skin_box->parse("box");
        return $vnT->skin_box->text("box");
      }
    }
  }

  /*** Display the block!  */
  function _print_block ($align = "")
  {
    global $vnT, $conf, $DB, $input;
    if ($this->title == NULL) {
      return $this->content;
    } else {
      $nd['f_title'] = $this->title;
      $nd['content'] = $this->content;
      $vnT->skin_box->assign("data", $nd);
      if ($align) {
        $vnT->skin_box->reset("box_" . $align);
        $vnT->skin_box->parse("box_" . $align);
        return $vnT->skin_box->text("box_" . $align);
      } else {
        $vnT->skin_box->reset("box");
        $vnT->skin_box->parse("box");
        return $vnT->skin_box->text("box");
      }
    }
  }

  //================================= BOX LEFT ===============================
  function block_left ()
  {
    global $DB, $conf, $func, $vnT, $input;
    $output = "";
    if (empty($input['mod']))
      $pages = "main";
    else
      $pages = $input['mod'];
    $sql = "select * from layout where align='left' and l_show=1 and  (FIND_IN_SET('$pages',module_show) or (module_show='') ) order by l_order ";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result)) {
      if ($row['type'] == 0) {
        $file_name = "blocks/" . $row['name'] . "/block_" . $row['name'] . ".php";
        //				echo "file_name = $file_name <br>";
        if (file_exists($file_name)) {
          $class_name = "block_" . $row['name'];
          if (! class_exists($class_name)) {
            $vnT->load_language($row['name'], "blocks");
            include $file_name;
            $this->title = $vnT_Block->get_title();
            $this->content = $vnT_Block->get_content();
          }
        } else
          include "blocks/index.php";
        if ($row['cache'] == 0) {
          $content = "<!-- Start_" . $row['name'] . " -->" . $this->_print_block("left") . "<!-- End_" . $row['name'] . " -->";
        } else {
          $content = $this->_print_block("left");
        }
      } else {
        $this->title = $func->HTML($row['title']);
        $this->content = $row['content'];
        $content = $this->_print_block("left");
      }
      $output .= $content;
    }
    return $output;
  }

  //====================== BOX RIGHT ========================
  function block_right ()
  {
    global $DB, $conf, $func, $vnT, $input;
    $output = "";
    // RIGHT
    if (empty($input['mod']))
      $pages = "main";
    else
      $pages = $input['mod'];
    $sql = "select * from layout where  align='right' and  l_show=1 and  (FIND_IN_SET('$pages',module_show) or (module_show='') ) order by l_order ";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result)) {
      if ($row['type'] == 0) {
        $file_name = "blocks/" . $row['name'] . "/block_" . $row['name'] . ".php";
        //echo  "<br>".$file_name ;
        if (file_exists($file_name)) {
          $class_name = "block_" . $row['name'];
          if (! class_exists($class_name)) {
            $vnT->load_language($row['name'], "blocks");
            include $file_name;
            $this->title = $vnT_Block->get_title();
            $this->content = $vnT_Block->get_content();
          }
        } else
          include "blocks/index.php";
        if ($row['cache'] == 0) {
          $content = "<!-- Start_" . $row['name'] . " -->" . $this->_print_block("right") . "<!-- End_" . $row['name'] . " -->";
        } else {
          $content = $this->_print_block("right");
        }
      } else {
        $this->title = $func->HTML($row['title']);
        $this->content = $row['content'];
        $content = $this->_print_block("right");
      }
      $output .= $content;
    }
    return $output;
  }
}
?>
