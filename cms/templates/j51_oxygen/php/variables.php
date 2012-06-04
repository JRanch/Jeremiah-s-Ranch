<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );

$path = $this->baseurl.'/templates/'.$this->template;
$app = JFactory::getApplication();

//factory
$menu 		 = & JSite::getMenu();
$document	 = &JFactory::getDocument();

//General
$app->getCfg('sitename');
$siteName = $this->params->get('siteName');
$templateparams	= $app->getTemplate(true)->params;

//Tracker Code
$head_tracker_code = $this->params->get('head_tracker_code');
$body_tracker_code = $this->params->get('body_tracker_code');

//Font Link & Color Styling
$articletitle_font_color = $this->params->get('articletitle_font_color');
$modulehead_font_color = $this->params->get('modulehead_font_color');
$content_link_color = $this->params->get('content_link_color');
$hornav_link_color = $this->params->get('hornav_link_color');
$hornav_droplink_color = $this->params->get('hornav_droplink_color');
$hornav_dropbackground_color = $this->params->get('hornav_dropbackground_color');

$logo_link_color = $this->params->get('logo_link_color');
$slogan_link_color = $this->params->get('slogan_link_color');

//Font Face Styling
$body_fontsize = $this->params->get('body_fontsize');
$body_fontstyle = $this->params->get('body_fontstyle');
$articlehead_fontstyle = $this->params->get('articlehead_fontstyle');
$modulehead_fontstyle = $this->params->get('modulehead_fontstyle');
$hornav_fontstyle = $this->params->get('hornav_fontstyle');
$logo_fontstyle = $this->params->get('logo_fontstyle');

//Logo and slogan
$logo_x = $this->params->get('logo_x');
$logo_y = $this->params->get('logo_y');
$logoimagefile = $this->params->get('logoimagefile');
$defaultlogoimage = "templates/<?php echo $this->template?>/images/logo.png";
$logo_font_color = $this->params->get('logo_font_color');
$slogan_font_color = $this->params->get('slogan_font_color');

//Layout
$sidecola_width = $this->params->get('sidecola_width');
$sidecolb_width = $this->params->get('sidecolb_width');
$column_layout = $this->params->get('column_layout');

//Top Menu
$renderer = $document->loadRenderer( 'module' );
$module	 = JModuleHelper::getModule( 'mod_menu' );
$menu_name = $this->params->get("top_menu", "parks");
$module->params	= "menutype=$menu_name\nendLevel=1";
$topmenu = $renderer->render( $module);
$topmenu_onoff = $this->params->get('topmenu_onoff');

//Hornav Menu
$renderer = $document->loadRenderer( 'module' );
$module	 = JModuleHelper::getModule( 'mod_menu' );
$menu_name = $this->params->get("hornav_menu", "mainmenu");
$module->params	= "menutype=$menu_name\nshowAllChildren=1\ntag_id=moomenu";
$hornav = $renderer->render( $module);
$hornav_font_color = $this->params->get('hornav_font_color');
$hornav_ddbackground_color = $this->params->get('hornav_ddbackground_color');

//Footer Menu
$renderer = $document->loadRenderer( 'module' );
$module	 = JModuleHelper::getModule( 'mod_menu' );
$menu_name = $this->params->get("footer_menu", "parks");
$module->params	= "menutype=$menu_name\nendLevel=1";
$footermenu = $renderer->render( $module);
$footermenu_onoff = $this->params->get('footermenu_onoff');

//Search
$search_onoff = $this->params->get('search_onoff');
$module	 = JModuleHelper::getModule( 'mod_search' );
$search = $renderer->render( $module);

//Top1 Module manual widths
$top1_auto = $this->params->get('top1_auto');
$top_1a_manual = $this->params->get('top_1a_manual');
$top_1b_manual = $this->params->get('top_1b_manual');
$top_1c_manual = $this->params->get('top_1c_manual');
$top_1d_manual = $this->params->get('top_1d_manual');
$top_1e_manual = $this->params->get('top_1e_manual');
$top_1f_manual = $this->params->get('top_1f_manual');

//Top2 Module manual widths
$top2_auto = $this->params->get('top2_auto');
$top_2a_manual = $this->params->get('top_2a_manual');
$top_2b_manual = $this->params->get('top_2b_manual');
$top_2c_manual = $this->params->get('top_2c_manual');
$top_2d_manual = $this->params->get('top_2d_manual');
$top_2e_manual = $this->params->get('top_2e_manual');
$top_2f_manual = $this->params->get('top_2f_manual');

//ContentTop Module manual widths
$contenttop_auto = $this->params->get('contenttop_auto');
$contenttop_a_manual = $this->params->get('contenttop_a_manual');
$contenttop_b_manual = $this->params->get('contenttop_b_manual');
$contenttop_c_manual = $this->params->get('contenttop_c_manual');

//ContentBottom Module manual widths
$contentbottom_auto = $this->params->get('contentbottom_auto');
$contentbottom_a_manual = $this->params->get('contentbottom_a_manual');
$contentbottom_b_manual = $this->params->get('contentbottom_b_manual');
$contentbottom_c_manual = $this->params->get('contentbottom_c_manual');

//Bottom1 Module manual widths
$bottom1_auto = $this->params->get('bottom1_auto');
$bottom_1a_manual = $this->params->get('bottom_1a_manual');
$bottom_1b_manual = $this->params->get('bottom_1b_manual');
$bottom_1c_manual = $this->params->get('bottom_1c_manual');
$bottom_1d_manual = $this->params->get('bottom_1d_manual');
$bottom_1e_manual = $this->params->get('bottom_1e_manual');
$bottom_1f_manual = $this->params->get('bottom_1f_manual');

//Bottom2 Module manual widths
$bottom2_auto = $this->params->get('bottom2_auto');
$bottom_2a_manual = $this->params->get('bottom_2a_manual');
$bottom_2b_manual = $this->params->get('bottom_2b_manual');
$bottom_2c_manual = $this->params->get('bottom_2c_manual');
$bottom_2d_manual = $this->params->get('bottom_2d_manual');
$bottom_2e_manual = $this->params->get('bottom_2e_manual');
$bottom_2f_manual = $this->params->get('bottom_2f_manual');

//Base1 Module manual widths
$base1_auto = $this->params->get('base1_auto');
$base_1a_manual = $this->params->get('base_1a_manual');
$base_1b_manual = $this->params->get('base_1b_manual');
$base_1c_manual = $this->params->get('base_1c_manual');
$base_1d_manual = $this->params->get('base_1d_manual');
$base_1e_manual = $this->params->get('base_1e_manual');
$base_1f_manual = $this->params->get('base_1f_manual');

//Base2 Module manual widths
$base2_auto = $this->params->get('base2_auto');
$base_2a_manual = $this->params->get('base_2a_manual');
$base_2b_manual = $this->params->get('base_2b_manual');
$base_2c_manual = $this->params->get('base_2c_manual');
$base_2d_manual = $this->params->get('base_2d_manual');
$base_2e_manual = $this->params->get('base_2e_manual');
$base_2f_manual = $this->params->get('base_2f_manual');

//Custom Css
$custom_css = $this->params->get('custom_css');

?>