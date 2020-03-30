<?php

if (! defined('IN_vnT'))
{
  die('Hacking attempt!');
}

/**
 * A HTML handling class
 *
 * @version : 1.0
 * @date upgrade : 09/01/2009 by Thai Son
 */

class vnT_HTML
{
  /**
   * Array of linked scripts
   *
   * @var		array
   * @access   private
   */
  var $_scripts = array();
  var $_scriptsFooter = array();
  /**
   * Array of scripts placed in the header
   *
   * @var  array
   * @access   private
   */
  var $_script = array();
  var $_scriptFooter = array();
  /**
   * Array of linked style sheets
   *
   * @var	 array
   * @access  private
   */
  var $_styleSheets = array();
  var $_styleSheetsFooter = array();
  
  /**
   * Array of included style declarations
   *
   * @var	 array
   * @access  private
   */
  var $_style = array();
  var $_styleFooter = array();
  
  /**
   * Adds a linked script to the page
   *
   * @param	string  $url		URL to the linked script
   * @param	string  $type		Type of script. Defaults to 'text/javascript'
   * @access   public
   */
  function addScript ($url, $type = "text/javascript" ,$footer=0)
  {

    if($footer){
      $this->_scriptsFooter[$url] = $type;
    }else{
      $this->_scripts[$url] = $type;
    }
  }
  
  /**
   * Adds a script to the page
   *
   * @access   public
   * @param	string  $content   Script
   * @param	string  $type	Scripting mime (defaults to 'text/javascript')
   * @return   void
   */
  function addScriptDeclaration ($content, $type = 'text/javascript',$footer=0)
  {

    if($footer){
      if (! isset($this->_scriptFooter[strtolower($type)]))
      {
        $this->_scriptFooter[strtolower($type)] = $content;
      }
      else
      {
        $this->_scriptFooter[strtolower($type)] .= chr(13) . $content;
      }
    }else{
      if (! isset($this->_script[strtolower($type)]))
      {
        $this->_script[strtolower($type)] = $content;
      }
      else
      {
        $this->_script[strtolower($type)] .= chr(13) . $content;
      }
    }


  }
  
  /**
   * Adds a linked stylesheet to the page
   *
   * @param	string  $url	URL to the linked style sheet
   * @param	string  $type   Mime encoding type
   * @param	string  $media  Media type that this stylesheet applies to
   * @access   public
   */
  function addStyleSheet ($url, $type = 'text/css', $media = null, $attribs = array(),$footer=0)
  {
    if($footer){
      $this->_styleSheetsFooter[$url]['mime'] = $type;
      $this->_styleSheetsFooter[$url]['media'] = $media;
      $this->_styleSheetsFooter[$url]['attribs'] = $attribs;
    }else{
      $this->_styleSheets[$url]['mime'] = $type;
      $this->_styleSheets[$url]['media'] = $media;
      $this->_styleSheets[$url]['attribs'] = $attribs;
    }

  }
  
  /**
   * Adds a stylesheet declaration to the page
   *
   * @param	string  $content   Style declarations
   * @param	string  $type		Type of stylesheet (defaults to 'text/css')
   * @access   public
   * @return   void
   */
  function addStyleDeclaration ($content, $type = 'text/css',$footer=0)
  {
    if($footer){
      if (! isset($this->_styleFooter[strtolower($type)]))
      {
        $this->_styleFooter[strtolower($type)] = $content;
      }
      else
      {
        $this->_styleFooter[strtolower($type)] .= chr(13) . $content;
      }
    }else{
      if (! isset($this->_style[strtolower($type)]))
      {
        $this->_style[strtolower($type)] = $content;
      }
      else
      {
        $this->_style[strtolower($type)] .= chr(13) . $content;
      }
    }

  }
  
  /**
   * toString
   *
   * @param	string  $content   Style declarations
   * @param	string  $type		Type of stylesheet (defaults to 'text/css')
   * @access   public
   * @return   void
   */
  function toString ($array = null, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey = false)
  {
    $output = array();
    
    if (is_array($array))
    {
      foreach ($array as $key => $item)
      {
        if (is_array($item))
        {
          if ($keepOuterKey)
          {
            $output[] = $key;
          }
          // This is value is an array, go and do it again!
          $output[] = $this->toString($item, $inner_glue, $outer_glue, $keepOuterKey);
        }
        else
        {
          $output[] = $key . $inner_glue . '"' . $item . '"';
        }
      }
    }
    
    return @implode($outer_glue, $output);
  }

