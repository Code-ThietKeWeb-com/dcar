
<!-- BEGIN: edit -->
<script language="javascript" >
$(document).ready(function() {
	$('#myForm').validate({
		rules: {			
				name: {
					required: true,
					minlength: 3
				},
				title: {
					required: true,
					minlength: 3
				}
	    },
	    messages: {
	    	
				name: {
						required: "{LANG.err_text_required}",
						minlength: "{LANG.err_length} 3 {LANG.char}" 
				} ,
				title: {
						required: "{LANG.err_text_required}",
						minlength: "{LANG.err_length} 3 {LANG.char}" 
				} 
		}
	});
});
</script>

{data.err}
<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm"  class="validate">
<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
	
		<tr class="form-required">
			<td class="row1" width="25%" >Site name : </td>
			<td  align="left" class="row0"><input name="name" id="name" type="text" size="50" maxlength="250" value="{data.name}" {data.readonly} ></td>
		</tr>
    <tr class="form-required">
			<td class="row1" >Title : </td>
			<td  align="left" class="row0"><input name="title" id="title" type="text" size="70" maxlength="250" value="{data.title}"></td>
		</tr>
    
		<tr >
    	<td class="row1" colspan="2" >
      <p class="font_err">{data.note}</p>
      {data.html_content}
      </td>
		</tr>

		<tr align="center">
    <td class="row1" >&nbsp; </td>
			<td class="row0" >
				<input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
				<input type="hidden" name="do_submit"	 value="1" />
				<input type="submit" name="btnSubmit" value="Submit" class="button">
				<input type="reset" name="btnReset" value="Reset" class="button">
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