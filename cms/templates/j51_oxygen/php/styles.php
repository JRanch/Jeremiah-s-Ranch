
<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );?>   

<link rel="stylesheet" href="templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template?>/css/reset.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template?>/css/typo.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template?>/css/template.css" type="text/css" />
<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/<?php echo $this->params->get('colorStyle'); ?>.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template?>/css/nexus.css" type="text/css" />

<?php /*?>Set Google font choices to body, articleheads, moduleheads and hornav menu<?php */?>
<?php if(($body_fontstyle == "Arial") || ($body_fontstyle == "Courier") || ($body_fontstyle == "Tahoma") || ($body_fontstyle == "Garamond") || ($body_fontstyle == "Georgia") || ($body_fontstyle == "Impact") || ($body_fontstyle == "Lucida Console") || ($body_fontstyle == "Lucida Sans Unicode") || ($body_fontstyle == "MS Sans Serif") || ($body_fontstyle == "MS Serif") || ($body_fontstyle == "Palatino Linotype") || ($body_fontstyle == "Tahoma") || ($body_fontstyle == "Times New Roman") || ($body_fontstyle == "Trebuchet MS") || ($body_fontstyle == "Verdana") || ($body_fontstyle == "Webdings")) : ?>
<style type="text/css">body{font-family:<?php echo ($body_fontstyle); ?> }</style>
<?php endif; ?>

<?php if(($articlehead_fontstyle == "Arial") || ($articlehead_fontstyle == "Courier") || ($articlehead_fontstyle == "Tahoma") || ($articlehead_fontstyle == "Garamond") || ($articlehead_fontstyle == "Georgia") || ($articlehead_fontstyle == "Impact") || ($articlehead_fontstyle == "Lucida Console") || ($articlehead_fontstyle == "Lucida Sans Unicode") || ($articlehead_fontstyle == "MS Sans Serif") || ($articlehead_fontstyle == "MS Serif") || ($articlehead_fontstyle == "Palatino Linotype") || ($articlehead_fontstyle == "Tahoma") || ($articlehead_fontstyle == "Times New Roman") || ($articlehead_fontstyle == "Trebuchet MS") || ($articlehead_fontstyle == "Verdana")) : ?>
<style type="text/css">h2{font-family:<?php echo ($articlehead_fontstyle); ?> }</style>
<?php endif; ?>

<?php if(($modulehead_fontstyle == "Arial") || ($modulehead_fontstyle == "Courier") || ($modulehead_fontstyle == "Tahoma") || ($modulehead_fontstyle == "Garamond") || ($modulehead_fontstyle == "Georgia") || ($modulehead_fontstyle == "Impact") || ($modulehead_fontstyle == "Lucida Console") || ($modulehead_fontstyle == "Lucida Sans Unicode") || ($modulehead_fontstyle == "MS Sans Serif") || ($modulehead_fontstyle == "MS Serif") || ($modulehead_fontstyle == "Palatino Linotype") || ($modulehead_fontstyle == "Tahoma") || ($modulehead_fontstyle == "Times New Roman") || ($modulehead_fontstyle == "Trebuchet MS") || ($modulehead_fontstyle == "Verdana")) : ?>
<style type="text/css">.module h3, .module_menu h3{font-family:<?php echo ($modulehead_fontstyle); ?> }</style>
<?php endif; ?>

<?php if(($hornav_fontstyle == "Arial") || ($hornav_fontstyle == "Courier") || ($hornav_fontstyle == "Tahoma") || ($hornav_fontstyle == "Garamond") || ($hornav_fontstyle == "Georgia") || ($hornav_fontstyle == "Impact") || ($hornav_fontstyle == "Lucida Console") || ($hornav_fontstyle == "Lucida Sans Unicode") || ($hornav_fontstyle == "MS Sans Serif") || ($hornav_fontstyle == "MS Serif") || ($hornav_fontstyle == "Palatino Linotype") || ($hornav_fontstyle == "Tahoma") || ($hornav_fontstyle == "Times New Roman") || ($hornav_fontstyle == "Trebuchet MS") || ($hornav_fontstyle == "Verdana")) : ?>
<style type="text/css">#hornav{font-family:<?php echo ($hornav_fontstyle); ?> }</style>
<?php endif; ?>

