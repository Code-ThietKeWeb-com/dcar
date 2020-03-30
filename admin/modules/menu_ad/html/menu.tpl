<!-- BEGIN: edit -->
<script language="javascript" >
$(document).ready(function() {
	$('#myForm').validate({
		rules: {
				pos: {
					required: true 
				},			
				title: {
					required: true,
					minlength: 3
				} 
				
	    },
	    messages: {	 
				pos: {
						required: "{LANG.err_select_required}"
				},			   	
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
	
     <tr>
            <td class="row1" width="25%" >{LANG.position} : </td>
		 <td class="row0" > <select name="pos" id="pos"   onchange="vnTMenu.changePos(this.value);" >{data.list_pos}</select></td>
          </tr>
          <tr class="form-required" >
            <td  class="row1">{LANG.menu_name} : </td>
            <td  class="row0"><input name="title" id="title" type="text" size="60" maxlength="250" value="{data.title}"  >
			</td>
          </tr>
  		<tr   >
            <td  class="row1">Name Action : </td>
            <td  class="row0"><input name="name" id="name" type="text" size="30" maxlength="250" value="{data.name}"  > <span class="font_err">Dùng để xác định trạng thái Current của menu</span>
			</td>
          </tr>	  
		  <tr  >
            <td class="row1">{LANG.menu_link} : </td>
            <td  class="row0"><input name="menu_link" id="menu_link" type="text" size="60" maxlength="250" value="{data.menu_link}" class="textfield"></td>
          </tr>

		<tr >
			<td class="row1"  >{LANG.picture} </td>
			<td class="row0">
				<div class="input-group" style="max-width: 500px;">
					<input type="text" name="picture"  id="picture"    class="form-control"  value="{data.picture}" />
					<div class="input-group-btn"><button type="button" class="button btnBrowseMedia" value="Browse server" data-obj="picture" data-mod="" data-folder="File" data-type="file" ><span class="img">Image</span></button></div>
				</div>

			</td>
		</tr>

		   <tr>
            <td class="row1">{LANG.target} : </td>
            <td  class="row0">{data.list_target}</td>
          </tr>
          
          
		<tr>
            <td  class="row1"> {LANG.sub_menu_of} : </td>
            <td class="row0"><span id="ext_parent">{data.list_parent}</span></td>
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
<div class="box-fillter">
	<div class="well well-sm fillter">
		<form action="{data.link_fsearch}" method="post" name="fSearch" class="form-inline md4">

			<div class="row">
				<div class="col-xs-6"> <select name="pos" id="pos" class="form-control" onchange="submit();" style="width: 100%">{data.list_pos}</select></div>
				<div class="col-xs-6">
					<div class="text-right" style="line-height: 25px">Tổng cộng : <b class="font_err">{data.totals}</b></div>
				</div>
			</div>
		</form>
	</div>

</div>

{data.err}
<form id="manage" name="manage" method="post" action="{data.link_action}">
	<div class="box-manage">
		<div class="nav-action nav-top">{data.button}</div>
		<div class="table-list table-responsive">

			<table  class="table table-sm table-bordered table-hover " id="table_list" >
				<thead>
				<tr height="25">
					<th width="5%" align="center" ><input type="checkbox" value="all" class="checkbox" name="checkall" id="checkall"/></td>
					<th width="10%" align="center" >{LANG.order}</th>
					<th width="10%" align="center" >Name</th>
					<th width="30%" align="left" >{LANG.title}</th>
					<th width="35%" align="left" >Link</th>
					<th width="10%"  align="center" >Action</th>
				</tr>
				</thead>
				<tbody>

				<!-- BEGIN: html_row -->
				<tr class="{row.class}" id="{row.row_id}">
					<td align="center" >{row.check_box}</td>
					<td align="left" >{row.order}</td>
					<td align="center" >{row.name}</td>
					<td align="left" >{row.title}</td>
					<td align="left" >{row.menu_link}</td>
					<td align="center" >{row.action}</td>
				</tr>
				<!-- END: html_row -->

				<!-- BEGIN: html_row_no -->
				<tr class="row0" >
					<td  colspan="7" align="center" class="font_err" >{mess}</td>
				</tr>
				<!-- END: html_row_no -->
				</tbody>
			</table>
		</div>
		<div class="nav-action nav-bottom">{data.button}</div>
	</div>
	<input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
<input type="hidden" name="do_action" id="do_action" value="" >
</form>
<br />
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1" class="bg_tab">
  <tr>
    <td  height="25">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: manage -->