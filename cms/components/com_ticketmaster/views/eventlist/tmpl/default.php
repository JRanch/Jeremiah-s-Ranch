<?php 
/************************************************************
 * @version			ticketmaster 3.0.1
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

$app 	 = JFactory::getApplication();
$pathway = $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_INFORMATION' ), 'index.php?option=com_ticketmaster');
$pathway->addItem($this->data->eventname, 'index.php?option=com_ticketmaster');

## Get document type and add it.
$document = JFactory::getDocument();

$cssfile = 'components/com_ticketmaster/assets/css-overrides/eventlist.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/eventlist.css' );
}

$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );

include_once( 'components/com_ticketmaster/assets/functions.php' );

jimport( 'joomla.filter.output' );

## Getting the parameters for this view:
$params = JComponentHelper::getParams('com_ticketmaster');
$loadview = $params->get('eventlist_view', '0');
$button_color = $params->get('button_color', 'btn');
$filter_on = $params->get('eventlist_showfilter', '1');
$show_venue = $params->get('eventlist_showvenue', '1');

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	
	if($this->config->load_bootstrap == 1){
		## Adding mootools for J!2.5
		JHTML::_( 'behavior.mootools' );		
		## Include the tooltip behaviour.
		JHTML::_('behavior.tooltip', '.hasTip');
		
		if ($this->config->load_jquery == 1) {
			$document->addScript('http://code.jquery.com/jquery-latest.js');
		}elseif ($this->config->load_jquery == 2) {
			$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/jquery/jquery.js');
		}
		
		
		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
		$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
		$button = 'btn btn-small '.$button_color;
		
	}else{	

		$document->addStyleSheet( 'components/com_ticketmaster/assets/css/bootstrap.css' );
		$button = 'button_rdticketmaster';
	}
}else{
	
	## We are in J3, load the bootstrap!
	jimport('joomla.html.html.bootstrap');
	$button = 'btn btn-small';
	
}

## Cleaning the %%READMORE%% if needed:
$description = str_replace('%%READMORE%%', "", $this->data->eventdescription);

?>

<script language="javascript" type="text/javascript">
function tableOrdering( order, dir, task )
{
        var form = document.adminForm;
 
        form.filter_order.value = order;
        form.filter_order_Dir.value = dir;
        document.adminForm.submit( task );
}
</script>

<form id="adminForm" action="<?php echo $this->action ;?>" method="post" name="adminForm" class="form-inline">

<h2><?php echo JText::_('COM_TICKETMASTER_INFORMATION').' '.$this->data->eventname; ?></h2>

<div id = "ticketmaster-categories" style="margin-bottom: 15px;">
    
	<?php echo $description; ?>
    
    <?php if ($filter_on == 1) { ?>
    	<div align="right"><?php echo JText::_('COM_TIKETMASTER_FILTER');?><?php echo $this->lists['ordering']; ?></div>
    <?php } ?>
    
</div>  

<?php if ($loadview == 1) { ?>

		<?php 
        $k = 0;
        for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
            
                ## Give give $row the this->item[$i]
                $row        = &$this->items[$i];
                $published 	= JHTML::_('grid.published', $row, $i );
                $checked    = JHTML::_('grid.id', $i, $row->ticketid );
                $alias 		= JFilterOutput::stringURLSafe($this->data->eventname.'-'.$row->ticketname);
                $link 		= JRoute::_('index.php?option=' . $option . '&view=event&id='.$row->ticketid.':'.$alias);

				$alias 		= JFilterOutput::stringURLSafe($row->venuename);
				$venue 		= JRoute::_('index.php?option=' . $option . '&view=venue&id='.$row->venueid.':'.$alias);
				
                $item->odd	= $k;
        
        ?>
        
        <div class="ticktmaster_box_heading">
            <div style="float:left;">
                <a href="<?php echo $link; ?>"><?php echo $row->ticketname; ?></a>
            </div>
            <div style="float:right; color:#FFF;">
                <?php echo date ($this->config->dateformat, strtotime($row->ticketdate)); ?>
                <?php if ( $row->show_end_date == 1 ){?>
                 - <?php echo date ($this->config->dateformat, strtotime($row->end_date)); ?>
                <?php } ?>                
            </div>
        </div>
        
        <div class="ticktmaster_box_content">
            <?php echo $row->ticketdescription; ?>
         
             <div class="ticktmaster_box_content_footer">
                
                <a href="<?php echo $link; ?>" class="<?php echo $button; ?> pull-right"><?php echo JText::_('COM_TICKETMASTER_SHOW_NOW'); ?></a>
        
                <?php if ($row->start_price > 0 && $row->end_price > 0) { ?>
                    <span class="label label-info pull-right" style="padding: 5px; margin-right: 5px;">
                        <?php echo JText::_( 'COM_TICKETMASTER_PRICE_RANGE' ); ?> 
                            <?php echo showprice($this->config->priceformat ,$row->start_price,$this->config->valuta); ?> - 
                            <?php echo showprice($this->config->priceformat ,$row->end_price,$this->config->valuta); ?>
                    </span>
                <?php } ?>
                
                <?php if ($row->start_price > 0 && $row->end_price == 0) { ?>
                    <span class="label label-info pull-right" style="padding: 5px; margin-right: 5px;">
                        <?php echo JText::_( 'COM_TICKETMASTER_PRICE_STARTS_AT' ); ?> 
                            <?php echo showprice($this->config->priceformat ,$row->start_price,$this->config->valuta); ?>
                    </span>
                <?php } ?>        
                
             	<a class="<?php echo $button; ?> pull-right" style="margin-right: 5px;" 
            	   href="<?php echo $venue; ?>"><?php echo JText::_('COM_TICKETMASTER_MORE_EVENTS_AT_VENUE'); ?>  </a>               
            
            </div>

            
        </div>        
        
        <?php
          $k=1 - $k;
          }
        ?>         

<?php }else{ ?>
 
    <table class="table">
        
        <thead>
            <th width="50%"><?php echo JText::_( 'COM_TICKETMASTER_TM_EVENTS' ); ?></th>
            <th width="15%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_DATE' ); ?></div></th>
            <th width="15%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PRICE' ); ?></div></th>
            <th width="20%"></th>
        </thead>
    
        <?php 
        $k = 0;
        for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
            
                ## Give give $row the this->item[$i]
                $row        = &$this->items[$i];
                $published 	= JHTML::_('grid.published', $row, $i );
                $checked    = JHTML::_('grid.id', $i, $row->ticketid );
                $alias 		= JFilterOutput::stringURLSafe($this->data->eventname.'-'.$row->ticketname);
                $link 		= JRoute::_('index.php?option=' . $option . '&view=event&id='.$row->ticketid.':'.$alias);
                $item->odd	= $k;
        
        ?>
        
        <tr>
            <td><a href="<?php echo $link; ?>"><?php echo $row->ticketname; ?></a></td>
            <td><div align="center"><?php echo date ($this->config->dateformat, strtotime($row->ticketdate)); ?>
                <?php if ( $row->show_end_date == 1 ){?>
                  - <?php echo date ($this->config->dateformat, strtotime($row->end_date)); ?>
                <?php } ?>
            </div></td>
            <td><div align="center"><?php echo showprice($this->config->priceformat ,$row->ticketprice,$this->config->valuta); ?></div></td>
            <td><div align="center"><a class="<?php echo $button; ?>" href="<?php echo $link; ?>"><?php echo JText::_('COM_TICKETMASTER_SHOW_NOW'); ?></a></div>
            	</td>
        </tr>
    
        <?php
          $k=1 - $k;
          }
        ?>           
    
    </table>

<?php } ?>

<input type = "hidden" name="ordering" value="<?php echo $this->ordering ;?>" />
<input type = "hidden" name="filter_order_Dir" value="" />

</form>