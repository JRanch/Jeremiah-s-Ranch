<?php

## Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

## Helper file for what you can do.
$address = $_SERVER['HTTP_HOST'].JURI::root(true);

require_once JPATH_COMPONENT.'/helpers/ticketmaster.php';
$canDo	= ticketmasterHelper::getActions($empty=0);
$user	= JFactory::getUser();

$app 	  = JFactory::getApplication();
$document = JFactory::getDocument();

$params = JComponentHelper::getParams('com_users');
$useractivation = $params->get('useractivation');
$user_registration = $params->get('allowUserRegistration');

$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	$version = '2.5';
}else{
	$version = '3.0';
}

## Setting the page title.
$document->setTitle( JText::_( 'COM_TICKETMASTER_CP' ) );
$document->addScript('https://code.jquery.com/jquery-latest.js');
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/j3-lightbox/css/colorbox.css' );
$document->addScript(JURI::root(true).'/administrator/components/com_ticketmaster/assets/j3-lightbox/js/jquery.colorbox.js');
$document->addScript(JURI::root(true).'/administrator/components/com_ticketmaster/assets/j3-lightbox/js/colorbox.js');	
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/component_css.css' );

JToolBarHelper::title(JText::_( 'COM_TICKETMASTER_CP' ), 'cpanel.png');

if ($canDo->get('core.admin')) {
	
	if($isJ30) {
		## Toolbar only visible in J3
		JToolBarHelper::preferences('com_ticketmaster');
	}
		
	JToolBarHelper::divider();
}



## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	$ext_installed = '1';
}

$url = 'http://rd-media.org/server.php';

$params = array (
        'api_password' => 'YPOYGT5433VBGQW', //%&JHFSD8s58sdfsdf
        'method' => 'getVersion', 
        'product' => '4',
		'joomla' => $version,
		'serving' => $address,
);

$options = array(
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => FALSE
);

$defaults = array(
        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($params),
        CURLOPT_HEADER => 0,
        CURLOPT_HTTPHEADER => array('Content-type: application/json'),
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 7
);

$ch = curl_init();
curl_setopt_array($ch, ($options + $defaults));

## Doing the CURL request to rd-media.org
$r = '';
if(!$result = curl_exec($ch)) {
    trigger_error(curl_error($ch));
} else{
    $r = curl_exec($ch);
}

curl_close($ch);

## Decode the JSON encoded data
$result = json_decode($r, true);

$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');
$icon['label'] = 'Needs a Value!';

if(!$isJ30) {
	JHTML::_('behavior.mootools');
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
}	

$help_urls = file_get_contents("http://rd-media.org/rdmedia.txt");
$help_url  = json_decode($help_urls, true);


if (!file_exists(JPATH_COMPONENT.'/assets/images/confirmation_logo.jpg')) { ?>
    
    <div class="alert alert-error">
      <strong><?php echo JText::_( 'COM_TICKETMASTER_CP_WARNING' ); ?></strong> <?php echo JText::_( 'COM_TICKETMASTER_CP_NO_LOGO_SET' ); ?> 
      <a target="_blank"  href="index.php?option=com_ticketmaster&controller=configuration"><?php echo JText::_( 'COM_TICKETMASTER_CP_CONFIGURATION' ); ?></a>
    </div>

<?php } 

if (!file_exists(JPATH_COMPONENT.'/assets/images/header.jpg')) { ?>
 	   
 	    <?php  if ($this->config->send_multi_ticket_only == 1 or $this->config->send_multi_ticket_admin == 1){ ?>   
		    <div class="alert alert-error">
		      <button type="button" class="close" data-dismiss="alert">&times;</button>
		      <h4><?php echo JText::_( 'COM_TICKETMASTER_CP_WARNING' ); ?></h4>
		      <?php echo JText::_( 'COM_TICKETMASTER_CP_NO_HEADER_SET' ); ?> 
		      <a target="_blank"  href="<?php echo $help_url['NoHeaderSet']['url']; ?>">
		      	<?php echo JText::_( 'COM_TICKETMASTER_READ_KNOWLEDGEBASE' ); ?>
		      </a>
		    </div>
    	<?php } ?>

<?php } 

