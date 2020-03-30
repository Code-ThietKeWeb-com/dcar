<!-- BEGIN: edit -->
<script language=javascript>
  $(document).ready(function () {
    $('#myForm').validate({
      rules: {
        nick: {
          required: true,
          minlength: 3
        }
      },
      messages: {

        nick: {
          required: "{LANG.err_text_required}",
          minlength: "{LANG.err_length} 3 {LANG.char}"
        }
      }
    });
  });
</script>
{data.err}
<div class="boxForm">
<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm"
      class="validate">
    <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable">
        <tr>

        <tr>
            <td class="row1" width="130" nowrap="">{LANG.fullname} :</td>
            <td class="row0"><input name="name" type="text" size="50" maxlength="250" value="{data.name}" class="form-control"></td>
        </tr>
        <tr>
            <td class="row1">{LANG.department} :</td>
            <td class="row0"><input name="title" type="text" size="50" maxlength="250" value="{data.title}" class="form-control"></td>
        </tr>

        <tr>
            <td class="row1">{LANG.phone} :</td>
            <td class="row0"><input name="phone" type="text" size="50" maxlength="250" value="{data.phone}" class="form-control"></td>
        </tr>
        <tr>
            <td class="row1">Email:</td>
            <td class="row0"><input name="email" type="text" size="50" maxlength="250" value="{data.email}" class="form-control"></td>
        </tr>

        <tr>
            <td class="row1">List chat:</td>
            <td class="row0">
                <table>
                    <tr>
                        <td width="90"><b>Zalo :</b></td>
                        <td><input type="text"name="nick[zalo]" value="{data.zalo}"/></td>
                    </tr>
                    <tr>
                        <td><b>Skype :</b></td>
                        <td><input type="text" name="nick[skype]" value="{data.skype}"/>
                        <td></td>
                    </tr>
                    <tr>
                        <td><b>Viber :</b></td>
                        <td><input type="text" name="nick[viber]" value="{data.viber}"/>
                        <td></td>
                    </tr>
                    <tr>
                        <td nowrap=""><b>Messenger :</b></td>
                        <td><input type="text" name="nick[messenger]" value="{data.messenger}"/>
                        <td></td>
                    </tr>

                </table>
            </td>
        </tr>

        <tr align="center">
            <td class="row1">&nbsp;</td>
            <td class="row0">
                <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}"/>
                <input type="hidden" name="do_submit" value="1"/>
                <input type="submit" name="btnAdd" value="Submit" class="button">
                <input type="reset" name="Submit2" value="Reset" class="button">
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
            <td width="100" nowrap="" align="left">{LANG.totals}: &nbsp;</td>
            <td align="left"><b class="font_err">{data.totals}</b></td>
        </tr>
    </table>
</form>
{data.err}
{data.table_list}
<table width="100%" border="0" align="center" cellspacing="1" cellpadding="1" class="bg_tab">
    <tr>
        <td height="25">{data.nav}</td>
    </tr>
</table>
<br/>
<!-- END: manage -->