<!-- BEGIN: edit -->
<div class="boxForm">
    <div class="container-fluid">
        <div class="row-title"><div class="f-title">{LANG.infomartion}</div></div>
        <div class="row">
            <div  class="col-md-6 col-xs-12">
                <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
                    <tr>
                        <td width="150" class="row1"  nowrap=""><strong>{LANG.full_name} </strong>:</td>
                        <td class="row0">{data.name}</td>
                    </tr>
                    <tr>
                        <td  class="row1"><strong>{LANG.address} </strong>:</td>
                        <td class="row0">{data.address}</td>
                    </tr>

                </table>

            </div>
            <div  class="col-md-6 col-xs-12">
                <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
                    <tr>
                        <td  class="row1" width="150"  nowrap=""><strong>Email</strong>:</td>
                        <td class="row0">{data.email}</td>
                    </tr>
                    <tr>
                        <td  class="row1"><strong>{LANG.phone} </strong>:</td>
                        <td class="row0">{data.phone}</td>
                    </tr>

                </table>

            </div>
        </div>


    </div>

    <div class="container-fluid" style="margin-top: 15px">
        <div class="row-title"><div class="f-title">{LANG.info_contact}</div></div>
        <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

            <tr>
                <td  class="row1" width="150" nowrap=""><strong>{LANG.subject} </strong>:</td>
                <td class="row0">{data.subject}</td>
            </tr>

            <tr>
                <td class="row1"  nowrap=""><strong>{LANG.content_contact} </strong>: </td>
                <td class="row0"><p align="justify"  >{data.content}</p></td>

            </tr>
        </table>
    </div>

</div>

<!-- END: edit -->


<!-- BEGIN: manage -->
<div class="box-fillter">
    <div class="well well-sm fillter">
        <form action="{data.link_fsearch}" method="post" name="fSearch" class="form-inline md6">

            <div class="input-group" style="width: 20%">
                <label class="small ng-binding">{LANG.view_day_from}</label>
                <div class="s-item s-date">
                    <div  class="item col-6"><input type="text" class="form-control datepicker" name="date_begin" style="width: 100%"  value="{data.date_begin}" placeholder="Từ" />
                    </div>
                    <div  class="item col-6" ><input type="text" class="form-control datepicker" name="date_end"  style="width: 100%"  value="{data.date_end}"   placeholder="đến"  />
                    </div>
                    <div class="clear"></div>
                </div>

            </div>

            <div class="input-group"  >
                <label class="small ng-binding">{LANG.status}</label>
                 <select class="form-control" name="status" id="status">{data.list_status}</select>
            </div>


            <div class="input-group" style="width: 40%" >
                <label class="small ng-binding">{LANG.search} </label>

                <div class="s-item">
                    <div  class="item col-5"><select class="form-control" name="search" id="search">{data.list_search}</select></div>
                    <div  class="item col-7"><input name="keyword" value="{data.keyword}" size="20" type="text" class="form-control" style="width: 100%" /></div>
                    <div class="clear"></div>
                </div>

            </div>
            <div class="searchbtn">
                <button type="submit" class="btn btn-primary ng-binding" name="btnGo" value="Search"  ><i class="fa fa-search"></i> Search</button>
            </div>
        </form>
        <div class="div-totals">
            Tổng cộng : <b class="font_err">{data.totals}</b>
        </div>
    </div>

</div>

{data.err}
{data.table_list}
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1" class="bg_tab">
  <tr>
    <td  height="25">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: manage -->