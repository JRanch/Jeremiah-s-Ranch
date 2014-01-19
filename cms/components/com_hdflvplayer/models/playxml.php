<?php
/**
 * @name 	        playxml.php
 ** @version	        2.1.0.1
 * @package	        Apptha
 * @since	        Joomla 1.5
 * @author      	Apptha - http://www.apptha.com/
 * @copyright 		Copyright (C) 2011 Powered by Apptha
 * @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @abstract      	Contus HD FLV Player Playlist XML file
 * @Creation Date	23 Feb 2011
 * @modified Date	28 Aug 2013
 */
## No direct acesss
defined('_JEXEC') or die();

## imports joomla libraries
jimport('joomla.application.component.model');
jimport('joomla.application.component.modellist');
jimport('joomla.html.parameter');

/*
 * hdflvplayer model Class for generate playxml
 */

class hdflvplayerModelplayxml extends HdflvplayerModel {

    function playgetrecords() {

        ## Variable declarations here
        $db = JFactory::getDBO();
        $playlistid = $mid = $itemid = $moduleid = $id = $videoid = 0;
        $rs_modulesettings = '';
        $playlistautoplay = $postrollads = $prerollads = $home_bol = $playlistrandom = 'false';
        
        ## Getting necessary inputs from URL
        $moduleid           = JRequest::getvar('mid', '', 'get', 'int');
        $playlistid         = JRequest::getvar('playid', '', 'get', 'var');
        $videoid            = JRequest::getvar('id', '', 'get', 'int');
        $compid             = JRequest::getvar('compid', '', 'get', 'int');

        ## Getting necessary inputs from URL if Joomla version is 3.0
        if (version_compare(JVERSION, '3.0.0', 'ge')) {
            $moduleid       = JRequest::getvar('mid');
            $playlistid     = JRequest::getvar('playid');
            $videoid        = JRequest::getvar('id');
            $compid         = JRequest::getvar('compid');
        }
        
        ## Fetch player settings
        $qry_settings = 'SELECT `player_icons` FROM `#__hdflvplayersettings`';
        $db->setQuery($qry_settings);
        $rs_settings = $db->loadResult();

        ## Fetch autoplay, description visibility settings
        if (!empty($rs_settings)) {
            $player_icons           = unserialize($rs_settings);
            if ($player_icons['playlist_autoplay'] == 1) {
                $playlistautoplay = 'true';
            } else {
                $playlistautoplay = 'false';
            }
        }

        ## Checks whether Module's video then, fetch settings from module params.
        if ($moduleid != 0) {
            $moduleid = $moduleid;
            $query = 'SELECT params FROM `#__modules` WHERE id=' . $moduleid;
            $db->setQuery($query);
            $rs_modulesettings = $db->loadResult();
            $app = JFactory::getApplication();
            
            ## Ferch Module Parameters
            $params = $app->getParams('mod_hdflvplayer');
            if (!version_compare(JVERSION, '3.0.0', 'ge')) {
                $aparams = new JParameter($rs_modulesettings);
            } else {
                $aparams = new JRegistry($rs_modulesettings);
            }
            ## Merge params
            $params->merge($aparams);
            
            $playlist = $params->get('playlistauto');
            if ($playlist == 0) {
                $playlistautoplay = 'false';
            } else {
                $playlistautoplay = 'true';
            }
        }

        ## getting playlist id
        if ($compid) {
            $playlistid = $compid;
        }

        ## Fetch video details
        if ($videoid != '') {
            
            ## Fetch the particular video detail
            $query = 'SELECT a.`id`, a.`title`, a.`filepath`, a.`times_viewed`, a.`videourl`,a.`thumburl`, a.`previewurl`, a.`hdurl`,
                    a.`playlistid`,a.`streamerpath`, a.`streameroption`, a.`postrollads`, a.`prerollads`, a.`midrollads`,a.`imaads`,
                    a.`description`,a.`targeturl`, a.`download`, a.`prerollid`, a.`postrollid`, a.`access`, a.`islive`,
                    b.`name`,c.`adsname`,c.`typeofadd`
                    FROM `#__hdflvplayerupload` AS a
                    LEFT JOIN #__hdflvplayername AS b ON a.playlistid=b.id
                    LEFT JOIN #__hdflvplayerads AS c ON a.prerollid = c.id
                    LEFT JOIN #__hdflvplayerads AS d ON a.postrollid = d.id
                    WHERE a.published=1 AND a.id=' . $videoid;
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            
            ## If video exist, then fetch related details
            if (count($rows) > 0) {
                $query = 'SELECT a.`id`, a.`title`, a.`filepath`, a.`times_viewed`, a.`videourl`,a.`thumburl`, a.`previewurl`, a.`hdurl`,
                        a.`playlistid`,a.`streamerpath`, a.`streameroption`, a.`postrollads`, a.`prerollads`, a.`midrollads`,a.`imaads`,
                        a.`description`,a.`targeturl`, a.`download`, a.`prerollid`, a.`postrollid`, a.`access`, a.`islive`,
                        b.`name`,c.`adsname`,c.`typeofadd`
                        FROM `#__hdflvplayerupload` AS a LEFT JOIN #__hdflvplayername AS b ON a.playlistid=b.id
                        LEFT JOIN #__hdflvplayerads AS c ON a.prerollid = c.id
                        LEFT JOIN #__hdflvplayerads AS d ON a.postrollid = d.id
                        WHERE a.published=1 AND a.id !=' . $videoid . ' AND b.id=' . $rows[0]->playlistid;
                $db->setQuery($query);
                $playlist = $db->loadObjectList();
            }
        }

        ## if playlist id exist, fetch the vidoes and the playlist detail
        else if ($playlistid != '') {
            if ($playlistid == 0) {
                $where = ' ORDER BY ordering ASC';
            } else {
                $where = ' AND a.playlistid=' . $playlistid . ' ORDER BY ordering ASC';
            }
            $query = 'SELECT a.`id`,  a.`title`, a.`filepath`, a.`times_viewed`, a.`videourl`, a.`thumburl`, a.`previewurl`, a.`hdurl`,
                    a.`playlistid`,a.`streamerpath`, a.`streameroption`, a.`postrollads`, a.`prerollads`, a.`midrollads`,a.`imaads`,
                    a.`description`,a.`targeturl`, a.`download`, a.`prerollid`, a.`postrollid`, a.`access`, a.`islive`,
                    b.`name`,c.`adsname`,c.`typeofadd`
                    FROM `#__hdflvplayerupload` AS a LEFT JOIN #__hdflvplayername AS b ON a.playlistid=b.id
                    LEFT JOIN #__hdflvplayerads AS c ON a.prerollid = c.id
                    LEFT JOIN #__hdflvplayerads AS d ON a.postrollid = d.id
                    WHERE a.published=1 ' . $where;

            $db->setQuery($query);
            $playlist = $db->loadObjectList();
        } else {
            ## Query to fetch default video
            $query = 'SELECT a.`id`,  a.`title`, a.`filepath`, a.`times_viewed`, a.`videourl`, a.`thumburl`, a.`previewurl`, a.`hdurl`,
                    a.`playlistid`,a.`streamerpath`, a.`streameroption`, a.`postrollads`, a.`prerollads`, a.`midrollads`,a.`imaads`,
                    a.`description`,a.`targeturl`, a.`download`, a.`prerollid`, a.`postrollid`, a.`access`, a.`islive`,
                    b.`name`,c.`adsname`,c.`typeofadd`
                    FROM `#__hdflvplayerupload` AS a LEFT JOIN #__hdflvplayername AS b ON a.playlistid=b.id
                    LEFT JOIN #__hdflvplayerads AS c ON a.prerollid = c.id
                    LEFT JOIN #__hdflvplayerads AS d ON a.postrollid = d.id
                    WHERE a.published=1  AND a.home=1';
            $db->setQuery($query);
            $rows = $db->loadObjectList();

            ## If default video not exist then, fetch 1st video
            if (empty($rows)) {
                $query = 'SELECT a.`id`,  a.`title`, a.`filepath`, a.`times_viewed`, a.`videourl`, a.`thumburl`, a.`previewurl`, a.`hdurl`,
                        a.`playlistid`,a.`streamerpath`, a.`streameroption`, a.`postrollads`, a.`prerollads`, a.`midrollads`,a.`imaads`,
                        a.`description`,a.`targeturl`, a.`download`, a.`prerollid`, a.`postrollid`, a.`access`, a.`islive`,
                        b.`name`,c.`adsname`,c.`typeofadd`
                        FROM `#__hdflvplayerupload` AS a LEFT JOIN #__hdflvplayername AS b ON a.playlistid=b.id
                        LEFT JOIN #__hdflvplayerads AS c ON a.prerollid = c.id
                        LEFT JOIN #__hdflvplayerads AS d ON a.postrollid = d.id
                        WHERE a.published=1  ORDER BY a.ordering ASC';
                $db->setQuery($query);
                $rows = $db->loadObjectList();
            }
            if (count($rows) > 0) {
                $query = 'SELECT a.`id`, a.`title`, a.`filepath`, a.`times_viewed`, a.`videourl`,a.`thumburl`, a.`previewurl`, a.`hdurl`,
                        a.`playlistid`,a.`streamerpath`, a.`streameroption`, a.`postrollads`, a.`prerollads`, a.`midrollads`,a.`imaads`,
                        a.`description`,a.`targeturl`, a.`download`, a.`prerollid`, a.`postrollid`, a.`access`, a.`islive`,
                        b.`name`,c.`adsname`,c.`typeofadd`
                        FROM `#__hdflvplayerupload` AS a LEFT JOIN #__hdflvplayername AS b ON a.playlistid=b.id
                        LEFT JOIN #__hdflvplayerads AS c ON a.prerollid = c.id
                        LEFT JOIN #__hdflvplayerads AS d ON a.postrollid = d.id
                        WHERE a.published=1 AND a.id !=' . $rows[0]->id . ' AND b.id=' . $rows[0]->playlistid;

                $db->setQuery($query);
                $playlist = $db->loadObjectList();
            }
        }
        ## assigns the video details into common variable, if playlist exist or video exist.
        if (count($rows) > 0) {
            $rs_video = array_merge($rows, $playlist);
        } else {
            $rs_video = $playlist;
        }

        ## function calling for generate playxml
        $this->showxml($rs_video, $playlistautoplay);
    }

