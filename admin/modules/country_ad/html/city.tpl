<!-- BEGIN: edit -->
<script language=javascript>
$(document).ready(function() {
	$('#myForm').validate({
		rules: {			
				country: {
					required: true
				},
				
				name: {
					required: true,
					minlength: 3
				},
				code: {
					required: true 
				}
	    },
	    messages: {
	    	country: {
						required: "{LANG.err_select_required}"
				} ,
				name: {
						required: "{LANG.err_text_required}",
						minlength: "{LANG.err_length} 3 {LANG.char}" 
				} ,
				code: {
						required: "{LANG.err_text_required}" 
				} 
		}
	});
});
</script>
{data.err}
<form action="{data.link_action}" method="post" name="myForm"  id="myForm"  class="validate">
 <table width="100%"  border="0" cellspacing="2" cellpadding="2"  class="admintable">

			<tr class="form-required" >
            <td  class="row1" width="30%" > Quốc gia : </td>
            <td class="row0" >{data.list_country} </td>
		  </tr>
      
       <tr class="form-required" >
            <td  class="row1"> Mã tĩnh thành : </td>
            <td class="row0"><input name="code" id="code" type="text"  size="10" maxlength="250" value="{data.code}" /> 
            </td>
		  </tr>      
         
          <tr class="form-required"  >
            <td  class="row1">Tên tỉnh thành (VN): </td>
            <td class="row0"><input name="name" type="text" id="name" size="50" maxlength="250" value="{data.name}" /></td>
          </tr>
           <tr class="form-required"  >
            <td  class="row1">Tên tỉnh thành (EN) : </td>
            <td class="row0"><input name="name_en" type="text" id="name_en" size="50" maxlength="250" value="{data.name_en}" /></td>
          </tr>

          <tr align="center">
            <td  class="row1">&nbsp;</td>
						<td class="row0" >
                            <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
            <input type="hidden" name="h_code" value="{data.code}" />
            <input type="hidden" name="do_submit" value="1" />
            <input type="submit" name="btnSubmit" value="Submit" class="button" / >
            <input type="reset" name="Submit2" value="Reset" class="button" />           
             </td></tr>
        </table>
    </form>
<br>
<!-- END: edit -->

<!-- BEGIN: manage -->
<div class="box-fillter">
    <div class="well well-sm fillter">
        <form action="{data.link_fsearch}" method="post" name="fSearch" class="form-inline md5">

            <div class="input-group"  >
                <label class="small ng-binding">Quốc gia</label>
                {data.list_country}
            </div>
            <div class="input-group"  >
                <label class="small ng-binding">Trạng thái</label>

                {data.list_display}

            </div>

            <div class="input-group" style="width: 35%" >
                <label class="small ng-binding">{LANG.search} </label>

                <div class="s-item">
                    <div  class="item col-5">{data.list_search}</div>
                    <div  class="item col-7"><input name="keyword" value="{data.keyword}" size="20" type="text" class="form-control" style="width: 100%" /></div>
                    <div class="clear"></div>
                </div>

            </div>



            <div class="searchbtn">
                <button type="submit" class="btn btn-primary ng-binding" name="btnGo" value="Search"  ><i class="fa fa-search"></i> Search</button>
            </div>
        </form>
        <div class="div-totals">
            Tổng cộng : <b class="font_err">{data.totals}</b>  | &nbsp;<a href="{data.link_excel}" target="_blank"><img src="{DIR_IMAGE}/icon_excel.gif"  alt="excel" align="absmiddle"/> <b>Xuất ra file Excel theo điều kiện Search</b> </a>
        </div>
    </div>

</div>


{data.err}

{data.table_list}
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1">
  <tr>
    <td  height="30">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: manage -->