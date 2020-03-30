<?php
/**
* @version $Id: module.html.php 6072 2006-12-20 02:09:09Z robs $
* @package Joomla
* @subpackage Installer
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access

/**
* @package Joomla
* @subpackage Installer
*/
class HTML_module {
/**
* @param array An array of records
* @param string The URL option
*/
	function showInstalledModules( $rows, $option ) {
	global $rooturl;
	
		
			?>
			<form action="setup.php" method="post" name="adminForm">
			<table width="100%" class="adminheading" cellspacing="0" cellpadding="0" border="0">
			<tr>
			<td bgcolor="#2b6082" align="left" class="btitle" >CÁC MODULES ĐÃ INSTALL</td>
		  </tr>

			</table>

			<table class="adminlist" cellspacing="1" cellpadding="1" border="0"  bgcolor="#999999" >
			<tr bgcolor="#CCCCCC">
				<th width="10%" class="title">
				Module
				</th>
				<th width="20%" class="title">
				Tên module
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
					<?php echo $row->mod_name; ?>
					</span>
					</td>
					<td align="left">
					<?php echo @$row->name != "" ? $row->name : "&nbsp;"; ?>
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
			Chưa có modules nào
			</td></tr>
			<?php
		}
		?>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="mod_installer" />
		<input type="hidden" name="element" value="module" />
		</form>
		<?php
	}
}
?>