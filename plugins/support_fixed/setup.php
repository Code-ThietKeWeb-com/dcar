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
      <td><?php echo $lang_plugins['link_phone']; ?> : </td>
      <td><input name="params[link_phone]" type="text" size="50" maxlength="250" value="<?php echo $params['link_phone']; ?>"  > 
      </td>
    </tr>

    <tr>
      <td><?php echo $lang_plugins['link_messenger']; ?> : </td>
      <td><input name="params[link_messenger]" type="text" size="50" maxlength="250" value="<?php echo $params['link_messenger']; ?>"  > 
      </td>
    </tr>

    <tr>
      <td><?php echo $lang_plugins['link_zalo']; ?> : </td>
      <td><input name="params[link_zalo]" type="text" size="50" maxlength="250" value="<?php echo $params['link_zalo']; ?>"  > 
      </td>
    </tr>

      <tr>
        <td><?php echo $lang_plugins['color']; ?> : </td>
        <td><input name="params[color]" type="text" size="20" maxlength="250" value="<?php echo $params['color']; ?>"  ></td>
      </tr>

      <tr>
        <td><?php echo $lang_plugins['effect_color']; ?> : </td>
        <td><input name="params[effect_color]" type="text" size="20" maxlength="250" value="<?php echo $params['effect_color']; ?>"  ></td>
      </tr>


      <tr>
        <td><?php echo $lang_plugins['postion']; ?> : </td>
        <td><?php echo vnT_HTML::selectbox("params[postion]", array("left"=>'Bên trái',"right"=>'Bên phải' ), $params['postion']); ?></td>
      </tr>

      <tr>
        <td><?php echo $lang_plugins['distance']; ?>  : </td>
        <td><input name="params[distance_width]" type="text" size="10" maxlength="250" value="<?php echo $params['distance_width']; ?>"  > x <input name="params[distance_height]" type="text" size="10" maxlength="250" value="<?php echo $params['distance_height']; ?>"  > px</td>
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