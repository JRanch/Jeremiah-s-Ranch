<?php 
/**
 * @version		1.0.0 ticketmaster $
 * @package		ticketmaster
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license		GNU/GPL
 * @author		Robert Dam
 * @author mail	info@rd-media.org
 * @website		http://www.rd-media.org
 */

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## Get document type and add it.
$document = &JFactory::getDocument();
$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );


?>

<h1><?php echo $this->item->mailsubject; ?></h1>

<?php echo $this->item->mailbody; ?>

<form action="<?php echo JRoute::_( 'index.php'); ?>" method="post" name="cancelForm">

   <div style="margin-top: 25px; text-align:left; width: 100%;">
        <a class="button small gray" onclick="javascript: document.cancelForm.submit();">
            <span><?php echo JText::_('COM_TICKETMASTER_CANCEL_ORDER'); ?></span>                      
        </a>
    </div>            

<input type="hidden" name="option" value="com_ticketmaster" />
<input type="hidden" name="controller" value="cancel" />
<input type="hidden" name="task" value="remove" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>     