<?php 
/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## Add the tooltip behaviour.
JHTML::_('behavior.tooltip');

$session =& JFactory::getSession();
## Gettig the orderid if there is one.
$redirect = $session->get('redirect');

## Check if the user is logged in.
$user = & JFactory::getUser();

## Adding the AJAX part
JHTML::script('ajax.js','components/com_ticketmaster/assets/', true);

$app = JFactory::getApplication();
$pathway = $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_PROFILE' ), 'index.php?option=com_ticketmaster&view=profile');
$pathway->addItem(JText::_( 'COM_TICKETMASTER_REVIEW_PROFILE' ));

## Get document type and add it.
$document = JFactory::getDocument();

$cssfile = 'components/com_ticketmaster/assets/css-overrides/checkout.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/checkout.css' );
}

$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );
$document->setTitle( JText::_( 'COM_TICKETMASTER_REVIEW_PROFILE' ) );
$document->addScript( JURI::root(true).'/components/com_ticketmaster/assets/javascripts/moovalid.js');
$document->addScript('/jquery/jquery-1.9.0.min.js');

if ($this->data->clientid != ''){
	$update = JText::_( 'COM_TICKETMASTER_REVIEW_PROFILE' );
	$formtext = JText::_( 'COM_TICKETMASTER_REVIEW_PROFILE' );
	$button = JText::_( 'COM_TICKETMASTER_REVIEW_PROFILE_CONTINUE' );
	$clientupdate = 1;
}else{
	$update = JText::_( 'COM_TICKETMASTER_MISSING_PROFILE' );
	$formtext = JText::_( 'COM_TICKETMASTER_MISSING_PROFILE' );
	$button = JText::_( 'COM_TICKETMASTER_CREATE_MY__PROFILE' );
	$clientupdate = 0;
}

if($this->config->load_bootstrap == 1){
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addScript('/jquery/jquery-1.9.0.min.js');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
	$button = 'btn btn-small';
}else{	
	$document->addStyleSheet( 'components/com_ticketmaster/assets/css/bootstrap.css' );
	$button = 'button_rdticketmaster';
}
?>


<script language="javascript">

	window.addEvent('domready', function() {
					var fields = {
							name:         		'Required [5-65]',
							address:			'Required [5-65]',	
							zipcode:			'Required [0-10]',
							city:				'Required [2-65]',
							phone:				'Required [5-15]',
							username:			'Required [3-15]',
							email:				'Email',
					};
					var val = new validate('general', fields, { 
							useAjaxSubmit:false,
							AjaxSubmitOptions: {
									evalScripts: true,
	
									onComplete: function(response) { 
											$('log').set('html',response);
									}
							}
					});
	});

</script>

<h2><?php echo $formtext; ?></h2>	

<?php echo JText::_('COM_TICKETMASTER_CHECK_PROFILE_DESC'); ?>

<form id="general" action="index.php" method="post">

        <div style="width:75%; margin-top:20px;">
            <table class="table table-striped">               
                    
                <tr>
                    <td width="40%"><?php echo JText::_( 'COM_TICKETMASTER_YOUR_GENDER' ); ?>*</td>
                    <td width="60%"><?php echo $this->lists['gender']; ?></td>
                </tr>  
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_NAME' ); ?>*</td>
                    <td><input name="name" type="text" id="name" class="inputbox" value="<?php echo $this->data->name; ?>" size="25" /></td>
                </tr> 
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_ADDRESS' ); ?>*</td>
                    <td><input name="address" type="text" id="address" class="inputbox" value="<?php echo $this->data->address; ?>" size="25" /></td>
                </tr>   
                <?php if($this->config->show_secondaddress != 0 ){ ?>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_ADDRESS' ); ?></td>
                    <td><input name="address2" type="text" id="address2" class="inputbox" value="<?php echo $this->data->address2; ?>" size="25" /></td>
                </tr>
                <?php } ?> 
                <?php if($this->config->show_thirdaddress != 0 ){ ?>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_ADDRESS' ); ?></td>
                    <td><input name="address3" type="text" id="address3" class="inputbox" value="<?php echo $this->data->address3; ?>" size="25" /></td>
                </tr>
                <?php } ?>  
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_ZIPCODE' ); ?>*</td>
                    <td><input name="zipcode" type="text" id="zipcode" class="inputbox" value="<?php echo $this->data->zipcode; ?>" size="25" /></td>
                </tr>  
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_CITY' ); ?>*</td>
                    <td><input name="city" type="text" id="city" class="inputbox" value="<?php echo $this->data->city; ?>" size="25" /></td>
                </tr>
                <?php if($this->config->show_country != 0 ){ ?>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_COUNTRY' ); ?></td>
                    <td><?php echo $this->lists['country']; ?></td>
                </tr>                
                <?php } ?> 
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_PHONE' ); ?></td>
                    <td><input name="phonenumber" type="text" id="phonenumber" class="inputbox" value="<?php echo $this->data->phonenumber; ?>" size="25" /></td>
                </tr>
                <?php if($this->config->show_birthday != 0 ){ ?>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_BIRTHDAY' ); ?></td>
                    <td><?php echo $this->lists['day']; ?>&nbsp;<?php echo $this->lists['month']; ?>&nbsp;<?php echo $this->lists['year']; ?></td>
                </tr>
                <?php } ?>                
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_EMAIL' ); ?>*</td>
                    <td><input name="emailaddress" type="text" id="emailaddress" class="inputbox" value="<?php echo $this->data->emailaddress; ?>" size="25" /></td>
                </tr>                
                <tr>
                    <td></td>
                    <td><input type="submit" value="<?php echo JText::_('COM_TICKETMASTER_PLEASE_PROCESS_ORDER'); ?>" class="button_rdticketmaster"></td>
                </tr>
            </table>

        </div>

<div style="clear:both"></div>

<input type="hidden" name="option" value="com_ticketmaster" />
<input type="hidden" name="controller" value="profile" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="userid" value="<?php echo $user->id; ?>" />
<input type="hidden" name="gid" value="0" /> 
<?php if ($clientupdate == 1){ ?>
    <input type="hidden" name="clientid" value="<?php echo $this->data->clientid; ?>" />
<?php } ?> 
<?php echo JHTML::_( 'form.token' ); ?>        
</form> 
