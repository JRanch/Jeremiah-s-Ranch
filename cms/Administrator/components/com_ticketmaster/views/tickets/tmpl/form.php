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

## Setting the toolbars up here..
$newCat	= ($this->data->eventid < 1);
$text = $newCat ? JText::_( 'COM_TICKETMASTER_ADD' ) : JText::_( 'COM_TICKETMASTER_EDIT' );
JToolBarHelper::title(''.$text.' '.JText::_( 'COM_TICKETMASTER_TICKET' ), 'generic.png');
JToolBarHelper::save();
if ($this->data->eventid < 1)  {
	## Cancel the operation
	JToolBarHelper::cancel();
} else {
	## For existing items the button is renamed `close`
	JToolBarHelper::cancel( 'cancel', JText::_( 'COM_TICKETMASTER_CLOSE' ) );
};

## initialize the editor
$editor = JFactory::getEditor();
JHTML::_('behavior.tooltip', '.hasTip');

## Get document type and add it.
$document = JFactory::getDocument();
$document->addScript('https://code.jquery.com/jquery-latest.js');
## Add the fancy lightbox for information fields.
$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.js');
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.css' );

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addStyleSheet('https://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css');
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}else{
	$document->addStyleSheet('https://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css');	
}

?>
<?php if ($this->venue->countvenues == 0) { ?>
    <div class="alert">
      <strong>Warning!</strong> <?php echo JText::_( 'COM_TICKETMASTER_NO_VENUE_STOP' ); ?>
    </div>
<?php } ?>

<div class="alert alert-error" id="date-error" style="display: none;"></div>

<script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

<script language="javascript">

	var JQ = jQuery.noConflict();
    
	JQ(function() {
      JQ( "#ticketdate" ).datepicker({
		  	numberOfMonths: 4,
			minDate: 0,
			dateFormat: 'yy-mm-dd'});
	});
	
	JQ(function() {
      JQ( "#end_date" ).datepicker({
		  	numberOfMonths: 4,
			minDate: 0,
			dateFormat: 'yy-mm-dd'});
	});	

	JQ(function() {
      JQ( "#sale_stop" ).datepicker({
		  	numberOfMonths: 4,
			minDate: 0,
			dateFormat: 'yy-mm-dd'});
	});		


</script>

<script type="text/javascript">

var JQ = jQuery.noConflict();

JQ(document).ready(function() {

	JQ("#jquerselect").bind("change", function(e){
	
	JQ.getJSON("index.php?option=com_ticketmaster&controller=jquery&task=getdata&format=raw&id=" + JQ("#jquerselect").val(),
			function(data){
			JQ.each(data, function(name,value) {
				if(name == 'show_seatplans'){ JQ('#show_seatplans').val(value); }
				if(name == 'counter_choice'){ JQ('#counter_choice').val(value); }	
				if(name == 'combine_multitickets'){ JQ('#combine_multitickets').val(value); }					
				if(name == 'ticket_size'){ JQ('#ticket_size').val(value); }	
				if(name == 'ticket_orientation'){ JQ('#ticket_orientation').val(value); }
				if(name == 'eventid'){ JQ('#eventid').val(value); }				
				if(name == 'venue') { JQ('#venue').val(value); }	
				if(name == 'published') { JQ('#published').val(value); }	
				if(name == 'show_end_date') { JQ('#show_end_date').val(value); }																					
				JQ("input[name='" + name + "']").val(value);
			});	
		});
	});
	
}); 

JQ(document).ready(function() {

	JQ("#getSampleData").bind("click", function(e){

	// Retreive the value of the ticket inputbox
	var select = JQ( "#sampledata" ).val();

	if(select != 0) {
		var link = 'index.php?option=com_ticketmaster&controller=jquery&task=getSampleData&format=raw&ticketid='+select;
	}else{
		var link = 'index.php?option=com_ticketmaster&controller=jquery&task=getSampleData&format=raw';
	}
				
	JQ.getJSON(link,
			function(data){
			JQ.each(data, function(name,value) {
				JQ("input[name='" + name + "']").val(value);
			});	
		});
	});
	
}); 


