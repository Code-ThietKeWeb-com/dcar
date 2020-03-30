
<!-- BEGIN: edit -->
<script language=javascript>
	function show_type(cat) 
	{		
 		switch (cat)
		{
			case '1' :  
				$("#ext_type1").show();
				$("#ext_type2").hide();
			break ;
			case '2' : 
				$("#ext_type1").hide();
				$("#ext_type2").show(); 
			break ; 
			default: 
				$("#ext_type1").show();
				$("#ext_type2").hide();
		}	
		
	} 
</script>
{data.err}
 <form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm" class="validate">
<table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center class="admintable">
	
 
	<tr class="form-required">
		<td width="20%" align="right" class="row1">Title : </td>
		<td width="80%" align="left" class="row0"><input name="title" type="text" id="title" size="50" maxlength="250" value="{data.title}"></td>
	</tr>
  
  <tr  >
		<td width="20%" align="right" class="row1">{LANG.type} : </td>
		<td width="80%" align="left" class="row0">{data.list_type}</td>
	</tr>
	<tbody id="ext_type1" {data.style1} >
	<tr  >
		<td align="right" class="row1"> Image :  </td>
		<td align="left" class="row0">{data.pic} 
    
    <div class="ext_upload">
      <input name="chk_upload" type="radio" value="0" checked> 
      Insert URL's image &nbsp; <input name="l_url" type="text" size="50" maxlength="250" value="{data.l_url}" onchange="do_ChoseUpload('ext_upload',0);" >  <br>
      <input name="chk_upload" type="radio" value="1"> Upload Picture &nbsp;&nbsp;&nbsp;
      <input name="image" type="file" id="image" size="30" maxlength="250" onchange="do_ChoseUpload('ext_upload',1);">
      </div>
              
    </td>
	</tr>
  <tr >
		<td  align="right" class="row1"> Link : </td>
		<td align="left" class="row0"><input name="link" type="text"  size="70" maxlength="250" value="{data.link}" /></td>
	</tr>
  
  </tbody>
  <tbody id="ext_type2" {data.style2} >
  <tr >
  	<td align="right" class="row1"> Nội dung :  </td>
		<td align="left" class="row0"><textarea name="script" id="script" rows="5" cols="50" style="width:95%" >{data.picture}</textarea>
    </td> 
  </tr>
      </tbody>
  
   
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

<div style="padding:10px;">    
	<h3 class="font_title" style="padding-bottom:10px;">TÀI LIỆU THAM KHẢO</h3>
  <div class="list" style="padding-left:10px;">
  	<ul style="list-style:none; margin-left:10px;">
    	<li style="padding:2px;list-style-type:square"><a href="http://developers.facebook.com/docs/plugins/" target="_blank">Tài liệu tham khảo cho Facebook</a></li>
      <li  style="padding:2px;list-style-type:square"><a href="https://developers.google.com/+/web/+1button/" target="_blank">Tài liệu tham khảo cho Google Plus</a></li>
      <li  style="padding:2px;list-style-type:square"><a href="https://dev.twitter.com/docs/tweet-button" target="_blank">Tài liệu tham khảo cho Twitter</a></li>
    </ul>
  </div>
</div>

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

