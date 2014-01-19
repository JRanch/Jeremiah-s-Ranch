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
 * @Creation Date	23 Feb 2011
 * @modified Date	28 Aug 2013
 */

## no direct access
defined('_JEXEC') or die('Access Denied!');

## Imports joomla libraries
jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter');
jimport('joomla.application.component.controller');

?>
<script type="text/javascript">
    function currentvideo(id, title, descr) {

        var xmlhttp;
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        }
        else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function()
        {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            }
        }
        xmlhttp.open("GET", "index.php?option=com_hdflvplayer&task=addview&thumbid=" + id, true);
        xmlhttp.send();
        var wndo = new dw_scrollObj('wn', 'lyr1');
        wndo.setUpScrollbar("dragBar", "track", "v", 1, 1);
        wndo.setUpScrollControls('scrollbar');
    }
</script>
<?php

## HDFLV Player plugin Class
class plgContenthdflvplayer extends JPlugin {

    function plgContenthdflvplayer(&$subject, $params) {
        parent::__construct($subject, $params);
    }

    function getPluginParams() {
        static $plgParams;

        if (!empty($plgParams)) {
            return $plgParams;
        }

        ## PARAMs
        $plugin     = & JPluginHelper::getPlugin('content', 'hdflvplayer');
        $plgParams  = new JParameter($plugin->params);

        ## Fetch Width, Height param Values
        $height     = $plgParams->get('height');
        $width      = $plgParams->get('width');

        ## Path to plugin folder
        $plgParams->set('dir_plg', JPATH_COMPONENT_ADMINISTRATOR . DS . 'content' . DS . 'hdflvplayer' . DS);
        $plgParams->set('uri_plg', JURI::base() . 'plugins/content/hdflvplayer/');

        ## Path to default videos folder
        $defdir = $plgParams->get('defaultdir', 'components/com_hdflvplayer/videos');
        if (!eregi('http://', $defdir)) {
            $defdir = JURI::base() . $defdir;
            $plgParams->get('defaultdir', $defdir);
        }
        $plgParams->set('uri_img', $defdir);

        return $plgParams;
    }

    ## Function to remove the spaces
    function removesextraspace($str1) {
        $str2 = trim(str_replace("]", "", (trim($str1))));
        return $str2;
    }

    ## Function to call onPrepareContent with Article and params.
    function onContentPrepare($context, &$article, &$params, $page = 0) {
        $this->onPrepareContent($article, $params, $page);
    }