Joomla.submitbutton = function(pressbutton) {
var form = document.adminForm;
	
	if(pressbutton == 'cancel'){
		submitform(pressbutton);
		return;
	}
 
	// Gettin the values from the form
	eventid = JQ('#eventid').val();
	venueid = JQ('#venue').val();
	
	// Check these entries
	if (eventid==0) {                 	
		alert ('<?php echo JText::_( 'COM_TICKETMASTER_NO_EVENT_CHOSEN' ); ?>');
		return
	} else if (venueid==0) {
		alert ('<?php echo JText::_( 'COM_TICKETMASTER_NO_VENUE_CHOSEN' ); ?>');
		return
	} else {
       submitform(pressbutton);
       return;
	}
	
}

</script>


<form action = "index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">

  <div class="span6">

<table class="table table-striped" width="100%">
    <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_EVENTID_CATEGORY' ); ?></td>
        <td colspan="2"><?php echo $this->lists['eventid']; ?></td>
    </tr>    
    <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_CHILD' ); ?></td>
        <td colspan="2"><?php echo $this->lists['parent']; ?>
            <a href="#mb_inline_parent" role="button" class="btn pull-right" data-toggle="modal">
                <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                      
        </td>
    </tr>
    <tr>
        <td class="key" width="50%">
        <?php echo JText::_( 'COM_TICKETMASTER_TICKETNAME' ); ?></label></td>
        <td width="50%" colspan="2"><input class="input" type="text" name="ticketname" id="ticketname" size="50" maxlength="50" 
        value="<?php echo $this->data->ticketname; ?>" />
        <?php if($this->data->scan_pin != 0){ ?>
            <a href="#mb_scan_pin" role="button" class="btn pull-right" data-toggle="modal">
                <?php echo $this->data->scan_pin; ?>
            </a>                         
		<?php } ?>
        </td>
    </tr>   
    <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_VENUE' ); ?></td>
        <td colspan="2"><?php echo $this->lists['venues']; ?></td>
    </tr> 
    <?php /*
    <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_NAMED_TICKET_REQUIRED' ); ?></td>
        <td colspan="2"><?php echo $this->lists['requires_name']; ?>
            <a href="#mb_inline_required_name" role="button" class="btn pull-right" data-toggle="modal">
                <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>         
        </td>
    </tr> 
    */ ?>    
    <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_FREE_TEXT_1' ); ?></td>
        <td colspan="2"><input class="input-medium" type="text" name="free_text_1" id="free_text_1" size="50" maxlength="150" 
        value="<?php echo $this->data->free_text_1; ?>" /></td>
    </tr> 
    <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_FREE_TEXT_2' ); ?></td>
        <td colspan="2"><input class="input-medium" type="text" name="free_text_2" id="free_text_2" size="50" maxlength="150" 
        value="<?php echo $this->data->free_text_2; ?>" /></td>
    </tr>         
     <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_EVENTCODE' ); ?></td>
        <td colspan="2"><input class="input-medium" type="text" name="eventcode" id="eventcode" size="25" maxlength="5"
        value="<?php echo $this->data->eventcode; ?>" /></td>
    </tr>  
    <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_TICKETS' ); ?></td>
        <td colspan="2"><input class="input-medium" type="text" name="totaltickets" id="totaltickets" size="25" maxlength="25"
        value="<?php echo $this->data->totaltickets; ?>" /></td>
    </tr>
    <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_TICKETS_START' ); ?></td>
        <td colspan="2"><input class="input-medium" type="text" name="starting_total_tickets" id="starting_total_tickets" size="25" maxlength="25"
        value="<?php echo $this->data->starting_total_tickets; ?>" />
            <a href="#mb_starting_total_tickets" role="button" class="btn pull-right" data-toggle="modal">
                <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>          
        </td>
    </tr>        
    <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_COUNTER_CHOICE' ); ?></td>
        <td colspan="2"><?php echo $this->lists['counter_choice']; ?>
            <a href="#mb_counter_choice" role="button" class="btn pull-right" data-toggle="modal">
                <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>           
        </td>
    </tr>      
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_PRICE' ); ?></td>
        <td width="50%"><input class="input-medium" type="text" name="ticketprice" id="ticketprice" size="25" maxlength="5"
        value="<?php echo $this->data->ticketprice; ?>" /></td>
    </tr>
    
    <?php if ($this->data->parent == 0) { ?>
        <tr>
            <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_STARTPRICE' ); ?></td>
            <td width="50%"><input class="input-medium" type="text" name="start_price" id="start_price" size="25" maxlength="5"
            value="<?php echo $this->data->start_price; ?>" />
            <a href="#mb_startprice" role="button" class="btn pull-right" data-toggle="modal">
                <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>               
            </td>
        </tr>
        <tr>
            <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_ENDPRICE' ); ?></td>
            <td width="50%"><input class="input-medium" type="text" name="end_price" id="end_price" size="25" maxlength="5"
            value="<?php echo $this->data->end_price; ?>" />
            <a href="#mb_endprice" role="button" class="btn pull-right" data-toggle="modal">
                <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>               
            </td>
        </tr>
    <?php } ?>        
    
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_MINIMUM_AMOUNT' ); ?></td>
        <td width="50%"><input class="input-medium" type="text" name="min_ordering" id="min_ordering" size="25" maxlength="5"
        value="<?php echo $this->data->min_ordering; ?>" /></td>
    </tr>
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_MAXIMUM_AMOUNT' ); ?></td>
        <td width="50%"><input class="input-medium" type="text" name="max_ordering" id="max_ordering" size="25" maxlength="5"
        value="<?php echo $this->data->max_ordering; ?>" /></td>
    </tr>                  
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_STARTDATE' ); ?></td>
        <td width="50%"><input class="input" type="text" name="ticketdate" id="ticketdate" size="25" maxlength="25"
                		value="<?php echo date ('Y-m-d', strtotime($this->data->ticketdate)); ?>" /></td>
    </tr>
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_ENDDATE' ); ?></td>
        <td width="50%"><input class="input" type="text" name="end_date" id="end_date" size="25" maxlength="25"
                		value="<?php echo date ('Y-m-d', strtotime($this->data->end_date)); ?>" /></td>
    </tr>    
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_SHOW_ENDDATE' ); ?></td>
        <td width="50%"><?php echo $this->lists['show_end_date']; ?></td>
    </tr>   
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_START_EVENT' ); ?></td>
        <td width="50%"><input class="input-medium" type="text" name="starttime" id="starttime" size="25" maxlength="10"
        value="<?php echo $this->data->starttime; ?>" /></td>
    </tr>
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_END_EVENT' ); ?></td>
        <td width="50%"><input class="input-medium" type="text" name="end_time" id="end_time" size="25" maxlength="10"
        value="<?php echo $this->data->end_time; ?>" /></td>
    </tr>
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_USE_SALE_STOP' ); ?></td>
        <td width="50%"><?php echo $this->lists['use_sale_stop']; ?>
            <a href="#mb_inline_sale_stop" role="button" class="btn pull-right" data-toggle="modal">
                <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                         
        </td>
    </tr> 
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_SCANNING_ON' ); ?></td>
        <td width="50%"><?php echo $this->lists['scans_on']; ?>
            <a href="#mb_inline_scans_on" role="button" class="btn pull-right" data-toggle="modal">
                <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                         
        </td>
    </tr>             
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_SALE_STOP' ); ?></td>
        <td width="50%"><input class="input" type="text" name="sale_stop" id="sale_stop" size="25" maxlength="25"
                		value="<?php echo date ('Y-m-d', strtotime($this->data->sale_stop)); ?>" /></td>
    </tr>  
    <?php if ($this->config->pro_installed == 1){ ?>
        <tr>
            <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_SEATPLANS' ); ?></td>
            <td width="50%"><?php echo $this->lists['show_seatplans']; ?></td>
        </tr> 
    <?php } ?>    
    <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_IS_PUBLISHED' ); ?></td>
        <td width="50%"><?php echo $this->lists['show_orderdate']; ?></td>
    </tr>    
	</table>

    <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_DESRIPTION' ); ?></h3>    
    
    <table class="table" width="100%">
        <tr>
            <td valign="top" colspan="3">
            <?php
            ## parameters : areaname, content, width, height, cols, rows
            echo $editor->display( 'ticketdescription',  $this->data->ticketdescription , '100%', '300', '100', '15' ) ; ?></td>
        </tr>
    </table>


  </div>
  
  <div class="span6">
  
    <table class="table table-striped" width="100%">
        <tr>
            <td class="key" width="50%"><?php echo JText::_( 'COM_TICKETMASTER_COPY_DATA' ); ?></td>
            <td class="key" width="50%"><?php echo $this->lists['jquerselect1']; ?>
                <a href="#mb_inline_copy" role="button" class="btn pull-right" data-toggle="modal">
                    <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
                </a>                       
            </td>
        </tr>  
    </table>  
    
    <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_TICKET_LAYOUT' ); ?></h3>    
  
    <table class="table table-striped" width="100%">  
        <tr>
            <td width="50%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_UPLOAD_PDF_FILE' ); ?></td>
            <td width="50%" colspan="2" valign="middle">
                <input name="pdffile" type="file" />
                <a href="#mb_inline_file" role="button" class="btn pull-right" data-toggle="modal">
                    <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
                </a>                             
            </td>
        </tr> 
        <tr>
            <td width="50%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_UPLOAD_JPG_FILE' ); ?></td>
            <td width="50%" colspan="2" valign="middle">
                <input name="jpgfile" type="file" />   
                <a href="#mb_inline_file_jpg" role="button" class="btn pull-right" data-toggle="modal">
                    <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
                </a>                              
            </td>
        </tr> 
        
         <tr>
            <td class="key" width="50%"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_SIZE' ); ?></td>
            <td class="key" width="50%"><?php echo $this->lists['ticket_size']; ?> <?php echo $this->lists['ticket_orientation']; ?>
                                    
            </td>
        </tr>    
        <tr>
            <td class="key" width="50%"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_COMBINEPDF' ); ?></td>
            <td class="key" width="50%"><?php echo $this->lists['combine_multitickets']; ?>
                                    
            </td>
        </tr>  
        
                  
    </table> 
    
    <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_LAYOUT' ); ?></h3>

    <table class="table table-striped" width="100%">  
           
        <tr>
          <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_NORMAL_COLOR_SIZE' ); ?></td>
          <td width="50%">
          
          		<div class="input-prepend input-append">
				  <span class="add-on">R</span>
				  <input name="ticket_fontcolor_r" type="text" value="<?php echo $this->data->ticket_fontcolor_r; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>  
          		<div class="input-prepend input-append">
				  <span class="add-on">G</span>
				  <input name="ticket_fontcolor_g" type="text" value="<?php echo $this->data->ticket_fontcolor_g; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>           
          		<div class="input-prepend input-append">
				  <span class="add-on">B</span>
				  <input name="ticket_fontcolor_b" type="text" value="<?php echo $this->data->ticket_fontcolor_b; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>  
				
				<div class="input-prepend input-append">
					<input name="ticket_fontsize" type="text" value="<?php echo $this->data->ticket_fontsize; ?>" class="input-mini" style="margin-left:20px; width:20px;" 
	                       size="1" maxlength="3" placeholder="<?php echo JText::_( '9' ); ?>" />
	                <span class="add-on">px</span>
				</div>
				
            </td>
        </tr> 
        
        <tr>
          <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_CLIENTDATA_COLOR_SIZE' ); ?></td>
          <td width="50%">
          
          		<div class="input-prepend input-append">
				  <span class="add-on">R</span>
				  <input name="clientdata_fontcolor_r" type="text" value="<?php echo $this->data->clientdata_fontcolor_r; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>  
          		<div class="input-prepend input-append">
				  <span class="add-on">G</span>
				  <input name="clientdata_fontcolor_g" type="text" value="<?php echo $this->data->clientdata_fontcolor_g; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>           
          		<div class="input-prepend input-append">
				  <span class="add-on">B</span>
				  <input name="clientdata_fontcolor_b" type="text" value="<?php echo $this->data->clientdata_fontcolor_b; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>  
				
				<div class="input-prepend input-append">
					<input name="clientdata_fontsize" type="text" value="<?php echo $this->data->clientdata_fontsize; ?>" class="input-mini" style="margin-left:20px; width:20px;" 
	                       size="1" maxlength="3" placeholder="<?php echo JText::_( '9' ); ?>" />
	                <span class="add-on">px</span>
				</div>
				
            </td>
        </tr>          
               
        <tr>
          <td valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_PRICE_COLOR_SIZE' ); ?></td>
          <td colspan="2" valign="middle">
                
                <div class="input-prepend input-append">
				  <span class="add-on">R</span>
				  <input name="ticketnr_fontcolor_r" type="text" value="<?php echo $this->data->ticketnr_fontcolor_r; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>  
          		<div class="input-prepend input-append">
				  <span class="add-on">G</span>
				  <input name="ticketnr_fontcolor_g" type="text" value="<?php echo $this->data->ticketnr_fontcolor_g; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>           
          		<div class="input-prepend input-append">
				  <span class="add-on">B</span>
				  <input name="ticketnr_fontcolor_b" type="text" value="<?php echo $this->data->ticketnr_fontcolor_b; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>  
				
				<div class="input-prepend input-append">
					<input name="ticketnr_fontsize" type="text" value="<?php echo $this->data->ticketnr_fontsize; ?>" class="input-mini" style="margin-left:20px; width:20px;" 
	                       size="1" maxlength="3" placeholder="<?php echo JText::_( '18' ); ?>" />
	                <span class="add-on">px</span>
				</div> 

            </td>
        </tr> 
        
        <tr>
          <td valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_ID_COLOR_SIZE' ); ?></td>
          <td colspan="2" valign="middle">
          
          		<div class="input-prepend input-append">
				  <span class="add-on">R</span>
				  <input name="ticketid_nr_fontcolor_r" type="text" value="<?php echo $this->data->ticketid_nr_fontcolor_r; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>  
          		<div class="input-prepend input-append">
				  <span class="add-on">G</span>
				  <input name="ticketid_nr_fontcolor_g" type="text" value="<?php echo $this->data->ticketid_nr_fontcolor_g; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>           
          		<div class="input-prepend input-append">
				  <span class="add-on">B</span>
				  <input name="ticketid_nr_fontcolor_b" type="text" value="<?php echo $this->data->ticketid_nr_fontcolor_b; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>  
				
				<div class="input-prepend input-append">
					<input name="ticketid_nr_fontsize" type="text" value="<?php echo $this->data->ticketid_nr_fontsize; ?>" class="input-mini" style="margin-left:20px; width:20px;" 
	                       size="1" maxlength="3" placeholder="<?php echo JText::_( '18' ); ?>" />
	                <span class="add-on">px</span>
				</div>
          
            </td>
        </tr> 
        
        <?php if ($this->config->pro_installed == 1){ ?>
            <tr>
              <td valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_SEATNUMBER_COLOR_SIZE' ); ?></td>
              <td colspan="2" valign="middle">
                    
          		<div class="input-prepend input-append">
				  <span class="add-on">R</span>
				  <input name="seatnumber_fontcolor_r" type="text" value="<?php echo $this->data->seatnumber_fontcolor_r; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>  
          		<div class="input-prepend input-append">
				  <span class="add-on">G</span>
				  <input name="seatnumber_fontcolor_g" type="text" value="<?php echo $this->data->seatnumber_fontcolor_g; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>           
          		<div class="input-prepend input-append">
				  <span class="add-on">B</span>
				  <input name="seatnumber_fontcolor_b" type="text" value="<?php echo $this->data->seatnumber_fontcolor_b; ?>" maxlength="3" class="input-mini" style="width:20px;" /> 
				</div>  
				
				<div class="input-prepend input-append">
					<input name="font_size_seatnumber" type="text" value="<?php echo $this->data->font_size_seatnumber; ?>" class="input-mini" style="margin-left:20px; width:20px;" 
	                       size="1" maxlength="3" placeholder="<?php echo JText::_( '18' ); ?>" />
	                <span class="add-on">px</span>
				</div>

                </td>
            </tr> 
        <?php } ?>             
    </table>     
	
    <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_LAYOUT_POSITIONS' ); ?></h3>
    
    <table class="table table-striped" width="100%">  
        <tr>
            <td colspan="3" valign="middle" class="key">
               
				<div class="alert alert-block">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <?php echo JText::_( 'COM_TICKETMASTER_TICKET_LAYOUT_POSITIONS_DESC' ); ?>
				</div>               
               <button class="btn btn-primary pull-right" id="getSampleData" style="margin-left: 5px;" type="button">
               		<?php echo JText::_( 'COM_TICKETMASTER_TICKET_LOAD_POSITIONS' ); ?>
               	</button>
                <?php echo $this->lists['jquerselect2']; ?>
            </div>
            </td>
          </tr>    
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_EVENTNAME' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="eventname_position" type="text" value="<?php echo $this->data->eventname_position; ?>" 
                            size="5" maxlength="15" />
                <a href="#mb_inline_pos" role="button" class="btn pull-right" data-toggle="modal">
                    <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
                </a>                        
            </td>
        </tr> 
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_DATE' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="date_position" type="text" value="<?php echo $this->data->date_position; ?>" 
                            size="5" maxlength="15" /></td>
        </tr>   
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_LOCATION' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="location_position" type="text" value="<?php echo $this->data->location_position; ?>" 
                            size="5" maxlength="15" /></td>
        </tr> 
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_ORDERID' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="orderid_position" type="text" value="<?php echo $this->data->orderid_position; ?>" 
                            size="5" maxlength="15" /></td>
        </tr> 
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_ORDERNUMBER' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="ordernumber_position" type="text" value="<?php echo $this->data->ordernumber_position; ?>" 
                            size="5" maxlength="15" /></td>
        </tr> 
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_PRICE' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="price_position" type="text" value="<?php echo $this->data->price_position; ?>" 
                            size="5" maxlength="15" /></td>
        </tr>  
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_BARCODE' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="bar_position" type="text" value="<?php echo $this->data->bar_position; ?>" 
                            size="5" maxlength="15" /></td>
        </tr>  
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_ORDERDATE' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="orderdate_position" type="text" value="<?php echo $this->data->orderdate_position; ?>" 
                            size="5" maxlength="15" /></td>
        </tr>  
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_NAME_CLIENT' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="name_position" type="text" value="<?php echo $this->data->name_position; ?>" 
                            size="5" maxlength="15" /></td>
        </tr> 
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_FREE_TEXT1' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="free_text1_position" type="text" value="<?php echo $this->data->free_text1_position; ?>" 
                            size="5" maxlength="15" /></td>
        </tr> 
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_FREE_TEXT2' ); ?></td>
            <td width="65%" colspan="2" valign="middle">
                <input name="free_text2_position" type="text" value="<?php echo $this->data->free_text2_position; ?>" 
                            size="5" maxlength="15" /></td>
        </tr> 
        <?php if ($this->config->pro_installed == 1){ ?>
            <tr>
                <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_POS_SEAT_NUMBER' ); ?></td>
                <td width="65%" colspan="2" valign="middle">
                    <input name="position_seatnumber" type="text" value="<?php echo $this->data->position_seatnumber; ?>" 
                                size="5" maxlength="15" /></td>
            </tr> 
        <?php } ?>          
        <tr>
            <td width="35%" valign="middle" class="key"><?php echo JText::_( 'COM_TICKETMASTER_USE_BARCODE' ); ?></td>
            <td width="65%" colspan="2" valign="middle"><?php echo $this->lists['pdf_use_qrcode']; ?> 
                <a href="#mb_inline_qr" role="button" class="btn pull-right" data-toggle="modal">
                    <img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
                </a>                          
            </td>
        </tr>                                                
    </table>
      
  </div>  

