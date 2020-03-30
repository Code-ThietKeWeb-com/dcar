<?php
	
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
  
  <tr  >
    <td  class="row1" >Folder : </td>
    <td  class="row0"> <strong class="font_err"><?php echo $data['folder']; ?></strong> </td> 
  </tr>

   <tr>
    <td class="row1">Tham số : </td>
    <td  class="row0">
    <table  border="0" cellspacing="0" cellpadding="0">
  
    <tr>
      <td>Num bits</td>
      <td><input name="params[bits]" type="text" size="20" maxlength="250" value="<?php echo $params['bits']; ?>"  ></td>
    </tr>
    <tr>
      <td>Speed</td>
      <td><input name="params[speed]" type="text" size="20" maxlength="250" value="<?php echo $params['speed']; ?>"  ></td>
    </tr>
    <tr>
      <td>Bangs</td>
      <td><input name="params[bangs]" type="text" size="20" maxlength="250" value="<?php echo $params['bangs']; ?>"  ></td>
    </tr>
    <tr>
      <td>List Color</td>
      <td><input name="params[colours]" type="text" size="70" maxlength="250" value="<?php echo $params['colours']; ?>"  ></td>
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