<?php
/**
 * @name 	        configxml.php
 ** @version	        2.1.0.1
 * @package	        Apptha
 * @since	        Joomla 1.5
 * @author      	Apptha - http://www.apptha.com/
 * @copyright 		Copyright (C) 2011 Powered by Apptha
 * @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @abstract      	Contus HD FLV Player config XML file
 * @Creation Date	23 Feb 2011
 * @modified Date	28 Aug 2013
 */
## No direct acesss
defined('_JEXEC') or die();

##  importing default joomla component
jimport('joomla.application.component.model');
jimport('joomla.application.component.modellist');
jimport('joomla.html.parameter');

/*
 * Class for generating player configuration xml
 */

class hdflvplayerModelconfigxml extends HdflvplayerModel {

    ## Function to get player settings
    function configgetrecords() {
        $base               = JURI::base();
        $playid = $playid_playlistname = $mid = $moduleid = $comppid = 0;
        $rs_moduleparams    = '';

        $db                 = JFactory::getDBO();
        $query              = 'SELECT `id`, `published`, `logopath`,`player_icons` , `player_colors`, `player_values` FROM `#__hdflvplayersettings`';
        $db->setQuery($query);
        $settingsrows       = $db->loadObject();

        $midrollads         = true;

        ## Playlist id
        $playid_playlistname = JRequest::getvar('playid', '', 'get', 'var');

        ##  Video id;
        $id                 = JRequest::getvar('id', '', 'get', 'int');
        if (version_compare(JVERSION, '3.0.0', 'ge')) {
            $id             = JRequest::getvar('id');
        }
        if ($id) {
            $playid         = $id;
        }

        ##  Module video id
        $videoid            = JRequest::getvar('videoid', '', 'get', 'int');
        if (version_compare(JVERSION, '3.0.0', 'ge')) {
            $videoid        = JRequest::getvar('videoid');
        }
        if ($videoid) {
            $playid         = $videoid;
        }

        ## fetch playlist id from URL parameter
        $comppid            = JRequest::getvar('compid', '', 'get', 'int');
        if (version_compare(JVERSION, '3.0.0', 'ge')) {
            $comppid        = JRequest::getvar('compid');
        }

        ## Fetch module id and the parameter settings
        $moduleid           = JRequest::getvar('mid', '', 'get', 'int');
        if (version_compare(JVERSION, '3.0.0', 'ge')) {
            $moduleid       = JRequest::getvar('mid');
        }
        if ($moduleid) {
            $moduleid       = $moduleid;
            $query          = "SELECT id,params FROM `#__modules` WHERE id=$moduleid and module='mod_hdflvplayer'";
            $db->setQuery($query);
            $rs_moduleparams = $db->loadObjectList();
            $midrollads     = false;
        }

        $this->configxml($rs_moduleparams, $settingsrows, $playid, $playid_playlistname, $moduleid, $comppid, $base, $midrollads);
    }

