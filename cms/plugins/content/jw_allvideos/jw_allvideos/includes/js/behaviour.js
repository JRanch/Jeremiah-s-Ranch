/**
 * @version		4.4
 * @package		AllVideos (plugin)
 * @author    JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

var allvideos = {
	ready: function(cb) {
		/in/.test(document.readyState) ? setTimeout('allvideos.ready('+cb+')', 9) : cb();
	},

	getRemoteJson: function(url) {
		var remoteJsonScript = document.createElement('script');
		remoteJsonScript.setAttribute('charset', 'utf-8');
		remoteJsonScript.setAttribute('type', 'text/javascript');
		remoteJsonScript.setAttribute('async', 'true');
		remoteJsonScript.setAttribute('src', url);
		return remoteJsonScript;
	},

	embed: function(el){
		var jsonpCallback = el.callback;
		var tempId = Math.floor(Math.random()*1000)+1;
		var responseContainer = [];
		window[jsonpCallback] = function(response){
			responseContainer.tempId = [response];
		};
		var head = document.getElementsByTagName('head')[0];
		var remotejson = this.getRemoteJson(el.url);
		head.appendChild(remotejson);
		remotejson.onload = function(){
			document.getElementById(el.playerID).innerHTML = responseContainer.tempId[0].html;
		}
	}
}
