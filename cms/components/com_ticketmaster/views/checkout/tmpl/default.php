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

## Check if the user is logged in.
$user =  JFactory::getUser();

$app = JFactory::getApplication();
$pathway = $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_CART_DETAILS' ), 'index.php?option=com_ticketmaster&view=cart');
$pathway->addItem(JText::_( 'COM_TICKETMASTER_LOGIN_OR_CREATE_ACCOUNT' ));

$info = $app->getUserState('com_ticketmaster.registration');

## Get document type and add it.
$document = &JFactory::getDocument();

$cssfile = 'components/com_ticketmaster/assets/css-overrides/checkout.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/checkout.css' );
}

$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );
$document->setTitle( JText::_( 'COM_TICKETMASTER_LOGIN_OR_CREATE_ACCOUNT' ) );
$document->addScript( JURI::root(true).'/components/com_ticketmaster/assets/javascripts/moovalid.js');

$document->addScript('/jquery/jquery-1.9.0.min.js');
$document->addScript( JURI::root(true).'/components/com_ticketmaster/assets/javascripts/jquery.tabify.js');
$document->addScript( JURI::root(true).'/components/com_ticketmaster/assets/javascripts/jquery.tabify.source.js');

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

### Create a custom password:
## Generate a random character string for the password.
function password($length = 7, $chars = '1234567890abcdefghijklmnopqrstuvwABCDEFGHIJKLMNOPQRSTUVWXYZ'){
	## Length of character list
	$chars_length = (strlen($chars) - 1);
	## Start our string
	$string = $chars{rand(0, $chars_length)};
	## Generate random string
	for ($i = 1; $i < $length; $i = strlen($string)){
		## Grab a random character from our list
		$r = $chars{rand(0, $chars_length)};
		## Make sure the same two characters don't appear next to each other
		if ($r != $string{$i - 1}) $string .=  $r;
	}
	## Return the string
	return $string;
}
?>

