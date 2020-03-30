<?php
$vnT_Block = new block_menu ;
class block_menu extends Blocks {
  var $cache= true;
	 //
	function get_title() {
		global $conf,$vnT,$input;
    $this->title = $vnT->lang['menu']['f_title'];
		return $this->title;
  }

	function get_content() {
		global $DB,$conf,$func,$vnT,$input;
		
		$vnT->html->addStyleSheet( ROOT_URI."blocks/menu/css/block_menu.css");		
		
		$have_sub = 0;
		$java_menu = "";		
		$text ='<div id="box_menu"><ul >';
		$res = $DB->query("select * from menu where parentid=0 and display=1 and lang='$vnT->lang_name' and pos='vertical'   order by menu_order ASC ,menu_id DESC");
		if ($num_menu = $DB->num_rows($res))
		{
			$i=0;
			$act = "?" . $_SERVER['QUERY_STRING'];
      if (empty($_SERVER['QUERY_STRING']))
      {
        $act = $vnT->cmd . "=mod:" . $input['mod'];				
      }
			
			while ($row = $DB->fetch_row($res))
			{
				$i++; 
				
        $menu_id = $row['menu_id'];
        $menu_link = $vnT->func->HTML($row['menu_link']) ;			 	
			  $link = (! strstr($menu_link, "http://")) ? $vnT->link_root . $menu_link : $menu_link;
				
        $title = $vnT->func->HTML($row['title']);
        $target = $row["target"];
        //echo "<br>act = $act <br> link = $link <br>kq=".strstr($act,$link);
				
				$current = "";
 				if ($act==$row['name']) {
          $current = " class='current'";
        } 
				
 	 			$last =  ($i==$num) ? " class='last' " : "";
				
				$text .="<li id='menu_{$menu_id}' {$last}  ><a href='{$link}' target='{$target}' {$current} >".$title."</a></li>";													
				//check submenu
				$res_sub = $DB->query("select * from menu where parentid=".$row['menu_id']." and display=1 and lang='$vnT->lang_name' and pos='vertical'  order by menu_order ASC ,menu_id DESC");
				if($num_sub = $DB->num_rows($res_sub))
				{
					$have_sub=1;					
					$menuname = "menu_".$menu_id;	
					$java_menu .= "\n".'var '.$menuname.'= ms_menu.addMenu(document.getElementById("'.$menuname.'"));';
					$j=0;
					
					while($row_sub = $DB->fetch_row($res_sub))
					{
						$sub_name = $vnT->func->HTML($row_sub['title']);
            $sub_menu_link = $row_sub['menu_link'] ;			 	
						$link_sub = (! strstr($sub_menu_link, "http://")) ? $vnT->link_root . $sub_menu_link : $sub_menu_link;							
						$java_menu .= "\n".$menuname.'.addItem("&nbsp;'.$sub_name.' ", "'.$link_sub.'") ; '."\n";						
						//lay sub
						$java_menu .= $this->trans_add_submenus($row_sub['menu_id'],$menuname.'.items['.$j.']',$menuname);					
						
						$j++;
					}// end while sub
					
				}//end if sub
				
			}// end while row
		}
	
		$text .='</ul></div>';
		$data['content'] = $text;
		
		//have sub
		if($have_sub)
		{
			$vnT->html->addStyleSheet( $vnT->dir_js."/transmenuC/menu.css");
			$vnT->html->addScript($vnT->dir_js."/transmenuC/transmenuC.js");
			$vnT->html->addScriptDeclaration('
					function init() {		
					if (TransMenu.isSupported()) {
						TransMenu.initialize();
					}
					}
					window.onload = function () {
					 init();
				 }
			');		
			$data['js'] = '<script language="javascript">
				if (TransMenu.isSupported()) {
					var ms_menu = new TransMenuSet(TransMenu.direction.right, 1, 0, TransMenu.reference.topRight);
					'.$java_menu.'
					TransMenu.renderAll();
				}
			</script>';
		}
		
		$this->content = $this->html_block_content ($data);
		return $this->content;
	}

//------------
function trans_add_submenus($menu_id, $item, $menu_name = 'menu'){
	global $DB,$conf,$func,$vnT,$input;

	$text = "";	
	//check have item
	 
	$sql = "select * from menu where parentid=".$menu_id." and display=1  and lang='$vnT->lang_name' and pos='vertical'   order by menu_order ASC ,menu_id DESC" ;
	$result = $DB->query($sql);
	if($num = $DB->num_rows($result))
	{
		
		$text .="\n".'var submenu_'.$menu_id.' = '.$menu_name.'.addMenu('.$item.')';
		$cc=0;	
		while($row = $DB->fetch_row($result))
		{
			$title = $vnT->func->HTML($row['title']);
      $menu_link = $row['menu_link'] ;			 	
		  $link = (! strstr($menu_link, "http://")) ? $vnT->link_root . $menu_link : $menu_link;
			
			$text .= "\n".' submenu_'.$menu_id.'.addItem("&nbsp;'.$title.' ", "'.$link.'") ; ';
			
			//check sub
			$text .= $this->trans_add_submenus($row['menu_id'], 'submenu_'.$menu_id.'.items['.$cc.']' ,'submenu_'.$menu_id) ;
			
			$cc++;	
		}//end while		
	}
	
	return $text ;
}


 //====================== html_box_statistics ===
function html_block_content($data){
global $input,$vnT,$conf;
return<<<EOF
{$data['content']}
{$data['js']}
EOF;
}

// end class
}
?>