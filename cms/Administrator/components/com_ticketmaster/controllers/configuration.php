<?php
/****************************************************************
 * @version				2.5.5 ticketmaster 						
 * @package				ticketmaster								
 * @copyright           Copyright © 2009 - All rights reserved.			
 * @license				GNU/GPL										
 * @author				Robert Dam									
 * @author mail         info@rd-media.org							
 * @website				http://www.rd-media.org						
 ***************************************************************/

defined('_JEXEC') or die ('No Acces to this file!');

jimport('joomla.application.component.controller');

## This Class contains all data for the car configuration
class TicketmasterControllerConfiguration extends JControllerLegacy {

   function display() {
	  
	 JRequest::setVar( 'layout', 'default'  );
     JRequest::setVar( 'view'  , 'configuration');
	 JRequest::setVar( 'edit', true );
	
	## Let's go to the view part.
     parent::display();
	  	  
   }
   
	function dbcheck() {
	
		JRequest::setVar( 'layout', 'dbcheck');
		JRequest::setVar( 'view', 'configuration');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}	 

	function save() {

		$post	= JRequest::get('post');

		$post['valuta'] = JRequest::getVar('valuta', '', 'POST', 'string', JREQUEST_ALLOWRAW);
		
		## import the file system.
		jimport('joomla.filesystem.file');
		
		## Get the logo that could be uploaded.
		$company_logo 	= JRequest::getVar( 'company_logo', '', 'files', 'array' );	
		
		## Ready for uploading image 1
		if (isset( $_FILES['company_logo']) and !$_FILES['company_logo']['error'] ) {

			## Make the filename safe and check if image ext is allowed
			$file 			= $_FILES['company_logo'];
			$file['name']	= JFile::makeSafe($file['name']);
			$allowed        = array('image/pjpeg','image/jpeg','image/JPG','image/jpg');	
			$path 			= JPATH_COMPONENT.DS.'assets'.DS.'images'.DS;
			$link           = 'index.php?option=com_ticketmaster&controller=configuraion';
			## maximum size for logo now 4 Meg
			$maxsize        = 1000000;
			
			## Check image size, if image is to big? Notice client.
			if ( $file['size'] > $maxsize) {
				JError::raiseWarning(100, ''.$file['name'].' '.JText::_( 'COM_TICKETMASTER_MAX_UPLOAD_1_MB' ).'');
				$this->setRedirect($link);
			}
			## Check if the image is in the of the supported extentions
			if (!in_array($file['type'], $allowed)){
				JError::raiseWarning(100, JText::_( 'COM_TICKETMASTER_UPLAODED_LOGO' ).': '.$file['name'].' '.
											JText::_( 'COM_TICKETMASTER_LOGO_ONLY_JPG' ).'');
				$this->setRedirect($link);
			}	
			
			## Gettin gthe new image name (allways confirmation_logo.jpg)
			$newimagename  = 'confirmation_logo.jpg';	
			
			chmod ( $file['tmp_name'], 0644);
			## Moving the file now to the destination folder (images), offcourse with the new name.
			if (!JFile::upload($file['tmp_name'], $path.$newimagename)) {
				JError::raiseWarning(100, ''.$file['name'].' '.JText::_( 'COM_TICKETMASTER_COULDNOT_MOVE_FILE' ).'');
				$this->setRedirect($link);
			} 					
		
		}			

		$model	= $this->getModel('configuration');

		if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_CONFIG_SAVED' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_CONFIG_NOTSAVED' );
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=configuration';

		$this->setRedirect($link, $msg);
	}


}	
?>