</div>


<input type="hidden" name="ticketid" value="<?php echo $this->data->ticketid; ?>" />
<input type="hidden" name="option" value="com_ticketmaster" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="tickets" />
</form>

<div id="mb_scan_pin" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_PINCODE_TICKET' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_PINCODE_TICKET_EXPLANATION' ); ?>      
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_copy" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_COPY' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_COPY_EXPLANATION' ); ?>      
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_parent" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_PARENT' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_PARENT_EXPLANATION' ); ?>     
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_sale_stop" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_USE_SALE_STOP' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_USE_SALE_STOP_EXPLANATION' ); ?>   
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_file" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_FILE' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_FILE_EXPLANATION' ); ?>   
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_file_jpg" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_FILE' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_JPGFILE_EXPLANATION' ); ?>   
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_qr" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_QR' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_QR_EXPLANATION' ); ?>   
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_pos" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_POS_EXPLANATION_HEADER' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_POS_EXPLANATION' ); ?>   
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_startprice" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_STARTPRICE' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_TICKET_STARTPRICE_EXPLANATION' ); ?>      
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_endprice" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_TICKET_ENDPRICE' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_TICKET_ENDPRICE_EXPLANATION' ); ?>      
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_counter_choice" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_COUNTER_CHOICE' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_COUNTER_CHOICE_EXPLANATION' ); ?>      
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
 
<div id="mb_starting_total_tickets" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_TICKETS_START' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_TOTAL_TICKETS_START_EXPLANATION' ); ?>      
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>


<div id="mb_inline_scans_on" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_SCANNING_ON' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_SCANNING_ON_OFF_DESC' ); ?>      
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>       

<div id="mb_inline_required_name" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_NAMED_TICKET_REQUIRED' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_NAMED_TICKET_REQUIRED_DESC' ); ?>      
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>  
