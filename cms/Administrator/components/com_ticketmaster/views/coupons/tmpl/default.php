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

$document = & JFactory::getDocument();

## Setup the toolbars.
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_COUPONS' ), 'generic.png' );
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::editList();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();

## Adding the old $option to the script
$option = 'com_rdsubs';

## Get document type and add it.
$document  = JFactory::getDocument();
$cssfile   = '../administrator/components/com_rdsubs/assets/css/backend.css';
$document->addStyleSheet( $cssfile );

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

## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	include_once $path;
}else{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'menu.php';
	include_once $path;
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

  <form action = "index.php" method="POST" name="adminForm" id="adminForm">

    <table class="table table-striped" width="100%">
      <thead>
        <tr>
          <th width="34" height="24"><div align="center"></div></th>
          <th colspan="2" class="title"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_COUPON_NAME' ); ?></div></th>
          <th class="title" width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_COUPON_CODE' ); ?></div></th>
		  <th class="title" width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_COUPON_ADDED' ); ?></div></th>
          <th class="title" width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_COUPON_EXPIRATION' ); ?></div></th>
          <th class="title" width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_LIMITATION' ); ?></div></th>
          <th class="title" width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_DISCOUNT' ); ?></div></th>
          <th class="title" width="85"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_SYSTEM_TYPE' ); ?></div></th>
          <th class="title" width="86"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PUBLISHED' ); ?></div></th>
        </tr>
      </thead>
      <?php 
           
           $k = 0;
           for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
            
            ## Give give $row the this->item[$i]
            $row        = &$this->items[$i];
            $published 	= JHTML::_('grid.published', $row, $i );
            $checked    = JHTML::_('grid.id', $i, $row->coupon_id );
    
        ?>
          <tr class="<?php echo "row$k"; ?>">
            <td><div align="center"><?php echo $checked; ?></div></td>
            <td colspan="2">
                 <?php echo $row->coupon_name; ?>            
            </td>
            <td width="100">
              <div align="center"><?php echo $row->coupon_code; ?></div></td>            
			<td width="100">
<div align="center">
                	<?php echo date ($this->config->dateformat, strtotime($row->coupon_added)); ?>
              </div>
            </td>
            <td width="100">
<div align="center">
               		<?php echo date ($this->config->dateformat, strtotime($row->coupon_valid_to)); ?>               
                </div>
            </td>
            <td width="100">
<div align="center">
                	<?php if ($row->coupon_limit == 0) { echo JText::_( 'COM_TICKETMASTER_NO' ); }else{ echo $row->coupon_used.' / '.$row->coupon_limit; } ?>               
                </div>             
            </td>
            <td width="100">
			  <div align="center">
                  <?php if ($row->coupon_type == 1){ 
                        echo $row->coupon_discount.'%'; 
                  }else{ 
					   echo $this->config->valuta; ?> <?php echo number_format($row->coupon_discount, 2, ',', '');              
                  } ?>                    
             </div>
          </td>
          <td width="85">
   	 		 <div align="center">
                  <?php if ($row->type == 0){ 
                        echo JText::_( 'COM_TICKETMASTER_MANUAL' );
                  }else{ 
                       echo JText::_( 'COM_TICKETMASTER_SYSTEM' );                
                  } ?>               
              </div>            
          </td>
          <td width="86">
   	 		 <div align="center">
					<?php echo $published; ?>                
             </div>            
          </td>            
      </tr>
      <?php
          $k=1 - $k;
          }
          ?>
    </table>  

      <input name = "option" type="hidden" value="com_ticketmaster" />
      <input name = "task" type="hidden" value="" />
      <input name = "boxchecked" type="hidden" value="0"/>
      <input name = "controller" type="hidden" value="coupons"/>
      <input name = "limitstart" type="hidden" value="<?php echo $this->pagination->limitstart; ?>" />
  </form>
    
    <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1">
        <tr>
            <td>
                <div align="center"><?php echo $this->pagination->getPagesLinks(); ?></div>
            </td>
        </tr>
    </table>           

