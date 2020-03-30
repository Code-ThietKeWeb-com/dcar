<!-- BEGIN: manage -->
<br />
<form action="{data.link_backup}" method="post" enctype="multipart/form-data" id="f_backup" name="f_backup"> 
 <table width="80%" border="0" cellspacing="1" cellpadding="1" class="admintable" align="center">
  <tbody>
  <tr height=20 class="row_title" > 
   <td colspan="2" ><strong>{LANG.backup_file}</strong></td>
  </tr>
  <tr class="row0" >
    <td width="20%" ><strong>{LANG.file_name} :</strong></td>
    <td ><input name="filename" type="text" class="textfiled" id="filename" value="{data.file_name}" size="50" /></td>
  </tr>
  
  <tr class="row0" >
    <td width="20%" ><strong>Theo thời gian :</strong> </td>
    <td ><input name="date_start" id="date_start" type="text"  size="15" maxlength="10" value="{data.date_start}" />&nbsp - <input name="date_end" id="date_end" type="text"  size="15" maxlength="10" value="{data.date_end}" /> &nbsp; <input name="ck_backup_time" id="ck_backup_time" type="checkbox" value="1" checked="checked"  /> <span class="font_err" >(Check chọn đễ backup file theo thời gian )</span></td>
  </tr>
  
  
  <tr class="row0" >
    <td >&nbsp;</td>
    <td><input type="submit" name="btnBAckup" value="{LANG.btn_backup}" class="button"></td>
  </tr>
  </tbody>
</table>
</form>
<br />
<form action="{data.link_backup_folder}" method="post" enctype="multipart/form-data" id="manage" name="manage">
<table cellspacing="1" class="adminlist">
<tbody>
		<tr height=20 class="row_title"><td colspan=5 ><b> Backup theo Folder </b></td></tr>
		<tr>
			<td align=center width="5%" ><input type="checkbox" id="checkall" name="checkall" class="checkbox" value="all"/></td>
      <td align=center width="10%" ><strong>Preview</strong></td>
			<td align=left ><strong>Folder name</strong></td>
			<td align=center width="15%" ><strong>Total file</strong></td>
			<td align=center  width="15%"><strong>Total Size</strong></td>
		</tr>
    {data.list_folder}
    <tr height=20 class="row_title">
    <td align=center width="5%" >&nbsp;</td>
    <td colspan=4 >
    <div><strong>Theo thời gian :</strong> <input name="date_start" id="date_start" type="text"  size="15" maxlength="10" value="{data.date_start}" />&nbsp - <input name="date_end" id="date_end" type="text"  size="15" maxlength="10" value="{data.date_end}" /> &nbsp; <input name="ck_backup_time" id="ck_backup_time" type="checkbox" value="1"  checked="checked" /> <span class="font_err" >(Check chọn đễ backup file theo thời gian )</span></div>
    <strong>{LANG.file_name} :</strong> <input name="filename" type="text" class="textfiled" id="filename" value="{data.file_name}" size="20" /> &nbsp;<input type="button" name="btnBAckup" value="Backup Folder" class="button" onclick="do_submit('do_backup')" ></td></tr>
</tbody>    
</table> 
<input type="hidden" name="do_action" id="do_action" value="" >
</form>
<br />
<br />


<form action="{data.link_import}" method="post" enctype="multipart/form-data" name="f_import"> 
 <table width="80%" border="0" cellspacing="1" cellpadding="1" class="admintable" align="center">
  <tbody>
  <tr height=20 class="row_title" > 
   <td colspan="2" ><strong>{LANG.import_database}</strong></td>
  </tr>
  <tr class="row0" >
    <td width="20%" ><strong>File (.zip) :</strong></td>
    <td ><input type="file" name="uploadFile" id="uploadFile" size="30"></td>
  </tr>
  <tr class="row0" >
    <td >&nbsp;</td>
    <td><input type="submit" name="btnImport" value="{LANG.btn_import}" class="button"></td>
  </tr>
  </tbody>
</table>
</form>
<br>
<table width="80%" class="admintable" align=center border=0 cellspacing=1 cellpadding=1>
<tbody>
		<tr height=20 class="row_title"><td colspan=4 ><b>{LANG.list_file_backup} </b></td></tr>
		<tr>
			<td align=left ><strong>{LANG.time_backup}</strong></td>
			<td align=left ><strong>{LANG.file_name}</strong></td>
			<td align=center ><strong>{LANG.size}</strong></td>
			<td align=center ><strong>Action</strong></td>
		</tr>
    {data.file_backup}
</tbody>    
</table> 
<br />
 


<br>
<br />
   
<!-- END: manage -->