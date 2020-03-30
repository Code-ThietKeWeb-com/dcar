
<!-- BEGIN: html_form_search -->
<table width="100%" border="0" cellspacing="1" cellpadding="1" class="box_menu_content">
<tr><td align="center">
<form action="{data.link_action}" method="post" name="fSearch">
<table border="0" cellspacing="2" cellpadding="2" align="center">
  <tr>
    <td>{LANG.title_view} : </td>
    <td>{LANG.day} <input type="text" name="day" size="10" value="{data.day}"></td>
    <td>{LANG.month} {data.list_thang}</td>
    <td>{LANG.year} {data.list_nam}</td>
    <td><input type="submit" name="btnGo" value="Search" class="button"></td>
  </tr>
</table>
</form>
{data.back}
</td></tr>
</table> 
<!-- END: html_form_search -->

<!-- BEGIN: html_result_day -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
  <tr>
    <td height="30"  align="center" >{data.text_total}</td>
  </tr>
  <tr>
    <td height="30" class="font_title" style="border-bottom:2px solid #B84120 ;">{LANG.stats_statistics}</td>
  </tr>
  <tr>
    <td>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
  <tr align="center">
    <td width="20%"><strong>Time</strong></td>
    <td width="20%"><strong>IP</strong></td>
    <td width="30%"><strong>Operating system</strong></td>
    <td width="30%"><strong>Browser</strong></td>
  </tr>
  {data.html_row}
</table></td>
</tr>
</table><br>

<table width="100%"  border="0" align="center" cellspacing="1" class="bg_tab" cellpadding="1">
  <tr>
    <td  height="25">{data.nav}</td>
  </tr>
</table>
<!-- END: html_result_day -->



<!-- BEGIN: html_web_referer -->
{data.back}
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
  
  <tr>
    <td>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
  <tr >
    <td width="10%" align="center"><strong>STT</strong></td>
    <td width="40%"><strong>Domain</strong></td>
    <td width="20%" align="center"><strong>Num Click</strong></td>
    <td width="20%"align="center"><strong>Date </strong></td>
  </tr>
  {data.html_row}
</table></td>
</tr>
</table><br>

<table width="100%"  border="0" align="center" cellspacing="1" class="bg_tab" cellpadding="1">
  <tr>
    <td  height="25">{data.nav}</td>
  </tr>
</table>
<!-- END: html_web_referer -->

<!-- BEGIN: html_manage -->
<br />
	
	<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
  <tr>
    <td height="30" class="font_title" style="border-bottom:2px solid #B84120 ;">{LANG.chart_statistics}</td>
  </tr>
  <tr>
    <td>
	
	<table width="100%"  border="0" cellspacing="1" cellpadding="1">
    <form name="myform" action="" method="post">
	<tr>
      <td width="100%" ><strong>{LANG.view_by} :</strong> {data.list_type}</td>
    </tr>
	</form>
    <tr>
      <td width="100%" >{data.sodo}</td>
    </tr>
	</table>
	
			</td>
  </tr>
</table>

<br>
<table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td width="33%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
  <tr>
    <td height="30" class="font_title" style="border-bottom:2px solid #B84120 ;">{LANG.detail_by_date}</td>
  </tr>
  <tr>
    <td>
         
		 
		 
		  <table width="100%"  border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td width="100%" >{data.chitiet} </td>
            </tr>
        </table>
		
		
		  </td>
  </tr>
</table>
	
	</td>
    <td width="34%" valign="top" style="padding:0px 5px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
      <tr>
        <td height="30" class="font_title" style="border-bottom:2px solid #B84120 ;">{LANG.detail_by_recent}</td>
      </tr>
      <tr>
        <td><table width="100%"  border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td width="100%" >{data.thongke} </td>
            </tr>
        </table></td>
      </tr>
    </table>
      <br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
      <tr>
        <td height="30" class="font_title" style="border-bottom:2px solid #B84120 ;">Browser</td>
      </tr>
      <tr>
        <td><table width="100%"  border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td width="100%" >{data.browser} </td>
            </tr>
        </table></td>
      </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
      <tr>
        <td height="30" class="font_title" style="border-bottom:2px solid #B84120 ;">Operating system</td>
      </tr>
      <tr>
        <td><table width="100%"  border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td width="100%" >{data.os} </td>
            </tr>
        </table></td>
      </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
      <tr>
        <td height="30"  style="border-bottom:2px solid #B84120 ;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="font_title">Nguồn Website truy cập tới</td>
    <td align="right" width="80" class="font_err"><a href="?mod=statistics&act=statistics&sub=web_referer">Xem tất cả &raquo;</a></td>
  </tr>
</table>
</td>
      </tr>
      <tr>
        <td><table width="100%"  border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td width="100%" >{data.web_referer} </td>
            </tr>
        </table></td>
      </tr>
    </table>
    
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
      <tr>
        <td height="30" class="font_title" style="border-bottom:2px solid #B84120 ;">{LANG.top_visited}</td>
      </tr>
      <tr>
        <td><table width="100%"  border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td width="100%" >{data.top10} </td>
            </tr>
        </table></td>
      </tr>
    </table></td>
    <td  valign="top">

		
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"  class="tableborder">
  <tr>
    <td height="30" class="font_title" style="border-bottom:2px solid #B84120 ;">{LANG.last_ip_visited} </td>
  </tr>
  <tr>
    <td>
          <table width="100%"  border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td width="100%" >{data.last_ip} </td>
            </tr>
        </table>
	</td>
  </tr>
</table>
	<br /></td>
  </tr>
</table>
<br>
<!-- END: html_manage -->