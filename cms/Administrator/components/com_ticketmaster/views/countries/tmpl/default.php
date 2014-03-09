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

## no direct access
defined('_JEXEC') or die('Restricted access');

## Setup the toolbars.
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_COUNTRIES' ), 'generic.png' );
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();

## Adding the old $option to the script
$option   = 'com_ticketmaster';
$document = JFactory::getDocument();

## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	include_once $path;
}else{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'menu.php';
	include_once $path;
}

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}else{
	## We are in J3, load the bootstrap!
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' );
	JHtml::_('bootstrap.framework');
	jimport('joomla.html.html.bootstrap');
}

?>

<script>

jQuery.noConflict();
  jQuery(document).ready(function(jQuery) {
		var oldSrc = '<?php echo JURI::root(true); ?>/administrator/templates/isis/images/admin/tick.png';
		var newSrc = '<?php echo JURI::root(true); ?>/administrator/components/com_ticketmaster/assets/images/tick.png';
		jQuery('img[src="' + oldSrc + '"]').attr('src', newSrc);
  });

	
</script>

<form action = "index.php" method="POST" name="adminForm" id="adminForm" >
  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th width="32" height="24"><div align="center">
        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?> ) ;" />
        </div></th>
        <th colspan="2" class="title"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_COUNTRY' ); ?></div></th>
        <th class="title" width="115"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_THREE_DIGIT_CODE' ); ?></div></th>
        <th class="title" width="115"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TWO_DIGIT_CODE' ); ?></div></th>
        <th class="title" width="120"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PUBLISHED' ); ?></div></th>
      </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		## Give give $row the this->item[$i]
		$row        = $this->items[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_('grid.id', $i, $row->country_id );
		$link       = 'index.php?option=' .$option. '&controller=countries&task=edit&cid[]='.$row->country_id;

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td><div align="center"><?php echo $checked; ?></div></td>
      <td colspan="2"><div align="left"><a href="<?php echo $link; ?>"><?php echo $row->country; ?></a></div></td>
      <td width="115"><div align="center"><?php echo $row->country_3_code; ?></div></td>
      <td width="115"><div align="center"><?php echo $row->country_2_code; ?></div></td>
      <td width="120"> <div id = "rds-publish-<?php echo $row->productid; ?>" align="center"><?php echo $published; ?></div></td>
    </tr>
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
  <input name = "task" type="hidden" value="" />
  <input name = "boxchecked" type="hidden" value="0"/>
  <input name = "controller" type="hidden" value="countries"/>
  <input name = "limitstart" type="hidden" value="<?php echo $this->pagination->limitstart; ?>" />
</form>
       