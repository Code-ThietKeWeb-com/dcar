
<!-- BEGIN: edit -->
{data.err}
<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm"  class="validate">
    <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

        <tr >
            <td class="row1" width="25%" >{LANG.group} : </td>
            <td  align="left" class="row0">{data.list_cat}</td>
        </tr>
        <tr class="form-required" >
            <td  colspan="2" class="row1" ><p>{LANG.note_add_email} : </p> <textarea name="text_email" cols="10" rows="7" style="width:95%" class="textarea"></textarea></td>
        </tr>
        <tr align="center">
            <td class="row1" >&nbsp; </td>
            <td class="row0" >
                <input type="hidden" name="do_submit"	 value="1" />
                <input type="submit" name="btnSubmit" value="Submit" class="button">
                <input type="reset" name="btnReset" value="Reset" class="button">
            </td>
        </tr>
    </table>
</form>
<br>
<!-- END: edit -->


<!-- BEGIN: send_mail -->
<script language="javascript" >
  $(document).ready(function() {
    $('#myForm').validate({
      rules: {
        subject: {
          required: true
        }
      },
      messages: {
        subject: {
          required: "{LANG.err_text_required}"
        }
      }
    });

  });


</script>
{data.err}
<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm"  class="validate">
    <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
        <tr>
            <td colspan="2"  align="center" class="row0" ><strong>{data.send_for}</strong></td>
        </tr>
        <tr class="form-required" >
            <td class="row1" width="25%" >Subject : </td>
            <td  align="left" class="row0"><input type="text" id="subject" name="subject" size="60" value="{data.subject}" ></td>
        </tr>
        <tr>
        <tr >
            <td class="row1" colspan="2"><p>Content : </p> {data.html_content} </td>
        </tr>
        <tr>

        <tr align="center">
            <td class="row1" >&nbsp; </td>
            <td class="row0" >
                <input type="hidden" name="do_submit"	 value="1" />
                <input type="submit" name="btnSubmit" value="Submit" class="button">
                <input type="reset" name="btnReset" value="Reset" class="button">
            </td>
        </tr>
    </table>
</form>
<br>
<!-- END: send_mail -->

<!-- BEGIN: manage -->
<div class="box-fillter" style="overflow: hidden">
    <div class="well well-sm fillter">
    <div class="row">
        <div class="col-md-8 col-xs-12">

                <form action="{data.link_fsearch}" method="post" name="fSearch" class="form-inline ">

                    <div class="input-group"  >
                        <label class="small ng-binding">{LANG.group}</label>
                        {data.list_cat}
                    </div>



                    <div class="input-group"   >
                        <label class="small ng-binding">Email</label>
                        <input name="keyword" value="{data.keyword}" size="20" type="text" class="form-control" style="width: 100%" />

                    </div>
                    <div class="searchbtn">
                        <button type="submit" class="btn btn-primary ng-binding" name="btnGo" value="Search"  ><i class="fa fa-search"></i> Search</button>
                    </div>

                    <div class="searchbtn">
                        <a href="{data.link_download}" target="_blank" class="btn btn-success" style="color: #FFFFFF"><span><i class="fa fa-download" aria-hidden="true"></i> Download Email List</span></a>
                    </div>

                </form>
                <div class="div-totals">
                    Tổng cộng : <b class="font_err">{data.totals}</b>
                </div>
            </div>


        <div class="col-md-4 col-xs-12">
            <form action="{data.link_action}" method="post" name="fSearch" class="form-inline ">

                <div class="input-group" >
                    <label class="small ng-binding">Hiện Popup Nhận Tin</label>
                    <select name="popup_newsletter" id="popup_newsletter" class="form-control">{data.list_popup_newsletter}</select>
                </div>



                <div class="searchbtn">
                    <button type="submit" class="btn btn-primary ng-binding" name="btnUpdate" id="btnUpdate" value="Update"  ><i class="fa fa-search"></i> Cập nhật</button>
                </div>


            </form>
        </div>
    </div>
    </div>

</div>

{data.err}
{data.table_list}
<br />
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1" class="bg_tab">
  <tr>
    <td  height="25">{data.nav}</td>
  </tr>
</table>
<br />

<!-- END: manage -->

