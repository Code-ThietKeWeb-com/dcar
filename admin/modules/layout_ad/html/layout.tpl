
<!-- BEGIN: edit -->
<script language="javascript" >
$(document).ready(function() {
	$('#myForm').validate({
		rules: {			
				title: {
					required: true,
					minlength: 3
				},
				name: {
					required: true,
					minlength: 2
				}
	    },
	    messages: {	    	
				title: {
						required: "{LANG.err_text_required}",
						minlength: "{LANG.err_length} 3 {LANG.char}" 
				},
				name: {
						required: "{LANG.err_text_required}",
						minlength: "{LANG.err_length} 2 {LANG.char}" 
				} 
		}
	});
});
</script>
<br>
{data.err}
      <form action="{data.link_action}" method="post" id="myForm" name="myForm"  class="validate" >
        <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable" >
            
  
            <tr class="form-required" >
              <td class="row1" width="20%" align="right"><b>Block Name:</b><br /></td>
              <td  class="row0" width="80%" align="left"><input name="name" id="name" value="{data.name}" size="50" type="text" /></td>
            </tr>
            <tr class="form-required" >
              <td class="row1" align="right" ><b>Block Title:</b><br /></td>
              <td  class="row0" align="left" ><input name="title" id="title" value="{data.title}" size="50"  type="text" /></td>
            </tr>
			<tr>
              <td  class="row1" align="right"><b>Type Block :</b><br /></td>
              <td class="row0" align="left" >{data.list_type}</td>
            </tr>
			 <tr id="tr_des" {data.style_des}>
              <td  class="row1" align="right"><b>Block Content :</b><br /></td>
              <td class="row0" align="left" >{data.html_content}</td>
            </tr>
			<tr>
              <td  class="row1" align="right"><b>Align :</b><br /></td>
              <td class="row0" align="left" >{data.list_align}</td>
            </tr>
						<tr>
              <td  class="row1" align="right"><b>Save cache :</b></td>
              <td class="row0" align="left" >{data.list_cache}</td>
            </tr>
			<tr>
              <td  class="row1" align="right"><b>Page show :</b><br /></td>
              <td class="row0" align="left" >{data.list_module_show}</td>
            </tr>
            <tr>
            <td  class="row1" align="right">&nbsp;</td>
              <td class="row0">
              <input type="hidden" name="do_submit" id="do_submit" value="1">
              <input name="btnSubmit" type="submit" value="Submit" class="button" />&nbsp;
              <input name="btnReset" type="reset" value="Reset" class="button" />
              </td>
            </tr>
        </table>
      </form>
<br>
<!-- END: edit -->

<!-- BEGIN: manage -->
<form action="{data.link_fsearch}" method="post" name="myform">
 <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable" >
  <tr>
    <td align="left" width="15%" class="row1">Modules : </td>
    <td align="left" class="row0">{data.list_module}</td>
  </tr>
</table>	
</form>
<br />
{data.err}
<form action="{data.link_action}" method="post" name="f_config" id="f_config" >
<table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable" >

      <tr class="row_title" >
        <td  width="45%" class="row_title" align="center">---------- <strong> Block left</strong> -------------</td>
        <td  class="row_title">&nbsp;</td>
        <td width="45%" class="row_title" align="center">---------- <strong> Block right </strong> -------------</td>
      </tr>
      <tr>
        <td valign="top" class="row0">
		
			<table width="100%" border="0" cellspacing="2" cellpadding="2">
			{data.box_left}
			<tr>
			  <td width="50%" align="right" >&nbsp;</td>
			  <td width="50%"  align="left">&nbsp;</td>
			</tr>
			</table>
		
		</td>
		
        <td valign="top" class="row0">
		
		<table width="100%" border="0" cellspacing="2" cellpadding="2">
          {data.box_middle}
		  <tr>
            <td width="50%" align="right">&nbsp;</td>
            <td width="50%"  align="left">&nbsp;</td>
          </tr>

        </table>
		
		</td>
		
        <td valign="top" class="row0">
		
		<table width="100%" border="0" cellspacing="2" cellpadding="2">
         {data.box_right}
		  <tr>
            <td width="50%" align="right">&nbsp;</td>
            <td width="50%"  align="left">&nbsp;</td>
          </tr>

        </table>
		
		</td>
      </tr>
      <tr>
        <td valign="top">&nbsp;</td>
        <td valign="top" align="center"> <input type="submit" name="btnEdit" value="Update &gt;&gt;" class="button" /></td>
        <td valign="top">&nbsp;</td>
      </tr>
    </table>
	</form>

