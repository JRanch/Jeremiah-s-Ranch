<?php
/*================================================================*\
|| # Copyright (C) 2010  Nexus. All Rights Reserved.           ||
|| # license - PHP files are licensed under  GNU/GPL V2           ||
|| # license - CSS  - JS - IMAGE files are Copyrighted material   ||
|| # Website: http://www.joomla.com                             ||
\*================================================================*/
defined('_JEXEC') or die;
// JPlugin::loadLanguage( 'tpl_SG1' );
JHTML::_('behavior.mootools');
define( 'nexus', dirname(__FILE__) );
require( nexus.DS."php/config.php");
require( nexus.DS."php/variables.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<jdoc:include type="head" />
<?php include (nexus.DS . "php/styles.php");?>
<?php include (nexus.DS . "php/scripts.php");?>

<?php echo ($head_tracker_code); ?>
</head>


<body> <div id="body_bg">
<div id="container_header" class="container"><div class="wrapper960">
<?php require( nexus.DS."php/layouts/header.php"); ?>
</div></div>

<div id="container_slideshow" class="container"><div class="wrapper960">
<?php require( nexus.DS."php/layouts/slideshow.php"); ?>
</div></div>

<div id="container_spacer2" class="container"><div class="wrapper960">
<div class="clear"></div>
</div></div>

<div id="container_breadcrumb" class="container"><div class="wrapper960">
<?php require( nexus.DS."php/layouts/breadcrumb.php"); ?>
</div></div>

<div id="container_top_modules" class="container"><div class="wrapper960">
<?php require( nexus.DS."php/layouts/top_modules.php"); ?>
</div></div>

<div id="container_main" class="container"><div class="wrapper960">
<?php require( nexus.DS."php/layouts/main.php"); ?>
</div></div>

<div id="container_bottom_modules" class="container"><div class="wrapper960">
<?php require( nexus.DS."php/layouts/bottom_modules.php"); ?>
</div></div>

<div id="container_spacer3" class="container"><div class="wrapper960">
<div class="clear"></div>
</div></div>

<div id="container_base" class="container"><div class="wrapper960">
<?php require( nexus.DS."php/layouts/base.php"); ?>
</div></div>
<?php echo ($body_tracker_code); ?>

</div>
  <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-35901308-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script></body> 
</html>