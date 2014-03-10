<?php

/****************************************************************
 * @version		Ticketmaster 2.5.5							
 * @package		ticketmaster									
 * @copyright	Copyright © 2009 - All rights reserved.			
 * @license		GNU/GPL											
 * @author		Robert Dam										
 * @author mail	info@rd-media.org								
 * @website		http://www.rd-media.org							
 ***************************************************************/

/*************************************************************************************
Lightbox provided by http://iaian7.com/webcode/mediaboxAdvanced
mediaboxAdvanced supports most online media formats, and anything not recognized is 
automatically contained within a dynamic frame, allowing you toopen a lightbox-style 
overlay with anything inside. From JPEG images or flash videos, to PHP pages, 
mediaboxAdvanced can display your content in an easy-to-style overlay.
*************************************************************************************/

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## initialize the editor
$document 	= JFactory::getDocument();
$editor 	= JFactory::getEditor();

## Setting the toolbars up here..
$newCat	= ($this->data->id < 1);
$text = $newCat ? JText::_( 'COM_TICKETMASTER_ADD' ) : JText::_( 'COM_TICKETMASTER_EDIT' ).' '.JText::_( 'COM_TICKETMASTER_VENUE_ITEM' ).'<small> ['.
				$this->data->venue.' ]</small>';
JToolBarHelper::title($text, 'generic.png');
JToolBarHelper::save();

if ($this->data->id < 1)  {
	## Cancel the operation
	JToolBarHelper::cancel();
} else {
	## For existing items the button is renamed `close`
	JToolBarHelper::cancel( 'cancel', 'Close' );
};

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addScript('https://code.jquery.com/jquery-latest.js');
	## Add the fancy lightbox for information fields.
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.js');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.css' );	
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}else{
	JHtml::_('bootstrap.framework');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/j3-lightbox/css/colorbox.css' );
	$document->addScript(JURI::root(true).'/administrator/components/com_ticketmaster/assets/j3-lightbox/js/jquery.colorbox.js');
	$document->addScript(JURI::root(true).'/administrator/components/com_ticketmaster/assets/j3-lightbox/js/colorbox.js');	
}
?>

<script>

var JQ = jQuery.noConflict();
	
	JQ(document).ready(function(){
	JQ(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});

});
	
</script>

<form action = "index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
  <div class="span6">
  
        <table class="table table-striped">
            <tr>
                <td width="50%">
                <?php echo JText::_( 'COM_TICKETMASTER_VENUE_NAME' ); ?></label></td>
                <td width="50%"><input class="text_area" type="text" name="venue" id="venue" size="50" maxlength="50" 
                		value="<?php echo $this->data->venue; ?>" /></td>
            </tr>
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_VENUE_ADDRESS' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="street" id="street" size="25" maxlength="255"
                value="<?php echo $this->data->street; ?>" /></td>
            </tr>
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_ZIP' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="zipcode" id="zipcode" size="25" maxlength="225"
                value="<?php echo $this->data->zipcode; ?>" /></td>
            </tr>    
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_CITY' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="city" id="city" size="25" maxlength="225"
                value="<?php echo $this->data->city; ?>" /></td>
            </tr>          
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_STATES' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="state" id="state" size="25" maxlength="25"
                value="<?php echo $this->data->state; ?>" /></td>
            </tr> 
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_COUNTRY' ); ?></td>
                <td width="50%"><?php echo $this->lists['country']; ?></td>
            </tr>     
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_URL' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="website" id="website" size="25" maxlength="225"
                value="<?php echo $this->data->website; ?>" /></td>
            </tr>
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_CONTACT_PERSON' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="contact_person" id="contact_person" size="25" maxlength="225"
                value="<?php echo $this->data->contact_person; ?>" /></td>
            </tr>
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_PHONE_NUMBER' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="phone_number" id="phone_number" size="25" maxlength="225"
                value="<?php echo $this->data->phone_number; ?>" /></td>
            </tr>             
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_CONTACT_EMAIL' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="email_address" id="email_address" size="25" maxlength="225"
                value="<?php echo $this->data->email_address; ?>" /></td>
            </tr>                                    
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_GOOGLEMAP' ); ?></td>
                <td><?php echo $this->lists['map']; ?> 
                <a class="iframe" href="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $this->data->city; ?>,+mi&amp;aq=&amp;sll=<?php echo $this->data->googlemaps_longitude; ?>,<?php echo $this->data->googlemaps_latitude; ?>&amp;sspn=0.026765,0.024848&amp;ie=UTF8&amp;hq=&amp;hnear=<?php echo $this->data->city; ?>+<?php echo $this->data->street; ?>&amp;z=15&amp;ll=<?php echo $this->data->googlemaps_longitude; ?>,<?php echo $this->data->googlemaps_latitude; ?>&amp;output=embed" rel="lightbox[external 640 360]" title="<?php echo $this->data->venue; ?>, <?php echo $this->data->street; ?>, <?php echo $this->data->city; ?>"><img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/show-now.png'; ?>"
                    border="0" alt="" /></a>          
                </td>
            </tr>
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_USE_OWN_LONG_LAT' ); ?></td>
                <td width="50%"><?php echo $this->lists['own_ll']; ?></td>
            </tr> 
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_ZOOM' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="googlemaps_precision" id="googlemaps_precision" size="25" maxlength="225"
                value="<?php echo $this->data->googlemaps_precision; ?>" /></td>
            </tr>                   
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_LATITUDE' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="googlemaps_latitude" id="googlemaps_latitude" size="25" maxlength="225"
                value="<?php echo $this->data->googlemaps_latitude; ?>" /></td>
            </tr>      
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_LONGITUDE' ); ?></td>
                <td width="50%"><input class="text_area" type="text" name="googlemaps_longitude" id="googlemaps_longitude" size="25" maxlength="225"
                value="<?php echo $this->data->googlemaps_longitude; ?>" /></td>
            </tr>          
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_STATE' ); ?></td>
                <td><?php echo $this->lists['published']; ?></td>       
        </table> 
        
        <div class="alert alert-info">
          <h4><?php echo JText::_( 'COM_TICKETMASTER_INFORMATION' ); ?></h4>
		  <?php echo JText::_( 'COM_TICKETMASTER_NOTE_VENUE_NEW' ); ?>
        </div>  
        
        <table width="100%" class="table table-striped">
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_METAKEYS' ); ?></td>
                <td width="50%"><textarea name="meta_keywords" cols="70" rows="3"><?php echo $this->data->meta_keywords; ?></textarea></td>
            </tr> 
            <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_METADESC' ); ?></td>
                <td width="50%"><textarea name="meta_description" cols="70" rows="3"><?php echo $this->data->meta_description; ?></textarea></td>
            </tr>      
        </table>   
        
        <table width="100%" class="table">
            <tr>
                <td width="100%" colspan="3" valign="top">
                <?php
                ## parameters : areaname, content, width, height, cols, rows
                echo $editor->display( 'locdescription',  $this->data->locdescription , '600px', '200', '100', '15' ) ; ?></td>
            </tr>
        </table>                    
  
  </div>
  <div class="span6">
  

  
  </div>
</div>


<input type="hidden" name="id" value="<?php echo $this->data->id; ?>" />
<input type="hidden" name="option" value="com_ticketmaster" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="venues" />
</form>
		
