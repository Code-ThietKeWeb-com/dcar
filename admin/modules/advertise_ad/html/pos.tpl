<!-- BEGIN: edit -->
<script language=javascript>
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
<table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center class="admintable">
		<tr class="form-required">
			<td class="row1" width="25%">{LANG.name}: </td>
			<td class="row0"><input type="text" name="name" id="name" value="{data.name}" class="textfiled"  size="20" {data.readonly} ></td>
		</tr>
    
	<tr class="form-required" >
			<td class="row1">{LANG.title}: </td>
			<td class="row0"><input type="text" name="title" id="title" value="{data.title}" class="textfiled"  size="50"></td>
		</tr> 
    
   <tr>
			<td class="row1">{LANG.type_show}: </td>
			<td class="row0">{data.list_type_show}</td>
		</tr>		        
    
  <tr>
  	<td class="row1"> {LANG.info}: </td>
    <td  class="row0" >
    <strong>{LANG.width}</strong>: <input type="text" value="{data.width}" size="2" name="width" class="textfiled"  />(px)&nbsp;&nbsp;&nbsp;
    <strong>{LANG.height}</strong>: <input type="text" value="{data.height}" size="2" name="height" class="textfiled"  />(px)&nbsp;&nbsp;&nbsp;
    <strong>Align</strong>: {data.list_align}
    </td>
  </tr>  
            
  <tr align="center">
  	<td class="row1">&nbsp;  </td>
     <td class="row0">
		 <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
			<input type="hidden" name="do_submit" value="1"  />
			<input type="submit" name="btnSubmit" value="Submit" class="button">
      <input type="reset" name="Submit2" value="Reset" class="button">            
	</td></tr>
</table>
</form>

<br>
<!-- END: edit -->

<!-- BEGIN: manage -->
<form action="{data.link_fsearch}" method="post" name="myform">
{data.err}
<table width="100%"  border="0" align="center" cellspacing="0" cellpadding="0" class="tableborder">

	<tr>
		<td ><strong>{LANG.totals}  : </strong><span class="font_err"><strong>{data.totals}</strong></span></td>
	</tr>
</table>


</form>
{data.table_list}

<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1">
  <tr>
    <td  height="30">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: manage -->