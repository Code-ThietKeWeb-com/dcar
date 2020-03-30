<?php

class HTML_plugin {
/**
* @param array An array of records
* @param string The URL option
*/
	function showInstalledplugins( $rows, $option ) {
	global $rooturl;
	
		
			?>
			<form action="setup.php" method="post" name="adminForm">
			<table width="100%" class="adminheading" cellspacing="0" cellpadding="0" border="0">
			<tr>
			<td bgcolor="#2b6082" align="left" class="btitle" >CÁC PLUGIN ĐÃ INSTALL</td>
		  </tr>

			</table>

			<table class="adminlist" cellspacing="1" cellpadding="1" border="0"  bgcolor="#999999" >
			<tr bgcolor="#CCCCCC">
				<th width="10%" class="title">
				Plugin Name
				</th>
				<th width="20%" class="title">
				Title
				</th>
				<th width="10%" align="left">
				Author
				</th>
				<th width="5%" align="center">
				Version
				</th>
				<th width="10%" align="center">
				Date
				</th>
				<th width="15%" align="left">
				Author E-mail
				</th>
				<th width="15%" align="left">
				Author URL
				</th>
			</tr>
			<?php
			if (count( $rows )) {
			$rc = 0;
			for ($i = 0, $n = count( $rows ); $i < $n; $i++) {
				$row =& $rows[$i];
				?>
				<tr class="<?php echo "row$rc"; ?>" bgcolor="#FFFFFF">
					<td align="left">
					<input type="radio" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);">
					<span class="bold">
					<?php echo $row->name; ?>
					</span>
					</td>
					<td align="left">
					<?php echo @$row->title != "" ? $row->title : "&nbsp;"; ?>
					</td>
					<td align="left">
					<?php echo @$row->author != "" ? $row->author : "&nbsp;"; ?>
					</td>
					<td align="center">
					<?php echo @$row->version != "" ? $row->version : "&nbsp;"; ?>
					</td>
					<td align="center">
					<?php echo @$row->creationdate != "" ? $row->creationdate : "&nbsp;"; ?>
					</td>
					<td>
					<?php echo @$row->authorEmail != "" ? $row->authorEmail : "&nbsp;"; ?>
					</td>
					<td>
					<?php echo @$row->authorUrl != "" ? "<a href=\"" .(substr( $row->authorUrl, 0, 7) == 'http://' ? $row->authorUrl : 'http://'.$row->authorUrl). "\" target=\"_blank\">$row->authorUrl</a>" : "&nbsp;";?>
					</td>
				</tr>
				<?php
				$rc = 1 - $rc;
			}
			
			?>
				<tr bgcolor="#FFFFFF">
					<td colspan="10" align="center" style="padding-top:50px;">
			<a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Please make a selection from the list to delete'); } else if (confirm('Are you sure you want to delete selected items? ')){ submitbutton('remove');}">

				<img src="<?=$rooturl . '/install/'?>images/delete_f2.png"  alt="Uninstall" name="remove" title="Uninstall" align="middle" border="0" />				<br />Uninstall</a>
					</td>
				</tr>
			<?
		} else {
			?>
			<tr>
			<td  bgcolor="#FFFFFF" colspan="7" align="center">
			Chưa có plugin nào
			</td></tr>
			<?php
		}
		?>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="mod_installer" />
		<input type="hidden" name="element" value="plugin" />
		</form>
		<?php
	}
}
?>