if ($useractivation == 1) { ?>
 	     
    <div class="alert">
    	<button type="button" class="close" data-dismiss="alert">&times;</button>
    	<h4><?php echo JText::_( 'COM_TICKETMASTER_CP_POTENTIAL_WARNING' ); ?></h4>
    	<?php echo JText::_( 'COM_TICKETMASTER_CP_USER_ACTIVATION_ON' ); ?>
    	<a target="_blank"  href="<?php echo $help_url['CpanelWarningUsers']['url']; ?>">
    		<?php echo JText::_( 'COM_TICKETMASTER_READ_KNOWLEDGEBASE' ); ?>
    	</a>
    </div>

<?php }elseif($useractivation == 2){ ?>

    <div class="alert">
    	<button type="button" class="close" data-dismiss="alert">&times;</button>
    	<h4><?php echo JText::_( 'COM_TICKETMASTER_CP_POTENTIAL_WARNING' ); ?></h4>
    	<?php echo JText::_( 'COM_TICKETMASTER_CP_ADMIN_ACTIVATION_ON' ); ?>
    	<a target="_blank"  href="<?php echo $help_url['CpanelWarningUsers']['url']; ?>">
    		<?php echo JText::_( 'COM_TICKETMASTER_READ_KNOWLEDGEBASE' ); ?>
    	</a>
    </div>

<?php }elseif($user_registration != 1){ ?>

    <div class="alert alert-error">
    	<button type="button" class="close" data-dismiss="alert">&times;</button>
    	<h4><?php echo JText::_( 'COM_TICKETMASTER_CP_WARNING' ); ?></h4>
    	<?php echo JText::_( 'COM_TICKETMASTER_CP_USER_REGISTRATION_IS_OFF' ); ?>
    	<a target="_blank"  href="<?php echo $help_url['CpanelWarningUsers']['url']; ?>">
    		<?php echo JText::_( 'COM_TICKETMASTER_READ_KNOWLEDGEBASE' ); ?>
    	</a>
    </div>

<?php } ?>

<?php if ($this->config->pro_installed == 1 &&  !file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php') ) { ?>
    
    <div class="alert alert-error">
    	<h4><?php echo JText::_( 'COM_TICKETMASTER_CP_WARNING' ); ?></h4>
    	<?php echo JText::_( 'COM_TICKETMASTER_TICKETMASTER_PRO_TURNED_ON' ); ?> 
    	<a href="index.php?option=com_ticketmaster&controller=cpanel&task=turnOffTicketmasterProSetting" class="btn btn-small btn-danger pull-right" style="margin-top:-10px;">
    		<?php echo JText::_( 'COM_TICKETMASTER_TURN_OFF' ); ?>
    	</a>
    </div>

<?php } ?>