<script language="javascript">

	var JQ = jQuery.noConflict();

	JQ(document).ready(function() {
		//Set default open/close settings
		//Hide/close all containers
		JQ('.acc_container').hide(); 
		//Add "active" class to first trigger, then show/open the immediate next container
		JQ('.acc_trigger:first').addClass('active').next().show(); 
		
		//On Click
		JQ('.acc_trigger').click(function(){
			//If immediate next container is closed...
			if( JQ(this).next().is(':hidden') ) { 
				//Remove all "active" state and slide up the immediate next container
				JQ('.acc_trigger').removeClass('active').next().slideUp(300); 
				//Add "active" state to clicked trigger and slide down the immediate next container
				JQ(this).toggleClass('active').next().slideDown(300); 
			}
			return false; //Prevent the browser jump to the link anchor
		});
	});

	window.addEvent('domready', function() {
		var fields = {
				name:         		'Required [5-65]',
				address:			'Required [3-65]',	
				zipcode:			'Required [1-10]',
				city:				'Required [1-65]',
				phone:				'Required [5-15]',
				username:			'Required [3-15]',
				password:			'Required [5-15]',
				password2:			'Required [5-15]',
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

<h2><?php echo JText::_('COM_TICKETMASTER_LOGIN_OR_CREATE_ACCOUNT'); ?></h2>

<h3 class="acc_trigger"><a href="#"><?php echo JText::_('COM_TICKETMASTER_LOGIN_NOW'); ?></a></h3>
<div class="acc_container">
    <div class="block">
    
		<form action="<?php echo JRoute::_( 'index.php'); ?>" method="post" name="loginForm">
        <?php echo JText::_('COM_TICKETMASTER_LOGIN_DESC'); ?>
        
        <div style="width:75%; margin-top:20px;">
            <table class="table table-striped">               
                    
                <tr>
                    <td width="40%" style="border:0px;"><?php echo JText::_( 'COM_TICKETMASTER_USERNAME' ); ?></td>
                    <td width="60%">
                        <input type="text" name="username" size="24" alt="<?php echo JText::_( 'Username' ); ?>" 
                        value="<?php echo JText::_( 'Username' ); ?>" 
                        onblur="if(this.value=='') this.value='<?php echo JText::_( 'Username' ); ?>';" 
                        onfocus="if(this.value=='<?php echo JText::_( 'Username' ); ?>') this.value='';" class="inputbox" />                     
                    </td>
                </tr>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_PASSWORD' ); ?></td>
                    <td>
                        <input type="password" name="password" size="24" alt="<?php echo JText::_( 'Password' ); ?>" 
                        value="<?php echo JText::_( 'Password' ); ?>" 
                        onblur="if(this.value=='') this.value='<?php echo JText::_( 'Password' ); ?>';" 
                        onfocus="if(this.value=='<?php echo JText::_( 'Password' ); ?>') this.value='';" class="inputbox" />                    
                    </td>
                </tr>   
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" value="<?php echo JText::_('COM_TICKETMASTER_LOGIN'); ?>" class="button_rdticketmaster"></td>
                </tr>                                           
            
            </table>
        </div>
        
        <input type="hidden" name="option" value="com_ticketmaster" />
        <input type="hidden" name="controller" value="checkout" />
        <input type="hidden" name="task" value="login" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>           
    </div>
</div>

<?php 
$params = JComponentHelper::getParams('com_users');

## Check what type of registration it is in config.
$useractivation = $params->get('useractivation');

if ($useractivation ==1){

?>
<h3 class="acc_trigger"><a href="#"><?php echo JText::_('COM_TICKETMASTER_ACTIVATE_ACCOUNT'); ?></a></h3>
<div class="acc_container">
    <div class="block">
		
        <form action="<?php echo JRoute::_( 'index.php'); ?>" method="post" name="activateForm">
        <?php echo JText::_('COM_TICKETMASTER_ACTIVATION_DESC'); ?>

        <div style="width:75%; margin-top:20px;">
            <table class="table table-striped">               
                    
                <tr>
                    <td width="40%"><?php echo JText::_( 'COM_TICKETMASTER_ENTER_ACTIVATION_CODE' ); ?></td>
                    <td width="60%">
                        <input type="text" name="token" size="24" alt="<?php echo JText::_( 'COM_TICKETMASTER_ACTIVATION_CODE' ); ?>" 
                        value="<?php echo JText::_( 'COM_TICKETMASTER_ACTIVATION_CODE' ); ?>" 
                        onblur="if(this.value=='') this.value='<?php echo JText::_( 'Password' ); ?>';" 
                        onfocus="if(this.value=='<?php echo JText::_( 'COM_TICKETMASTER_ACTIVATION_CODE' ); ?>') this.value='';" class="inputbox" />                
                    </td>
                </tr>  
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" value="<?php echo JText::_('COM_TICKETMASTER_ACTIVATE'); ?>" class="button_rdticketmaster"></td>
                </tr>                                           
            
            </table>
        </div>
      
        <input type="hidden" name="option" value="com_ticketmaster" />
        <input type="hidden" name="controller" value="checkout" />
        <input type="hidden" name="task" value="activate" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form> 
                  
    </div>
</div>

<?php } ?>

<h3 class="acc_trigger"><a href="#"><?php echo JText::_('COM_TICKETMASTER_CREATEACCOUNT_NOW'); ?></a></h3>
<div class="acc_container">
    <div class="block">
    	
        <?php echo JText::_('COM_TICKETMASTER_FREE_ACCOUNT'); ?><br/><br/>
		
        <form id="general" action="index.php?option=com_ticketmaster&controller=checkout" method="post" name="general">
        
        <div style="width:75%; margin-top:20px;">
            <table class="table table-striped">               
                    
                <tr>
                    <td width="40%"><?php echo JText::_( 'COM_TICKETMASTER_YOUR_GENDER' ); ?>*</td>
                    <td width="60%"><?php echo $this->lists['gender']; ?></td>
                </tr>  
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_NAME' ); ?>*</td>
                    <td><input name="name" type="text" id="name" class="inputbox" value="<?php echo $info[name]; ?>" size="25" /></td>
                </tr> 
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_ADDRESS' ); ?>*</td>
                    <td><input name="address" type="text" id="address" class="inputbox" value="<?php echo $info[address]; ?>" size="25" /></td>
                </tr>   
                <?php if($this->config->show_secondaddress != 0 ){ ?>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_ADDRESS' ); ?></td>
                    <td><input name="address2" type="text" id="address2" class="inputbox" value="<?php echo $info[address2]; ?>" size="25" /></td>
                </tr>
                <?php } ?> 
                <?php if($this->config->show_thirdaddress != 0 ){ ?>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_ADDRESS' ); ?></td>
                    <td><input name="address3" type="text" id="address3" class="inputbox" value="<?php echo $info[address3]; ?>" size="25" /></td>
                </tr>
                <?php } ?>  
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_ZIPCODE' ); ?>*</td>
                    <td><input name="zipcode" type="text" id="zipcode" class="inputbox" value="<?php echo $info[zipcode]; ?>" size="25" /></td>
                </tr>  
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_CITY' ); ?>*</td>
                    <td><input name="city" type="text" id="city" class="inputbox" value="<?php echo $info[city]; ?>" size="25" /></td>
                </tr>
                <?php if($this->config->show_country != 0 ){ ?>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_COUNTRY' ); ?></td>
                    <td><?php echo $this->lists['country']; ?></td>
                </tr>                
                <?php } ?> 
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_PHONE' ); ?></td>
                    <td><input name="phonenumber" type="text" id="phonenumber" class="inputbox" value="<?php echo $info[phonenumber]; ?>" size="25" /></td>
                </tr>
                <?php if($this->config->show_birthday != 0 ){ ?>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_BIRTHDAY' ); ?></td>
                    <td><?php echo $this->lists['day']; ?>&nbsp;<?php echo $this->lists['month']; ?>&nbsp;<?php echo $this->lists['year']; ?></td>
                </tr>
                <?php } ?>                
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_EMAIL' ); ?>*</td>
                    <td><input name="emailaddress" type="text" id="emailaddress" class="inputbox" value="<?php echo $info[email]; ?>" size="25" /></td>
                </tr>                
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_RETYPE_EMAIL' ); ?>*</td>
                    <td><input name="email2" type="text" id="email2" class="inputbox" size="25" /></td>
                </tr>
                <?php if($this->config->show_mailchimp_signup != 0 ){ ?>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_NEWSLETTER_SIGNUP' ); ?></td>
                    <td>
                      <input type="radio" name="emailUpdates" value="Yes" /> <?php echo JText::_( 'COM_TICKETMASTER_YES' ); ?>
                      <input type="radio" name="emailUpdates" value="No" checked="checked" /> <?php echo JText::_( 'COM_TICKETMASTER_NO' ); ?>
                    </td>
                </tr>
				<?php } ?>
            </table>
            
			<?php if($this->config->auto_username != 1 ){ ?>
            
            <h2><?php echo JText::_('COM_TICKETMASTER_REGISTER_USERINFO'); ?></h2>            
            
            <table class="table table-striped">  
                
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_USERNAME' ); ?>*</td>
                    <td><input name="username" type="text" id="username"  class="inputbox" value="<?php echo $info[username]; ?>" size="25" /></td>
                </tr>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_PASSWORD' ); ?>*</td>
                    <td><input name="password" type="password" id="password" class="inputbox" value="" size="25" /></td>
                </tr> 
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_YOUR_PASSWORD_2' ); ?>*</td>
                    <td><input name="password2" type="password" id="password2" class="inputbox" value="" size="25" /></td>
                </tr> 
                <tr>
                    <td></td>
                    <td><img id="captcha" src="<?php echo JURI::root(); ?>components/com_ticketmaster/assets/captcha/securimage.php" alt="CAPTCHA Image" /></td>
                </tr>
                <tr>
                    <td><?php echo JText::_( 'COM_TICKETMASTER_ENTER_SECURITY_CODE' ); ?>*</td>
                    <td><input name="security_code" type="text" id="security_code" class="inputbox" value="" size="10" /></td>
                </tr> 
                <tr>
                    <td></td>
                    <td><input type="submit" value="<?php echo JText::_('COM_TICKETMASTER_REGISTER_NOW'); ?>" class="button_rdticketmaster"></td>
                </tr>                                                                                    
                
            </table>            
            
            <?php } ?>
        </div>                      
        
        <div style="clear:both; margin-top:15px; padding-top:15px; margin-bottom:10px;">&nbsp;</div>
        
        <?php if ($useractivation ==1){ ?>
        
        	<div style="padding:5px; border:1px #FF0000 solid; margin-bottom: 15px; font-weight:bold; width:75%; color:#FF0000; text-align:center;">
				<?php echo JText::_('COM_TICKETMASTER_FREE_ACCOUNT_ACTIVATION_INFO'); ?>
            </div>
        
		<?php } ?>         
        
        <?php if($this->config->show_country == 0 ){ ?>
        	<input type="hidden" id="country_id" name="country_id" value="1" />
        <?php } ?> 
        <?php if($this->config->show_birthday == 0 ){ ?>
        	<input type="hidden" id="birthday" name="birthday" value="1212-12-12" />
        <?php } ?>            
        <input type="hidden" name="option" value="com_ticketmaster" />
        <input type="hidden" name="controller" value="checkout" />
        <input type="hidden" name="task" value="save" />  
        <?php echo JHTML::_( 'form.token' ); ?>     
        
        </form>

    </div>
</div>

