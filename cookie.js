/* $Id: cookie.js,v 1.0 2010/10/15/ 16:46:46 laudarch Exp $ */

var cookies = {
	/* asigns cookie with a value */
	set: function(cname, cvalue, expires, path, domain, secure)
	{
		document.cookie = escape(cname) + '=' + escape(cvalue)
			+ (expires ? ';Expires=' + expires.toGMTString() : '')
			+ (path    ? ';Path='    + path   : '')
			+ (domain  ? ';Domain='  + domain : '')
			+ (secure  ? ';Secure'            : '');
	},

	/* reads cookie value */
	get: function(cname)
	{
		var cvalue = null;
		var posname = document.cookie.indexOf(escape(cname) + '=');
		if (posname != -1) {
			var posvalue = posname + (escape(cname) + '=').length;
			var endpos = document.cookie.indexOf(';', posvalue);
			if (endpos != -1) {
				cvalue = unescape(document.cookie.substring(
					posvalue, endpos));
			} else {
				cvalue = unescape(document.cookie.substring(
					posvalue));
			}
		}
		return (cvalue);
	}
};
