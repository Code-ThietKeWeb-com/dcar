function ShowCat(type) {			
	if ( type == 'news' ){
		getobj("ul_cat").style.display = "";
	}else{
		getobj("ul_cat").style.display = "none";
	}
}