<?php

/****************************************************************
 * @version		1.0.0 ticketmaster $							*
 * @package		ticketmaster									*
 * @copyright	Copyright © 2009 - All rights reserved.			*
 * @license		GNU/GPL											*
 * @author		Robert Dam										*
 * @author mail	info@rd-media.org								*
 * @website		http://www.rd-media.org							*
 ***************************************************************/

function showprice($holder,$price,$currency) {
	
		if ($holder == 1) { $price = $currency.' '.number_format($price, 2, ',', '.'); }
		if ($holder == 2) { $price = number_format($price, 2, ',', '.').' '.$currency; }
		if ($holder == 3) { $price = $currency.' '.number_format($price, 2, ',', ''); }
		if ($holder == 4) { $price = number_format($price, 2, ',', '').' '.$currency; }
		if ($holder == 5) { $price = $currency.' '.number_format($price, 2, '.', ''); }
		if ($holder == 6) { $price = number_format($price, 2, '.', '').' '.$currency; }
		if ($holder == 7) { $price = number_format($price, '2' , '.', ',').' '.$currency; }
		if ($holder == 8) { $price = $currency.' '.number_format($price, '2' , '.', ','); }	
		if ($holder == 9)  { $price = number_format($price, '0' , '', ',').' '.$currency; }	
		if ($holder == 10) { $price = $currency.' '.number_format($price, '0' , '', ','); }	
		if ($holder == 11) { $price = number_format($price, '0' , '', '.').' '.$currency; }	
		if ($holder == 12) { $price = $currency.' '.number_format($price, '0' , '', '.'); }					
    
	return $price;  
}

function showmonth($holder) {
	
		if ($holder == 01) { $month = JText::_('COM_TICKETMASTER_JANUARI'); }
		if ($holder == 02) { $month = JText::_('COM_TICKETMASTER_FEBRUARI'); }
		if ($holder == 03) { $month = JText::_('COM_TICKETMASTER_MARCH'); }
		if ($holder == 04) { $month = JText::_('COM_TICKETMASTER_APRIL'); }
		if ($holder == 05) { $month = JText::_('COM_TICKETMASTER_MAY'); }
		if ($holder == 06) { $month = JText::_('COM_TICKETMASTER_JUNE'); }
		if ($holder == 07) { $month = JText::_('COM_TICKETMASTER_JULY'); }
		if ($holder == 08) { $month = JText::_('COM_TICKETMASTER_AUGUST'); }	
		if ($holder == 09) { $month = JText::_('COM_TICKETMASTER_SEPTEMBER'); }	
		if ($holder == 10) { $month = JText::_('COM_TICKETMASTER_OCTOBER'); }	
		if ($holder == 11) { $month = JText::_('COM_TICKETMASTER_NOVEMBER'); }	
		if ($holder == 12) { $month = JText::_('COM_TICKETMASTER_DECEMBER'); }					
    
	return $month;  
}

function Template($bootstrap) {

		## Check if this is Joomla 2.5 or 3.0.+
		## If Joomla 3 is present we will check if bootstrap is needed to load.
		
		$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');	
	
		if(!$isJ30 && $bootstrap == 1){
			
			## Bootstrap is turned on :) 
			## We may load the bootstrap files now.
			$tpl = 'bootstrap';
		
		}elseif($isJ30){
			
			## joomla 3 with bootstrap on.
			$tpl = 'bootstrap';
		
		}else{
			
			## Load normal templates.
			$tpl=null;
		
		}
    
	return $tpl;  
}

?>