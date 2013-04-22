<?php

// JMasterFramework Copyright Joomla51.com
// Author Joomla51
// Single User Commercial Licence. For use on one domain only.


//COUNT MODULES IN TOP-1 - DECIDE WIDTH - COLLAPSE IF NECESSARY 
$top1_counted = 0;
if ($this->countModules('top-1a')) $top1_counted++;
if ($this->countModules('top-1b')) $top1_counted++;
if ($this->countModules('top-1c')) $top1_counted++;
if ($this->countModules('top-1d')) $top1_counted++;
if ($this->countModules('top-1e')) $top1_counted++;
if ($this->countModules('top-1f')) $top1_counted++;
if ( $top1_counted == 6 ) {
	$top1_width = '16.66%';}
else if ( $top1_counted == 5 ) {
	$top1_width = '20%';
} else if ($top1_counted == 4) {
	$top1_width = '25%';
} else if ($top1_counted == 3) {
	$top1_width = '33.3%';
} else if ($top1_counted == 2) {
	$top1_width = '50%';
} else if ($top1_counted == 1) {
	$top1_width = '100%';
}

//COUNT MODULES IN TOP-2 - DECIDE WIDTH - COLLAPSE IF NECESSARY
$top2_counted = 0;
if ($this->countModules('top-2a')) $top2_counted++;
if ($this->countModules('top-2b')) $top2_counted++;
if ($this->countModules('top-2c')) $top2_counted++;
if ($this->countModules('top-2d')) $top2_counted++;
if ($this->countModules('top-2e')) $top2_counted++;
if ($this->countModules('top-2f')) $top2_counted++;
if ( $top2_counted == 6 ) {
	$top2_width = '16.66%';
}
else if ( $top2_counted == 5 ) {
	$top2_width = '20%';
} else if ($top2_counted == 4) {
	$top2_width = '25%';
} else if ($top2_counted == 3) {
	$top2_width = '33.3%';
} else if ($top2_counted == 2) {
	$top2_width = '50%';
} else if ($top2_counted == 1) {
	$top2_width = '100%';
}

//COUNT MODULES IN CONTENTTOP - DECIDE WIDTH - COLLAPSE IF NECESSARY
$contenttop_counted = 0;
if ($this->countModules('contenttop-a')) $contenttop_counted++;
if ($this->countModules('contenttop-b')) $contenttop_counted++;
if ($this->countModules('contenttop-c')) $contenttop_counted++;
if ( $contenttop_counted == 3 ) {
	$contenttop_width = '33.3%';
}
elseif ( $contenttop_counted == 2 ) {
	$contenttop_width = '49.9%';
} else if ($contenttop_counted == 1) {
	$contenttop_width = '100%';
}

//COUNT MODULES IN CONTENTBOTTOM - DECIDE WIDTH - COLLAPSE IF NECESSARY
$contentbottom_counted = 0;
if ($this->countModules('contentbottom-a')) $contentbottom_counted++;
if ($this->countModules('contentbottom-b')) $contentbottom_counted++;
if ($this->countModules('contentbottom-c')) $contentbottom_counted++;
if ( $contentbottom_counted == 3 ) {
	$contentbottom_width = '33.3%';
}
elseif ( $contentbottom_counted == 2 ) {
	$contentbottom_width = '49.9%';
} else if ($contentbottom_counted == 1) {
	$contentbottom_width = '100%';
}


//COUNT MODULES IN BOTTOM-1 - DECIDE WIDTH - COLLAPSE IF NECESSARY
$bottom1_counted = 0;
if ($this->countModules('bottom-1a')) $bottom1_counted++;
if ($this->countModules('bottom-1b')) $bottom1_counted++;
if ($this->countModules('bottom-1c')) $bottom1_counted++;
if ($this->countModules('bottom-1d')) $bottom1_counted++;
if ($this->countModules('bottom-1e')) $bottom1_counted++;
if ($this->countModules('bottom-1f')) $bottom1_counted++;
if ( $bottom1_counted == 6 ) {
	$bottom1_width = '16.66%';
}
else if ( $bottom1_counted == 5 ) {
	$bottom1_width = '20%';
} else if ($bottom1_counted == 4) {
	$bottom1_width = '25%';
} else if ($bottom1_counted == 3) {
	$bottom1_width = '33.3%';
} else if ($bottom1_counted == 2) {
	$bottom1_width = '50%';
} else if ($bottom1_counted == 1) {
	$bottom1_width = '100%';
}

//COUNT MODULES IN BOTTOM-2 - DECIDE WIDTH - COLLAPSE IF NECESSARY
$bottom2_counted = 0;
if ($this->countModules('bottom-2a')) $bottom2_counted++;
if ($this->countModules('bottom-2b')) $bottom2_counted++;
if ($this->countModules('bottom-2c')) $bottom2_counted++;
if ($this->countModules('bottom-2d')) $bottom2_counted++;
if ($this->countModules('bottom-2e')) $bottom2_counted++;
if ($this->countModules('bottom-2f')) $bottom2_counted++;
if ( $bottom2_counted == 6 ) {
	$bottom2_width = '16.66%';
}
else if ( $bottom2_counted == 5 ) {
	$bottom2_width = '20%';
} else if ($bottom2_counted == 4) {
	$bottom2_width = '25%';
} else if ($bottom2_counted == 3) {
	$bottom2_width = '33.3%';
} else if ($bottom2_counted == 2) {
	$bottom2_width = '50%';
} else if ($bottom2_counted == 1) {
	$bottom2_width = '100%';
}

//COUNT MODULES IN BASE-1 - DECIDE WIDTH - COLLAPSE IF NECESSARY
$base1_counted = 0;
if ($this->countModules('base-1a')) $base1_counted++;
if ($this->countModules('base-1b')) $base1_counted++;
if ($this->countModules('base-1c')) $base1_counted++;
if ($this->countModules('base-1d')) $base1_counted++;
if ($this->countModules('base-1e')) $base1_counted++;
if ($this->countModules('base-1f')) $base1_counted++;
if ( $base1_counted == 6 ) {
	$base1_width = '16.66%';
}
else if ( $base1_counted == 5 ) {
	$base1_width = '20%';
} else if ($base1_counted == 4) {
	$base1_width = '25%';
} else if ($base1_counted == 3) {
	$base1_width = '33.3%';
} else if ($base1_counted == 2) {
	$base1_width = '50%';
} else if ($base1_counted == 1) {
	$base1_width = '100%';
}

//COUNT MODULES IN BASE-2 - DECIDE WIDTH - COLLAPSE IF NECESSARY
$base2_counted = 0;
if ($this->countModules('base-2a')) $base2_counted++;
if ($this->countModules('base-2b')) $base2_counted++;
if ($this->countModules('base-2c')) $base2_counted++;
if ($this->countModules('base-2d')) $base2_counted++;
if ($this->countModules('base-2e')) $base2_counted++;
if ($this->countModules('base-2f')) $base2_counted++;
if ( $base2_counted == 6 ) {
	$base2_width = '16.66%';
}
else if ( $base2_counted == 5 ) {
	$base2_width = '20%';
} else if ($base2_counted == 4) {
	$base2_width = '25%';
} else if ($base2_counted == 3) {
	$base2_width = '33.3%';
} else if ($base2_counted == 2) {
	$base2_width = '50%';
} else if ($base2_counted == 1) {
	$base2_width = '100%';
}


?>

 