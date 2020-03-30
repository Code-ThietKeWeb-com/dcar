/*--------------- LoadAjaxOrder ----------------*/
function LoadAjax(doAction,str_value,ext_display) 
{
 
	var mydata =  "do="+doAction + str_value;
	ajax = new sack("modules/country_ad/ajax/ajax.php");
	ajax.method = "GET" ;
	ajax.element = ext_display;
	ajax.runAJAX(mydata);
		
}