  function fetchHead ()
  {

    $tab = "";
    $tagEnd = ' />';
    $lnEnd = "\n";

    $strHtml = '';

    // Generate stylesheet links
    foreach ($this->_styleSheets as $strSrc => $strAttr)
    {
      $strHtml .= $tab.'<link rel="stylesheet" href="' . $strSrc . '" type="' . $strAttr['mime'] . '"';
      if (! is_null($strAttr['media']))
      {
        $strHtml .= ' media="' . $strAttr['media'] . '" ';
      }
      if ($temp = $this->toString($strAttr['attribs']))
      {
        $strHtml .= ' ' . $temp;

      }
      $strHtml .= $tagEnd . $lnEnd;
    }

    // Generate stylesheet declarations
    foreach ($this->_style as $type => $content)
    {
      $strHtml .= $tab . '<style type="' . $type . '">' . $lnEnd;

      $strHtml .= $content . $lnEnd;

      $strHtml .= $tab . '</style>' . $lnEnd;
    }

    // Generate script file links
    foreach ($this->_scripts as $strSrc => $strType)
    {
      $strHtml .= $tab . '<script type="' . $strType . '" src="' . $strSrc . '"></script>' . $lnEnd;
    }

    // Generate script declarations
    foreach ($this->_script as $type => $content)
    {
      $strHtml .= $tab . '<script type="' . $type . '">' . $lnEnd;
      $strHtml .= $content . $lnEnd;
      $strHtml .= $tab . '</script>' . $lnEnd;
    }


    return $strHtml;
  }



  function fetchFooter ()
  {

    $tab = "\t";
    $tagEnd = ' />';
    $lnEnd = "\n";

    $strHtml = '';

    // Generate stylesheet links
    foreach ($this->_styleSheetsFooter as $strSrc => $strAttr)
    {
      $strHtml .= $tab.'<link rel="stylesheet" href="' . $strSrc . '" type="' . $strAttr['mime'] . '"';
      if (! is_null($strAttr['media']))
      {
        $strHtml .= ' media="' . $strAttr['media'] . '" ';
      }
      if ($temp = $this->toString($strAttr['attribs']))
      {
        $strHtml .= ' ' . $temp;

      }
      $strHtml .= $tagEnd . $lnEnd;
    }

    // Generate stylesheet declarations
    foreach ($this->_styleFooter as $type => $content)
    {
      $strHtml .= $tab . '<style type="' . $type . '">' . $lnEnd;

      $strHtml .= $content . $lnEnd;

      $strHtml .= $tab . '</style>' . $lnEnd;
    }

    // Generate script file links
    foreach ($this->_scriptsFooter as $strSrc => $strType)
    {
      $strHtml .= $tab . '<script type="' . $strType . '" src="' . $strSrc . '"></script>' . $lnEnd;
    }

    // Generate script declarations
    foreach ($this->_scriptFooter as $type => $content)
    {
      $strHtml .= $tab . '<script type="' . $type . '">' . $lnEnd;
      $strHtml .= $content . $lnEnd;
      $strHtml .= $tab . '</script>' . $lnEnd;
    }


    return $strHtml;
  }

  function fetchStyle ()
  {
    $tab = "\t";
    $tagEnd = ' />';
    $lnEnd = "\n";
    $strHtml ='';
    // Generate stylesheet links
    foreach ($this->_styleSheets as $strSrc => $strAttr)
    {
      $strHtml .= $tab.'<link rel="stylesheet" href="' . $strSrc . '" type="' . $strAttr['mime'] . '"';
      if (! is_null($strAttr['media']))
      {
        $strHtml .= ' media="' . $strAttr['media'] . '" ';
      }
      if ($temp = $this->toString($strAttr['attribs']))
      {
        $strHtml .= ' ' . $temp;

      }
      $strHtml .= $tagEnd . $lnEnd;
    }

    // Generate stylesheet declarations
    foreach ($this->_style as $type => $content)
    {
      $strHtml .= $tab . '<style type="' . $type . '">' . $lnEnd;

      $strHtml .= $content . $lnEnd;

      $strHtml .= $tab . '</style>' . $lnEnd;
    }


    return $strHtml;
  }

  function fetchScript ()
  {

    $tab = "\t";
    $tagEnd = ' />';
    $lnEnd = "\n";

    $strHtml = '';

    // Generate script file links
    foreach ($this->_scripts as $strSrc => $strType)
    {
      $strHtml .= $tab . '<script type="' . $strType . '" src="' . $strSrc . '"></script>' . $lnEnd;
    }

    // Generate script declarations
    foreach ($this->_script as $type => $content)
    {
      $strHtml .= $tab . '<script type="' . $type . '">' . $lnEnd;
      $strHtml .= $content . $lnEnd;
      $strHtml .= $tab . '</script>' . $lnEnd;
    }

    return $strHtml;
  }



  /**
   * function radio button yes no
   *
   * @param	 $name	: ten radio
   * @param	 $yes   : trang thai yes-no	
   * @return : string
   */
  function radio_yesno ($name = "radiobutton", $yes = 0)
  {
    global $vnT, $conf;
    $txt = '';
    for ($i = 1; $i >= 0; $i --)
    {
      $txt .= '<input type="radio" name="' . $name . '" id="' . $name . '" value="' . $i . '" ' . (($i == $yes) ? "checked" : "") . ' >' . (($i) ? $vnT->lang["yes"] : $vnT->lang["no"]) . '&nbsp;&nbsp;&nbsp;';
    }
    return $txt;
  }
  
