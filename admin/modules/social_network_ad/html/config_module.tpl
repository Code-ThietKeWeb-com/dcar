<!-- BEGIN: manage -->
<br />
<form action="{data.link_action}" method="post" name="f_config" id="f_config" >
{data.err}

<div id="tabs">
		<ul> 
				{data.list_li} 
		</ul> 
		{data.list_div}
    
</div>

<div align="center" style="padding:10px;"> <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" /><input type="submit" name="btnUpdate" id="btnUpdate" value="Update >>" class="button"> </div>
</form>

<br />
<!-- END: manage -->