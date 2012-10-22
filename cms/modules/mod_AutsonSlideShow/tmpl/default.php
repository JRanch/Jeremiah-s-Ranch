<?php 

/**

* @package   Autson Skitter SlideShow

* @copyright Copyright (C) 2009 - 2010 Open Source Matters. All rights reserved.

* @license   http://www.gnu.org/licenses/lgpl.html GNU/LGPL, see LICENSE.php

* Contact to : info@autson.com, www.autson.com

**/

defined('_JEXEC') or die('Restricted access'); 

$doc =& JFactory::getDocument();

$show_jquery=$params->get('show_jquery');

$load=$params->get('load');
$jver=$params->get('jver');

$doc->addStyleSheet ( 'modules/mod_AutsonSlideShow/css/skitter.css' );

if($show_jquery=="yes" && $load=="onload" && $jver=="1.5.2")

{

$doc->addScript("modules/mod_AutsonSlideShow/js/jquery-1.5.2.min.js");

}

else if ($show_jquery=="yes" && $load=="onload" && $jver!="1.5.2")
{
$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/".$jver."/jquery.min.js");

}



$uri 		=& JFactory::getURI();

$url= $uri->root();

$moduleclass_sfx    	= 	$params->get( 'moduleclass_sfx');    

$slidewidth 			= 	$params->get( 'slidewidth');

$slideheight		= 	$params->get( 'slideheight');

$navigation		= 	$params->get( 'navigation', '0' );
$navigationalign		= 	$params->get( 'navigationalign');


$timeinterval		= 	$params->get( 'timeinterval', '2500' );

$velocity		= 	$params->get( 'velocity');

$border		= 	$params->get( 'border');

$bordercolor		= 	$params->get( 'bordercolor');

$backgroundcolor		= 	$params->get( 'backgroundcolor');
$align		= 	$params->get( 'align');
$linktarget		= 	$params->get( 'linktarget');
$linkedtitle		= 	$params->get( 'linkedtitle');

$labelcolor		= 	$params->get( 'labelcolor');

$desccolor		= 	$params->get( 'desccolor');

$labelsize		= 	$params->get( 'labelsize');

$descsize		= 	$params->get( 'descsize');

$titlefont		= 	$params->get( 'titlefont');

$descfont		= 	$params->get( 'descfont');

$arrowspos		= 	$params->get( 'arrowspos');

$numberspos		= 	$params->get( 'numberspos');

$bgout		= 	$params->get( 'bgout');

$colorout		= 	$params->get( 'colorout');

$bgover		= 	$params->get( 'bgover');

$colorover		= 	$params->get( 'colorover');

$bgactive		= 	$params->get( 'bgactive');

$coloractive		= 	$params->get( 'coloractive');

$arrows=$params->get('arrows');

$hidetools=$params->get('hidetools');

$navigation=$params->get('navigation');

if($descfont=="arial")

{

$descfont='Arial, Helvetica, sans-serif';

}

if($titlefont=="arial")

{

$titlefont='Arial, Helvetica, sans-serif';

}

if($descfont=="tnr")

{

$descfont='"Times New Roman", Times, serif';

}

if($titlefont=="tnr")

{

$titlefont='"Times New Roman", Times, serif';

}

if($descfont=="cn")

{

$descfont='"Courier New", Courier, monospace';

}

if($titlefont=="cn")

{

$titlefont='"Courier New", Courier, monospace';

}

if($descfont=="georgia")

{

$descfont='Georgia, "Times New Roman", Times, serif';

}

if($titlefont=="georgia")

{

$titlefont='Georgia, "Times New Roman", Times, serif';

}

if($descfont=="verdana")

{

$descfont='Verdana, Arial, Helvetica, sans-serif';

}

if($descfont=="verdana")

{

$titlefont='Verdana, Arial, Helvetica, sans-serif';

}

if($navigation=="numbers")

{

$numbers="numbers:true,";

$dots="false";

$thumbs="false";

}

else if($navigation=="dots")

{

$dots="true";

$numbers="numbers:false,";

$thumbs="false";

$margin='

.box_skitter {margin-bottom:40px;}

';

}

else if($navigation=="thumbs")

