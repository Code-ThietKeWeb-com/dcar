<!-- BEGIN: edit -->
{data.err}
<form action="{data.link_action}" method="post" name="myForm" id="myForm" enctype="multipart/form-data" class="validate"   >
    <div class="postbox">
        <div class="handlediv"></div>
        <h3 class="hndle">
            <span>THAY ĐỔI GIAO DIỆN ADMIN</span>
        </h3>
        <div class="inside">
            <table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center class="adminlist">
                <tr>
                    <td align="left" class="row0">
                        <div class="c_skin">
                            <div class="c_title">
                                Chọn theo mẫu có sẵn
                            </div>
                            {data.list_skin}
                        </div>
                    </td>
                </tr>
                <tr align="center">
                    <td class="row0" >
                        <div class="c_skin_footer">
                            <input type="submit" name="btnAdd" value="Update" class="button newButton" />
                        </div>
                        <input type="hidden" name="do_submit" value="1">
                    </td></tr>
            </table>
        </div>
    </div>

</form>
<!-- END: edit -->