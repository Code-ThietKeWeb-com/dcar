<!-- BEGIN: manage -->
<br />
<form action="{data.link_action}" method="post" name="f_config" id="f_config" >
{data.err}

<div id="tabs">
		<ul>
				<li><a href="#tabConfig"><span>SEO Trang chủ</span></a></li>
				{data.list_li} 
		</ul>
		<div id="tabConfig">
				

 <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

 
   <tr >
    <td width="25%" align="right" class="row1"> <strong>{LANG.index_title} : </strong></td>
    <td  align="left" class="row0"><input name="indextitle" type="text"  size="50" maxlength="250" value="{data.indextitle}" style="width:95%"></td>
  </tr>
  <tr>
    <td align="right" class="row1"><strong> {LANG.meta_keyword} : </strong></td>
    <td  align="left" class="row0"><textarea name="meta_keyword" rows="3" cols="50" style="width:95%">{data.meta_keyword}</textarea></td>
    </tr>
  <tr >
    <td align="right" class="row1"><strong> {LANG.meta_description} : </strong></td>
    <td  align="left" class="row0"><textarea name="meta_description" rows="3" cols="50" style="width:95%">{data.meta_description}</textarea></td>
    </tr>
  
    
    <tr  >
     <td  align="right" class="row1" > <strong>Meta mở rộng :</strong></td>
     <td align="left" class="row0" ><textarea name="meta_extra" rows="4" cols="50" style="width:95%">{data.meta_extra}</textarea> </td>
    </tr>
   
    </table>

		</div>
		{data.list_div}
    
</div>

<div align="center" style="padding:10px;"> <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" /><input type="submit" name="btnUpdate" id="btnUpdate" value="Update >>" class="button"> </div>
</form>

<br />
<!-- END: manage -->