<?php
/**
 * @name 	        playerbase.php
 ** @version	        2.1.0.1
 * @package	        Apptha
 * @since	        Joomla 1.5
 * @author      	Apptha - http://www.apptha.com/
 * @copyright 		Copyright (C) 2011 Powered by Apptha
 * @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @abstract      	Contus HD FLV Player playerbase model file
 * @Creation Date	23 Feb 2011
 * @modified Date	28 Aug 2013
 */

## No direct acesss
defined('_JEXEC') or die();

##  Importing Default Joomla Component
jimport( 'joomla.application.component.model' );

/*
 * Model class for play skin
 */
class hdflvplayerModelplayerbase extends HdflvplayerModel
{
    
    function playerskin()
    {
        $playerpath = realpath(dirname(__FILE__) . '/../hdflvplayer/hdplayer.swf');
        $this->showplayer($playerpath);
    }

    function showplayer($playerpath)
    {
        ob_clean();
        header("content-type:application/x-shockwave-flash");
        readfile($playerpath);
        exit();
    }
}