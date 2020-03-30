<!-- BEGIN: manage -->
<br />
<form action="{data.link_action}" method="post" name="f_config" id="f_config" >
{data.err}

<div id="tabs">
		<ul>
				<li><a href="#tabConfig"><span>Module About</span></a></li>
				{data.list_li} 
		</ul>
		<div id="tabConfig">
				<br>

 <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

 <tr >
    <td width="25%" align="right" class="row1"> <strong>Chức năng MXH : </strong></td>
    <td  align="left" class="row0">{data.about_social_network}</td>
  </tr>
   <tr >
    <td  align="right" class="row1"> <strong>Share MXH : </strong></td>
    <td  align="left" class="row0">{data.about_list_share}</td>
  </tr>
  <tr>
    <td align="right" class="row1"><strong> Like MXH : </strong></td>
    <td  align="left" class="row0">{data.about_list_like}</td>
  </tr>
 <tr >
			<td width="25%" align="right" class="row1"> <strong>Facebook Comment : </strong></td>
			<td  align="left" class="row0">{data.about_facebook_comment} </td>
		</tr>
   
    </table>

		</div>
		{data.list_div}
    
</div>

<div align="center" style="padding:10px;"> <input type="submit" name="btnUpdate" id="btnUpdate" value="Update >>" class="button"> </div>
</form>

<br />
<!-- END: manage -->