    ## function to generate configxml
    function configxml($rs_moduleparams, $settingsrows, $playid, $playid_playlistname, $moduleid, $comppid, $base, $allowmidrollads) {

        ## Declaration here
        $playlist_open = $postrollads = $prerollads = $autoplay = $zoom = $fullscreen = $skinautohide = $timer = $share = $playlist_autoplay = $hddefault = $playlist = false;
        $playlistxml = $vquality = $videoid = $IMAAds_path = '';
        $playid_playlistname = 0;
        
        ## Declare XML, skin,logo, download, email paths
        $skinPath               = $base . 'components/com_hdflvplayer/hdflvplayer/skin/skin_hdflv_white.swf';
        $downloadPath           = $base . 'components/com_hdflvplayer/hdflvplayer/download.php';
        $playlistxml            = $base . 'components/com_hdflvplayer/models/playxml.php';
        $midadsxml              = $base . 'index.php?option=com_hdflvplayer&task=midrollxml';
        $emailpath              = $base . 'components/com_hdflvplayer/hdflvplayer/email.php';
        $logopath               = $base . 'components/com_hdflvplayer/videos/' . $settingsrows->logopath;
        $languagexml            = $base . 'index.php?option=com_hdflvplayer&task=languagexml';
        $adsxml                 = $base . 'index.php?option=com_hdflvplayer&task=adsxml';
        $playlistxml            = $base . 'index.php?option=com_hdflvplayer&task=playxml';
        $imaAdsXML              = $base . 'index.php?option=com_hdflvplayer&task=imaadsxml';
        
        ## Unserialize the player's parameters here for component.
        $player_colors          = unserialize($settingsrows->player_colors);
        $player_icons           = unserialize($settingsrows->player_icons);
        $player_values          = unserialize($settingsrows->player_values);

        ## Fetch Player Icon settings for component
        $stagecolor             = "0x" . $player_values['stagecolor'];

        if ($allowmidrollads == 'true') {
            if (($player_icons['midrollads'] == 0)) {
                $midrollads     = 'false';
            } else {
                $midrollads     = 'true';
            }
        } else {
            $midrollads         = 'false';
        }

        if($player_icons['autoplay'] == 1) {
            $autoplay = 'true'; 
        } else {
                $autoplay = 'false';
        }

        if ($player_icons['zoom'] == 1){
            $zoom = 'true';
        } else {
            $zoom = 'false';
        }

        if ($player_icons['fullscreen'] == 1) {
            $fullscreen = 'true';
            } else {
            $fullscreen = 'false';
        }

        if ($player_icons['skin_autohide'] == 1){
            $skinautohide = 'true';
            } else {
            $skinautohide = 'false';
            }
        if ($player_icons['timer'] == 1){
            $timer = 'true';
            } else {
            $timer = 'false';
        }

        $target_url = $player_values['logourl'];
        if (empty($target_url)) {
            $target_url = JURI::base();
        } else if (!strstr($target_url, 'http') && !strstr($target_url, 'https')) {
            $target_url = "http://" . $target_url;
        }

        if ($player_icons['shareurl'] == 1){ $share = 'true'; } else { $share = 'false'; }
        if ($player_icons['email'] == 1){ $playerEmail = 'true'; } else { $playerEmail = 'false'; }
        if ($player_icons['progressbar'] == 1){ $progressControl = 'true'; } else { $progressControl = 'false'; }
        if ($player_icons['volumevisible'] == 1){ $volumecontrol = 'true'; } else { $volumecontrol = 'false'; }
        if ($player_icons['email'] == 1){ $playerEmail = 'true'; } else { $playerEmail = 'false'; }
        if ($player_icons['playlist_autoplay'] == 1){ $playlist_autoplay = 'true'; } else { $playlist_autoplay = 'false'; }
        if ($player_icons['hddefault'] == 1){ $hddefault = 'true'; } else { $hddefault = 'false'; }
        if ($player_icons['download'] == 1){ $playerDownload = 'true'; } else { $playerDownload = 'false'; }
        if ($player_icons['playlist_open'] == 1){ $playlist_open = 'true'; } else { $playlist_open = 'false'; }
        if ($player_icons['postrollads'] == 0){ $postrollads = 'false'; } else { $postrollads = 'true'; }
        if ($player_icons['prerollads'] == 0){ $prerollads = 'false'; } else { $prerollads = 'true'; }
        if ($player_icons['adsSkip'] == 0){ $adsSkip = 'false'; } else { $adsSkip = 'true'; }
        if ($player_icons['showTag'] == 0){ $showTag = 'false'; } else { $showTag = 'true'; }
        if ($player_icons['imaAds'] == 0){ $imaAds = 'false'; } else { $imaAds = 'true'; }
        if ($player_icons['midrollads'] == 0){ $midrollads = 'false'; } else { $midrollads = 'true'; }
        if ($player_icons['embedcode_visible'] == 0){ $embedcode_visible = 'false'; } else { $embedcode_visible = 'true'; }
        if ($player_icons['imageDefault'] == 0){ $imageDefault = 'false'; } else { $imageDefault = 'true'; }

        ## Fetch Player values settings for component
        $buffer             = $player_values['buffer'];
        $normalscale        = $player_values['normalscale'];
        $fullscreenscale    = $player_values['fullscreenscale'];
        $volume             = $player_values['volume'];
        $trackCode          = $player_values['googleanalyticsID'];
        if ($player_values['related_videos'] == '1' || $player_values['related_videos'] == '3'){ 
            $playlist = 'true'; 
        } else { 
            $playlist = 'false'; 
        }
        
        if ($player_values['licensekey'] != ''){ 
            $license = $player_values['licensekey']; 
        } else { 
            $license = ''; 
        }

        ## If module params available
        if ($rs_moduleparams != '') {
            if (version_compare(JVERSION, '3.0', 'ge')) {
                $params = new JRegistry($rs_moduleparams[0]->params, '');
            } else {
                $params = new JParameter($rs_moduleparams[0]->params, '');
            }


            $playlist = $params->get('enablexml');
            if ($playlist == 0){ $playlist = 'false'; } else { $playlist = 'true'; }

            ## Getting admin param settings for module
            $videocat           = $params->get('videocat');
            $videoId            = $params->get('videoid');
            $playlistId         = $params->get('playlistid');
            $autoplay           = $params->get('autoplay');
            $playlist_autoplay  = $params->get('playlistauto');
            $buffer             = $params->get('buffer');
            $normalscale        = $params->get('normalscrenscale');
            $fullscreenscale    = $params->get('fullscrenscale');
            $volume             = $params->get('volume');
            $skinautohide       = $params->get('skinautohide');
            $fullscreen         = $params->get('fullscreen');
            $zoom               = $params->get('zoom');
            $timer              = $params->get('timer');
            $share              = $params->get('share');
            $playlistopen       = $params->get('playlist_open');
            $stagecolor         = "0x" . $params->get('stagecolor');
            
            ## Check whether Video or playlist selected for the module
            if ($moduleid != '' &&  $playid == '') {
            if (version_compare(JVERSION, '2.5', 'ge') || version_compare(JVERSION, '3.0', 'ge') || version_compare(JVERSION, '1.6', 'ge') || version_compare(JVERSION, '1.7', 'ge')) {
               if ($videocat->videocat == 1){ $videoid = $videoId->videoid; } else { $playid_playlistname = $playlistId->playlistid; }
            } else {
               if ($videocat['videocat'] == 1){ $videoid = $videoId['videoid']; } else { $playid_playlistname = $playlistId['playlistid']; }
            }
            }
            if ($autoplay == 0){ $autoplay = "false"; } else { $autoplay = 'true'; }
            if ($playlist_autoplay == 0){ $playlist_autoplay = 'false'; } else { $playlist_autoplay = 'true'; }
            if ($skinautohide == 0){ $skinautohide = 'false'; } else { $skinautohide = 'true'; }
            if ($fullscreen == 0){ $fullscreen = 'false'; } else { $fullscreen = 'true'; }
            if ($zoom == 0){ $zoom = 'false'; } else { $zoom = 'true'; }
            if ($timer == 0){ $timer = 'false'; } else { $timer = 'true'; }
            if ($share == 0){ $share = 'false'; } else { $share = 'true'; }
            if ($playlistopen == 0){ $playlist_open = 'false'; } else { $playlist_open = 'true'; }
        }
      
        ## Generate playxml path based on Module and component settings
        if ($moduleid != '') {
            if ($playid != '') {
                $playlistxml = $base . 'index.php?option=com_hdflvplayer&task=playxml&id=' . $playid . '&mid=' . $moduleid;
            } else {
                $playlistxml = $base . 'index.php?option=com_hdflvplayer&task=playxml&playid=' . $playid_playlistname . '&mid=' . $moduleid;
            }
        } elseif ($playid_playlistname != '' && $playid != 0) {
            $playlistxml = $base . 'index.php?option=com_hdflvplayer&task=playxml&playid=' . $playid_playlistname . '&id=' . $playid;
        } elseif ($playid != 0) {
            $playlistxml = $base . 'index.php?option=com_hdflvplayer&task=playxml&id=' . $playid;
        }

        if ($playid_playlistname == 'true' && $moduleid == '') {
            $playlistxml = $base . 'index.php?option=com_hdflvplayer&task=playxml&id=' . $playid . '&playid=true';
        }
        if ($comppid != '') {
            if ($playid != '') {
                $playlistxml = $base . 'index.php?option=com_hdflvplayer&task=playxml&compid=' . $comppid . '&id=' . $playid;
            }
            else
                $playlistxml = $base . 'index.php?option=com_hdflvplayer&task=playxml&compid=' . $comppid;
        }

        ## Generates configxml here
        header("content-type:text/xml;charset=utf-8");
        echo '<?xml version="1.0" encoding="utf-8"?>';
        echo '<config>
                <stagecolor>' . $player_values['stagecolor'] . '</stagecolor>
                <autoplay>' . $autoplay . '</autoplay>
                <buffer>' . $player_values['buffer'] . '</buffer>
                <volume>' . $player_values['volume'] . '</volume>
                <normalscale>' . $player_values['normalscale'] . '</normalscale>
                <fullscreenscale>' . $player_values['fullscreenscale'] . '</fullscreenscale>
                <license>' . $player_values['licensekey'] . '</license>
                <logopath>' . $logopath . '</logopath>
                <logoalpha>' . $player_values['logoalpha'] . '</logoalpha>
                <logoalign>' . $player_values['logoalign'] . '</logoalign>
                <logo_target>' . $target_url . '</logo_target>
                <sharepanel_up_BgColor>' . $player_colors['sharepanel_up_BgColor'] . '</sharepanel_up_BgColor>
                <sharepanel_down_BgColor>' . $player_colors['sharepanel_down_BgColor'] . '</sharepanel_down_BgColor>
                <sharepaneltextColor>' . $player_colors['sharepaneltextColor'] . '</sharepaneltextColor>
                <sendButtonColor>' . $player_colors['sendButtonColor'] . '</sendButtonColor>
                <sendButtonTextColor>' . $player_colors['sendButtonTextColor'] . '</sendButtonTextColor>
                <textColor>' . $player_colors['textColor'] . '</textColor>
                <skinBgColor>' . $player_colors['skinBgColor'] . '</skinBgColor>
                <seek_barColor>' . $player_colors['seek_barColor'] . '</seek_barColor>
                <buffer_barColor>' . $player_colors['buffer_barColor'] . '</buffer_barColor>
                <skinIconColor>' . $player_colors['skinIconColor'] . '</skinIconColor>
                <pro_BgColor>' . $player_colors['pro_BgColor'] . '</pro_BgColor>
                <playButtonColor>' . $player_colors['playButtonColor'] . '</playButtonColor>
                <playButtonBgColor>' . $player_colors['playButtonBgColor'] . '</playButtonBgColor>
                <playerButtonColor>' . $player_colors['playerButtonColor'] . '</playerButtonColor>
                <playerButtonBgColor>' . $player_colors['playerButtonBgColor'] . '</playerButtonBgColor>
                <relatedVideoBgColor>' . $player_colors['relatedVideoBgColor'] . '</relatedVideoBgColor>
                <scroll_barColor>' . $player_colors['scroll_barColor'] . '</scroll_barColor>
                <scroll_BgColor>' . $player_colors['scroll_BgColor'] . '</scroll_BgColor>
                <skin>' . $skinPath . '</skin>
                <skin_autohide>' . $skinautohide . '</skin_autohide>
                <languageXML>' . $languagexml . '</languageXML>
                <registerpage>' . $player_values['urllink'] . '</registerpage>
                <playlistXML>' . $playlistxml . '</playlistXML>
                <playlist_open>' . $playlist_open . '</playlist_open>
                <showPlaylist>' . $playlist . '</showPlaylist>
                <HD_default>' . $hddefault . '</HD_default>
                <adXML>' . $adsxml . '</adXML>
                <preroll_ads>' . $prerollads . '</preroll_ads>
                <postroll_ads>' . $postrollads . '</postroll_ads>
                <midrollXML>' . $midadsxml . '</midrollXML>
                <midroll_ads>' . $midrollads . '</midroll_ads>
                <shareURL>' . $emailpath . '</shareURL>
                <embed_visible>' . $embedcode_visible . '</embed_visible>
                <Download>' . $playerDownload . '</Download>
                <downloadUrl>' . $downloadPath . '</downloadUrl>
                <adsSkip>' . $adsSkip . '</adsSkip>
                <adsSkipDuration>' . $player_values['adsSkipDuration'] . '</adsSkipDuration>
                <relatedVideoView>' . $player_values['relatedVideoView'] . '</relatedVideoView>
                <imaAds>' . $imaAds . '</imaAds>
                <imaAdsXML>' . $imaAdsXML . '</imaAdsXML>
                <trackCode>' . $trackCode . '</trackCode>
                <showTag>' . $showTag . '</showTag>
                <timer>' . $timer . '</timer>
                <zoomIcon>' . $zoom . '</zoomIcon>
                <email>' . $playerEmail . '</email>
                <shareIcon>' . $share . '</shareIcon>
                <fullscreen>' . $fullscreen . '</fullscreen>
                <volumecontrol>' . $volumecontrol . '</volumecontrol>
                <playlist_auto>' . $playlist_autoplay . '</playlist_auto>
                <progressControl>' . $progressControl . '</progressControl>
                <imageDefault>' . $imageDefault . '</imageDefault>
                </config>';
        exit();
    }                   ## Config XML function ends here
}                       ## Config XML class ends here
?>