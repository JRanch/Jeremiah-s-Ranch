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


// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$function	= JRequest::getCmd('function', 'jSelectContact');

$document = JFactory::getDocument();
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' );

?>
<font color="#0033CC" face="Arial, Helvetica, sans-serif">
	<h3><?php echo JText::_('COM_TICKETMASTER_SELECT_EVENTLIST'); ?></h3>
</font>
<?php echo JText::_('COM_TICKETMASTER_SELECT_EVENTLIST_DESC'); ?>
<br/><br/>
<form action="<?php echo JRoute::_('index.php?option=com_contact&category=category&layout=modal&tmpl=component');?>" method="post" name="adminForm" id="adminForm">

	<table class="table table-striped">
		<thead>
			<tr>
				<th width="12%" class="title"><div align="center"><?php echo JText::_('COM_TICKETMASTER_CATID'); ?>
				  </div>
				<th width="58%"><div align="left"><?php echo JText::_('COM_TICKETMASTER_CATNAME'); ?></div></th>
				<th width="15%"><?php echo JText::_('COM_TICKETMASTER_LANGUAGE'); ?></div></th>
			  <th width="15%"><?php echo JText::_('COM_TICKETMASTER_PUBLISHED'); ?></div></th>
		  </tr>
		</thead>

		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><div align="center"><?php echo $item->eventid; ?></div></td>
				<td align="center">
					<div align="left"><a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->eventid; ?>', '<?php echo $this->escape(addslashes($item->eventname)); ?>');">
					  <?php echo $this->escape($item->eventname); ?></a> </div></td>
		  		<td class="center">
					<?php if ($item->language=='*'):?>
						<?php echo JText::alt('JALL','language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
					<?php endif;?>				</td>
				<td align="center">
					<?php if ($item->published == 1){ echo JText::_('COM_TICKETMASTER_YES'); }else{ echo JText::_('COM_TICKETMASTER_NO'); } ?>				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<input type="hidden" name="task" value="" />

	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
