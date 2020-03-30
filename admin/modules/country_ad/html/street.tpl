<!-- BEGIN: edit -->
{data.err}
<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="news"  class="validate">
<table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center class="admintable">
		<tr class="form-required" >
      <td  class="row1" width="30%" > Thành phố : </td>
      <td class="row0" >{data.list_city} </td>
		</tr>
      
    <tr class="form-required" >
      <td  class="row1" width="30%" > Quận huyện : </td>
      <td class="row0" ><span id="ext_state">{data.list_state}</span></td>
		</tr>
    <tr class="form-required" >
      <td  class="row1"> Mã đường : </td>
      <td class="row0"><input name="code" type="text"  size="10" maxlength="250" value="{data.code}" />  </td>
		</tr>      
         
    <tr class="form-required"  >
      <td  class="row1">Tên đường : </td>
      <td class="row0"><input name="name" type="text" id="name" size="50" maxlength="250" value="{data.name}" /></td>
    </tr>
    

    <tr align="center">
      <td  class="row1">&nbsp;</td>
      <td class="row0" >
          <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
      <input type="hidden" name="do_submit" value="1" />
      <input type="hidden" name="h_code" value="{data.code}" />
      <input type="submit" name="btnSubmit" value="Submit" class="button" / >
      <input type="reset" name="Submit2" value="Reset" class="button" />           
	   </td>
    
    </tr>
</table>
</form>
<br>
<!-- END: edit -->

<!-- BEGIN: manage -->
{data.err}
<form action="{data.link_search}" method="post"  name="fSearch"  >
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1" class="tableborder">
  
  <tr  >
      <td  width="15%" > <strong>Thành phố :</strong> </td>
      <td c >{data.list_city} </td>
		</tr>
      
    <tr >
      <td  > <strong>Quận huyện : </strong></td>
      <td  ><span id="ext_state">{data.list_state}</span></td>
		</tr>
    <tr>
		<td   ><strong>Tìm theo :</strong></td>
	  <td >{data.list_search} <strong> Từ khóa :</strong> <input name="keyword"  value="{data.keyword}"type="text"> <input name="btnSearch" type="submit" value=" Search ! "></td>
  </tr>
 
    <tr>
    <td ><strong>Tổng cộng  : </strong></td>
    <td ><span class="font_err"><strong>{data.totals}</strong></span></td>
  </tr>
    
</table>
</form>
<br />
{data.table_list}

<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1">
  <tr>
    <td  height="30">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: manage -->