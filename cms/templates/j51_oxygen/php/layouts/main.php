<!--Setting up Layout for MainContent and Side Columns. Check to see if modules are enabled or disabled in the sidecolumns-->
<?php
if($this->countModules('sidecol-a') == 0 || $this->countModules('sidecol-b') == 0) $contentwidth = '_full';
if($this->countModules('sidecol-a') >= 1 || $this->countModules('sidecol-b') >= 1) $contentwidth = '_remainder';
?>

<div id ="main" class="block_holder">

<!--Side Columns Layout-->                          
<?php if ($this->countModules( 'sidecol-a' )) : ?>
    <div id="sidecol_a" class="side_margins">
        <div class="sidecol_block">
            <jdoc:include type="modules" name="sidecol-a" style="mod_standard" />
        </div>
    </div>
<?php endif; ?>
       
    
<?php if ($this->countModules( 'sidecol-b' )) : ?>    
    <div id="sidecol_b" class="side_margins">
        <div class="sidecol_block">
            <jdoc:include type="modules" name="sidecol-b" style="mod_standard" />
        </div>
    </div>
<?php endif; ?>
<!--End Side Columns Layout-->

<!--Find Content width and show component area-->
<div id="content<?php echo $contentwidth; ?>" class="side_margins">

<!--Modules ContentTop-->
<?php if($this->params->get('contenttop_auto') != '1') : ?>
<?php if ($this->countModules('contenttop-a') || $this->countModules('contenttop-b') || $this->countModules('contenttop-c')) { ?>
<div class="wrapper_contenttop">
            <?php if ($this->countModules('contenttop-a')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_width ?>;"><div class="module_margin"><jdoc:include type="modules" name="contenttop-a"  style="mod_standard"/></div></div><?php } ?>
            <?php if ($this->countModules('contenttop-b')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_width ?>;"><div class="module_margin"><jdoc:include type="modules" name="contenttop-b"  style="mod_standard"/></div></div><?php } ?>
            <?php if ($this->countModules('contenttop-c')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_width ?>;"><div class="module_margin"><jdoc:include type="modules" name="contenttop-c"  style="mod_standard"/></div></div><?php } ?>
            <div class="clear"></div>
            </div>
<?php }?>
            
<?php else : ?>
            
<?php if ($this->countModules('contenttop-a') || $this->countModules('contenttop-b') || $this->countModules('contenttop-c')) { ?>
<div class="wrapper_contenttop">
            <?php if ($this->countModules('contenttop-a')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_a_manual ?>%;"><div class="module_margin"><jdoc:include type="modules" name="contenttop-a"  style="mod_standard"/></div></div><?php } ?>
            <?php if ($this->countModules('contenttop-b')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_b_manual ?>%;"><div class="module_margin"><jdoc:include type="modules" name="contenttop-b"  style="mod_standard"/></div></div><?php } ?>
            <?php if ($this->countModules('contenttop-c')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_c_manual ?>%;"><div class="module_margin"><jdoc:include type="modules" name="contenttop-c"  style="mod_standard"/></div></div><?php } ?>
<div class="clear"></div>
</div>
    <?php }?>
<?php endif; ?>
<!--End Modules ContentTop-->

 	<div class="maincontent">
            <div class="message">
                <?php if ($this->getBuffer( 'message' )) : ?>
                    <jdoc:include type="message" />
                <?php endif; ?>
            </div>
        <jdoc:include type="component" />
	</div>

<!--Modules ContentBottom-->
<?php if($this->params->get('contentbottom_auto') != '1') : ?>
<?php if ($this->countModules('contentbottom-a') || $this->countModules('contentbottom-b') || $this->countModules('contentbottom-c')) { ?>
<div class="wrapper_contentbottom">
            <?php if ($this->countModules('contentbottom-a')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_width ?>;"><div class="module_margin"><jdoc:include type="modules" name="contentbottom-a"  style="mod_standard"/></div></div><?php } ?>
            <?php if ($this->countModules('contentbottom-b')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_width ?>;"><div class="module_margin"><jdoc:include type="modules" name="contentbottom-b"  style="mod_standard"/></div></div><?php } ?>
            <?php if ($this->countModules('contentbottom-c')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_width ?>;"><div class="module_margin"><jdoc:include type="modules" name="contentbottom-c"  style="mod_standard"/></div></div><?php } ?>
            <div class="clear"></div>
            </div>
<?php }?>
            
<?php else : ?>
            
<?php if ($this->countModules('contentbottom-a') || $this->countModules('contentbottom-b') || $this->countModules('contentbottom-c')) { ?>
<div class="wrapper_contentbottom">
            <?php if ($this->countModules('contentbottom-a')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_a_manual ?>%;"><div class="module_margin"><jdoc:include type="modules" name="contentbottom-a"  style="mod_standard"/></div></div><?php } ?>
            <?php if ($this->countModules('contentbottom-b')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_b_manual ?>%;"><div class="module_margin"><jdoc:include type="modules" name="contentbottom-b"  style="mod_standard"/></div></div><?php } ?>
            <?php if ($this->countModules('contentbottom-c')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_c_manual ?>%;"><div class="module_margin"><jdoc:include type="modules" name="contentbottom-c"  style="mod_standard"/></div></div><?php } ?>
<div class="clear"></div>
</div>
    <?php }?>
<?php endif; ?>
<!--End Modules ContentBottom-->

</div>
<div class="clear"></div>
<!--End Content width and show component area-->
           
            

</div>
<div class="clear"></div>