


<!-- BEGIN: admin_session -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
    <tr>
        <td height="25" class="font_title" style="border-bottom:2px solid #B84120 ;">Admin Sessions in 30 minutes</td>
    </tr>
    <tr>
        <td>

        </td>
    </tr>
</table>

<div class="table-list table-responsive">

    <table  class="table table-sm table-bordered table-hover " id="table_list" >
        <thead>
        <tr>
            <th class="row_title" width="30%" align="center">Username</th>
            <th class="row_title" width="15%" align="center">Time</th>
            <th class="row_title" width="15%" align="center">IP</th>
            <th class="row_title" width="15%" align="center">Action</th>
            <th class="row_title" width="15%" align="center">Sub</th>
            <th class="row_title" width="10%" align="center"  >ID</th>
        </tr>
        </thead>
        <tbody>

        <!-- BEGIN: html_item -->
        <tr>
            <td class="row" align="left"><b><a href="?mod=admin&act=adminsession&id={row.adminid}">{row.username}</a></b></td>
            <td class="row" align="center">{row.time}&nbsp;</td>
            <td class="row" align="center">{row.ip}&nbsp;</td>
            <td class="row" align="center">{row.act}&nbsp;</td>
            <td class="row" align="center">{row.sub}&nbsp;</td>
            <td class="row" align="center" >{row.pid}&nbsp;</td>
        </tr>
        <!-- END: html_item -->

        </tbody>
    </table>
</div>

</br>

<!-- END: admin_session -->


<!-- BEGIN: manage -->
<table width="100%"  border="0" align="center" cellspacing="2" cellpadding="2"  class="tableborder"  >
    <tr>
        <td>{data.table_list_time}</td>
    </tr>
</table>
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
