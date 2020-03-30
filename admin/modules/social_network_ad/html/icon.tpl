
<!-- BEGIN: edit -->
{data.err}
 <form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm" class="validate">
<table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center class="admintable">
	
 
	<tr class="form-required">
		<td width="20%" align="right" class="row1">Title : </td>
		<td width="80%" align="left" class="row0"><input name="title" type="text" id="title" size="50" maxlength="250" value="{data.title}"></td>
	</tr>
	<tr class="form-required">
		<td  align="right" class="row1"> Link : </td>
		<td align="left" class="row0"><input name="link" type="text"  size="70" maxlength="250" value="{data.link}" /></td>
	</tr>
	<tr>
		<td align="right" class="row1"> Image :  </td>
		<td align="left" class="row0"><div id="ext_picture" class="picture" >{data.pic}</div>
      <input type="hidden" name="picture"	 id="picture" value="{data.picture}" />
      <div id="btnU_picture" class="div_upload" {data.style_upload} ><div class="button2"><div class="image"><a title="Add an Image" class="thickbox" id="add_image" href="?mod=media&act=popup_media&type=image&&module=weblink&folder=weblink&obj=picture&TB_iframe=true&width=900&height=474" >Chọn hình</a></div></div></div>              
    </td>
	</tr>
   <tr >
            <td class="row1" align="right"  >Target : </td>
			<td align="left" class="row0">{data.list_target}</td>
          </tr>
	 
      
      <tr>
		  	<td align="right" class="row1">{LANG.display}:&nbsp;</td>
			<td align="left" class="row0">{data.list_display}</td>
		  </tr>	
      
      
	
	<tr align="center">
  	<td  align="right" class="row1">&nbsp;  </td>
    
		<td class="row0">
			<input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
			<input type="hidden" name="do_submit" value="1">
			<input type="submit" name="btnAdd" value="Submit" class="button">
			<input type="reset" name="Submit2" value="Reset" class="button">            
		</td>
	</tr>
</table>
</form>
	<br />
<!-- END: edit -->

<!-- BEGIN: manage -->
<br>
<form action="{data.link_fsearch}" method="post" name="myform">
<table width="100%" border="0" cellspacing="2" cellpadding="2" align="center" class="tableborder">

  <tr>
    <td align="left" width="15%">{LANG.totals} : </td>
    <td align="left"><strong>{data.totals}</strong></td>
  </tr>
  <tr>
  </table>
</form>
{data.err}
<br />
{data.table_list}
<br />
<!-- END: manage -->

