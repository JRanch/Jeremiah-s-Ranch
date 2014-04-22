<?php

/****************************************************************
 * @version			2.5.5											
 * @package			com_ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

$app     = JFactory::getApplication();

## Only perform this is error reporting is on max!
if ($app->getCfg('error_reporting') == 'maximum'){
	error_reporting(0);
}

## Setting the toolbars up here..
$newCat	= ($this->data->coupon_id < 1);
$text = $newCat ? JText::_( 'COM_TICKETMASTER_ADD' ) : JText::_( 'COM_TICKETMASTER_EDIT' );

JToolBarHelper::title(''.$text.' '.JText::_( 'COM_TICKETMASTER_COUPON' ), 'generic.png');
JToolBarHelper::save();

if ($this->data->coupon_id < 1)  {
	## Cancel the operation
	JToolBarHelper::cancel();
} else {
	## For existing items the button is renamed `close`
	JToolBarHelper::cancel( 'cancel', 'Close' );
};

## initialize the editor
$editor    = JFactory::getEditor();
$document  = JFactory::getDocument();
$document->addScript('http://code.jquery.com/jquery-latest.js');

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addStyleSheet('http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css');
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}else{
	$document->addStyleSheet('http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css');	
}

?>

<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

<script language="javascript">

	var JQ = jQuery.noConflict();
    
	JQ(function() {
      JQ( "#coupon_valid_to" ).datepicker({
		  	numberOfMonths: 3,
			minDate: 0,
			dateFormat: 'yy-mm-dd'});
	});


</script>

<div class="row-fluid">
  <div class="span6">
  
    <form action = "index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">
    
    <table class="table table-striped">
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_COUPON_NAME' ); ?></label></td>
            <td width="50%"><input class="text_area" type="text" name="coupon_name" id="coupon_name" size="45" maxlength="150" 
                    value="<?php echo $this->data->coupon_name; ?>" />
                    <a href="#inline_coupon_name" role="button" class="btn pull-right" data-toggle="modal">
                        <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
                    </a>                      
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_( 'COM_TICKETMASTER_COUPON_CODE' ); ?></label></td>
            <td><input class="text_area" type="text" name="coupon_code" id="coupon_code" size="45" maxlength="15" 
                    value="<?php echo $this->data->coupon_code; ?>" />
                    <a href="#inline_coupon_code" role="button" class="btn pull-right" data-toggle="modal">
                        <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
                    </a>                      
            </td>
        </tr>  
        <tr>
            <td><?php echo JText::_( 'COM_TICKETMASTER_COUPON_LIMIT' ); ?></label></td>
            <td><input class="text_area" type="text" name="coupon_limit" id="coupon_limit" size="45" maxlength="5" 
                    value="<?php echo $this->data->coupon_limit; ?>" />
            </td>
        </tr>          
        <tr>
            <td><?php echo JText::_( 'COM_TICKETMASTER_COUPON_EXPIRATION' ); ?></td>
            <td><input class="input" type="text" name="coupon_valid_to" id="coupon_valid_to" size="25" maxlength="25"
                            value="<?php echo date ('Y-m-d', strtotime($this->data->coupon_valid_to)); ?>" />                            
            </td>
        </tr> 
        <tr>
            <td><?php echo JText::_( 'COM_TICKETMASTER_TYPE_COUPON' ); ?></label></td>
            <td><?php echo $this->lists['coupon_type']; ?></td>
        </tr>
        <tr>
            <td><?php echo JText::_( 'COM_TICKETMASTER_COUPON_DISCOUNT' ); ?></label></td>
            <td><input class="text_area" type="text" name="coupon_discount" id="coupon_discount" size="7" maxlength="3" 
                    value="<?php echo $this->data->coupon_discount; ?>" />
                    <a href="#inline_coupon_discount" role="button" class="btn pull-right" data-toggle="modal">
                        <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
                    </a>                      
            </td>
        </tr>                         
        <tr>
            <td><?php echo JText::_( 'COM_TICKETMASTER_PUBLISHED' ); ?></td>
            <td><?php echo $this->lists['published']; ?>      
            </td>
        </tr>       
    </table>
    
    <input type="hidden" name="coupon_id" value="<?php echo $this->data->coupon_id; ?>" />
    <input type="hidden" name="option" value="com_ticketmaster" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="coupons" />
    </form>
  
  
  </div>
  <div class="span6"></div>
</div>



<div id="inline_coupon_discount" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_COUPON_DISCOUNT' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_COUPON_DISCOUNT_DESC' ); ?><br/><br/>       
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="inline_coupon_name" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_COUPON_NAME' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_COUPON_NAME_DESC' ); ?><br/><br/>       
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="inline_coupon_code" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_COUPON_CODE' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_COUPON_CODE_DESC' ); ?><br/><br/>       
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
	
		