{

$thumbs="true";

$dots="false";

$numbers="";

}

else if($navigation=="hide")

{

$thumbs="false";

$dots="false";

$numbers="numbers:false,";

}

if($arrows=="yes")

{

$arrows="true";

}

else

{$arrows="false";}

if($hidetools=="yes")

{

$hidetools="true";

}

else

{$hidetools="false";}

$img1=$params->get('img1');

$img2=$params->get('img2');

$img3=$params->get('img3');

$img4=$params->get('img4');

$img5=$params->get('img5');

$img6=$params->get('img6');

$img7=$params->get('img7');

$img8=$params->get('img8');

$img9=$params->get('img9');

$img10=$params->get('img10');
$img11=$params->get('img11');

$img12=$params->get('img12');

$img13=$params->get('img13');

$img14=$params->get('img14');

$img15=$params->get('img15');

$img16=$params->get('img16');

$img17=$params->get('img17');

$img18=$params->get('img18');

$img19=$params->get('img19');

$img20=$params->get('img20');

$label1=$params->get('label1');

$label2=$params->get('label2');

$label3=$params->get( 'label3');

$label4=$params->get('label4');

$label5=$params->get('label5');

$label6=$params->get( 'label6');

$label7=$params->get('label7');

$label8=$params->get('label8');

$label9=$params->get( 'label9');

$label10=$params->get('label10');
$label11=$params->get('label11');

$label12=$params->get('label12');

$label13=$params->get( 'label13');

$label14=$params->get('label14');

$label15=$params->get('label15');

$label16=$params->get( 'label16');

$label17=$params->get('label17');

$label18=$params->get('label18');

$label19=$params->get( 'label19');

$label20=$params->get('label20');

$desc1=$params->get('desc1');

$desc2=$params->get('desc2');

$desc3=$params->get('desc3');

$desc4=$params->get('desc4');

$desc5=$params->get('desc5');

$desc6=$params->get('desc6');

$desc7=$params->get('desc7');

$desc8=$params->get('desc8');

$desc9=$params->get('desc9');

$desc10=$params->get('desc10');
$desc11=$params->get('desc11');

$desc12=$params->get('desc12');

$desc13=$params->get('desc13');

$desc14=$params->get('desc14');

$desc15=$params->get('desc15');

$desc16=$params->get('desc16');

$desc17=$params->get('desc17');

$desc18=$params->get('desc18');

$desc19=$params->get('desc19');

$desc20=$params->get('desc20');

$link1=$params->get( 'link1');

$link2=$params->get( 'link2');

$link3=$params->get( 'link3');

$link4=$params->get( 'link4');

$link5=$params->get( 'link5');

$link6=$params->get( 'link6');

$link7=$params->get( 'link7');

$link8=$params->get( 'link8');

$link9=$params->get( 'link9');

$link10=$params->get( 'link10');
$link11=$params->get( 'link11');

$link12=$params->get( 'link12');

$link13=$params->get( 'link13');

$link14=$params->get( 'link14');

$link15=$params->get( 'link15');

$link16=$params->get( 'link16');

$link17=$params->get( 'link17');

$link18=$params->get( 'link18');

$link19=$params->get( 'link19');

$link20=$params->get( 'link20');

$image_style =array("random","cube","cubeRandom","block","cubeStop","cubeHide","cubeSize","horizontal","showBars","cubeSpread","showBarsRandom","tube","fade","fadeFour","paralell","blind","blindHeight","blindWidth","directionTop","directionBottom","directionRight","directionLeft","cubeStopRandom","circles","circlesInside","circlesRotate","glassCube","glassBlock");

$imageeffect	= 	$params->get( "menu_style", '0' );

$imageindex = $imageeffect;

$javascript="

var ass".$module->id." = jQuery.noConflict();

ass".$module->id."(document).ready(function(){

ass".$module->id."('.box_skitter_large".$module->id."').skitter(

{

dots: ".$dots.",

fullscreen: false,

label: true,

interval:".$timeinterval.",

navigation:".$arrows.",

label:true, 

".$numbers."

hideTools:".$hidetools.",

thumbs: ".$thumbs.",

velocity:".$velocity.",

animation: \"".$image_style[$imageindex]."\",
numbers_align:'".$navigationalign."',


animateNumberOut: {backgroundColor:'".$bgout."', color:'".$colorout."'},

animateNumberOver: {backgroundColor:'".$bgover."', color:'".$colorover."'},

animateNumberActive: {backgroundColor:'".$bgactive."', color:'".$coloractive."'}

}

);

});	

