<?php
	session_start();
	define('IN_vnT',1);
	define('DS', DIRECTORY_SEPARATOR);
	
	require_once("../../../_config.php"); 
	include($conf['rootpath']."includes/class_db.php"); 
	$DB = new DB;	
	//Functions
	include ($conf['rootpath'] . 'includes/class_functions.php');
	include($conf['rootpath'] . 'includes/admin.class.php');
	$func  = new Func_Admin;
	$conf = $func->fetchDbConfig($conf);
	
 	if(empty($_SESSION['admin_session']))	{
		die("Ban khong co quyen truy cap trang nay") ;
	}
	
  require_once($conf['rootpath'].'libraries/excel/PHPExcel.php');
  $file_name = 'List_city' ;
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	// Set properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Tinh Thanh Pho');
 		
		$lang = ($_GET['lang']) ? $_GET['lang'] : "vn";
	  $country = ($_GET['country']) ? $_GET['country'] : "VN";
    $search = ($_GET['search']) ? $_GET['search'] : "code";
    $keyword = $_GET['keyword'];
    $display = (isset($_GET['display'])) ? $_GET['display'] : "-1";
    
    $where = "where country ='{$country}' ";
    if ($display != - 1)
    {
      $where .= " and display = $display ";
    }
    if (! empty($search) && ! empty($keyword))
    {
      $where .= " and $search like '%$keyword%' ";
    }
		
  $sql = "SELECT * FROM iso_cities  $where ORDER BY c_order ASC ,name ASC";
		
	// 
	$reuslt = $DB->query($sql);
	if ($num = $DB->num_rows($reuslt))
	{
		 
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'ID')
		->setCellValue('B1', 'Tên Tỉnh/TP')
		;
 				
		
		$dong =2;	
		$stt=0;
		while($row = $DB->fetch_row($reuslt))
		{
			$stt++;
		
			$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue('A'.$dong, $row['id'])
										->setCellValue('B'.$dong, $row['name'])
										;
					
			$dong++;
		}
	}	
	
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);

$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);

$DB->close();
$objPHPExcel->getActiveSheet()->getStyle('A1:A'.($dong).'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B1:B'.($dong).'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);



// Redirect output to a client's web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$file_name.'.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output'); 

exit;
 

?>