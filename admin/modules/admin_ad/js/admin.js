
function checkAction(act)	{
	act_name = act+"[]";
	for ( i=0;i < document.fAdmin.elements.length ; i++ ){
		if (document.fAdmin.elements[i].type=="checkbox" && document.fAdmin.elements[i].value==act){
			checked = document.fAdmin.elements[i].checked;
			
		}
		
		if (document.fAdmin.elements[i].type=="checkbox" && document.fAdmin.elements[i].name==act_name){
			if (checked==true){
					document.fAdmin.elements[i].checked = true;
			}else{
					document.fAdmin.elements[i].checked = false;
			}
		}
		
	}

}

function checkOne(act)	{
	ok=0;
	for ( i=0;i < document.fAdmin.elements.length ; i++ ){
		act_name = act+"[]";
		if (document.fAdmin.elements[i].type=="checkbox" && document.fAdmin.elements[i].name==act_name){
			if(document.fAdmin.elements[i].checked){
				ok=1;
			}
		}
	}
	
	for ( i=0;i < document.fAdmin.elements.length ; i++ ){
		if (document.fAdmin.elements[i].type=="checkbox" && document.fAdmin.elements[i].value==act){
			if(ok==1){
				document.fAdmin.elements[i].checked = true;
			}else{
				document.fAdmin.elements[i].checked = false;
			}
			
		}
	}
	
}

function showPermission(obj) {			
	var type = obj.value;
	if ( type == '0' ){
		getobj("trQuyen").style.display = "none";
	}else{
		getobj("trQuyen").style.display = "";
	}
}


