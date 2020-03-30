<?php
/*================================================================================*\
|| 							Name code : funtions_main.php 		 	          	     		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/

if (! defined('IN_vnT'))
{
  die('Hacking attempt!');
}

 
//--------- Box_System
function Box_System ()
{
  global $Template, $func, $DB, $conf, $vnT;

  $text = '<table width="90%"  border="0" cellspacing="2" cellpadding="2" align="center">
					<tr>
						<td width="40%"><strong>PHP Version : </strong></td>
						<td width="60%">' . phpversion() . ' </td>
					</tr>
					<tr>
						<td><strong>MySQL Version : </strong></td>
						<td>' . mysql_get_server_info() . ' </td>
					</tr>
					<tr>
						<td><strong>Server Software : </strong></td>
						<td>' . @$_SERVER["SERVER_SOFTWARE"] . '</td>
					</tr>
					<tr>
						<td><strong>Clien Browser : </strong></td>
						<td>' . @$_SERVER["HTTP_USER_AGENT"] . '</td>
					</tr>
					<tr>
						<td><strong>IP Address : </strong></td>
						<td>' . @$_SERVER['REMOTE_ADDR'] . '</td>
					</tr>
				</table>';  
				
	$data['id'] = "box_system";
	$data['f_title'] = $vnT->lang['f_system'];
	$data['content'] = $text ;
	$Template->reset('box');
	$Template->assign('data', $data);
	$Template->parse("box");	
	return $Template->text("box");
						
}


//--------- Box_Contact
function Box_Contact ()
{
  global $Template, $func, $DB, $conf, $vnT;

  $text = '<table class="adminlist">
    <tbody>
    	<tr>	
		<td width="5%" align="center"><strong>#</strong>	</td>	
		<td width="25%" align="center"><strong>' . $vnT->lang['date_contact'] . '</strong> </td>
		<td width="20%" class="center"><strong>' . $vnT->lang['fullname'] . '</strong></td>
		<td width="35%" class="center"><strong>Email</strong></td>
	</tr>';
    $res = $DB->query("select id,datesubmit,name,email from contact where dateanswer='' order by id DESC LIMIT 0,5");
    if ($DB->num_rows($result)) {
      $i = 1;
      while ($row = $DB->fetch_row($result)) {
        $text .= '<tr>
						<td align="center">' . $i . '</td>
						<td align="center">' . @date("d/m/Y", $row['datesubmit']) . '</td>
						<td align="center"><a href="?mod=contact&act=contact&sub=edit&id=' . $row['id'] . '"><strong>' . $row['name'] . '</strong></a></td>	
						<td >' . $row['email'] . '</td>	
						</tr>';
        $i ++;
      }
    } else {
      $text .= '<tr>
						<td colspan="4">' . $vnT->lang['no_have_new_contact'] . '</td>
					</tr>';
    }
	$text .= '<tr></table>';		
			
	$data['id'] = "box_contact";
	$data['f_title'] = $vnT->lang['f_new_contact'];
	$data['content'] = $text ;
	$Template->reset('box');
	$Template->assign('data', $data);
	$Template->parse("box");	
	return $Template->text("box");
						
}


//--------- Box_Statistics
function Box_Statistics ()
{
  global $Template, $func, $DB, $conf, $vnT;
	
	  //======
    $text = '<table class="adminlist"><tbody>

                        <tr>
                          <td><strong>' . $vnT->lang['online'] . ' : </strong></td>
                          <td>' . $DB->do_get_num("sessions") . '</td>
                        </tr>';
    $query = "select count from counter";
    $data_arr = $DB->query($query);
    $totals = 0;
    while ($row = $DB->fetch_row($data_arr)) {
      $totals += $row['count'];
    }
    $text .= '<tr>
                          <td><strong>' . $vnT->lang['vister'] . ' : </strong></td>
                          <td>' . $totals . '</td>
                        </tr>
                      </tbody></table>';
											
	$data['id'] = "box_statistics";
	$data['f_title'] = $vnT->lang['f_statistics'];
	$data['content'] = $text ;
	$Template->reset('box');
	$Template->assign('data', $data);
	$Template->parse("box");	
	return $Template->text("box");
						
}


//--------- Box_AdminLog
function Box_AdminLog ()
{
  global $Template, $func, $DB, $conf, $vnT;
	$text = '<table class="adminlist">
    <tbody>
    	<tr>	
				<td width="20%" align="center">Username</td>	
				<td width="25%" align="center">Time </td>
				<td width="15%" align="center">Action</td>
				<td width="20%" align="center">Page</td>
				<td width="20%" align="center">ID</td>
			</tr>';
	
	$sql = "SELECT l.*,a.username 
				FROM adminlogs l,admin a 
				WHERE a.adminid=l.adminid 				 
				ORDER BY l.id DESC LIMIT 0,5";
	$result = $DB->query($sql);
	while($row = $DB->fetch_row($result))
	{
		 $text .= '<tr>
						<td align="center">'.$row['username'].'</td>
						<td align="center">'.@date("H:i, d/m/Y ", $row['time']).'</td>
						<td align="center">' . $row['action'] . '</td>
						<td align="center">'. $row['cat'].'</td>	
						<td align="center">' . $row['pid'] . '</td>	
						</tr>';
        $i ++;
	}
				
	$text .= '<tr></table>';													
	$data['id'] = "box_adminlog";
	$data['f_title'] = $vnT->lang['f_adminlog'];
	$data['content'] = $text ;
	$Template->reset('box');
	$Template->assign('data', $data);
	$Template->parse("box");	
	return $Template->text("box");
						
}

?>