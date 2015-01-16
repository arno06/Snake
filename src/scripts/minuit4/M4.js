var M4 = 
{
	browser:(function()
	{
		var ua = navigator.userAgent;
		return {
			IE:ua.indexOf("MSIE")>-1,
			FF:ua.indexOf("Firefox")>-1,
			CHROME:ua.indexOf("Chrome")>-1,
			SAFARI:ua.indexOf("AppleWebKit")>-1&&ua.indexOf("Chrome")===-1
		};
	})()
};

function Request(pTarget, pParams)
{
	var xhr_object = false;
    if (window.XMLHttpRequest)
    	xhr_object = new XMLHttpRequest();
    else if (window.ActiveXObject)
    {
    	var t = ['Msxml2.XMLHTTP','Microsoft.XMLHTTP'],i = 0;
    	while(!xhr_object&&t[i++])
    		try {xhr_object = new ActiveXObject(t[i]);}catch(e){}
    }
	if(!xhr_object)
		return null;
	var ref = this, v = "", j = 0;
	for(i in pParams)
		v += (j++>0?"&":"")+i+"="+pParams[i];
	xhr_object.open("POST", pTarget, true);
	xhr_object.onreadystatechange=function()
	{
		if(xhr_object.readyState==4)
		{
			switch(xhr_object.status)
			{
				case 304:
				case 200:
					var ct = xhr_object.getResponseHeader("Content-type");
					if(ct.indexOf("json")>-1)
						eval("xhr_object.responseJSON = "+xhr_object.responseText+";");
					if(ref.onCompleteHandler)
						ref.onCompleteHandler(xhr_object);
				break;
				case 403:
				case 404:
				case 500:
					if(ref.onErrorHandler)
						ref.onErrorHandler(xhr_object.responseText);
				break;
			}
		}
	};
	
	xhr_object.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset:ISO-8859-1');
	xhr_object.send(v);
	return this;
}
Request.prototype.onComplete = function(pFunction){this.onCompleteHandler = pFunction;return this;};
Request.prototype.onError = function(pFunction){this.onErrorHandler = pFunction;return this;};
Request.load = function (pUrl, pParams){return new Request(pUrl, pParams);};
Request.update = function(pId, pUrl, pParams){return Request.load(pUrl, pParams).onComplete(function(pResponse){document.getElementById(pId).innerHTML = pResponse.responseText;});};

function Element(){}
Element.create = function (pNode, pProperties)
{
	var e = document.createElement(pNode);
	for(var i in pProperties)
	{
		switch(i)
		{
			case "text":
				e.appendChild(document.createTextNode(pProperties[i]));
			break;
			default:
				e.setAttribute(i, pProperties[i]);
			break;
		}
	}
	return e;
};