<!-- BEGIN: edit -->
{data.err}
<style>
    .picture img {
        height: 30px;
    }
</style>
<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myform" class="validate">
    <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable">

        <tr class="form-required">
            <td width="24%" align="right" class="row1">Code name :</td>
            <td width="76%" align="left" class="row0"><input name="name" type="text" id="name" size="40" maxlength="250"  value="{data.name}"/></td>
        </tr>
        <tr class="form-required">
            <td align="right" class="row1">Title :</td>
            <td align="left" class="row0"><input name="title" type="text" id="title" size="40" maxlength="250" value="{data.title}"/></td>
        </tr>
        <tr>
            <td align="right" class="row1">Picture :</td>
            <td align="left" class="row0">
                <div id="ext_picture" class="picture" >{data.pic}</div>
                <input type="hidden" name="picture" id="picture" value="{data.picture}" />
                <div id="btnU_picture" class="div_upload" {data.style_upload} ><button type="button" class="button btnBrowseMedia" value="Browse server" data-obj="picture" data-mod="lang" data-folder="lang" data-type="image" ><span class="img"><i class="fa fa-image"></i> Chọn hình</span></button></div>
            </td>
        </tr>
        <tr>
            <td align="right" class="row1">Charset :</td>
            <td align="left" class="row0"><input name="charset" type="text" id="charset" size="40" maxlength="250"  value="{data.charset}"/></td>
        </tr>
        <tr>
            <td align="right" class="row1">Date format :</td>
            <td align="left" class="row0"><input name="date_format" type="text" id="date_format" size="40"  maxlength="250" value="{data.date_format}"/></td>
        </tr>
        <tr>
            <td align="right" class="row1">Time format :</td>
            <td align="left" class="row0"><input name="time_format" type="text" id="time_format" size="40"   maxlength="250" value="{data.time_format}"/></td>
        </tr>
        <tr>
            <td align="right" class="row1">Unit :</td>
            <td align="left" class="row0"><input name="unit" type="text" id="unit" size="40" maxlength="250"      value="{data.unit}"/></td>
        </tr>
        <tr>
            <td align="right" class="row1">Number format :</td>
            <td align="left" class="row0"><input name="num_format" type="text" id="num_format" size="40" maxlength="250"  value="{data.num_format}"/></td>
        </tr>
        <tr>
            <td align="right" class="row1">Is Default :</td>
            <td align="left" class="row0">{data.list_default}</td>
        </tr>
        <tr>
            <td align="right" class="row1">&nbsp;</td>
            <td class="row0">
                <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
                <input type="hidden" name="do_submit" value="1">
                <input type="submit" name="Submit" value="Submit" class="button">
                <input type="reset" name="Submit2" value="Reset" class="button">

            </td>
        </tr>
    </table>
</form>

<!-- END: edit -->

<!-- BEGIN: manage -->
{data.err}
{data.table_list}
<br/>

<!-- END: manage -->


<!-- BEGIN: edit_phrase -->
<form action="{data.link_action}" method="post" name="myform" id="myform">
    <table width="100%" border="0" cellspacing="1" cellpadding="1" align="center"  class="admintable">
        <tr >
            <td width="15%" class="row1">&nbsp;<strong>Language</strong> :</td>
            <td width="20%" class="row0">{data.list_lang}</td>
            <td width="15%" class="row1">{data.picture}</td>
            <td  class="row0"><input type="submit" name="btn_SetDefault" value="Set Default" class="button"/></td>
        </tr>
        <tr >
            <td class="row1"><strong> Type</strong>:</td>
            <td class="row0">{data.list_type}</td>
            <td align="right" class="row1"><strong>Phrase</strong> :</td>
            <td class="row0">{data.list_pharse}</td>
        </tr>
    </table>

    <br/>
    <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable">
        <tr bgcolor="#F7F7F7">
            <td colspan=2 height=25><strong>Header for &nbsp;<span class="font_err">{data.phrase}</span></strong></td>
        </tr>
        <tr>
            <td align="center" bgcolor="#FFFFFF"><textarea name="header" cols="80" rows="5"  class="form-control">{data.header}</textarea></td>
        </tr>
    </table>
    <br/>
    <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable">
        <tr class="row1">
            <td colspan=2 height=25><strong>{LANG.edit_pharse_lang}&nbsp;<span class="font_err">{data.phrase}</span></strong></td>
        </tr>
        <!-- BEGIN: row_lang -->
        <tr>
            <td valign="top" width="25%" class="row1"><strong>{row.varname}</strong></td>
            <td class="row0"><textarea name="cot[{row.varname}]" cols="50" rows="3" class="textarea"  style="width:100%;height:40px;">{row.text}</textarea></td>
        </tr>
        <!-- END: row_lang -->

    </table>
    <br/>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center"><input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" /><input name="btnUpdate" type="submit" value="Submit" class="button"/></td>
        </tr>
    </table>

</form><br/>

<!-- END: edit_phrase -->