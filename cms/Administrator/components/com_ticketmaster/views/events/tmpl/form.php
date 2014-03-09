<?php

/****************************************************************
 * @version			2.5.5											
 * @package			ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## This if for new events only.
error_reporting(0);

if (!$this->data->eventid){
	$events = JText::_( 'COM_TICKETMASTER_NEW_EVENT' );
}else{
	$events = $this->data->eventname.' <small>('.$this->data->eventid.')</small>';
}	

## Setting the toolbars up here..
$newCat	= ($this->data->eventid < 1);
$text = $newCat ? JText::_( 'COM_TICKETMASTER_ADD' ) : JText::_( 'COM_TICKETMASTER_EDIT' );

JToolBarHelper::title(''.$text.' '.$events.'', 'generic.png');
JToolBarHelper::save();
if (!$this->data->eventid)  {
	## Cancel the operation
	JToolBarHelper::cancel();
	
} else {
	## For existing items the button is renamed `close`
	JToolBarHelper::cancel( 'cancel', 'Close' );
	
};

## initialize the editor
$editor = JFactory::getEditor();

JHTML::_('behavior.tooltip', '.hasTip');
JHTML::_('behavior.modal');

## Get document type and add it.
$document = JFactory::getDocument();
$document->addScript('http://code.jquery.com/jquery-latest.js');
## Add the fancy lightbox for information fields.
$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.js');
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.css' );
$document->addScript( JURI::root(true).'/components/com_ticketmaster/assets/javascripts/moovalid.js');


## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet('http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}else{
	$document->addStyleSheet('http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css');
}
?>


<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

<script language="javascript">

	var JQ = jQuery.noConflict();

	JQ(function() {
      JQ( "#eventdate" ).datepicker({
		  	numberOfMonths: 4,
			minDate: 0,
			dateFormat: 'yy-mm-dd'});
	});	
	
	JQ(function() {
      JQ( "#closingdate" ).datepicker({
		  	numberOfMonths: 4,
			minDate: 0,
			dateFormat: 'yy-mm-dd'});
	});		

</script>

<form action = "index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
  <div class="span6">

        <table class="table table-striped" width="100%">
            <tr>
                <td width="40%">
                <?php echo JText::_( 'COM_TICKETMASTER_EVENTNAME' ); ?></label></td>
                <td width="40%"><input class="text_area" type="text" name="eventname" id="eventname" size="40" maxlength="40" 
                    value="<?php echo $this->data->eventname; ?>" />
                </td>
            </tr>
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_EVENTCODE' ); ?></td>
                <td><input class="text_area" type="text" name="groupname" id="groupname" size="25" maxlength="5"
                value="<?php echo $this->data->groupname; ?>" />
                    <a href="#mb_inline_code" role="button" class="btn pull-right" data-toggle="modal">
                        <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
                    </a>                                
                </td>
            </tr>
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_EVENTTICKETS' ); ?></td>
                <td><input class="text_area" type="text" name="totaltickets" id="totaltickets" size="25" maxlength="25"
                value="<?php echo $this->data->totaltickets; ?>" />
                    <a href="#mb_totaltickets" role="button" class="btn pull-right" data-toggle="modal">
                        <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
                    </a>                                 
                </td>
            </tr>    
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_EVENTDATE' ); ?></td>
                <td><input class="input" type="text" name="eventdate" id="eventdate" size="25" maxlength="25"
                		value="<?php echo date ('Y-m-d', strtotime($this->data->eventdate)); ?>" /></td>
            </tr>
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_CLOSINGDATE' ); ?></td>
                <td><input class="input" type="text" name="closingdate" id="closingdate" size="25" maxlength="25"
                		value="<?php echo date ('Y-m-d', strtotime($this->data->closingdate)); ?>" /></td>                
            </tr>
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_STATE' ); ?></td>
                <td><?php echo $this->lists['published']; ?></td>
            </tr>    
        </table>
  
  </div>
  <div class="span6">
  
    <table width="100%" class="table table-striped">
        <tr>
            <td width="100%" colspan="3" valign="top">
            <?php
            ## parameters : areaname, content, width, height, cols, rows
            echo $editor->display( 'eventdescription',  $this->data->eventdescription , '100%', '200', '100', '15' ) ; ?></td>
        </tr>
    </table>  
  
  </div>
</div>  

<input type="hidden" name="eventid" value="<?php echo $this->data->eventid; ?>" />
<input type="hidden" name="option" value="com_ticketmaster" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="events" />
</form>

<div id="mb_inline_code" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_CODE' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_CODE_EXPLANATION' ); ?>     
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_totaltickets" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_TICKETS' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_TOTAL_TICKETS_EXPLANATION' ); ?>     
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
 		
