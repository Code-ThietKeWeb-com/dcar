
function browser_8406() 
{
	this.ua         = " " + navigator.userAgent.toLowerCase();
	this.av         = parseInt(navigator.appVersion);
	this.isWin      = (this.ua.indexOf("win") >= 0);
	this.isWinVista = false;
	this.isMac      = (this.ua.indexOf("mac") >= 0);
	this.isLinux    = (this.ua.indexOf("linux") >= 0);
	this.isIE       = (this.ua.indexOf("msie") >= 0);
	this.isNav4     = ((this.ua.indexOf("mozilla") >= 0) && (this.ua.indexOf("compatible") == -1) && (this.av < 5));
	this.isFirefox  = (this.ua.indexOf("firefox") >= 0);
	this.isOpera    = (this.ua.indexOf("opera") > 0);
	if(this.isOpera) {
		this.isIE = false;
	}
	this.isSafari    = (this.ua.indexOf("applewebkit") > 0);
	this.isKonqueror = (this.ua.indexOf("konqueror") > 0);
	this.isGecko     = (this.ua.indexOf("gecko/") > 0);
	this.isCamino    = (this.ua.indexOf("camino/") > 0);
	// Check for Vista
	if(this.isWin) {
		this.isWinVista = (this.ua.indexOf("windows nt 6.0") >= 0);
	}
	this.isIE3   = (this.isIE && (this.av < 4));
	this.isIE4   = (this.isIE && (this.av == 4) && (this.ua.indexOf("msie 4") != -1));
	this.isIE5up = (this.isIE && !this.isIE3 && !this.isIE4);
	this.isIE6   = (this.isIE5up && (this.ua.indexOf("msie 6") >= 0));
}

function FeedbackFixIE6Stupid()
{
    var mybrowser_8406 = new browser_8406();
    
    var exp_8406    = document.compatMode == "CSS1Compat" ? "expression(document.documentElement.scrollTop+document.documentElement.clientHeight-this.clientHeight)" : "expression(document.body.scrollTop+document.body.clientHeight-this.clientHeight)";
    var css_8406    = ".feedback-fixed {position:fixed; bottom:0px; right:0px; z-index:999;text-align:right;}";
    var css_ie_8406 = "body {background: url(data:image/gif;base64,AAAA) fixed;} .feedback-fixed {_position:absolute; z-index:999; right:0px;text-align:right; _top:" + exp_8406 + ";}";
    if(mybrowser_8406.isIE && document.compatMode && document.compatMode == "BackCompat") 
	    css_8406 = css_ie_8406;
	    
    document.writeln("<style type=\"text/css\">" + css_8406 + "</style>");
    document.writeln("<!--[if lte IE 6]>");
    document.writeln("<style type=\"text/css\">" + css_ie_8406 + "</style>");
    document.writeln("<![endif]-->");
}

FeedbackFixIE6Stupid();