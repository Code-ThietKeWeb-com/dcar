function getObj(id,d)
{
	var i,x;  if(!d) d=document; 
	if(!(x=d[id])&&d.all) x=d.all[id]; 
	for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][id];
	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=ylib_getObj(id,d.layers[i].document);
	if(!x && document.getElementById) x=document.getElementById(id); 
	return x;
};

function AddFiled()
{
	var nextHiddenIndex = (parseInt(getObj("num").value)+1);
	//alert ("dong = "+nextHiddenIndex)
	getObj("dong" + nextHiddenIndex).style.display = document.all ? "block" : "table-row";
	getObj("num").value = nextHiddenIndex;
	if(nextHiddenIndex >= 20) getObj("attachMoreLink").style.display = "none";
	
}	