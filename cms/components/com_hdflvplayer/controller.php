<?php
/**
 * @name 	        hdflvplayer
 ** @version	        2.1.0.1
 * @package	        Apptha
 * @since	        Joomla 1.5
 * @subpackage	        hdflvplayer
 * @author      	Apptha - http://www.apptha.com/
 * @copyright 		Copyright (C) 2011 Powered by Apptha
 * @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @abstract      	com_hdflvplayer installation file.
 ** @Creation Date	23 Feb 2011
 ** @modified Date	28 Aug 2013
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/*
 * Class for player view of com_hdflvplayer component
 */
//if(version_compare(JVERSION,'1.6.0','ge')) {
//	$jlang = JFactory::getLanguage();
//        $jlang->load('com_contushdvideoshare', JPATH_SITE, $jlang->get('tag'), true);
//        $jlang->load('com_contushdvideoshare', JPATH_SITE, null, true);
//}
class hdflvplayerController extends ContushdflvplayerController {
 
   // Displaying player on the page
   public function display($cachable = false, $urlparams = false){
           $view = $this->getView('player');
        if ($model = $this->getModel('player')) {
           $view->setModel($model, true);
        }
        $view->displayplayer();
    }

    //Configuration xml for player
    function configxml() {
        $view = $this->getView('configxml');
        if ($model = $this->getModel('configxml')) {
            $view->setModel($model, true);
        }
        $view->configget();
    }

    //Playlist xml for player
    function playxml() {
        $view = $this->getView('playxml');
        if ($model = $this->getModel('playxml')) {
            $view->setModel($model, true);
        }
        $view->playget();
    }

	// midroll ads for player
     function midrollxml() {
        $view = $this->getView('midrollxml');
        if ($model = $this->getModel('midrollxml')) {
            $view->setModel($model, true);
        }
        $view->getads();
    }
	// imaadsxml ads for player
     function imaadsxml() { 
        $view = $this->getView('imaadsxml');
        if ($model = $this->getModel('imaadsxml')) {
            $view->setModel($model, true);
        }
        $view->getads();
    }



    //video data for player
    function videourl() {
        $view = $this->getView('videourl');
        if ($model = $this->getModel('videourl')) {
            //Push the model into the view (as default)
            //Second parameter indicates that it is the default model for the view
            $view->setModel($model, true);
        }
        $view->getvideourl();
    }

    //.swf file for player
    function player() {

        $view = $this->getView('playerbase');
        if ($model = $this->getModel('playerbase')) {
            //Push the model into the view (as default)
            //Second parameter indicates that it is the default model for the view
            $view->setModel($model, true);
        }
        $view->loadplayer();
    }

    // adds for player
    function adsxml() {
        $view = $this->getView('adsxml');
        if ($model = $this->getModel('adsxml')) {
            $view->setModel($model, true);
        }
        $view->getads();
    }

    //'send to e-mail' for player
    function email() {
        $view = $this->getView('email');
        if ($model = $this->getModel('email')) {
            $view->setModel($model, true);
        }
        $view->emailplayer();
    }

//googleadds for player
    function googleadd() {
        $view = $this->getView('googleadd');
        if ($model = $this->getModel('googleadd')) {
            $view->setModel($model, true);
        }
        $view->googlescript();
    }

// lanugagexml for player
    function languagexml() {
        $view = $this->getView('language');
        if ($model = $this->getModel('languagexml')) {
            $view->setModel($model, true);
        }
        $view->language();
    }

// Google Adds counts
    function addview() {
        $view = $this->getView('addcount');
        if ($model = $this->getModel('addview')) {
            $view->setModel($model, true);
        }
        $view->getaddview();
    }

// viewed Ad's for player
    function impressionclicks() {
        $view = $this->getView('impressionclicks');
        if ($model = $this->getModel('impressionclicks')) {
            $view->setModel($model, true);
        }
        $view->impressionclicks();
    }
    
//Function for Ajax control redirect
    function ajaxredirects() {
    	$compid = JRequest::getVar('compid');
    	$Itemid = JRequest::getVar('Itemid');
        echo $url = JRoute::_('index.php?option=com_hdflvplayer&compid='.$compid.'&Itemid='.$Itemid,false);
    }
    
}
?>