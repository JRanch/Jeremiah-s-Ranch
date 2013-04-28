<?php 
/**
 * @version		2.5.4 ticketmaster $ROBERT-20121101
 * @package		Ticketmaster
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license		GNU/GPL
 * @author		Robert Dam
 * @author mail	info@rd-media.org
 * @website		http://www.rd-media.org
 */

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

$app =& JFactory::getApplication();
$pathway =& $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_PROFILE' ), 'index.php?option=com_ticketmaster&view=profile');
$pathway->addItem(JText::_( 'COM_TICKETMASTER_REVIEW_PROFILE' ));

## Get document type and add it.
$document = &JFactory::getDocument();

$cssfile = 'components/com_ticketmaster/assets/css-overrides/myprofile.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/myprofile.css' );
}

$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );
$document->setTitle( JText::_( 'COM_TICKETMASTER_MY_PROFILE' ) );
$document->addScript( JURI::root(true).'/components/com_ticketmaster/assets/javascripts/moovalid.js');
$document->addScript('/jquery/jquery-1.9.0.min.js');

?>
<script language="javascript">

	window.addEvent('domready', function() {
		var fields = {
				name:         		'Required [5-65]',
				address:			'Required [5-65]',	
				zipcode:			'Required [0-10]',
				city:				'Required [2-65]',
				phone:				'Required [5-15]',
				username:			'Required [5-15]',
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


<script type="text/javascript">

	var JQ = jQuery.noConflict();
	
	JQ(document).ready(function() {
	 
		JQ('#link').click(function() {
			JQ("form#general").submit();
		});
	 
	});
 
</script>


<h2 class="contentheading"><?php echo JText::_('COM_TICKETMASTER_MY_PROFILE'); ?></h2>	

<?php echo JText::_('COM_TICKETMASTER_MY_PROFILE_DESC'); ?>

<form id="general" action="index.php" method="post">

<div style="float:left; width: 49%;">

    <div id = "ticketmaster_detail_items" style="padding-top:17px;">
                
        <div id = "ticketmaster_detail">
            <div id = "ticketmaster_detail_name">
                <?php echo JText::_( 'COM_TICKETMASTER_YOUR_NAME' ); ?>
            </div>
            <div id = "ticketmaster_detail_specs">
               <input name="name" type="text" id="name" class="inputbox" value="<?php echo $this->data->name; ?>" size="25" />
            </div>    
        </div>    
        
        <div id = "ticketmaster_divider"></div>
        
        <div id = "ticketmaster_detail">
            <div id = "ticketmaster_detail_name">
                <?php echo JText::_( 'COM_TICKETMASTER_YOUR_ADDRESS' ); ?>
            </div>
            <div id = "ticketmaster_detail_specs">
               <input name="address" type="text" id="address" class="inputbox" value="<?php echo $this->data->address; ?>" size="25" />
            </div>    
        </div>  
        
        <?php if($this->config->show_secondaddress != 0 ){ ?>
            <div id = "ticketmaster_detail">
                <div id = "ticketmaster_detail_name">
                    <?php echo JText::_( 'COM_TICKETMASTER_YOUR_ADDRESS' ); ?>
                </div>
                <div id = "ticketmaster_detail_specs">
                   <input name="address2" type="text" id="address2" class="inputbox" value="<?php echo $this->data->address2; ?>" size="25" />
                </div>    
            </div>  
        <?php } ?>
    
        <?php if($this->config->show_thirdaddress != 0 ){ ?>
            <div id = "ticketmaster_detail">
                <div id = "ticketmaster_detail_name">
                    <?php echo JText::_( 'COM_TICKETMASTER_YOUR_ADDRESS' ); ?>
                </div>
                <div id = "ticketmaster_detail_specs">
                   <input name="address3" type="text" id="address3" class="inputbox" value="<?php echo $this->data->address3; ?>" size="25" />
                </div>    
            </div>  
        <?php } ?> 
        
        <div id = "ticketmaster_detail">
            <div id = "ticketmaster_detail_name">
                <?php echo JText::_( 'COM_TICKETMASTER_YOUR_ZIPCODE' ); ?>
            </div>
            <div id = "ticketmaster_detail_specs">
               <input name="zipcode" type="text" id="zipcode" class="inputbox" value="<?php echo $this->data->zipcode; ?>" size="25" />
            </div>    
        </div> 
    
        <div id = "ticketmaster_detail">
            <div id = "ticketmaster_detail_name">
                <?php echo JText::_( 'COM_TICKETMASTER_YOUR_CITY' ); ?>
            </div>
            <div id = "ticketmaster_detail_specs">
               <input name="city" type="text" id="city" class="inputbox" value="<?php echo $this->data->city; ?>" size="25" />
            </div>    
        </div>  
    
        <div id = "ticketmaster_detail">
            <div id = "ticketmaster_detail_name">
                
            </div>
            <div id = "ticketmaster_detail_specs">
    
            </div>    
        </div>
    
    </div>

</div>

<div style="float:left; width: 49%;">

    <div id = "ticketmaster_detail_items" style="padding-top:17px;">
                
        <div id = "ticketmaster_detail">
            <div id = "ticketmaster_detail_name">
                <?php echo JText::_( 'COM_TICKETMASTER_YOUR_PHONE' ); ?>
            </div>
            <div id = "ticketmaster_detail_specs">
               <input name="phonenumber" type="text" id="phonenumber" class="inputbox" value="<?php echo $this->data->phonenumber; ?>" size="25" />
            </div>    
        </div>  
    
        <div id = "ticketmaster_detail">
            <div id = "ticketmaster_detail_name">
                <?php echo JText::_( 'COM_TICKETMASTER_YOUR_EMAIL' ); ?>
            </div>
            <div id = "ticketmaster_detail_specs">
               <input name="emailaddress" type="text" id="emailaddress" class="inputbox" value="<?php echo $this->data->emailaddress; ?>" size="25" />
    
            </div>    
        </div> 
        
        <div id = "ticketmaster_detail">
            <div id = "ticketmaster_detail_name">
                <?php echo JText::_( 'COM_TICKETMASTER_YOUR_BIRTHDAY' ); ?>
            </div>
            <div id = "ticketmaster_detail_specs">
               <?php echo $this->lists[day]; ?>&nbsp;<?php echo $this->lists[month]; ?>&nbsp;<?php echo $this->lists[year]; ?>
            </div>    
        </div> 
        
		<?php if($this->config->show_country != 0 ){ ?>
            <div id = "ticketmaster_detail">
                <div id = "ticketmaster_detail_name">
                    <?php echo JText::_( 'COM_TICKETMASTER_YOUR_COUNTRY' ); ?>
                </div>
                <div id = "ticketmaster_detail_specs">
                   <?php echo $this->lists[country]; ?>
                </div>    
            </div>  
        <?php } ?>                     
    
    </div>    

</div>

<div style="clear:both"></div>
     
<div id = "ticketmaster_divider"></div>

<div id = "ticketmaster_detail">
    <div id = "ticketmaster_detail_name">
        
    </div>
    <div id = "ticketmaster_detail_specs">
        <a class="button small gray" id="link">
            <span><?php echo JText::_('COM_TICKETMASTER_SAVE_MY_PROFILE'); ?></span>
        </a>            
    </div>    
</div> 

</div>

<div style="clear:both"></div>

<input type="hidden" name="option" value="com_ticketmaster" />
<input type="hidden" name="controller" value="profile" />
<input type="hidden" name="task" value="myprofile" />
<input type="hidden" name="userid" value="<?php echo $user->id; ?>" />
<input type="hidden" name="gid" value="0" /> 
<input type="hidden" name="clientid" value="<?php echo $this->data->clientid; ?>" />

<?php echo JHTML::_( 'form.token' ); ?>        
</form> 


