// JavaScript Document
var jQuery = jQuery.noConflict();


jQuery(document).ready(function(){
				//Examples of how to assign the ColorBox event to elements
				jQuery(".group1").colorbox({rel:'group1'});
				jQuery(".group2").colorbox({rel:'group2', transition:"fade"});
				jQuery(".group3").colorbox({rel:'group3', transition:"none", width:"75%", height:"75%"});
				jQuery(".group4").colorbox({rel:'group4', slideshow:true});
				jQuery(".ajax").colorbox();
				jQuery(".youtube").colorbox({iframe:true, innerWidth:640, innerHeight:510});
				jQuery(".iframe").colorbox({iframe:true, width:"50%", height:"90%"});
				jQuery(".inline").colorbox({inline:true, width:"50%"});
				jQuery(".callbacks").colorbox({
					onOpen:function(){ alert('onOpen: colorbox is about to open'); },
					onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
					onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
					onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
					onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
				});
				

			});