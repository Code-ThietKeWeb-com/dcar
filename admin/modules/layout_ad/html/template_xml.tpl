<!-- BEGIN: edit -->
{data.err}
<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm"  class="validate">
    <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
        <tr class="form-required">
            <td class="row1" width="20%">{LANG.title} </td>
            <td class="row0"><input name="title" type="text" size="50" maxlength="250" value="{data.title}" class="textfield"  style="width: 99%"></td>
        </tr>

        <tr >
            <td class="row1" width="20%">{LANG.picture}: </td>
            <td class="row0">
                {data.pic}
                <input name="chk_upload" type="radio" value="0" checked>
                Insert URL's image &nbsp; <input name="picture" type="text" size="50" maxlength="250" > <br>
                <input name="chk_upload" type="radio" value="1"> Upload Picture &nbsp;&nbsp;&nbsp;
                <input name="image" type="file" id="image" size="30" maxlength="250" style="display: inline-block">
            </td>
        </tr>

        <tr >
            <td class="row1" >Mô tả </td>
            <td class="row0"><textarea  name="description" id="description" rows="2" style="width: 99%">{data.description}</textarea></td>
        </tr>


        <tr >
            <td class="row1" >Nội dung HTML: </td>
            <td class="row0"> {data.html_content}</td>
        </tr>

        <tr style="display: none" >
            <td class="row1" >Nội dung CSS: </td>
            <td class="row0"><textarea  name="content_css" id="content_css" rows="15" style="width: 99%">{data.content_css}</textarea> </td>
        </tr>

        <tr>
            <td class="row1" width="20%">Display: </td>
            <td class="row0">{data.list_display}</td>
        </tr>
        <tr align="center">
            <td class="row1" >&nbsp; </td>
            <td class="row0" >
                <input type="hidden" name="do_submit"	 value="1" />
                <input type="submit" name="btnAdd" value="Submit" class="button">
                <input type="reset" name="Submit2" value="Reset" class="button">
            </td>
        </tr>
    </table>
</form>
<br>
<!-- END: edit -->

<!-- BEGIN: manage -->
<form action="{data.link_fsearch}" method="post" name="fSearch">
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