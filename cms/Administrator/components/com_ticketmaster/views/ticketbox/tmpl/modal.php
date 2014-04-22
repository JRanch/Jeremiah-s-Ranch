<?php
/****************************************************************
 * @version		1.0.0 ticketmaster $							*
 * @package		ticketmaster									*
 * @copyright	Copyright © 2009 - All rights reserved.			*
 * @license		GNU/GPL											*
 * @author		Robert Dam										*
 * @author mail	info@rd-media.org								*
 * @website		http://www.rd-media.org							*
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

$document = & JFactory::getDocument();
$document->addStyleSheet('components/com_ticketmaster/assets/component_css.css');

?>
<script>
	window.addEvent('domready', function() {

	  /* ajax replace element text */
	  $('userid').addEvent('change', function(event) {
		//prevent the page from changing
		event.stop();
		var BarCode = document.getElementById('barcode').value.replace(/[^0-9]/g,'');
		//make the ajax call, replace text
		var req = new Request.HTML({
		  method: 'get',
		  url: 'index.php?option=com_ticketmaster&controller=ticketbox&task=checkticket&format=raw&id='+BarCode,
		  data: { 'do' : '1' },
		  //onRequest: function() { alert('Request made. Please wait...'); },
		  update: $('message-here'),
		  onComplete: function(response) { $('message-here').setStyle('background','#fffea1');
		  }
		}).send();
	  });
	});

function disableEnterKey(e)
{
     var key;

     if(window.event)
          key = window.event.keyCode;     //IE
     else
          key = e.which;     //firefox

     if(key == 13)
          return false;
     else
          return true;
}


function disableCtrlKeyCombination()
{
//list all CTRL + key combinations you want to disable
var forbiddenKeys = new Array("a","i");
var key;
var isCtrl;

if(window.event)
{
key = window.event.keyCode;     //IE
if(window.event.ctrlKey)
isCtrl = true;
else
isCtrl = false;
}
else
{

key = e.which;     //firefox
if(e.ctrlKey)
isCtrl = true;
else
isCtrl = false;
}

//if ctrl is pressed check if other key is in forbidenKeys array
if(isCtrl)
{
for(i=0; i<forbiddenKeys.length; i++)
{
//case-insensitive comparation
if(forbiddenKeys[i].toLowerCase() == String.fromCharCode(key).toLowerCase())
{
alert(‘Key combination CTRL + ‘+String.fromCharCode(key) +’ has been disabled.’);
return false;
}
}
}
return true;
}


</script>

<div id = "message-here">
test
</div>

    
<form action = "index.php" method="POST" name="adminForm">

   <table class="admintable" >
    <tr>
        <td class="key">
        <?php echo JText::_( 'COM_TICKETMASTER_VENUE_NAME' ); ?></label></td>
        <td width="352" colspan="2"><input type="text" name="mytext" onKeyPress="return disableCtrlKeyCombination()"></td>
    </tr>
    </table>

  <input name = "option" type="hidden" value="com_ticketmaster" />
  <input name = "limitstart" type="hidden" value="<?php echo $this->pagination->limitstart; ?>" />
  <input name = "task" type="hidden" value="checkticket" />
  <input name = "boxchecked" type="hidden" value="0"/>
  <input name = "controller" type="hidden" value="ticketbox"/>
</form>