    ## Function to generate playxml
    function showxml($rs_video, $playlistautoplay) {
    ## xml file header displaying here
        ob_clean();
        header("content-type: text/xml");
        echo '<?xml version="1.0" encoding="utf-8"?>';
        echo '<playlist autoplay="' . $playlistautoplay . '" random="false">';
        $current_path = 'components/com_hdflvplayer/videos/';
        $hdvideo = $video = '';

        $db = JFactory::getDBO();

        $query_ads = "SELECT count(id) FROM #__hdflvplayerads WHERE published=1 AND typeofadd='mid' "; ## and home=1";## and id=11;";
        $db->setQuery($query_ads);
        $midadsCount = $db->loadResult();


        ## Check whether or not, video available
        if (count($rs_video) > 0) {

            foreach ($rs_video as $rows) {
                $timage = '';
                $streamername = '';

                ## fetch Video, thumb, Preview,HD URL for upload method videos
                if ($rows->filepath == 'File' || $rows->filepath == 'FFmpeg') {

                    ## Get Video URL
                    if ($rows->videourl != '') {
                        $video = JURI::base() . $current_path . $rows->videourl;
                    } else {
                        $video = '';
                    }
                    
                    ## Get Video HD URL
                    if ($rows->hdurl != '') {
                        $hdvideo = JURI::base() . $current_path . $rows->hdurl ;
                    } else {
                        $hdvideo = '';
                    }
                    
                    ## Get Preview image URL
                    $previewimage = JURI::base() . $current_path . $rows->previewurl;
                    
                    ## Get Thumb Image URL
                    $timage = JURI::base() . $current_path . $rows->thumburl;
                }

                ## fetch Video, thumb, Preview,HD URL for URL method videos
                elseif ($rows->filepath == 'Url') {
                    $video          = $rows->videourl;
                    $previewimage   = $rows->previewurl;
                    $timage         = $rows->thumburl;
                    $hdvideo        = $rows->hdurl;
                }

                ## fetch Video, thumb, Preview,HD URL for YouTube method videos
                elseif ($rows->filepath == 'Youtube') {
                    $video = $rows->videourl;
                    $previewimage = $rows->previewurl;
                    $timage = $rows->thumburl;
                    if ($rows->hdurl != '') {
                        $hdvideo = $rows->hdurl;
                    }
                }

                ## Checks for streamer option
                if ($rows->streameroption == 'lighttpd') {      ## If it lighttpd
                    $streamername = $rows->streameroption;
                } else {
                    $streamername = $rows->streamerpath;
                }
                if ($rows->streameroption == 'rtmp') {          ## If it RTMP
                    $streamername = $rows->streamerpath;
                } else {
                    $streamername = '';
                }

                ## Checks for postroll ads enabled
                if ($rows->postrollid != '') {
                    if ($rows->postrollads == 0) {
                        $postrollads = 'false';
                    } else {
                        $postrollads = 'true';
                    }
                } else {
                    $postrollads = 'false';
                }

                ## Checks for preroll ads enabled
                if ($rows->prerollid != '') {
                    if ($rows->prerollads == 0) {
                        $prerollads = "false";
                    } else {
                        $prerollads = "true";
                    }
                } else {
                    $prerollads = "false";
                }

                ## Checks for Mid-roll ad
                if ($midadsCount > 0) {
                    if ($rows->midrollads == 0) {
                        $midrollads = 'false'; 
                    } else {
                        $midrollads = 'true';
                    }
                } else {
                    $midrollads = 'false';
                }
                
                ## Checks for IMA ad
                $imaads = $rows->imaads;
                    if ($imaads == 0) {
                        $imaad = 'false'; 
                    } else {
                        $imaad = 'true';
                    }

                ## Fetche Download, target URL, Post-roll, Pre-roll Ad Ids
                if ($rows->download == 0) {
                    $download = 'false';
                } else {
                    $download = 'true';
                }
                if ($rows->targeturl == '') {
                    $targeturl = '';
                } else {
                    $targeturl = $rows->targeturl;
                }
                if ($rows->postrollads == '1') {
                    $postrollid = $rows->postrollid;
                } else {
                    $postrollid = 0;
                }
                if ($rows->prerollads == '1') {
                    $prerollid = $rows->prerollid;
                } else {
                    $prerollid = 0;
                }

                $user = JFactory::getUser();
                $memberid = $user->get('id');
                
                ## Checks for member Access
                if (version_compare(JVERSION, '1.6.0', 'ge')) {
                    $uid = $user->get('id');
                    if ($uid) {
                        $query = $db->getQuery(true);
                        $query->select('g.id AS group_id')
                                ->from('#__usergroups AS g')
                                ->leftJoin('#__user_usergroup_map AS map ON map.group_id = g.id')
                                ->where('map.user_id = ' . (int) $uid);
                        $db->setQuery($query);
                        $message = $db->loadObjectList();
                        foreach ($message as $mess) {
                            $accessid[] = $mess->group_id;
                        }
                    } else {
                        $accessid[] = 1;
                    }
                } else {
                    $accessid = $user->get('aid');
                }

                if (version_compare(JVERSION, '1.6.0', 'ge')) {
                    $query = $db->getQuery(true);
                    if ($rows->access == 0)
                        $rows->access = 1;
                    $query->select('rules as rule')
                            ->from('#__viewlevels AS view')
                            ->where('id = ' . (int) $rows->access);
                    $db->setQuery($query);
                    $message = $db->loadResult();
                    $accessLevel = json_decode($message);
                }

                $member = "true";

                if (version_compare(JVERSION, '1.6.0', 'ge')) {
                    $member = "false";
                    foreach ($accessLevel as $useracess) {
                        if (in_array("$useracess", $accessid) || $useracess == 1) {
                            $member = "true";
                            break;
                        }
                    }
                } else {
                    if ($rows->access != 0) {
                        if ($accessid != $rows->access && $accessid != 2) {
                            $member = "false";
                        }
                    }
                }

                ## Checks for Islive
                $islive = 'false';
                if ($streamername != '') {
                    ($rows->islive == 1) ? $islive = 'true' : $islive = 'false';
                }
                if (!preg_match('/vimeo/', $video)) {

                    echo '<mainvideo member="' . $member . '" uid="'.$memberid.'" 
                            views="' . $rows->times_viewed . '"
                            streamer_path="' . $streamername . '"
                            video_isLive="' . $islive . '"
                            video_id = "' . htmlspecialchars($rows->id) . '"
                            video_url = "' . htmlspecialchars($video) . '"
                            thumb_image = "' . htmlspecialchars($timage) . '"
                            preview_image = "' . htmlspecialchars($previewimage) . '"
                            allow_midroll = "' . $midrollads . '"
                            allow_ima = "' . $imaad . '"
                            allow_postroll = "' . $postrollads . '"
                            allow_preroll = "' . $prerollads . '"
                            postroll_id = "' . $postrollid . '"
                            preroll_id = "' . $prerollid . '"
                            allow_download = "' . $download . '"
                            video_hdpath = "' . $hdvideo . '"
                            copylink = "">
                            <title><![CDATA[' . htmlspecialchars($rows->title) . ']]></title>
                            <tagline targeturl=""><![CDATA[' . htmlspecialchars(strip_tags($rows->description)) . ']]></tagline>
                            </mainvideo>';
                }
            }
        }
        echo '</playlist>';
        exit();
    }
}
?>