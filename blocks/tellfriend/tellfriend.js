
function do_sendmail(f,lang) {
	key='f_email,lang';
	value =f.f_email.value + ','+lang;
	url = 'blocks/tellfriend/ajax_tellfriend.php';
	sendReq(url,key ,value,'ext_tellfriend');
	return false;
}