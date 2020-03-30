<!-- BEGIN: manage -->
<div class="boxForm">
<form action="{data.link_action}" method="post" name="f_config" id="f_config" >
{data.err}

    <div class="container-fluid">
        <div class="row-title"><div class="f-title">Search Engine Optimization (SEO)</div></div>
        <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

            <tr>
                <td class="row1" valign="top" width="20%" >Friendly Title :</td>
                <td class="row0"><input name="friendly_title" id="friendly_title" type="text" size="70" maxlength="250" value="{data.friendly_title}" class="textfield"   style="width: 100%"></td>
            </tr>
            <tr>
                <td class="row1">Meta Keyword :</td>
                <td class="row0"><input name="metakey" id="metakey" type="text" size="70" maxlength="250" value="{data.metakey}" class="textfield"  style="width: 100%"></td>
            </tr>
            <tr>
                <td class="row1">Meta Description : </td>
                <td class="row0"><textarea name="metadesc" id="metadesc" rows="3" cols="50"    class="textarea"  style="width: 100%">{data.metadesc}</textarea></td>
            </tr>
            <tr>
                <td class="row1">Rebuild Link SEO: </td>
                <td class="row0"><input type="button" name="Rebuild" value=" &nbsp; Rebuild Link &nbsp;" class="button" onclick="location.href='{data.link_rebuild}'" /></td>
            </tr>
        </table>
    </div>



    <div class="container-fluid" style="margin-top: 15px">
         <div class="row-title"><div class="f-title">{LANG.setting_module}</div></div>
          <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

                <tr >
                    <td   class="row1" width="20%"> <strong>{LANG.n_list} : </strong></td>
                    <td  align="left" class="row0"><input name="n_list" type="text" size="20" maxlength="250" value="{data.n_list}" class="textfiled"></td>
                </tr>



                <tr>
                    <td class="row1"  >{LANG.imgthumb_width} :</td>
                    <td class="row0"><input name="imgthumb_width" size="20" type="text" class="textfiled" value="{data.imgthumb_width}" onKeyPress="return is_num(event,'imgthumb_width')"></td>
                </tr>


                <tr>
                    <td  class="row1"  >{LANG.img_width} :</td>
                    <td class="row0"><input name="img_width" size="20" type="text" class="textfiled" value="{data.img_width}"  onkeypress="return is_num(event,'img_width')"></td>
                </tr>
            </table>

       

    </div>


    <div class="form-footer">
        <div class="div-button">
            <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
            <input type="hidden" name="do_submit" id="do_submit" value="1" />
            <button type="submit" name="btnSubmit" id="btnSubmit" value="Submit" class="btn btn-primary"><span>Submit</span></button>
            <button type="reset" name="btnReset" id="btnReset" value="Reset" class="btn btn-default"><span>Cancel</span></button>
        </div>
    </div>
</form>
</div> 


<!-- END: manage -->