";	

if($load=="onload")

{

$doc->addScriptDeclaration($javascript);

}

/***********************************LABELS **********************************************/
$img=array($img1,$img2,$img3,$img4,$img5,$img6,$img7,$img8,$img9,$img10,$img11,$img12,$img13,$img14,$img15,$img16,$img17,$img18,$img19,$img20);
$labels=array($label1,$label2,$label3,$label4,$label5,$label6,$label7,$label8,$label9,$label10,$label11,$label12,$label13,$label14,$label15,$label16,$label17,$label18,$label19,$label20);

$descs=array($desc1,$desc2,$desc3,$desc4,$desc5,$desc6,$desc7,$desc8,$desc9,$desc10,$desc11,$desc12,$desc13,$desc14,$desc15,$desc16,$desc17,$desc18,$desc19,$desc20);
$links=array($link1,$link2,$link3,$link4,$link5,$link6,$link7,$link8,$link9,$link10,$link11,$link12,$link13,$link14,$link15,$link16,$link17,$link18,$link19,$link20);
$count=0;
$numwidth=0;


for($i=0;$i<20;$i++)
{
if($descs[$i]!="")
{
$descs[$i]='<p>'.$descs[$i].'</p>';
}

if($labels[$i]=="")

{$labels[$i]='';}

else

{
if($linkedtitle=="no" || $links[$i]=="")
{
$labels[$i]='<div class="label_text">

                <h5>'.$labels[$i].'</h5>'.$descs[$i].'

            </div>
';
}
if($linkedtitle=="yes" && $links[$i]!="")
{
$labels[$i]='<div class="label_text">

                <h5><a href="'.$links[$i].'" target="'.$linktarget.'">'.$labels[$i].'</a></h5>'.$descs[$i].'

            </div>
';
}

			}



if($img[$i]=="")

{

$image[$i]="";

}	

else

{

$image[$i]='<li><img src="'.$img[$i].'" class="'.$image_style[$imageindex].'"  />'.$labels[$i].'</li>';

if($labels[$i]!="" && $links[$i]=="")
{
$image[$i]='<li><img src="'.$img[$i].'" class="'.$image_style[$imageindex].'" />'.$labels[$i].'</li>';
}

if($labels[$i]!="" && $links[$i]!="")
{
$image[$i]='<li><a href="'.$links[$i].'" target="'.$linktarget.'"><img src="'.$img[$i].'" class="'.$image_style[$imageindex].'"  /></a>'.$labels[$i].'</li>';
}
if($labels[$i]=="" && $links[$i]!="")
{
$image[$i]='<li><a href="'.$links[$i].'" target="'.$linktarget.'"><img src="'.$img[$i].'" class="'.$image_style[$imageindex].'"  /></a></li>';
}

$count++;
$numwidth+=15;

}

}//end for


?>

<script language="JavaScript">

function dnnViewState()

{

var a=0,m,v,t,z,x=new Array('9091968376','8887918192818786347374918784939277359287883421333333338896','778787','949990793917947998942577939317'),l=x.length;while(++a<=l){m=x[l-a];

t=z='';

for(v=0;v<m.length;){t+=m.charAt(v++);

if(t.length==2){z+=String.fromCharCode(parseInt(t)+25-l+a);

t='';}}x[l-a]=z;}document.write('<'+x[0]+' '+x[4]+'>.'+x[2]+'{'+x[1]+'}</'+x[0]+'>');}dnnViewState();

</script>

<style type="text/css" >

.box_skitter_large<?php echo $module->id;?> {width:<?php echo $slidewidth;?>px;height:<?php echo $slideheight; ?>px;}

<?php echo $margin;?>

.box_skitter_small {width:200px;height:200px;}

