<!-- BEGIN: edit -->
<script language="javascript" >
$(document).ready(function() {

  // validate signup form on keyup and submit
  $.validator.addMethod( "checkuser", function( value, element ) {
    return this.optional( element ) || /^[a-zA-Z0-9_.]{4,30}$/mg.test( value );
  }, "{LANG.err_username_invalid}" );


  jQuery.validator.addMethod("re_password", function( value, element ) {
		if($("#password").val()!='' || $("#re_password").val()!='' ){
			if($("#password").val()!=$("#re_password").val()) {
				return false	;
			}else{
				return true ;		
			}
		}else{
			return true ;	
		}		
	}, "{LANG.err_re_password_incorrect}");

  // validate signup form on keyup and submit
  $.validator.addMethod( "alphanumeric", function( value, element ) {
    return this.optional( element ) || /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=])[^\s]{6,}$/mg.test( value );
  }, "{LANG.err_password_invalid}" );


  $('#myForm').validate({
		rules: {			
				username: {
					required: true,
					minlength: 3,
                    checkuser: true
				},
			  password : {
				/*alphanumeric:true*/
			  },
				re_password : "re_password",
				email: {
					required: true,
					email: true
				}
	    },
	    messages: {	    	
				username: {
						required: "{LANG.err_text_required}",
						minlength: "{LANG.err_length} 3 {LANG.char}" 
				},
				email: {
						required: "{LANG.err_text_required}",
						email: "{LANG.err_email_invalid}" 
				} 
		}
	});

  setTimeout(function(){
    $("input[type=password]").attr('readonly', false);
  },500);

});
</script>
<div class="boxFo1rm">
{data.err}
<form action="{data.link_action}" method="post" name="myForm" id="myForm" class="validate">
<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
<tr class="form-required">
	<td width="150"  nowrap="" class="row1"><strong>Username :</strong> </td>
	<td  class="row0"><input name="username" type="text" id="username" size="40" maxlength="50" value="{data.username}" class="form-control" style="width: 400px" {data.readonly} autocomplete="off" ></td>
</tr>



	<tr>
		<td nowrap="" class="row1"><strong>Password :</strong> </td>
		<td class="row0"><input name="password" type="password" id="password" size="40" maxlength="50" value="" class="form-control" style="width: 400px" autocomplete="{data.rand_autocomplete}" readonly></td>
	</tr>

<tr>
	<td nowrap="" class="row1"><strong>Nhập lại Password :</strong> </td>
	<td class="row0"><input name="re_password" type="password" id="re_password" size="40" maxlength="50" value="" class="form-control" style="width: 400px" autocomplete="{data.rand_autocomplete}" readonly ></td>
</tr>
	<!-- BEGIN: html_pass_old -->

	<tr>
		<td nowrap="" class="row1"><strong>Password cũ:</strong> </td>
		<td class="row0"><input name="password_old" type="password" id="password_old" size="40" maxlength="50" value="" class="form-control" style="width: 400px" autocomplete="{data.rand_autocomplete}" readonly> <span class="font_err">(Nhập Password cũ nếu muốn thay đổi mật khẩu)</span></td>
	</tr>

	<!-- END: html_pass_old -->


<tr class="form-required">
	<td nowrap="" class="row1" ><strong>Email :</strong> </td>
	<td class="row0" ><input name="email" type="text" id="email" size="40" maxlength="50" value="{data.email}" class="form-control" style="width: 400px"></td>
</tr>
<tr >
	<td nowrap="" class="row1" ><strong>{LANG.group} : </strong></td>
	<td class="row0" >{data.list_group}</td>
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
</div>
<!-- END: edit -->

<!-- BEGIN: manage -->
<form action="{data.link_fsearch}" method="post" name="myform">
<table width="100%" border="0" cellspacing="2" cellpadding="2" align="center" class="tableborder">
  <tr>
    <td width="150" nowrap="" align="left">{LANG.totals}: &nbsp;</td>
    <td  align="left"><b class="font_err">{data.totals}</b></td>
  </tr>

  </table>
</form>
{data.err}
{data.table_list}
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1" class="bg_tab">
  <tr>
    <td  height="25">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: manage -->