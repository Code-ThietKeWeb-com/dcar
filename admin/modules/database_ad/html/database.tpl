<!-- BEGIN: manage -->
<div id="container-1" class="container-tabs">
		<ul>
				<li><a href="#info_database"><span>{LANG.info_database}</span></a></li>
				<li><a href="#optimize_db"><span>{LANG.optimize_db}</span></a></li>
				<li><a href="#repair_database"><span>{LANG.repair_database}</span></a></li>
        <li><a href="#analyze_database"><span>{LANG.analyze_database}</span></a></li>
		</ul>
		<div id="info_database">
				{data.info_database}
		</div>
		<div id="optimize_db">
				{data.optimize_db}
		</div>
		<div id="repair_database">
			 {data.repair_database}
		</div>
    <div id="analyze_database">
			 {data.analyze_database}
		</div>
    
</div>
<!-- END: manage -->


<!-- BEGIN: backup -->
<br> 
 <form action="{data.link_backup_table}" method="post">
<table width="80%" border="0" cellspacing="1" cellpadding="1" align="center"  class="admintable">
 <tbody>
  <tr height=20 class="row_title">
    <td colspan="2" ><strong>{LANG.backup_list_table}</strong></td>
  </tr>
  <tr class="row0">
    <td width="55%" valign="top">{LANG.mess_backup_table}</td>
    <td  >{data.table_select}</td>
  </tr>
  <tr>
    <td colspan="2" align="center" class="tittle"><input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" /><input name="btnBackupTable" type="submit" value ="{LANG.btn_backup}" class="button">&nbsp;<input name="reset" type="reset" value="{LANG.btn_reset}" class="button" ></td>
  </tr>
 </tbody>
</table>
 </form>
 <br />

<form action="{data.link_import_sql}" method="post" enctype="multipart/form-data" name="f_import"> 
 <table width="80%" border="0" cellspacing="1" cellpadding="1" class="admintable" align="center">
  <tbody>
  <tr height=20 class="row_title" > 
   <td colspan="2" ><strong>{LANG.import_database}</strong></td>
  </tr>
  <tr class="row0" >
    <td width="20%" ><strong>File (.sql, .gz) :</strong></td>
    <td ><input type="file" name="filesql" id="filesql" size="30"></td>
  </tr>
  <tr class="row0" >
    <td >&nbsp;</td>
    <td><input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" /><input type="submit" name="btnImport" value="{LANG.btn_import}" class="button"></td>
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
			<td align=left ><strong>{LANG.dbname}</strong></td>
			<td align=center ><strong>{LANG.size}</strong></td>
			<td align=center ><strong>Action</strong></td>
		</tr>
    {data.file_backup}
</tbody>    
</table> 
<br />
<p align="center">
   <a href="{data.link_backup_all}"><img src="{IMAGE_MOD}/backup.gif"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="{data.link_del_all}"><img src="{IMAGE_MOD}/del.gif"></a></p>
   
<!-- END: backup -->