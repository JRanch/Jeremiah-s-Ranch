<?php

## no direct access
defined('_JEXEC') or die('Restricted access');

$option = JRequest::getVar('option', 'com_ticketmaster');
$controller = JRequest::getWord('controller');

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');


## Empty all vars, so we can use them later for the class="active"
$config = '';
$events = '';
$tickets = '';
$ticketbox = '';
$transactions = '';
$export = '';
$venues = '';
$visitors = '';
$coupons = '';
$countries = '';
$mail = '';
$waitinglist = '';
$scans = '';
$default = '';

switch ($controller) {
    case 'configuration':
        $config = 'class="active"';
        break;
    case 'events':
        $events = 'class="active"';
        break;
    case 'tickets':
        $tickets = 'class="active"';
        break;
    case 'ticketbox':
        $ticketbox = 'class="active"';
        break;	
    case 'transactions':
        $transactions = 'class="active"';
        break;	
    case 'export':
        $export = 'class="active"';
        break;	
    case 'venues':
        $venues = 'class="active"';
        break;			
    case 'visitors':
        $visitors = 'class="active"';
        break;
    case 'countries':
        $countries = 'class="active"';
        break;				
    case 'mail':
        $mail = 'class="active"';
        break;
    case 'import':
        $import = 'class="active"';
        break;	
    case 'coupons':
        $coupons = 'class="active"';
        break;
    case 'waitinglist':
        $waitinglist = 'class="active"';
        break;						
    case 'scans':
        $scans = 'class="active"';
        break;
    default:
       	$default = 'class="active"';										
}

if(!$isJ30) {
  $style = "style=\"font-size:105%;\"";
}else{
  $style = "";
}

?>


<?php if ($option == 'com_ticketmaster') { ?>
    
    <ul class="nav nav-tabs">
      <li <?php echo $default; ?>> 
      		<a href="index.php?option=com_ticketmaster"><?php echo JText::_( 'COM_TICKETMASTER_CPANEL' ); ?></a>
      </li>
      <li <?php echo $events; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=events"><?php echo JText::_( 'COM_TICKETMASTER_CATEGORIES_EVENTS' ); ?></a>
      </li>
      <li  <?php echo $tickets; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=tickets"><?php echo JText::_( 'COM_TICKETMASTER_EVENT_TICKETS' ); ?></a>
      </li>
      <li <?php echo $ticketbox; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=ticketbox"><?php echo JText::_( 'COM_TICKETMASTER_SOLDTICKETS' ); ?></a>
      </li>
      <li <?php echo $transactions; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=transactions"><?php echo JText::_( 'COM_TICKETMASTER_TRANSACTION' ); ?></a>
      </li>
      <li <?php echo $export; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=export"><?php echo JText::_( 'COM_TICKETMASTER_EXPORT_XLS' ); ?></a>
      </li>   
      <li <?php echo $venues; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=venues"><?php echo JText::_( 'COM_TICKETMASTER_VENUESMANAGEMENT' ); ?></a>
      </li>
      <li <?php echo $coupons; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=coupons"><?php echo JText::_( 'COM_TICKETMASTER_COUPONS' ); ?></a>
      </li>      
      <li <?php echo $visitors; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=visitors"><?php echo JText::_( 'COM_TICKETMASTER_CLIENTS' ); ?></a>
      </li>
      <li <?php echo $countries; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=countries"><?php echo JText::_( 'COM_TICKETMASTER_COUNTRIELIST' ); ?></a>
      </li>   
      <li <?php echo $mail; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=mail"><?php echo JText::_( 'COM_TICKETMASTER_MSG_CENTER' ); ?></a>
      </li>
      <li <?php echo $waitinglist; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=waitinglist"><?php echo JText::_( 'COM_TICKETMASTER_WAITINGLIST' ); ?></a>
      </li>  
      <li <?php echo $scans; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=scans"><?php echo JText::_( 'COM_TICKETMASTER_SCANS' ); ?></a>
      </li>            
      <li <?php echo $config; ?>>
      		<a href="index.php?option=com_ticketmaster&controller=configuration"><?php echo JText::_( 'COM_TICKETMASTER_CONFIG' ); ?></a>
      </li>        
    </ul>
    
<?php }else{ ?>
    
    <ul class="nav nav-tabs">
      <li  <?php echo $default; ?>>
      	<a href="index.php?option=com_ticketmaster"><?php echo JText::_( 'COM_TICKETMASTEREXT_CPANEL_TM' ); ?></a></li>
      <li  <?php echo $tickets; ?>>
      	<a href="index.php?option=com_ticketmasterext&controller=tickets"><?php echo JText::_( 'COM_TICKETMASTER_SEATPLAN' ); ?></a></li>
      <li  <?php echo $import; ?>>
      	<a href="index.php?option=com_ticketmasterext&controller=import"><?php echo JText::_( 'COM_TICKETMASTER_SEATPLAN_COPIER' ); ?></a></li>
    </ul>  
    
<?php } ?>

