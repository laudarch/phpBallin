/* $Id: secure.js,v 1.1 2014/03/17/ 19:43:32 laudarch Exp $ */

/**
 * Secure input by checking for potential SQLi parameters.
 * also has email validation and keyboard functions as addons
 */
var secure = {
    /**
     * validate email address
     *
     * @param  (string) email   - email address
     * @return (boolean) false  - return false on error(mismatch)
     * @type (boolean)
     */
    validateEmail: function(emailAddress)
    {
        var ret = false;
        var regEx = /^([a-zA-Z0-9_\.''\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (emailAddress.match(regEx))
            ret = true;
 
        return (ret);
    },

    /**
     * check input for potential sqli
     * XXX: needs more checking for %2e,%2f,%20 et al
     *
     * @param  (string) string   - input data
     * @return (boolean) true/false  - return false on no-match
     * @type (boolean)
     */
    safe: function(data)
    {
        var ret = false;
        var dataFirst = data.substring(0,1);
        var dataLast = data.substring(data.length-1);

        if (dataFirst != "'" || dataLast != "'")
            ret = true;

        return (ret);
    },

    /**
     * remove blanks spaces from input
     *
     * @param  (string) string  - input data
     * @return (boolean) false  - return false on error(mismatch)
     * @type (boolean)
     */
    stripBlanks: function(Source)
    {
        var newString;
        var i;
        var j;
        var blank;
        blank = " ";
        newString = "";
        aString = Source;   

        for (i = 0; i < aString.length; i++) { 
            if (aString.charAt(i)     != blank && 
                aString.charCodeAt(i) != 13    && 
                aString.charCodeAt(i) != 10) {
                break;
            }
        }

        for (j=aString.length-1; j>=0; j--) {
            if (aString.charAt(j)     != blank && 
                aString.charCodeAt(j) != 13    && 
                aString.charCodeAt(j) != 10) {
                    break;
            }
        }

        for (k=i;k<=j;k++) {
            newString += aString.charAt(k);
        }
        return (newString); 
    },

    /**
     * Add event to queue with callback
     *
     * @param (string)   element   - element can be document or window or any
     * @param (string)   eventName - 
     * @param (function) callback  - executed when event is fired
     */
    addEvent: function (element, eventName, callback)
    {
    	if (element.addEventListener) {
        	element.addEventListener(eventName, callback, false);
    	} else if (element.attachEvent) {
        	element.attachEvent("on" + eventName, callback);
    	} else {
        	element["on" + eventName] = callback;
    	}
    },

    /**
     * check to see if the enter key has been pressed
     * then call func
     *
     * @param  (function) callback  - callback function
     */
    onEnterKey: function(func)
    {
	this.addEvent(document, "keypress", function (e) {
                                e = e || window.event;
                                if (e.keyCode == 13) func();
                     });

    }
}
