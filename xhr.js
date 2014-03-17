/* $Id: xhr.js,v 1.0 2011/05/31 17:22:00 laudarch Exp $ */

/**
 * complete asynchronous xmlhttprequest function
 */
var a$ = {
    /**
     * asychronous xmlhttprequest
     *
     * @param  (string) url     - url to open
     * @param  (array)  options - defines method, callback, etc
     * @return (boolean) false  - return false on error
     * @type (boolean)
     */
    xhr:function( url, options)
    {
        var o = options ? options : {};
        
        if (typeof options == "function") {
            o = {};
            o.callback = options;
        }
        
        var xhr     = this.getxhr();
        var method  = o.method  || 'get';
        var restype = o.restype || 'text';
        var async   = o.async   || false;           
        var params  = o.data    || null;
        var i       = 0;
        var ret;
		
        if (!xhr) return(false);
        xhr.open(method, url, async);

        if (o.headers) {
            for (; i < o.headers.length; i++) {
              xhr.setRequestHeader(o.headers[i].name, o.headers[i].value);
            }
        }

        if ((/POST/i).test(method)) {
            xhr.setRequestHeader("Content-type",
                                 "application/x-www-form-urlencoded");
            xhr.setRequestHeader("Content-length", params.length);
            xhr.setRequestHeader("Accept", "*/*");
            xhr.setRequestHeader("Connection", "close");
    	}

        if ((/POST/i).test(method)) {
            xhr.send(params);
        } else {
            xhr.send(null);
        }

        handleResp  = (o.callback != null) ?
	               o.callback          :
		       function(rt) {alert(rt);};
        handleError = (o.error && typeof(o.error) == 'function') ?
                       o.error                                   :
                       function(e) {alert(e);};

        /**
         * internal handler
         */
        function hdl()
        {
            if (xhr.readyState == 4) {
                delete(xhr);
                if (xhr.status===0 || xhr.status==200) {
                    if (/plain/i.test(restype)) ret = xhr.responseText;
                    if (/text/i.test(restype))  ret = xhr.responseText;
                    if (/xml/i.test(restype))   ret = xhr.responseXML;
                    if (/json/i.test(restype))  ret = xhr.responseText;
                    handleResp(ret);
                }
                if ((/^[4(5|0)]/).test(xhr.status))
                	handleError(xhr.responseText);
            }
        }

        if (async) {
            xhr.onreadystatechange = hdl;
        }
        if (!async) hdl();

        return (this);
    },

   /**
    * get XMLHttpRequest Object
    *
    * @return (object) xmlhttprequest object
    * @type (object)
    */
    getxhr:function()
    {
	   var req = false;
	   try {
		   req = new XMLHttpRequest();
	   } catch (e1) {
		   try {
			   req = new ActiveObject("Msxml2.XMLHTTP");
		   } catch (e2) {
			   try {
				   req = new ActiveObject("Microsoft.XMLHTTP");
			   } catch (e3) {
				   return (false);
			   }
		   }
	   }
	   return (req);
    }
};
