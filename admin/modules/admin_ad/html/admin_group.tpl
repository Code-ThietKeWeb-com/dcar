
<!-- BEGIN: edit -->
<script type="text/javascript" >

	function checkAction(group,act)	{
		group_name = "action["+group+"][]";
		act_name = group+"["+act+"][]";
		for ( i=0;i < document.fAdmin.elements.length ; i++ ){
			if (document.fAdmin.elements[i].type=="checkbox" && document.fAdmin.elements[i].name==group_name && document.fAdmin.elements[i].value==act){
				checked = document.fAdmin.elements[i].checked;
				
			}
			
			if (document.fAdmin.elements[i].type=="checkbox" && document.fAdmin.elements[i].name==act_name){
				if (checked==true){
						document.fAdmin.elements[i].checked = true;
				}else{
						document.fAdmin.elements[i].checked = false;
				}
			}
			
		}
	
	}
	
	function checkOne(group,act)	{
		ok=0;
		group_name = "action["+group+"][]";
		act_name = group+"["+act+"][]";
		
		for ( i=0;i < document.fAdmin.elements.length ; i++ ){
			
			if (document.fAdmin.elements[i].type=="checkbox" && document.fAdmin.elements[i].name==act_name){
				if(document.fAdmin.elements[i].checked){
					ok=1;
				}
			}
		}
		
		for ( i=0;i < document.fAdmin.elements.length ; i++ ){
			if (document.fAdmin.elements[i].type=="checkbox" && document.fAdmin.elements[i].name==group_name && document.fAdmin.elements[i].value==act){
				if(ok==1){
					document.fAdmin.elements[i].checked = true;
				}else{
					document.fAdmin.elements[i].checked = false;
				}
				
			}
		}
		
	}
	
	function showPermission(obj) {			
		var type = obj.value;
		if ( type == '0' ){
			getobj("trQuyen").style.display = "none";
		}else{
			getobj("trQuyen").style.display = "";
		}
	}

	function checkform(f) {			
		var title = f.title.value;
		if (title == '') {
			alert('Vui l�ng t�n nh�m ');
			f.title.focus();
			return false;
		}
		return true;
	}
	
</script>
{data.err}
<form action="{data.link_action}" method="post" name="fAdmin" id="fAdmin" class="validate">
<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
<tr class="form-required">
	<td width="20%" align="right" class="row1"><strong>{LANG.group_name} :</strong> </td>
	<td  class="row0"><input name="title" id="title" type="text" size="50" maxlength="250" value="{data.title}"></td>
</tr>
<tr>
	<td align="right" class="row1"><strong>{LANG.permission} :</strong> </td>
	<td class="row0">{data.list_phanquyen}</td>
</tr>

<tr>
	<td align="right" class="row1" >&nbsp;</td>
	<td height="50" class="row0" >
		<input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
	<input name="do_submit" id="do_submit" type="hidden" value="1">
	<input type="submit" name="Submit" value="Submit" class="button">&nbsp;&nbsp;
    <input type="reset" name="reset" value="Reset" class="button">
    </td>
</tr>
</table>
</form>
<script language="javascript" >
$(document).ready(function() {
	$('#myForm').validate({
		rules: {			
				title: {
					required: true,
					minlength: 3
				}
	    },
	    messages: {	    	
				title: {
						required: "{LANG.err_text_required}",
						minlength: "{LANG.err_length} 3 {LANG.char}" 
				} 
		}
	});
});
</script>
<br />
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