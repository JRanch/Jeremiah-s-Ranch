<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );?>



<!-- Hornav Dropdown -->
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/dropdown.js" charset="utf-8"></script>
<script type="text/javascript" >
window.addEvent('domready', function() {
	var myMenu = new MenuMatic();
});
</script>


<!-- Equalize Top1 Module Heights -->
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/equalizer.js" charset="utf-8"></script>
<script type="text/javascript" >
window.addEvent('domready', function () {
	var columnizer = new Equalizer('.top-1 .module').equalize('height');
	});
</script>

<!-- Equalize Top2 Module Heights -->
<script type="text/javascript" >
window.addEvent('domready', function () {
	var columnizer = new Equalizer('.top-2 .module').equalize('height');
	});
</script>

<!-- Equalize Contenttop Module Heights -->
<script type="text/javascript" >
window.addEvent('domready', function () {
	var columnizer = new Equalizer('.contenttop .module').equalize('height');
	});
</script>

<!-- Equalize Contentbottom Module Heights -->
<script type="text/javascript" >
window.addEvent('domready', function () {
	var columnizer = new Equalizer('.contentbottom .module').equalize('height');
	});
</script>

<!-- Equalize Bottom1 Module Heights -->
<script type="text/javascript" >
window.addEvent('domready', function () {
	var columnizer = new Equalizer('.bottom-1 .module').equalize('height');
	});
</script>

<!-- Equalize Bottom2 Module Heights -->
<script type="text/javascript" >
window.addEvent('domready', function () {
	var columnizer = new Equalizer('.bottom-2 .module').equalize('height');
	});
</script>

<!-- Equalize Base1 Module Heights -->
<script type="text/javascript" >
window.addEvent('domready', function () {
	var columnizer = new Equalizer('.base-1 .module').equalize('height');
	});
</script>

<!-- Equalize Base2 Module Heights -->
<script type="text/javascript" >
window.addEvent('domready', function () {
	var columnizer = new Equalizer('.base-2 .module').equalize('height');
	});
</script>