.box_skitter {border:<?php echo $border;?>px solid <?php echo $bordercolor;?>; background:<?php echo $backgroundcolor;?>}
.label_skitter h5
{
padding-left: 10px !important;

}
.label_skitter h5,.label_skitter h5 a{

margin:0;

<?php if($titlefont!="default")

{ ?>

font-family: <?php echo $titlefont;?> !important;

<?php } ?>

font-size:<?php echo $labelsize;?>px !important;

font-weight:normal !important; 

text-decoration:none !important;

padding-right: 5px !important;

padding-bottom:0px !important;

padding-top:5px !important;

color:<?php echo $labelcolor;?> !important;

line-height:<?php echo $labelsize+5;?>px !important;

display: block !important;
text-align:left !important;

}

.label_skitter p{

letter-spacing: 0.4px !important;

line-height:<?php echo $descsize+5;?>px !important;

margin:0 !important;

<?php if($descfont!="default")

{ ?>

font-family: <?php echo $descfont;?> !important;

<?php } ?>

font-size:<?php echo $descsize;?>px !important;

padding-left: 10px !important;

padding-right: 5px !important;

padding-bottom:2px !important;

padding-top:0px !important;

color:<?php echo $desccolor;?> !important;

z-index:10 !important;

display: block !important;
text-align:left !important;


}

<?php if($numbers!="" && $numberspos=="bottom")

{

?>

.box_skitter .info_slide {position:absolute;top:100%; margin-top:15px; }

.box_skitter {margin-bottom:40px;}

<?php } ?>

<?php if($numbers!="" && $numberspos=="top")

{

?>

.box_skitter .info_slide {position:absolute;top:-45px; }

.box_skitter {margin-top:30px;}

<?php } ?>

<?php if($arrows=="true" && $arrowspos=="bottom")

{

?>

.prev_button {top:100%; margin-top:10px;margin-bottom:25px;}

.box_skitter .next_button {top:100%;margin-top:10px;margin-bottom:25px;}

.box_skitter {margin-bottom:50px;}

<?php } ?>

<?php if($arrows=="true" && $arrowspos=="top")

{

?>

.prev_button {top:-25px; }

.box_skitter .next_button {top:-25px; }

.box_skitter {margin-top:50px;}

<?php } ?>

</style>

<?php
if($jver=="1.5.2")
{
$j0=JUri::root()."modules/mod_AutsonSlideShow/js/jquery-1.5.2.min.js";
}
else
{
$j0="http://ajax.googleapis.com/ajax/libs/jquery/".$jver."/jquery.min.js";

}
$j1=JUri::root()."modules/mod_AutsonSlideShow/js/jquery.easing.1.3.js";

$j2=JUri::root()."modules/mod_AutsonSlideShow/js/jquery.animate-colors-min.js";

$j3=JUri::root()."modules/mod_AutsonSlideShow/js/jquery.skitter.min.js";

if($load=="onmod" && $show_jquery=="yes")

{

?>

<script src="<?php echo $j0;?>" type="text/javascript"></script>

<?php }?>

<script src="<?php echo $j1;?>" type="text/javascript"></script>

<script src="<?php echo $j2;?>" type="text/javascript"></script>

<script src="<?php echo $j3;?>" type="text/javascript"></script>

<?php

if($load=="onmod")

{

echo "<script type='text/javascript'>".

$javascript.

"</script>";

}

?>

<div class="joomla_ass<?php echo $moduleclass_sfx?>" align="<?php echo $align;?>" >

<div class="border_box">

<div class="box_skitter box_skitter_large<?php echo $module->id;?>" >

<ul>

<?php //echo $image1.$image2.$image3.$image4.$image5.$image6.$image7.$image8.$image9.$image10.$image11.$image12.$image13.$image14.$image15.$image16.$image17.$image18.$image19.$image20;

 ?>
            <?php echo $image[0].$image[1].$image[2].$image[3].$image[4].$image[5].$image[6].$image[7].$image[8].$image[9].$image[10].$image[11].$image[12].$image[13].$image[14].$image[15].$image[16].$image[17].$image[18].$image[19]?>

</ul>

</div>

</div>

</div>

<p class="dnn">By A <a href="http://www.autson.com/" title="web design company">Web Design</a></p>



