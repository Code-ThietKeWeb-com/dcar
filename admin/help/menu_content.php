<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#CCCCCC">

<?php
$str_title	=	"title_".$lang;
$result_g = $DB->query("select * from admin_menu where parentid=0 order by displayorder ASC , id ASC ");

while ($row_g=$DB->fetch_row($result_g)) {
    ?><tr  ><td   bgcolor="#0061B5" style="padding:2px;"><a onclick="showhide('menu_<?php echo $row_g['id']; ?>');" href="javascript:;" style="color:#FFFFFF"><img src="images/but_tru.gif" name="img_menu_<?=$row_g['id']?>" id="img_menu_<?=$row_g['id']?>" width="10" height="10" border="0">&nbsp;<b><?=$row_g[$str_title]?></b></a>
	<table border="0" width="100%" cellpadding="0" cellspacing="0">
	<tr><td height="10"></td></tr>
	</table> 

    <table width="100%"  border="0" cellspacing="0" cellpadding="0" align=center id="menu_<?=$row_g['id']?>">

    <?php // Show page list

        $query_page = $DB->query("SELECT * FROM admin_menu WHERE parentid='{$row_g['id']}' and (sub='' or sub IS NULL) order by displayorder ASC , id ASC ");

        while ($page=$DB->fetch_row($query_page)) {

            ?><tr height="20"><td width="100%" bgcolor="#FFFFFF" align="left" style="padding:1px;" onmouseover="this.bgColor='#E6F4FF'" onmouseout="this.bgColor='#FFFFFF'">&nbsp;&rsaquo; <a onclick="load_content('<?php echo $page['id']; ?>');" href="#" style="color:#000000"><?php echo $page[$str_title]; ?></a></td></tr><?php

        }

    ?>

    </table>

    </td></tr>

<?php

}

?>

</table>