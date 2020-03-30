<style>
table.adminlist {
	width: 100%;
	border-spacing: 1px;
	background-color: #e7e7e7;
	color: #666;
}

table.adminlist td,table.adminlist th {
	padding: 4px;
}

table.adminlist thead th {
	text-align: center;
	background: #f0f0f0;
	color: #666;
	border-bottom: 1px solid #999;
	border-left: 1px solid #fff;
}

table.adminlist thead a:hover {
	text-decoration: none;
}

table.adminlist thead th img {
	vertical-align: middle;
}

table.adminlist tbody th {
	font-weight: bold;
}

table.adminlist tbody tr {
	background-color: #fff;
	text-align: left;
}

table.adminlist tbody tr.row1 {
	background: #f9f9f9;
	border-top: 1px solid #fff;
}

table.adminlist tbody tr.row0:hover td,table.adminlist tbody tr.row1:hover td
	{
	background-color: #ffd;
}

table.adminlist tbody tr td {
	height: 25px;
	background: #fff;
	border: 1px solid #fff;
}

table.adminlist tbody tr.row1 td {
	background: #f9f9f9;
	border-top: 1px solid #FFF;
}

table.adminlist tfoot tr {
	text-align: center;
	color: #333;
}

table.adminlist tfoot td,table.adminlist tfoot th {
	background-color: #f3f3f3;
	border-top: 1px solid #999;
	text-align: center;
}
</style>
<?php
ob_start();
phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
$phpinfo = ob_get_contents();
ob_end_clean();
preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
$output = preg_replace('#<table#', '<table class="adminlist" align="center"', $output[1][0]);
$output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
$output = preg_replace('#border="0" cellpadding="3" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', $output);
$output = preg_replace('#<hr />#', '', $output);
$output = str_replace('<div class="center">', '', $output);
$output = str_replace('</div>', '', $output);

echo $output;

?>