  /**
   * function checkbox  
   *
   * @param	 :
   * @return : 
   */
  function checkbox ($name, $value = "", $checked = 0, $ext = "")
  {
    global $vnT, $conf;
    return '<input type="checkbox" name="' . $name . '" value="' . $value . '" ' . (($checked == 1) ? "checked" : "") . ' class="checkbox"  ' . $ext . ' >';
  }
  
  /**
   * function selectbox yes no
   *
   * @param	 $name	: ten radio
   * @param	 $yes   : trang thai yes-no	
   * @return : string
   */
  function list_yesno ($name, $yes = 0, $ext = "")
  {
    global $vnT, $conf;
    $output = '<select name="' . $name . '" id="' . $name . '"  class="select"  ' . $ext . ' >';
    for ($i = 1; $i >= 0; $i --)
    {
      $output .= '<option value="' . $i . '" ' . (($i == $yes) ? "selected" : "") . ' >' . (($i) ? $vnT->lang["yes"] : $vnT->lang["no"]) . '</option>';
    }
    
    $output .= '</select>';
    return $output;
  }
  
	/**
   * function list_radio  
   *
   * @param	 :
   * @return : 
   */
  function list_radio ($name, $arr = array(), $did = "", $ext = "") 
  {
    global $vnT, $conf;
		$list="";
		$i= 0;
		foreach ($arr as $k => $v)
    {
			$i++;
			$checked="";
			
			if($i==1) $checked = " checked " ;
			if($did) {	$checked = ($k == $did) ? " checked " : "" ;			}
      $list .= '<input type="radio" name="' . $name . '" value="' . $k . '" ' . $checked . ' class="radio"  ' . $ext . ' > '.$v.' &nbsp;&nbsp; ';
    }
		 return $list; 
  }
	
  /**
   * function selectbox
   *
   * @param	 :
   * @return : 
   */
  function selectbox ($name, $arr = array(), $value = "", $fisrtSel = "", $ext = "")
  {
    global $vnT, $conf;
    $list = '<select name="' . $name . '" id="' . $name . '" size="1" ' . $ext . ' class="select form-control" >';
    $list .= (! empty($fisrtSel)) ? '<option value="">' . $fisrtSel . '</option>' : '';
    while (list ($v, $name) = each($arr))
    {
      $list .= '<option value="' . $v . '" ' . (($value == $v) ? "selected" : "") . '>' . $name . '</option>';
    }
    $list .= '</select>';
    return $list;
  }
  
  /**
   * Write a <a></a> element
   *
   * @access	public
   * @param	string 	The relative URL to use for the href attribute
   * @param	string	The target attribute to use
   * @param	array	An associative array of attributes to add
   */
  function link ($url, $text, $attribs = null)
  {
    
    return '<a href="' . $url . '" ' . $attribs . '>' . $text . '</a>';
  }
  
  /**
   * Write a <img></amg> element
   *
   * @access	public
   * @param	string 	The relative or absoluete URL to use for the src attribute
   * @param	string	The target attribute to use
   * @param	array	An associative array of attributes to add
   */
  function image ($url, $alt, $attribs = null)
  {
    
    if (strpos($url, 'http') !== 0)
    {
      $url = ROOT_URL . '/' . $url;
    }
    
    return '<img src="' . $url . '" alt="' . $alt . '" ' . $attribs . ' />';
  }
  
  /**
   * Write a <iframe></iframe> element
   *
   * @access	public
   * @param	string 	The relative URL to use for the src attribute
   * @param	string	The target attribute to use
   * @param	array	An associative array of attributes to add
   * @param	string	The message to display if the iframe tag is not supported
   */
  function iframe ($url, $name, $attribs = null, $noFrames = '')
  {
    
    return '<iframe src="' . $url . '" ' . $attribs . ' name="' . $name . '">' . $noFrames . '</iframe>';
  }
	
	/**
   * function selectbox order 
   *
   * @param	 $name	: ten radio
   * @param	 $yes   : trang thai 
   * @return : string
   */
  function list_order ($name, $did = "desc", $ext = "")
  {
    global $vnT, $conf;
    $output = '<select name="' . $name . '" id="' . $name . '"  class="select"  ' . $ext . ' >';
		
		$output .= '<option value="asc" ' . ( ($did == "asc") ? 'selected' : '' ) . ' >' . $vnT->lang['asc'] . '</option>';
		
		$output .= '<option value="desc" ' . ( ($did == "desc") ? 'selected' : '' ) . ' >' . $vnT->lang['desc'] . '</option>';
    
    $output .= '</select>';
    return $output;
  }

}