<br/>
<div class="row-fluid">
  <div class="span7">
  
        <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster'); ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/cpanel_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_CPANEL'); ?></span>
        </a>
        </div>
        
        <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=events'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/category_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_CATEGORIES'); ?></span>
        </a></div>     

        <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=tickets'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/tickets_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_EVENT_TICKETS'); ?></span>
        </a></div> 
        
        <?php if ($ext_installed == 1) { ?>
        	<div class="icon">
            <a href="<?php echo 'index.php?option=com_ticketmasterext&amp;controller=tickets'; ?>">
                <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/seatplan_48.png'; ?>"
                border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_SEATPLAN'); ?></span>
            </a></div> 
        
        <?php } ?>

        <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&controller=ticketbox'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/soldtickets_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_SOLDTICKETS'); ?></span>
        </a></div>  

        <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&controller=venues'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/venues_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_VENUES'); ?></span>
        </a></div>  
		
        <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&controller=tickets&task=add'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/newticket_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_ADDTICKET'); ?></span>
        </a></div>                

        
        <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=visitors'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/user_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_CLIENTS'); ?></span>
        </a>
        </div> 
        
        <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=countries'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/globe_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_COUNTRIES'); ?></span>
        </a>
        </div>         

         <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=export'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/export_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_EXPORT_XLS'); ?></span>
        </a></div>  
        
         <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=coupons'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/discount_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_COUPONS'); ?></span>
        </a></div>          
        
         <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=transactions'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/money_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_TRANSACTION'); ?></span>
        </a></div>    

         <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=scans'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/scanning-icon-48x48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_SCANS'); ?></span>
        </a></div>        
        
         <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=waitinglist'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/waiting-48x48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_WAITINGLIST'); ?></span>
        </a></div>                
		
        <?php if ($canDo->get('core.admin')) { ?>

        <div class="icon">
        <a href="<?php echo 'index.php?option='.
			JRequest::getCmd('option','com_ticketmaster').'&amp;controller=mail'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/email_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_EMAILS'); ?></span>
        </a></div>
    
        <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=configuration'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/config_48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_CONFIG'); ?></span>
        </a></div>
        
        <div class="icon">
        <a href="<?php echo 'index.php?option='.JRequest::getCmd('option','com_ticketmaster').'&amp;controller=configuration&task=dbcheck'; ?>">
            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/database48x48.png'; ?>"
            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_DB_CHECK'); ?></span>
        </a></div>        
        
        <?php if(!$isJ30) { ?>
	        <div class="icon">
	        <a class="iframe" href="index.php?option=com_config&amp;view=component&amp;component=com_ticketmaster&amp;path=&amp;tmpl=component">
	            <img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/parameters_48.png'; ?>"
	            border="0" alt="<?php echo $icon['label']; ?>" /> <span><?php echo JText::_('COM_TICKETMASTER_PARAMETERS'); ?></span>
	        </a></div>       
        <?php } ?>
        
        <?php } ?>
  
  
  </div>
  <div class="span5">

    <table class="table table-striped">           
            <tr>
                <td width="30%"><div align="left"><?php echo JTEXT::_('COM_TICKETMASTER_RELEASEDATE'); ?></div> </td>
                <td width="70%"><?php echo $this->data['creationDate']; ?></td>
            </tr> 			
            <tr>
                <td width="20%"><div align="left"><?php echo JTEXT::_('COM_TICKETMASTER_CVERSION'); ?></div></td>
                <td>RD-Ticketmaster Version <?php echo $this->data['version']; ?></td>
            </tr>
            <?php if ($result['version'] != $this->data['version']) { ?>
            <tr>
                <td width="20%"><div align="left"><font color="#00CC00"><strong><?php echo JTEXT::_('Now available'); ?></strong></font></div></td>
                <td><strong><font color="#00CC00">RD-Ticketmaster Version <?php echo $result['version']; ?> is available!</font></strong></td>
        </tr>
            <?php } ?>            
        <tr>
                <td><div align="left"><?php echo JTEXT::_('COM_TICKETMASTER_SUPPORTPAGES'); ?></div></td>
                <td><a href="http://www.rd-media.org/" target="_blank"><?php echo JTEXT::_('COM_TICKETMASTER_CLICK_4_SUPPORT'); ?></a></td>
        </tr>  
        <tr>
                <td><div align="left"><?php echo JTEXT::_('COM_TICKETMASTER_DEVELOPMENT'); ?></div></td>
                <td><a href="http://www.rd-media.org/" target="_blank">Robert Dam - Netherlands &copy;</a></td>
        </tr>
        <tr>
            <td><div align="left"><?php echo JTEXT::_('COM_TICKETMASTER_LICENCE'); ?></div></td>
            <td><a href="http://rd-media.org/licences-gnu-gpl.html" target="_blank">GNU GENERAL PUBLIC LICENSE V3</a></td>
        </tr>
        <tr>
            <td><div align="left"><?php echo JTEXT::_('COM_TICKETMASTER_API_CODE'); ?></div></td>
            <td><?php echo $this->config->scan_api; ?></td>
        </tr> 
        <tr>
            <td><div align="left"><?php echo JTEXT::_('COM_TICKETMASTER_FOLLOW_US_NOW'); ?></div></td>
            <td>
				<a href="https://twitter.com/rdmedia_org" target="_blank">
                	<img src="<?php echo JURI::base().'components/com_ticketmaster/assets/icons/twitter-icon.png'; ?>" />           
                </a>
				<a href="https://www.facebook.com/rdmediadotorg" target="_blank">
                	<img src="<?php echo JURI::base().'components/com_ticketmaster/assets/icons/facebook-icon.png'; ?>" />           
                </a>                 
            </td>
        </tr>                              
           <tr>
            <td colspan="2">               
                
				<?php if ( function_exists("curl_version") == "Enabled" ){ ?>
                    <?php if ($result['version'] == $this->data['version']) { ?>
                          <a href="http://rd-media.org" class="btn btn-large btn-block" target="_blank">
                            <font color="#009900"><?php echo JTEXT::_('COM_TICKETMASTER_VERSION_OK').' ('.$result['version'].')'; ?></font>
                          </a>
                    <?php }else{ ?>
                          <a href="http://rd-media.org" class="btn btn-large btn-block btn-danger" target="_blank">
                            <?php echo JTEXT::_('COM_TICKETMASTER_NEW_VERSION_AVAILABLE').' ('.$result['version'].')'; ?>
                          </a>                       
                    <?php } ?>
                 <?php }else{ ?>   
                      <?php echo JTEXT::_('COM_TICKETMASTER_CURL_NOT_ENABLED').' ('.$result['version'].')'; ?>             
                 <?php } ?> 
     
                  <a href="http://bit.ly/13vajQP" class="btn btn-large btn-block" target="_blank">
                    Please, review Ticketmaster at the Joomla JED!
                  </a>                                   
                
            </td>
          </tr>                                                           
    </table>

  
  </div>
</div>


<div id="cpanel" style="width: 100%;">

    
    <div style="float:left; width:60%;">
    	

    </div>  

	<div style="float:right; width:40%;">  
    

    

  </div>     
    
</div>



<div style="clear: both;"></div>