<?php if(($logo_fontstyle == "Arial") || ($logo_fontstyle == "Courier") || ($logo_fontstyle == "Tahoma") || ($logo_fontstyle == "Garamond") || ($logo_fontstyle == "Georgia") || ($logo_fontstyle == "Impact") || ($logo_fontstyle == "Lucida Console") || ($logo_fontstyle == "Lucida Sans Unicode") || ($logo_fontstyle == "MS Sans Serif") || ($logo_fontstyle == "MS Serif") || ($logo_fontstyle == "Palatino Linotype") || ($logo_fontstyle == "Tahoma") || ($logo_fontstyle == "Times New Roman") || ($logo_fontstyle == "Trebuchet MS") || ($logo_fontstyle == "Verdana")) : ?>
<style type="text/css">h1.logo-text a{font-family:<?php echo ($logo_fontstyle); ?> }</style>
<?php endif; ?>
<?php /*?>End Set Google font choices to body, articleheads, moduleheads and hornav menu<?php */?>

<?php if($this->direction == 'rtl') : ?>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template?>/css/template_rtl.css" type="text/css" />
<?php endif; ?>

<style type="text/css">
/*--Set Logo Image position and locate logo image file--*/ 
h1.logo a {left:<?php echo ($logo_x); ?>px}
h1.logo a {top:<?php echo ($logo_y); ?>px}

<?php if($this->params->get('logoimagefile') == '') : ?>
h1.logo a {background: url(<?php echo $defaultlogoimage; ?>) no-repeat; z-index:1;}

<?php elseif($this->params->get('logoimagefile') != '') : ?>
h1.logo a {background: url(<?php echo $this->baseurl ?>/<?php echo $logoimagefile; ?>) no-repeat; z-index:1;}
<?php endif; ?>
/*--End Set Logo Image position and locate logo image file--*/ 

/*--Body font size--*/
body{font-size: <?php echo ($body_fontsize); ?>}

/*--Text Colors for Module Heads and Article titles--*/ 
h2, h2 a:link, h2 a:visited, .content_header, .articleHead {color: <?php echo ($articletitle_font_color); ?> }
.module h3, .module_menu h3 {color: <?php echo ($modulehead_font_color); ?> }
a {color: <?php echo ($content_link_color); ?> }

/*--Text Colors for Logo and Slogan--*/ 
h1.logo-text a {color: <?php echo ($logo_font_color); ?> }
p.site-slogan {color: <?php echo ($slogan_font_color); ?> }

/*--Hornav Ul text color and dropdown background color--*/
#hornav ul li a{color: <?php echo ($hornav_font_color); ?> }
#subMenusContainer ul, #subMenusContainer ol{background-color: <?php echo ($hornav_ddbackground_color); ?> }

/*--Start Style Side Column and Content Layout Divs--*/
/*--Get Side Column widths from Parameters--*/
#sidecol_a {width: <?php echo ($sidecola_width); ?>px }
#sidecol_b {width: <?php echo ($sidecolb_width); ?>px }

/*--Check and see what modules are toggled on/off then take away columns width, margin and border values from overall width*/
<?php if($this->countModules( 'sidecol-a') >= 1 && $this->countModules('sidecol-b') >= 1) : ?>
#content_remainder {width:<?php echo 888 - ($sidecola_width + $sidecolb_width) ?>px }

<?php elseif($this->countModules('sidecol-a') >= 1 && $this->countModules('sidecol-b') == 0) : ?>
#content_remainder {width:<?php echo 888 - ($sidecola_width) ?>px }

<?php elseif($this->countModules('sidecol-a') == 0 && $this->countModules('sidecol-b') >= 1) : ?>
#content_remainder {width:<?php echo 890 - ($sidecolb_width) ?>px }

<?php endif; ?>

/*Style Side Column A, Side Column B and Content Divs layout*/
<?php if($this->params->get('column_layout') == 'SCOLA-SCOLB-COM') : ?>
	#sidecol_a {float:left;}
	#sidecol_b {float:left;}
	#content_remainder {float:left;}

/*Style Content, Side Column A, Side Column B Divs layout*/	
<?php elseif($this->params->get('column_layout') == 'COM-SCOLA-SCOLB') : ?>
	#content_remainder {float:left;}
	#sidecol_a {float:right;}
	#sidecol_b {float:right;}

/*Style Side Column A, Content, Side Column B Divs layout*/
<?php elseif($this->params->get('column_layout') == 'SCOLA-COM-SCOLB') : ?>  
	#sidecol_a {float:left;}
	#sidecol_b {float:right;}
	#content_remainder {float:left;}
<?php endif; ?>
/*--End Style Side Column and Content Layout Divs--*/

/*--Load Custom Css Styling--*/
<?php echo ($custom_css); ?>
</style>




