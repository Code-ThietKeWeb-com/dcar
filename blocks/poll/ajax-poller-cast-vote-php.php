<?php
if(isset($_GET['pollId'])){
	$optionId = false;
	require_once("../../_config.php"); 
	require_once("../../includes/class_db.php"); 
	$DB = new DB;
	
	if(isset($_GET['optionId'])){
		$optionId = $_GET['optionId'];
		$optionId = preg_replace("/[^0-9]/si","",$optionId);
	}
	$pollId = $_GET['pollId'];
	$pollId = preg_replace("/[^0-9]/si","",$pollId);
	
	if(isset($_GET['lang'])) $lang =$_GET['lang']  ; else $lang="vn";
	// Insert new vote into the database
	// You may put in some more code here to limit the number of votes the same ip adress could cast.
	
	if($optionId)$DB->query("update poller_option Set vote=vote+1 where id ='".$optionId."'");
	
	// Returning data as xml
	
	echo '<?xml version="1.0" ?>';
	
	$res = $DB->query("select * from poller where id='".$pollId."'");
	if($inf = $DB->fetch_row($res))
	{
		$arr_tmp = unserialize($inf['pollerTitle']);
		echo "<pollerTitle>".$arr_tmp[$lang]." </pollerTitle>\n";
		$resOptions = $DB->query("select * from poller_option where pollerID='".$inf["id"]."' order by pollerOrder") ;
		while($infOptions = $DB->fetch_row($resOptions))
		{
			$tmp = unserialize($infOptions['optionText']);
			$optionText = $tmp[$lang];
			echo "<option>\n";
			echo "\t<optionText>".$optionText."</optionText>\n";					
			echo "\t<optionId>".$infOptions["id"]."</optionId>\n";					
			echo "\t<votes>".$infOptions["vote"]."</votes>\n";							
			echo "</option>";				
		}	
		echo "<pollerMessage>".$inf['description']." </pollerMessage>\n";
	}
	exit;

}else{
	echo "No success";
	
}

?>