    ## Function to fetch Article content and necessary inputs for player
    function onPrepareContent(&$row, &$params, $limitstart) {
        $db = JFactory::getDBO();

        ## Fetch Inputs for Player from article content.
        $regexwidth = '/\[hdplay videoid(.*?)]/i';
        preg_match_all($regexwidth, $row->text, $matches);

        $widthm     = $matches[0];
        $cnt        = count($widthm);

        $width      = 0;
        $height     = 0;
        $enablexml  = 0;
        $filepath = $videos = $thumImg = '';
        for ($i = 0; $i < $cnt; $i++) {
            $strwhole = $widthm[$i];

            $playname   = '';
            $autoplay   = 'false';
            $width      = 0;
            $height     = 0;
            $enablexml  = 0;

            ## Fetch No.of Inputs given
            $no = explode(" ", $strwhole);

            ## Fetch Width, Height,Playlist Id, Video Id, Autoplay values from given content.
            for ($k = 0; $k < count($no); $k++) {
                $str = $no[$k];
                if (strstr($str, 'videoid')) {
                    $fileidarr  = explode("=", $str);
                    $idval      = $this->removesextraspace(trim($fileidarr[1]));
                    $idval      = rtrim($idval);
                }
                if (strstr($str, 'width')) {
                    $widtharr   = explode("=", $no[$k]);
                    $width      = $this->removesextraspace(trim($widtharr[1]));
                }
                if (strstr($str, 'height')) {
                    $heightarr  = explode("=", $no[$k]);
                    $height     = $this->removesextraspace(trim($heightarr[1]));
                }
                if (strstr($str, 'playlist')) {
                    $playlistarr    = explode("=", $no[$k]);
                    $playname       = $this->removesextraspace(trim($playlistarr[1]));
                }
                if (strstr($str, 'autoplay')) {
                    $autoplayarr    = explode("=", $no[$k]);
                    $autoplay       = $this->removesextraspace(trim($autoplayarr[1]));
                }
            }

            ## Fetch filepath,videourl,thumburl values for given Video or Video from Playlist
            if ($idval != '') {
                $query = 'SELECT filepath,videourl,thumburl FROM #__hdflvplayerupload WHERE published=1 AND  id=' . $idval;
                $db->setQuery($query);
                $field = $db->loadObjectList();
            } elseif ($idval != '' && $playname != '') {
                $query = 'SELECT filepath,videourl,thumburl FROM #__hdflvplayerupload WHERE published=1 AND  playlistid=' . $playname . ' AND id=' . $idval;
                $db->setQuery($query);
                $field = $db->loadObjectList();
            }

            ## Checks for File path
            if (!empty($field)) {
                $filepath = $field[0]->filepath;

                ## If file option File or FFMpeg then, below fetch will work for Video & Thumb URL
                if ($filepath == "File" || $filepath == "FFmpeg") {
                    $current_path = "components/com_hdflvplayer/videos/";
                    $videos     = JURI::base() . $current_path . $field[0]->videourl;
                    $thumImg    = JURI::base() . $current_path . $field[0]->thumburl;
                }
                ## If file option Youtube then, below fetch will work for Video & Thumb URL
                elseif ($filepath == "Youtube") {
                    $videos     = $field[0]->videourl;
                    $thumImg    = $field[0]->thumburl;
                }
            }
            ## If Width, Height params empty, then set default values.
            if ($width == 0) {
                $width = 700;
            }
            if ($height == 0) {
                $height = 400;
            }
            $video = '';
            $regex = $strwhole;

            ## Function calling for load player with necessary inputs.
            $replace = $this->addVideoHdplayer($video, $width, $height, $enablexml, $idval, $playname, $autoplay, $filepath, $videos, $thumImg);
            $row->text = str_replace($regex, $replace, $row->text);
        }
    }
function detect_mobile()
{
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';

    $mobile_browser = '0';

    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

    if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', $agent))
        $mobile_browser++;

    if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))
        $mobile_browser++;

    if(isset($_SERVER['HTTP_X_WAP_PROFILE']))
        $mobile_browser++;

    if(isset($_SERVER['HTTP_PROFILE']))
        $mobile_browser++;

    $mobile_ua = substr($agent,0,4);
    $mobile_agents = array(
                        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
                        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
                        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
                        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
                        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
                        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
                        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
                        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
                        'wapr','webc','winw','xda','xda-'
                        );

    if(in_array($mobile_ua, $mobile_agents))
        $mobile_browser++;

    if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
        $mobile_browser++;

    // Pre-final check to reset everything if the user is on Windows
    if(strpos($agent, 'windows') !== false)
        $mobile_browser=0;

    // But WP7 is also Windows, with a slightly different characteristic
    if(strpos($agent, 'windows phone') !== false)
        $mobile_browser++;

    if($mobile_browser>0)
        return true;
    else
        return false;
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
        
    ## Function for loading player with necessary inputs
    function addVideoHdplayer($video, $width, $height, $enablexml, $idval, $playid, $autoplay, $filepath, $videos, $thumImg) {

        ## Variables initialization
        $baseurl        = JURI::base();
        $baseurl1       = substr_replace($baseurl, "", -1);
        $idval          = trim($idval);
        $replace        = '';
        $db             = JFactory::getDBO();

        ## Query for fetch Google Adsense
        $query = 'SELECT closeadd,reopenadd,ropen,publish,showaddp FROM #__hdflvaddgoogle WHERE publish=1 AND id=1';
        $db->setQuery($query);
        $fields = $db->loadObject();

        ## HTML5 PLAYER START
        
$mobile = $this->detect_mobile();
   if($mobile === true){
       if(!empty($filepath)){
if ($this->getHTMLVideoAccessLevel($idval) == 'true') {

        ## Checks for File or FFMpeg
        if ($filepath == "File" || $filepath == "FFmpeg") {

            $replace    .='<video id="video" poster="' . $thumImg . '" src="' . $videos . '" width="' . $width . '" height="' . $height . '" autobuffer controls onerror="failed(event)">
                        Html5 Not support This video Format.
                        </video>';
        }
        ## Checks for Youtube videos
        elseif ($filepath == "Youtube") {
            if (preg_match('/www\.youtube\.com\/watch\?v=[^&]+/', $videos, $vresult)) {
                $urlArray   = explode("=", $vresult[0]);
                $videoid    = trim($urlArray[1]);
                $replace    .='<iframe width="' . $width . '" height="' . $height . '" src="http://www.youtube.com/embed/' . $videoid . '" frameborder="0" allowfullscreen></iframe>';
            }
        }
}
else {
$replace    .='<div id="video" style="background-color:#000000;" >
                    <h3 style="color:#e65c00;vertical-align: middle;height:' . $height . 'px;display: table-cell;width:' . $width . 'px; ">There are no videos in this playlist</h3>
                </div>';
}
}
else {
$replace    .='<div id="video" style="background-color:#000000;" >
                    <h3 style="color:#e65c00;vertical-align: middle;height:' . $height . 'px;display: table-cell;width:' . $width . 'px; ">Please login to watch this video.</h3>
                </div>';
}
   } else {
       ## Checks for Vimeo Player
        if (preg_match('/vimeo/', $videos, $vresult)) {


            $split      = explode("/", $videos);

            $replace    .='<iframe src="http://player.vimeo.com/video/' . $split[3] . '?title=0&amp;byline=0&amp;portrait=0" width="' . $width . '" height="' . $height . '" frameborder="0"></iframe>';
        }
        ## Else Flash player
        else {

            $replace    .='<div class="HDFLVPlayer1" id="HDFLVPlayer1" align="center" style="position: relative;width:' . $width . 'px;height:' . $height . 'px" >'
                        . '<embed src="' . $baseurl . 'index.php?option=com_hdflvplayer&task=player&playid=' . $playid . '&id=' . $idval . '" allowFullScreen="true"  allowScriptAccess="always"type="application/x-shockwave-flash"wmode="opaque" flashvars="baserefJ=' . $baseurl1 . '&autoplay=' . $autoplay . '&showPlaylist=' . $playid . '" width="' . trim($width) . '" height="' . trim($height) . '"/></embed>';
       ## If not empty then load values for Google Ads
        if (!empty($fields)) {
            $detailmodule = array('closeadd' => $fields->closeadd, 'reopenadd' => $fields->reopenadd, 'ropen' => $fields->ropen, 'publish' => $fields->publish, 'showaddp' => $fields->showaddp);
            $addheight = (int) $height - 108;

            ## Checks for Google ads enabled for Plugin or not.
            if ($detailmodule['showaddp'] == 1) {
                ?>
                <script language="javascript">
                    var closeadd    = <?php echo $detailmodule['closeadd'] * 1000; ?>;
                    var ropen       = <?php echo $detailmodule['ropen'] * 1000; ?>;
                </script>
                <script src="components/com_hdflvplayer/hdflvplayer/googleadds.js"></script>
                
                <?php
                $replace .=' <div style="position:absolute;text-align:center;bottom: 50px;width:' . $width . 'px;" >
                            <div id="lightm" style="width:234px;margin:0 auto">
                            <img id="closeimgm" src="components/com_hdflvplayer/images/close.png" style=" width:48px;height:12px;cursor:pointer; left:"' . $width . '"px;" onclick="googleclose();">
                            <iframe height="60" scrolling="no" align="middle" width="234" id="IFrameName" src=""     name="IFrameName" marginheight="0" marginwidth="0" frameborder="0"></iframe>
                            </div> </div></div>';
        }
   }
          $replace         . '</div>';
        }
   }

## HTML5 PLAYER  END
        return $replace;
    }
}
?>