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

## Temporary turning off error reporting:
## It is turned off for the AJAX requests we are doing.
error_reporting(0);

function TicketmasterBuildRoute(&$query)
{
       $segments = array();
       if(isset($query['view']))
       {
                $segments[] = $query['view'];
                unset( $query['view'] );
       }
       if(isset($query['id']))
       {
                $segments[] = $query['id'];
                unset( $query['id'] );
       };
       return $segments;
}


function TicketmasterParseRoute($segments)
{
       $vars = array();
       switch($segments[0])
       {
               case 'eventlist':
                       $vars['view'] = 'eventlist';
                       $id = explode( ':', $segments[1] );
                       $vars['id'] = (int) $id[0];					   
                       break;
               case 'event':
                       $vars['view'] = 'event';
                       $id = explode( ':', $segments[1] );
                       $vars['id'] = (int) $id[0];
                       break;
               case 'venue':
                       $vars['view'] = 'venue';
                       $id = explode( ':', $segments[1] );
                       $vars['id'] = (int) $id[0];
                       break;					   
               case 'cart':
                       $vars['view'] = 'cart';
                       break;			
               case 'checkout':
                       $vars['view'] = 'checkout';
                       break;	
               case 'payment':
                       $vars['view'] = 'payment';
                       break;	
               case 'ordercomplete':
                       $vars['view'] = 'ordercomplete';
                       break;	
               case 'cancel':
                       $vars['view'] = 'cancel';
                       break;	
               case 'account':
                       $vars['view'] = 'account';
                       break;	
               case 'profile':
                       $vars['view'] = 'profile';
                       break;						   					   					   					   					   					   					   		   
       }
       return $vars;
}