<br>
<!-- END: manage -->

<!-- BEGIN: config_layout -->
<form action="{data.link_fsearch}" method="post" name="myform">
<table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable" >
  <tr>
    <td align="left" width="15%" class="row1">Modules : </td>
    <td align="left" class="row0">{data.list_module}</td>
  </tr>
</table>	
</form>
<br />
 {data.err}
	<form action="{data.link_action}" method="post" name="theAdminForm" id="theAdminForm">
		
      <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable" >

            <tr class="row_title">
              <td class="font_title" align="center" width="33%">Left Column</td>
              <td class="font_title" align="center" width="1%"></td>
              
              <td class="font_title" align="center" width="33%">Right Column</td>
            </tr>
            <tr>
              <td class="row0" valign="middle" width="33%"><div align="center">
                <select name="layout_l_col" class="dropdown" multiple="multiple" size="20" style="width:200px">
                  {data.option_left}
                </select>
              </div></td>
              <td class="row1" valign="middle" width="1%"><div align="center">
                <input name="lm_right" value="&gt;&gt;" size="30" onClick="addItems(this.form.layout_l_col, this.form.layout_r_col); removeItems(this.form.layout_l_col);" class="textinput" type="button" />
                <br />
                <input name="lm_left" value="&lt;&lt;" size="30" onClick="addItems(this.form.layout_r_col, this.form.layout_l_col); removeItems(this.form.layout_r_col);" class="textinput" type="button" />
              </div></td>
              <td class="row0" valign="middle" width="33%"><div align="center">
                <select name="layout_r_col" class="dropdown" multiple="multiple" size="20" style="width:200px">
                  {data.option_right}
                </select>
              </div></td>
            </tr>
            <tr>
              <td class="row0" valign="middle" width="33%"><div align="center">
                <input name="left_up" value="Up" size="30" onClick="moveUpList(this.form.layout_l_col);" class="textinput" type="button" />
                <input name="left_down" value="Down" size="30" onClick="moveDownList(this.form.layout_l_col);" class="textinput" type="button" />
                <br />
              </div></td>
              <td class="row1" valign="middle" width="1%">&nbsp;</td>
              <td class="row0" valign="middle" width="33%"><div align="center">
                <input name="right_up" value="Up" size="30" onClick="moveUpList(this.form.layout_r_col);" class="textinput" type="button" />
                <input name="right_down" value="Down" size="30" onClick="moveDownList(this.form.layout_r_col);" class="textinput" type="button" />
                <br />
              </div></td>
            </tr>
            <tr>
              <td class="pformstrip" colspan="5" align="center">
							<input name="layout_l" value="" type="hidden">
							<input name="layout_r" value="" type="hidden">
							<input name="btnSubmit" type="submit" id="button" accesskey="s" onClick="this.form.layout_l.value = makeStringFromSelect(this.form.layout_l_col);  this.form.layout_r.value = makeStringFromSelect(this.form.layout_r_col);" value="Submit Layout" class="button" /></td>
            </tr>
          </tbody>
        </table>

    </form>

<!-- END: config_layout -->

<!-- BEGIN: list_block -->
<br />
{data.err}
<br />
{data.table_list}
<table width="95%"  border="0" align="center" cellspacing="1" cellpadding="1">
  <tr>
    <td  height="30">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: list_block -->