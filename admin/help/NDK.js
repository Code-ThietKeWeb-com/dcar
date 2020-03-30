/* - - - - - - - - - - - - - - - - - - - - - - -
Online Clinic Javascript
 - - - - - - - - - - - - - - - - - - - - - - - */
var ie45,ns6,ns4,dom;
if (navigator.appName=="Microsoft Internet Explorer") ie45=parseInt(navigator.appVersion)>=4;
else if (navigator.appName=="Netscape"){  ns6=parseInt(navigator.appVersion)>=5;  ns4=parseInt(navigator.appVersion)<5;}
dom=ie45 || ns6;

var but_cong = "images/but_cong.gif";  
var but_tru = "images/but_tru.gif"; 

var http=createRequestObject();
var objectId = '';
var loadok = 0;
var currentid = 0;
var pagearray = new Array(1);
var currentpage=0;

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function createRequestObject(htmlObjectId){
    var obj;
    var browser = navigator.appName;
    
    if(browser == "Microsoft Internet Explorer"){
        obj = new ActiveXObject("Microsoft.XMLHTTP");
    }
    else{
        obj = new XMLHttpRequest();
    }
    return obj;    
}

function sendReq(serverFileName, variableNames, variableValues,objId) {
    var paramString = '';
    
    objectId = objId;
    
    variableNames = variableNames.split(',');
    variableValues = variableValues.split(',');
    
    for(i=0; i<variableNames.length; i++) {
        paramString += variableNames[i]+'='+variableValues[i]+'&';
    }
    paramString = paramString.substring(0, (paramString.length-1));
            
    if (paramString.length == 0) {
        http.open('get', serverFileName);
    }
    else {
        http.open('get', serverFileName+'?'+paramString);
    }
    http.onreadystatechange = handleResponse;
    http.send(null);
}

function handleResponse() {
    
    if (http.readyState == 4) {
        responseText = http.responseText;
        getobj(objectId).innerHTML = responseText;
    } else {
        getobj(objectId).innerHTML = "<br><br><table align=center width='200' border='0' cellspacing='1' cellpadding='1' bgcolor='#CCCCCC'><tr><td align='center' bgcolor='#FFFFFF' style='padding:3px' height='50'><img src='images/wait.gif' border=0><br>Loading content ...</td></tr></table><br><br>";
    }
        
}

function change_icon(imgDocID,url) {
document.images[imgDocID].src = url;
}  

function showhide(id) {
el = document.all ? document.all[id] :   dom ? document.getElementById(id) :   document.layers[id];
els = dom ? el.style : el;
img_els = 'img_'+id;
    if (dom){
      if (els.display == "none") {
            els.display = "";
            getobj(img_els).src=but_tru;
        } else {
            els.display = "none";
            getobj(img_els).src=but_cong;
            }
      }
    else if (ns4){
      if (els.display == "show") {
            els.display = "hide";
            getobj(img_els).src=but_cong;
        } else { 
            els.display = "show";
            getobj(img_els).src=but_tru;
               }
    }
}

function getobj(id) {
el = document.all ? document.all[id] :   dom ? document.getElementById(id) :   document.layers[id];
return el;
}

function showobj(id) {
obj=getobj(id);
els = dom ? obj.style : obj;
    if (dom){
        els.display = "";
    } else if (ns4){
        els.display = "show";
    }
}

function hideobj(id) {
obj=getobj(id);
els = dom ? obj.style : obj;
    if (dom){
        els.display = "none";
    } else if (ns4){
        els.display = "hide";
    }
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function openPopUp(url, windowName, w, h, scrollbar) {
           var winl = (screen.width - w) / 2;
           var wint = (screen.height - h) / 2;
           winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scrollbar ;
           win = window.open(url, windowName, winprops);
           if (parseInt(navigator.appVersion) >= 4) { 
                win.window.focus(); 
           } 
}

function show_search() {
    hideobj('menu_content');
    showobj('f_search');
}

function show_menu_content() {
    hideobj('f_search');
    showobj('menu_content');
}

function show_home(id) {
    hideobj('f_search');
    showobj('menu_content');
    load_content(id);
}

function do_search(f) {
    var keyword = f.keyword.value;
    if (keyword.length<3) {
        alert('Keyword too short (at least 3 character)');
        f.keyword.focus();
    } else {
        sendReq('search.php', 'keyword', keyword,'search_result');
    }
    return false;
}

function load_content(id) {
    currentid=id;
    if (currentpage<pagearray.length-1) {
        pagearray.length=currentpage+1;
    } 
    currentpage++;
    pagearray[currentpage] = id;

    if (currentpage>1) getobj('img_back').src='images/undo.gif';
    else getobj('img_back').src='images/undo1.gif'; 
    if (currentpage<pagearray.length-1) getobj('img_next').src='images/redo.gif';
    else getobj('img_next').src='images/redo1.gif'; 
    
    sendReq('content.php', 'id', id,'content');
}

function do_back() {
    if (currentpage>1) {
        currentpage--;
    
    if (currentpage>1) getobj('img_back').src='images/undo.gif';
    else getobj('img_back').src='images/undo1.gif'; 
    if (currentpage<pagearray.length-1) getobj('img_next').src='images/redo.gif';
    else getobj('img_next').src='images/redo1.gif'; 
    
        var pid = pagearray[currentpage];
        currentid=pid;
        sendReq('content.php', 'id', pid,'content');
    }
}

function do_next() {
    if (currentpage<pagearray.length-1) {
        currentpage++;
    
    if (currentpage>1) getobj('img_back').src='images/undo.gif';
    else getobj('img_back').src='images/undo1.gif'; 
    if (currentpage<pagearray.length-1) getobj('img_next').src='images/redo.gif';
    else getobj('img_next').src='images/redo1.gif'; 
    
        var pid = pagearray[currentpage];
        currentid=pid;
        sendReq('content.php', 'id', pid,'content');
    }
}

function do_print() {
    url="content.php?print=1&id="+currentid;
    openPopUp(url, "HelpPrint", 800, 600, 'yes');
}