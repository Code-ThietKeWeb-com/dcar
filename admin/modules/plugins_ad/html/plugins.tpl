<!-- BEGIN: edit -->
{data.err}
<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm"  class="validate">
<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
    <tr>
  		<td class="row1" width="30%" >Type: </td>
			 <td  class="row0" >{data.list_type}</td>
		  </tr>
		  <tr class="form-required">
			 <td class="row1">Nick name: </td>
			 <td class="row0"><input name="nick" type="text" size="50" maxlength="250" value="{data.nick}"></td>
		  </tr>
		   <tr>
			 <td class="row1">{LANG.department} : </td>
			 <td  class="row0"><input name="title" type="text" size="50" maxlength="250" value="{data.title}"></td>
		  </tr>		  
		  <tr>
			 <td class="row1">{LANG.fullname} : </td>
			 <td  class="row0"><input name="name" type="text" size="50" maxlength="250" value="{data.name}"></td>
		  </tr>
		  <tr>
			 <td  class="row1">{LANG.phone} : </td>
			 <td class="row0"><input name="phone" type="text" size="50" maxlength="250" value="{data.phone}"></td>
		  </tr>

		
			
		<tr align="center">
    <td class="row1" >&nbsp; </td>
			<td class="row0" >
				<input type="hidden" name="do_submit"	 value="1" />
				<input type="submit" name="btnAdd" value="Submit" class="button">
				<input type="reset" name="Submit2" value="Reset" class="button">
			</td>
		</tr>
	</table>
</form>


<br>
<!-- END: edit -->

<!-- BEGIN: manage -->
<form action="{data.link_fsearch}" method="post" name="myform">
<table width="100%" border="0" cellspacing="2" cellpadding="2" align="center" class="tableborder">
  <tr>
    <td width="15%" align="left">{LANG.totals}: &nbsp;</td>
    <td width="85%" align="left"><b class="font_err">{data.totals}</b></td>
  </tr>
</table>
</form>
{data.err}
<br />
{data.table_list}
<br />
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1" class="bg_tab">
  <tr>
    <td  height="25">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: manage -->