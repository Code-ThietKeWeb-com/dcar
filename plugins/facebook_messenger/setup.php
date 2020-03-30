<?php
  if (@file_exists(PATH_PLUGINS . DS . $folder. DS ."lang/".$lang.".php"))
  {
    include ("lang/".$lang.".php");
  }else{
    $lang_plugins = array() ;
  }
  
?>
<form action="<?php echo $link_action ?>" method="post" enctype="multipart/form-data" name="myForm"  class="validate">
<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
	<tr  >
    <td  class="row1" >Name : </td>
    <td  class="row0"> <strong class="font_err"><?php echo $data['name']; ?></strong> </td> 
  </tr>
  
  <tr class="form-required" >
    <td  class="row1" width="20%">Title : </td>
    <td  class="row0"><input name="title" type="text" size="60" maxlength="250" value="<?php echo $data['title']; ?>"  >
  </td>
  </tr>


   <tr>
    <td class="row1">Tham số : </td>
    <td  class="row0">
    <table  border="0" cellspacing="0" cellpadding="0">
  
    <tr>
      <td><?php echo $lang_plugins['fanpage_url']; ?> : </td>
      <td><input name="params[fanpage_url]" type="text" size="50" maxlength="250" value="<?php echo $params['fanpage_url']; ?>"  >
        <span class="font_err" >Example :  https://www.facebook.com/congtyweb/ </span>
      </td>
    </tr>

      <tr>
        <td><?php echo $lang_plugins['type_show']; ?> : </td>
        <td><?php echo vnT_HTML::selectbox("params[type_show]", array(1=>'Dạng Icon','2'=>'Dang chat cuối trang'), $params['type_show']); ?></td>
      </tr>

      <tr>
        <td><?php echo $lang_plugins['postion']; ?> : </td>
        <td><?php echo vnT_HTML::selectbox("params[postion]", array("left"=>'Chạy bên trái',"right"=>'Chạy bên phải' ), $params['postion']); ?></td>
      </tr>

      <tr>
        <td><?php echo $lang_plugins['fb_width']; ?> x <?php echo $lang_plugins['fb_height']; ?> : </td>
        <td><input name="params[fb_width]" type="text" size="10" maxlength="250" value="<?php echo $params['fb_width']; ?>"  > x <input name="params[fb_height]" type="text" size="10" maxlength="250" value="<?php echo $params['fb_height']; ?>"  > px</td>
      </tr>
    
  </table>

    </td>
  </tr>
  
  <tr>
    <td class="row1">Hiển thị : </td>
    <td  class="row0"><?php echo $data['list_display']?></td>
  </tr>



		<tr align="center">
    <td class="row1" >&nbsp; </td>
			<td class="row0" >
				<input type="hidden" name="do_submit"	 value="1" />
				<input type="submit" name="btnSubmit" value="Submit" class="button">
				<input type="reset" name="btnReset" value="Reset" class="button">
			</td>
		</tr>
	</table>
</form>