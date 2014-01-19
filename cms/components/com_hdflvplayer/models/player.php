<?php
/**
 * @name 	        player.php
 ** @version	        2.1.0.1
 * @package	        Apptha
 * @since	        Joomla 1.5
 * @author      	Apptha - http://www.apptha.com/
 * @copyright 		Copyright (C) 2011 Powered by Apptha
 * @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @abstract      	Contus HD FLV Player player model file
 * @Creation Date	23 Feb 2011
 * @modified Date	28 Aug 2013
 */

## No direct acesss
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

/*
 * HDFLV player Model class to fetch Video details to display player
 */
class hdflvplayerModelplayer extends HdflvplayerModel
{

	function showhdplayer()
	{
		$playid             = 0;
		$playlistid         = $compid = '';
		$db                 = JFactory::getDBO();
		$thumbid            = $pwhere ='';

		## Function call to get player settings.
		$settingsrows       = $this->getplayersettings();

		## Query for fetch Playlists
		$query              = 'SELECT `id`, `name` FROM `#__hdflvplayername` WHERE published=1';
		$db->setQuery( $query );
		$rs_playlistname    = $db->loadObjectList();

		## Fetch Playlist ID from Module Parameter
		$params             = JComponentHelper::getParams( 'com_hdflvplayer' );
		$playlistnameid     = $params->get('playlistnameid');
		if(isset($playlistnameid))
		{
			if($playlistnameid != 0)
			$playlistid = $playlistnameid;
		}

                if(!version_compare(JVERSION, '3.0.0', 'ge')) {
		$playid             = JRequest::getvar('id','','get','var');
                
                ## Fetch Playlist ID from URL
		$compid             = JRequest::getvar('compid','','get','int');
                }else{
                  $playid           = JRequest::getvar('id');

                  ## Fetch Playlist ID from URL
		$compid             = JRequest::getvar('compid');
                }
		if($compid != '')
		{
			$playlistid = $compid;
		}

		## If Video ID available, then fetch that video details
		if($playid!='')
		{
		 $query             = 'SELECT `id`, `title`, `times_viewed`, `filepath`, `videourl`, `thumburl`, `previewurl`, `hdurl`,
                                    `home`, `playlistid`,`streamerpath`, `streameroption`, `postrollads`, `prerollads`, `midrollads`, `description`,
                                    `targeturl`, `download`, `prerollid`, `postrollid`, `access`, `islive` FROM `#__hdflvplayerupload`
                                     WHERE published=1 AND id='.$playid.' ORDER BY ordering ASC ';
		$db->setQuery( $query );
		$rows               = $db->loadObject();

		}
		else if($playlistid != '' && $playlistid != 0 )
		{
		## Fetch Videos of the selected Playlist
		$query              = 'SELECT `id`, `title`, `times_viewed`, `filepath`, `videourl`, `thumburl`, `previewurl`,
                                    `hdurl`, `home`, `playlistid`, `streamerpath`, `streameroption`, `postrollads`,
                                    `prerollads`, `midrollads`, `description`, `targeturl`, `download`, `prerollid`, `postrollid`, `access`, `islive`
                                     FROM `#__hdflvplayerupload`
                                     WHERE published=1 AND playlistid='.$playlistid.' ORDER BY ordering ASC';
		$db->setQuery( $query );
		$rows               = $db->loadObject();
		}
		## Else fetch default video or 1st video details from table.
		else
		{
		## Query to fetch default video
		$query              = 'SELECT `id`, `title`, `times_viewed`, `filepath`, `videourl`, `thumburl`, `previewurl`, `hdurl`,
                                    `home`, `playlistid`,`streamerpath`, `streameroption`, `postrollads`, `prerollads`, `midrollads`, `description`,
                                    `targeturl`, `download`, `prerollid`, `postrollid`, `access`, `islive` FROM `#__hdflvplayerupload`
                                     WHERE published=1 AND home=1 ORDER BY ordering ASC limit 1';
                $db->setQuery( $query );
                $rows_home          = $db->loadObject();

			## If default video not exist then, fetch 1st video
			if(empty($rows_home))
			{
				$query = 'SELECT `id`, `title`, `times_viewed`, `filepath`, `videourl`, `thumburl`, `previewurl`, `hdurl`,
                                        `home`, `playlistid`,`streamerpath`, `streameroption`, `postrollads`, `prerollads`, `midrollads`, `description`,
                                        `targeturl`, `download`, `prerollid`, `postrollid`, `access`, `islive` FROM `#__hdflvplayerupload`
                                         WHERE published=1 ORDER BY ordering ASC LIMIT 1';
				$db->setQuery( $query );
				$rows = $db->loadObject();
			}
			else{
				$rows = $rows_home;
			}

		}


		if (!empty($rows)) {
			$thumbid    = $rows->id;
                        $playid     = $rows->id;
			## Update Viewed count for Video
			$query      = 'UPDATE #__hdflvplayerupload SET times_viewed=1+times_viewed WHERE id='.$playid;
			$db->setQuery($query );
			$db->query();
			$playid     = $rows->id;
		}
		## If Playlist Id available
		if($playlistid != '' && $playlistid != 0 )
		{

                 if($playid!='')
                 {
                     $pwhere        = ' AND id NOT IN ('.$playid.')';
                 }
                 $query             = 'SELECT count(`id`) FROM `#__hdflvplayerupload`
                                        WHERE published=1 AND playlistid='.$playlistid. $pwhere.'  ORDER BY ordering ASC';
                $db->setQuery( $query );
		$rs_playlist_count = $db->loadResult();

                $limit_query        = 'SELECT count(`id`) FROM `#__hdflvplayerupload`
                                     WHERE published=1 AND playlistid='.$playlistid. $pwhere.' ORDER BY ordering ASC ';

		## Fetch Videos of the selected Playlist
		$query1              = 'SELECT `id`, `title`, `times_viewed`, `filepath`, `videourl`, `thumburl`, `previewurl`,
                                    `hdurl`, `home`, `playlistid`, `streamerpath`, `streameroption`, `postrollads`,
                                    `prerollads`, `midrollads`, `description`, `targeturl`, `download`, `prerollid`, `postrollid`, `access`, `islive`
                                     FROM `#__hdflvplayerupload`
                                     WHERE published=1 AND playlistid='.$playlistid. $pwhere.' ORDER BY ordering ASC ';
		}
		## Fetch the remaining videos
		else if($playid!='')
		{
                $query              = 'SELECT `playlistid` FROM `#__hdflvplayerupload` WHERE published=1 and id='.$playid;
		$db->setQuery( $query );
		$playlistId_video   = $db->loadResult();
                
                $limit_query        = 'SELECT count(`id`) FROM `#__hdflvplayerupload` WHERE published=1 AND id NOT IN ('.$playid.') AND playlistid='.$playlistId_video.' ORDER BY ordering ASC ';

                 $query1             = 'SELECT `id`, `title`, `times_viewed`, `filepath`, `videourl`, `thumburl`, `previewurl`,
                                    `hdurl`, `home`, `playlistid`, `streamerpath`, `streameroption`, `postrollads`,
                                    `prerollads`, `midrollads`, `description`, `targeturl`, `download`, `prerollid`, `postrollid`, `access`,
                                    `islive` FROM `#__hdflvplayerupload` WHERE published=1 AND id NOT IN ('.$playid.') AND playlistid='.$playlistId_video.'
                                    ORDER BY ordering ASC ';
		}
                $length             = 1;
		$start              = 0;
                if(!empty($limit_query)){
                $db->setQuery( $limit_query );
		$rs_playlist_count  = $db->loadResult();
                } else {
                    $rs_playlist_count = 0;
                }

		## Fetch Total No.of Videos for pagination
		if($rs_playlist_count > 0)
		{
				$total = $rs_playlist_count;
		}
		else
		{
                                $total = 0;
		}
                ## Pagination variables initialize here
		$pageno = 1;

                if(!version_compare(JVERSION, '3.0.0', 'ge')) {
                    $page = JRequest::getvar('page','','get','int');
                }else{
                    $page = JRequest::getvar('page');
                }

		if($page)
		{
                    $pageno = $page;
		}
                $player_values = unserialize($settingsrows->player_values);
		## Fetch No.of Related Videos per page from Settings
		if($player_values['nrelated'] != '')
		{
                    $length = $player_values['nrelated'];
		}
		else
		{
                    $length = 4;
		}

		## If not settings available, default value.
		if($length == 0)
		{
                    $length = 1;
		}

		$pages = ceil($total/$length);
		if($pageno == 1)
		{
                    $start = 0;
		}
		else
		{
                    $start = ($pageno - 1) * $length;
		}
                if(!empty($query1)){
                $query1          = $query1.'LIMIT '.$start.','.$length;
		$db->setQuery( $query1 );
		$rs_playlist    = $db->loadobjectList();
                } else {
                    $rs_playlist = array();
                }

		## Fetch the URL
		$playerpath     = JURI::base().'components/com_hdflvplayer/hdflvplayer/hdplayer.swf';
		$baseurl        = str_replace(':','%3A',JURI::base());
		$baseurl        = substr_replace($baseurl ,"",-1);
		$baseurl        = str_replace('/','%2F',$baseurl);

		$emailpath      = JURI::base()."/index.php?option=com_hdflvplayer&task=email";
		$youtubeurl     = JURI::base()."/index.php?option=com_hdflvplayer&task=youtubeurl&url=";
		$logopath       = JURI::base()."/components/com_hdflvplayer/videos/".$settingsrows->logopath;
		$playlistXML    = '';

		## Fetch the Google Ads info from Table
		$query          = 'SELECT `id`, `code`, `showoption`, `closeadd`, `reopenadd`, `publish`, `ropen`, `showaddc`, `showaddm`, `showaddp` FROM `#__hdflvaddgoogle` WHERE publish=1 AND id=1';
		$db->setQuery( $query );
		$fields         = $db->loadObject();

		## Assigns info into one array and returns
		if(!empty($fields))
		{
		$insert_data_array = array('playerpath' => $playerpath,'baseurl'=>$baseurl,'thumbid'=>$thumbid,'rs_playlist'=>$rs_playlist,'length'=>$length,'total'=>$total,'closeadd'=>$fields->closeadd,'showoption'=>$fields->showoption,'reopenadd'=>$fields->reopenadd,'ropen'=>$fields->ropen,'publish'=>$fields->publish,'showaddc'=>$fields->showaddc,'rs_playlistname'=>$rs_playlistname,'rs_title'=>$rows);
		}
		else
		{
		$insert_data_array = array('playerpath' => $playerpath,'baseurl'=>$baseurl,'thumbid'=>$thumbid,'rs_playlist'=>$rs_playlist,'length'=>$length,'total'=>$total,'closeadd'=>'','showoption'=>'','reopenadd'=>'','ropen'=>'','publish'=>'','showaddc'=>'','rs_playlistname'=>$rs_playlistname,'rs_title'=>$rows);
		}

		$settingsrows =  $insert_data_array;
		return $settingsrows;

	}

	## Function to fetch Player settings
	function getplayersettings()
	{
		## Query to fetch necessary settings.
		$db     = JFactory::getDBO();
		$query  = 'SELECT  `player_icons` , `player_values`,`logopath`
        		  FROM `#__hdflvplayersettings`';
		$db->setQuery( $query );
		$settingsrows = $db->loadObject();
		return $settingsrows;
	}
        /* function to get html video access level */
	function getHTMLVideoAccessLevel($vid){
		$user = JFactory::getUser();
                $db     = JFactory::getDBO();
                $query = 'SELECT `access` FROM `#__hdflvplayerupload`
                   WHERE id =' . $vid . ' AND published=1';
                $db->setQuery($query);
                $rows = $db->loadResult();
            
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
                    if ($rows == 0)
                        $rows = 1;
                    $query->select('rules as rule')
                            ->from('#__viewlevels AS view')
                            ->where('id = ' . (int) $rows);
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
                    if ($rows != 0) {
                        if ($accessid != $rows && $accessid != 2) {
                            $member = "false";
                        }
                    }
                }
                return $member;
	}
}