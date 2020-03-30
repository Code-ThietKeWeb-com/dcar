<!-- BEGIN: manage -->
 {data.err} 
<br> 
<form id="form1" name="form1" method="post" action="{data.link_action}">
<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
        
    <tr >
      <td align="right"  class="row1" width="20%" > <strong>{LANG.cache} : </strong></td>
      <td  align="left" class="row0">{data.list_cache}</td>
    </tr>
    
    <tr >
      <td align="right"  class="row1" > <strong>Cấu hình cache trang chủ : </strong></td>
      <td  align="left" class="row0">
      <table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100" nowrap="nowrap">Xóa cache theo :</td>
    <td width="100" nowrap="nowrap"><input type="radio" name="cache_conf[main][clear_type]"  id="r_main" value="1" {data.main.checked1} /> Theo thời gian</td>
    <td><input name="cache_conf[main][cache_time]" id="cache_time_main" type="text"  size="10"  value="{data.main.cache_time}" > giờ</td>
    <td width="120" nowrap="nowrap">|  <input type="radio" name="cache_conf[main][clear_type]"  id="r_main" value="0" {data.main.checked0} /> Xóa thủ công</td>
    <td width="100" nowrap="nowrap">Size: <strong>{data.main.cache_size}</strong></td>
    <td><input  type="button" name="btnClearMod" value="Xóa cache" class="button" onclick="location.href='{data.link_clear_main}'"></td>
  </tr>
</table>
      </td>
    </tr>


<!-- BEGIN: html_mod -->
  <tr height=20 class="row_title">
    <td colspan="2" ><strong>Modules {row.module_name}</strong></td>
  </tr>
  <tr >
      <td align="right"  class="row1" > <strong>Cache của List : </strong></td>
      <td  align="left" class="row0">
      	<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100" nowrap="nowrap">Xóa cache theo :</td>
    <td width="100" nowrap="nowrap"><input type="radio" name="cache_conf[{row.module}][list][clear_type]"  id="r_{row.module}" value="1" {row.list.checked1} /> Theo thời gian</td>
    <td><input name="cache_conf[{row.module}][list][cache_time]" id="cache_time_{row.module}" type="text"  size="10"  value="{row.list.cache_time}" > giờ</td>
    <td width="120" nowrap="nowrap">|  <input type="radio" name="cache_conf[{row.module}][list][clear_type]"  id="r_{row.module}" value="0" {row.list.checked0} /> Xóa thủ công</td>
    <td width="100" nowrap="nowrap">Size: <strong>{row.list.cache_size}</strong></td>
    <td><input  type="button" name="btnClearMod" value="Xóa cache" class="button" onclick="location.href='{row.list.link_clear}'" ></td>
  </tr>
</table>

      </td>
    </tr>
  <tr >
      <td align="right"  class="row1" > <strong>Cache của chi tiết : </strong></td>
      <td  align="left" class="row0">
      	<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100" nowrap="nowrap">Xóa cache theo :</td>
    <td width="100" nowrap="nowrap"><input type="radio" name="cache_conf[{row.module}][detail][clear_type]"  id="r_{row.module}" value="1" {row.detail.checked1} /> Theo thời gian</td>
    <td><input name="cache_conf[{row.module}][detail][cache_time]" id="cache_time_{row.module}_detail" type="text"  size="10"  value="{row.detail.cache_time}" > giờ </td>
    <td width="120" nowrap="nowrap">|  <input type="radio" name="cache_conf[{row.module}][detail][clear_type]"  id="r_{row.module}" value="0" {row.detail.checked0} /> Xóa thủ công</td>
    <td width="100" nowrap="nowrap">Size: <strong>{row.detail.cache_size}</strong></td>
    <td><input  type="button" name="btnClearMod" value="Xóa cache" class="button" onclick="location.href='{row.detail.link_clear}'"></td>
  </tr>
</table>

      </td>
    </tr>    
<!-- END: html_mod -->    
   <tr height=10 >
    <td colspan="2" ><strong>Các module còn lại: </strong></td>
  </tr>
      <tr >
      <td align="right"  class="row1" > Giới thiệu ,trang tĩnh, Liên hệ ...</td>
      <td  align="left" class="row0">
      <table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100" nowrap="nowrap">Xóa cache theo :</td>
    <td width="100" nowrap="nowrap"><input type="radio" name="cache_conf[other][clear_type]"  id="r_other" value="1" {data.other.checked1} /> Theo thời gian</td>
    <td><input name="cache_conf[other][cache_time]" id="cache_time_main" type="text"  size="10"  value="{data.other.cache_time}" > giờ</td>
    <td width="120" nowrap="nowrap">|  <input type="radio" name="cache_conf[other][clear_type]"  id="r_other" value="0" {data.other.checked0}  /> Xóa thủ công</td>
    <td width="100" nowrap="nowrap">Size: <strong>{data.other.cache_size}</strong></td>
    <td><input  type="button" name="btnClearMod" value="Xóa cache" class="button" onclick="location.href='{data.link_clear_other}'"></td>
  </tr>
</table>
      </td>
    </tr>

      <tr align="center"  height="50"  >
       
        <td  align="right" class="row1" >&nbsp; </td>
         <td class="row0" >
    <input type="hidden" name="do_submit" value="1">
   <input type="submit" name="btnEdit" value="Update >>" class="button"> &nbsp;	
   
   <input  type="button" name="btnClear" value="Xóa tất cả cache" class="button" onclick="location.href='{data.link_clear_all}'" />  
		
     
    </td>
  </tr>
    </table>

</form> 
 
<br />
<!-- END: manage -->