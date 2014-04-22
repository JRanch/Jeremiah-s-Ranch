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

## Helper file for what you can do.
require_once JPATH_COMPONENT.'/helpers/ticketmaster.php';
$canDo	= ticketmasterHelper::getActions($empty=0);
$user	= JFactory::getUser();

## Setting the toolbars up here..
$newCat	= ($this->data->emailid < 1);
$text = $newCat ? JText::_( 'COM_TICKETMASTER_ADD' ) : JText::_( 'COM_TICKETMASTER_EDIT' );

JToolBarHelper::title(''.$text.' '.JText::_( 'COM_TICKETMASTER_MTEMPLATE' ).' <em><small>"'.$this->data->mailsubject.'"</small></em>', 'generic.png');

if ($canDo->get('core.admin')) {
	JToolBarHelper::save();
}
if ($this->data->emailid < 1)  {
	## Cancel the operation
	JToolBarHelper::cancel();
} else {
	## For existing items the button is renamed `close`
	JToolBarHelper::cancel( 'cancel', 'Close' );
}

## initialize the editor
$editor = JFactory::getEditor();
JHTML::_('behavior.tooltip', '.hasTip');

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
}	
?>


<form action = "index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid"> 

  <div class="span6">

        <table width="100%" class="table table-striped" name="adminform">
             <tr>
                <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_TEMPLATE_TYPE' ); ?></td>
                <td width="50%"><?php echo $this->lists['template'];  ?>
                </td>
            </tr>
        
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_EMAIL_SUBJECT' ); ?></td>
                <td><input class="text_area" type="text" name="mailsubject" id="mailsubject" size="50" maxlength="50" 
                        value="<?php echo $this->data->mailsubject; ?>" />
                </td>
            </tr>
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_MAIL_SHORT_DESC' ); ?></td>
                <td><input class="text_area" type="text" name="description" id="description" size="50" maxlength="250"
                           value="<?php echo $this->data->description; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_MAIL_REPLYTO_NAME' ); ?></td>
                <td><input class="text_area" type="text" name="reply_to_name" id="reply_to_name" size="50" maxlength="250"
                           value="<?php echo $this->data->reply_to_name; ?>" /></td>
            </tr> 
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_MAIL_REPLYTO_MAIL' ); ?></td>
                <td><input class="text_area" type="text" name="reply_to_email" id="reply_to_email" size="50" maxlength="250"
                           value="<?php echo $this->data->reply_to_email; ?>" /></td>
            </tr>            
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_MAIL_FROMNAME' ); ?></td>
                <td><input class="text_area" type="text" name="from_name" id="from_name" size="50" maxlength="250"
                              value="<?php echo $this->data->from_name; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_MAIL_FROMEMAIL' ); ?></td>
                <td><input class="text_area" type="text" name="from_email" id="from_email" size="50" maxlength="250"
                            value="<?php echo $this->data->from_email; ?>" /></td>
            </tr>          
            <tr>
                <td><?php echo JText::_( 'COM_TICKETMASTER_RECEIVE_BCC' ); ?></td>
                <td><?php echo $this->lists['bcc'];  ?></td>
            </tr>
            <tr>
                <td width="274"><?php echo JText::_( 'COM_TICKETMASTER_PUBLISHED' ); ?></td>
                <td><?php echo $this->lists['published']; ?> </span>
                </td>
            </tr>
        </table>
        
        
        <table class="table table-striped">
            <tr>
                <td valign="top" colspan="3">
                <?php
                ## parameters : areaname, content, width, height, cols, rows
                echo $editor->display( 'mailbody',  $this->data->mailbody , '650', '200', '100', '15' ) ; ?></td>
            </tr>
        </table>

  <div class="span6">
  
  </div>
  
</div>

<input type = "hidden" name="emailid" value="<?php echo $this->data->emailid; ?>" />
<input type = "hidden" name="option" value="com_ticketmaster" />
<input type = "hidden" name="task" value="" />
<input type = "hidden" name="controller" value="mail" />
</form>
		
