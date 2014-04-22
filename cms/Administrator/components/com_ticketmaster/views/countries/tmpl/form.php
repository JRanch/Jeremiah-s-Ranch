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
## Only perform this if is error reporting is on max!
if ($app->getCfg('error_reporting') == 'maximum'){
	error_reporting(0);
}

## Setting the toolbars up here..
$newCat	= ($this->data->country_id < 1);
$text   = $newCat ? JText::_( 'COM_TICKETMASTER_ADD' ) : JText::_( 'COM_TICKETMASTER_EDIT' );

JToolBarHelper::title(''.$text.' '.JText::_( 'COM_TICKETMASTER_COUNTRY' ).': '.$this->data->country, 'generic.png');
JToolBarHelper::save();

if ($this->data->country_id < 1)  {
	## Cancel the operation
	JToolBarHelper::cancel();
} else {
	## For existing items the button is renamed `close`
	JToolBarHelper::cancel( 'cancel', 'Close' );
};

## initialize the editor
$editor = JFactory::getEditor();

## Get document type and add it.
$document = &JFactory::getDocument();

## Add the fancy lightbox for information fields. (Mootools 2.0 Functionality)
$document->addScript( JURI::root(true).'/administrator/components/com_rdsubs/assets/lightbox/mediabox.js');
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_rdsubs/assets/lightbox/mediabox.css' );

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}	
?>

<form action = "index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
  <div class="span6">

	<h3><?php echo JText::_( 'COM_TICKETMASTER_COUNTRY_INFO' ); ?></h3>

    <table class="table table-striped">
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_COUNTRYNAME' ); ?></label></td>
            <td width="50%" colspan="2"><input class="text_area" type="text" name="country" id="country" size="50" maxlength="50" 
                    value="<?php echo $this->data->country; ?>" />
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_( 'COM_TICKETMASTER_THREE_DIGIT_CODE' ); ?></label></td>
            <td><input class="text_area" type="text" name="country_3_code" id="country_3_code" size="5" maxlength="10" 
                    value="<?php echo $this->data->country_3_code; ?>" />
            </td>
        </tr>  
        <tr>
            <td><?php echo JText::_( 'COM_TICKETMASTER_TWO_DIGIT_CODE' ); ?></label></td>
            <td><input class="text_area" type="text" name="country_2_code" id="country_2_code" size="5" maxlength="10" 
                    value="<?php echo $this->data->country_2_code; ?>" />
            </td>
        </tr>               
        <tr>
            <td><?php echo JText::_( 'COM_TICKETMASTER_PUBLISHED' ); ?></td>
            <td><?php echo $this->lists['published']; ?>      
            </td>
        </tr>  
    </table>  
  
  </div>
  <div class="span6">
  </div>
</div>


<input type="hidden" name="country_id" value="<?php echo $this->data->country_id; ?>" />
<input type="hidden" name="option" value="com_ticketmaster" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="countries" />
</form>
	
		