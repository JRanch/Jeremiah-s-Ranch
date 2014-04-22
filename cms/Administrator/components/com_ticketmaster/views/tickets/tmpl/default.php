<?php
/****************************************************************
 * @version				Ticketmaster 3.0.2						
 * @package				ticketmaster								
 * @copyright           Copyright © 2009 - All rights reserved.			
 * @license				GNU/GPL										
 * @author				Robert Dam									
 * @author mail         info@rd-media.org								
 * @website				http://www.rd-media.org						
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

## Add the CSS file for extra toolbars.
$document = & JFactory::getDocument();
$document->addStyleSheet('components/com_ticketmaster/assets/component_css.css');

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	$cleanup_button = 'cleanup';
	$sync_button 	= 'sync';
}else{
	$cleanup_button = 'trash';
	$sync_button 	= 'star';
}

## Setup the toolbars.
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_TICKETS_OVERVIEW' ), 'generic.png' );
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolbarHelper::custom( 'cleanuptickets', $cleanup_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_CLEANUP' ), false, false);
JToolbarHelper::custom( 'noactivation', $sync_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_SYNC' ), false, false);
JToolBarHelper::deleteList();

## Adding the old $option to the script
$option = 'com_ticketmaster';

## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	include_once $path;
}else{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'menu.php';
	include_once $path;
}

$document = JFactory::getDocument();
$document->addScript('https://code.jquery.com/jquery-latest.js');
$document->addStyleSheet('https://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css');	

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	$document = JFactory::getDocument();
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}else{
	JHtml::_('bootstrap.framework');
	jimport('joomla.html.html.bootstrap');	
}
?>
<script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script language="javascript">

var jQuery = jQuery.noConflict();

jQuery(document).ready(function() {
  
	jQuery('#ordering-button').click(function() {
	    jQuery("form input:checkbox").attr ( "checked" , true );
		jQuery("#task").val("saveorder");
	    jQuery("#adminForm").submit();
	});
	  
});

jQuery(function() {
    jQuery( document ).tooltip();
  });

</script>

<form action = "index.php" method="POST" name="adminForm" id="adminForm" class="form-inline">
<table class="table table-striped">
<tr>
	<td align="right" width="100%">
		<button onclick="this.form.submit();" class="btn pull-right"><?php echo JText::_( 'COM_TICKETMASTER_SEARCH' ); ?></button>
        <?php echo $this->lists['eventid']; ?>
	</td>
</tr>
</table>

  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th width="20" height="24"><div align="center"></div></th>
        <th class="title" width="388" colspan="2"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_TICKETNAME' ); ?></div></th>
        <th class="title" width="76"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PRICE' ); ?></div></th>
        <th class="title" width="200"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_EVENT_LOCATION' ); ?></div></th>
        <th class="title" width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_DATE' ); ?></div></th>
        <th class="title" width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_START_EVENT' ); ?></div></th>
        <th class="title" width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_SALESTOP' ); ?></div></th>
        <th class="title" width="100">
        	<div align="center">
        		<a href="javascript:void(0)" title="<?php echo JText::_( 'COM_TICKETMASTER_AVAILABLE_INFO' ); ?>"><?php echo JText::_( 'COM_TICKETMASTER_AVAILABLE' ); ?></a>
        	</div>
        </th>
        <th class="title" width="100"><div align="center">
        	<button class="btn btn-mini" id="ordering-button" data-toggle="popover" data-placement="top" 
            	data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." type="button"><i class="icon-star"></i> 
				<?php echo JText::_( 'COM_TICKETMASTER_SAVE_ORDERING_NEW' ); ?></button>
         </div></th>
        <th class="title" width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PUBLISHED' ); ?></div></th>
      </tr>
    </thead>
    
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		## Give give $row the this->item[$i]
		$row        = &$this->items[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_('grid.id', $i, $row->ticketid );
		$link       = 'index.php?option=' .$option. '&controller=tickets&task=edit&cid[]='.$row->ticketid;
		$charts     = 'index.php?option=com_ticketmasterext&controller=tickets&task=edit&tmpl=component&cid[]='.$row->ticketid;

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td><div align="center"><?php echo $checked; ?></div></td>
      <td colspan="2"><div align="left"><strong><?php echo $row->eventname; ?></strong><br />
	  <a href="<?php echo $link;?>"> <?php echo $row->ticketname; ?></a></div></td>
      <td>
      	<div align="center"><?php echo $this->config->valuta; ?> 
			  <?php echo number_format($row->ticketprice, 2, ',', ''); ?>
              <?php if ($row->show_seatplans == 1) { ?>
              <a href="<?php echo $charts; ?>"
              title="<?php echo $row->ticketname; ?>" class="btn btn-small"><?php echo JText::_( 'COM_TICKETMASTER_SEATCHARTS' ); ?></a> 
              <?php } ?>
         </div>
      </td>
      <td><div align="left"><strong><?php echo $row->venue; ?> - <?php echo $row->city; ?></strong><br />
            <?php echo JText::_( 'COM_TICKETMASTER_CODE' ); ?> (<?php echo $row->eventcode; ?>)</em></small
	  ></div></td>
      <td><div align="center"><?php echo date ("d-m-Y", strtotime($row->ticketdate)); ?>
      <?php if ($row->show_end_date == 1) {
		echo '<br/>'.date ("d-m-Y", strtotime($row->end_date)); 
      } ?>
      </div></td>
      <td><div align="center"><?php echo $row->starttime; ?></div></td>
      <td><div align="center">
	  	<?php  if ($row->use_sale_stop == 1){ ?>
				   <span class="label label-success"><?php echo JText::_( 'COM_TICKETMASTER_YES' ); ?></span>
			   <?php }else{ ?>
				   <span class="label label-important"><?php echo JText::_( 'COM_TICKETMASTER_NO' ); ?></span>
			  <?php } ?>
          </div>
      </td>
      <td>
      	<?php if ($row->totaltickets < 25) { ?>
			<div align="center"><span class="badge badge-important"><?php echo $row->totaltickets; ?> / <?php echo $row->starting_total_tickets; ?></span></div>
        <?php } else { ?>
      		<div align="center"><span class="badge badge-info"><?php echo $row->totaltickets; ?> / <?php echo $row->starting_total_tickets; ?></span></div>
        <?php } ?>    
      </td>
      <td width="117"><div align="center">
              <input name="order[]" type="text" value="<?php echo $row->ordering; ?>" 
              	size="1" class="input-mini" style="text-align:center; width:25px;"/>       
      </div></td>
      <td width="122">
<div align="center"><?php 
			  if ($row->published == 1){ ?>
				   <span class="label label-success"><?php echo JText::_( 'COM_TICKETMASTER_YES' ); ?></span>
			   <?php }else{ ?>
				   <span class="label label-important"><?php echo JText::_( 'COM_TICKETMASTER_NO' ); ?></span>
			  <?php } ?>
          </div>      </td>
    </tr>
    
   			<?php

               $k2 = 0;
               for ($i2 = 0, $n2 = count($this->childs); $i2 < $n2; $i2++ ){
                
                ## Give give $row the this->item[$i]
                $second     = &$this->childs[$i2];
                $publishing = JHTML::_('grid.published', $second, $i2 );
                $checking   = JHTML::_('grid.id', $i2, $second->ticketid );
                $link       = 'index.php?option=' .$option. '&controller=tickets&task=edit&cid[]='.$second->ticketid;
        
            ?>
            <?php if ($row->ticketid == $second->parent) { ?>
            
            <tr class="<?php echo "row$k2"; ?>">
              <td valign="middle"><div align="center">

              </div></td>
              <td width="20" valign="middle"><div align="center"><?php echo $checking; ?></div></td>
              <td width="230" valign="middle">
              	<strong><?php echo $row->ticketname; ?></strong><br />
                <a href="<?php echo $link;?>"><?php echo $second->ticketname; ?></a>              </td>
          <td valign="middle"><div align="center"><?php echo $this->config->valuta; ?> 
			  		<?php echo number_format($second->ticketprice, 2, ',', ''); ?></div></td>
              <td valign="middle"><div align="left"><?php echo $second->location; ?></div></td>
              <td valign="middle"><div align="center"><?php echo date ("d-m-Y", strtotime($second->ticketdate)); ?></div></td>
              <td valign="middle"><div align="center"><?php echo $second->starttime; ?></div></td>
              <td valign="middle"><div align="center"><?php echo $second->groupname; ?></div></td>        
              <td valign="middle"><div align="center"><span class="badge"><?php echo $second->totaltickets; ?> / <?php echo $second->starting_total_tickets; ?></span></div></td>
              <td width="117" valign="middle"><div align="center">
              <input name="order[]" type="text" value="<?php echo $second->ordering; ?>" 
              	size="1" class="input-mini" style="text-align:center; width:25px;"/>              
              </div></td>
              <td width="122" valign="middle">
      <div align="center">
			  <?php if ($second->published == 1){ ?>
				   <span class="label label-success"><?php echo JText::_( 'COM_TICKETMASTER_YES' ); ?></span>
			   <?php }else{ ?>
				   <span class="label label-important"><?php echo JText::_( 'COM_TICKETMASTER_NO' ); ?></span>
			  <?php } ?>
                    </div>                </td>
    </tr>
            
            <?php } ?>
            
            <?php
              $k=1 - $k;
              }
            ?>    
    
    
    <?php
	  $k=1 - $k;
	  }
	  ?>
  </table>  
  
  <table width="100%" align="center" class="adminlist">
    <tfoot>
        <tr>
            <td colspan="7"><div align="center"><?php echo $this->pagination->getListFooter(); ?></div></td>
        </tr>  
    </tfoot>   
    </table>    
  
  <input name = "option" type="hidden" value="com_ticketmaster" />
  <input name = "task" id="task" type="hidden" value="" />
  <input name = "boxchecked" type="hidden" value="0"/>
  <input name = "controller" type="hidden" value="tickets"/>
  <input type="hidden" name="filter_order" value="ordering" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />  
  <?php echo JHTML::_( 'form.token' ); ?>  
  

</form>
        