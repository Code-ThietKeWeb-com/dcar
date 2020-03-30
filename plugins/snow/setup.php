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
      <td>Chọn hiệu ứng</td>
      <td><?php
      	 echo vnT_HTML::selectbox("params[imgfolder]", array('snow'=>'Mùa đông','tet'=>'Mùa Tết','valentin'=>'Lễ Tình Yêu','halloween'=>'Halloween'), $params['imgfolder']);
			?></td>
    </tr>
    
    <tr>
      <td>usePNG</td>
      <td><?php echo vnT_HTML::list_yesno("params[usePNG]",$params['usePNG']); ?></td>
    </tr>
    <tr>
      <td>flakeTypes</td>
      <td><input name="params[flakeTypes]" type="text" size="20" maxlength="250" value="<?php echo $params['flakeTypes']; ?>"  ></td>
    </tr>
    <tr>
      <td>flakesMax</td>
      <td><input name="params[flakesMax]" type="text" size="20" maxlength="250" value="<?php echo $params['flakesMax']; ?>"  ></td>
    </tr>
    <tr>
      <td>vMax</td>
      <td><input name="params[vMax]" type="text" size="20" maxlength="250" value="<?php echo $params['vMax']; ?>"  ></td>
    </tr>
    <tr>
      <td>flakeWidth</td>
      <td><input name="params[flakeWidth]" type="text" size="20" maxlength="250" value="<?php echo $params['flakeWidth']; ?>"  ></td>
    </tr>
    <tr>
      <td>flakeHeight</td>
      <td><input name="params[flakeHeight]" type="text" size="20" maxlength="250" value="<?php echo $params['flakeHeight']; ?>"  ></td>
    </tr>
    <tr>
      <td>snowCollect</td>
      <td> <?php echo vnT_HTML::list_yesno("params[snowCollect]",$params['snowCollect']); ?></td>
    </tr>
     <tr>
      <td>snowCollect</td>
      <td> <?php echo vnT_HTML::list_yesno("params[snowCollect]",$params['snowCollect']); ?></td>
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