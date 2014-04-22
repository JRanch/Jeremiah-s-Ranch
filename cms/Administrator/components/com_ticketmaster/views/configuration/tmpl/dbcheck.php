<?php

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

$app = JFactory::getApplication();

## Helper file for what you can do.
require_once JPATH_COMPONENT.'/helpers/ticketmaster.php';
$canDo	= ticketmasterHelper::getActions($empty=0);
$user	= JFactory::getUser();

## Include the toolbars for saving.
JToolBarHelper::title( JText::_( 'Ticketmaster Database Checker' ), 'config.png');	
## Make sure the user is authorized to click the save button

## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	include_once $path;
}else{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'menu.php';
	include_once $path;
}

include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'dbclass.php');

$database_prefix = $app->getCfg('dbprefix');
$remote_db       = str_replace("#__", $database_prefix, $this->remote["database"]);
$local_db        = $this->local[0]["Create Table"];

?>

<div class="row-fluid">
	<div class="span12">
		<?php echo JText::_( 'COM_TICKETMASTER_DB_CHECK_DESC' ); ?><br/>
	</div>
</div>

<div class="row-fluid">

  

  <div class="span2" style="text-align:center;">
	  
	  <form action = "index.php" method="POST" name="adminForm" id="adminForm" class="form-inline">
	  
	  <table class="table class="table table-striped"">
		 <th>
			<strong><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_TABLE' ); ?></strong>
		 </th>	
		 <tr>
		   <td>
			<?php echo $this->lists['tables']; ?>
		   </td>
		 </tr>	
		 <tr>
		   <td>
			<button class="btn btn-block" type="submit"><?php echo JText::_( 'COM_TICKETMASTER_CHANGE_TABLE' ); ?></button> 
		   </td>
		 </tr>			 		 
	  </table>
	
	  <input name = "option" type="hidden" value="com_ticketmaster" />	  
	  <input name = "task" type="hidden" value="dbcheck" />
	  <input name = "boxchecked" type="hidden" value="0"/>
	  <input name = "controller" type="hidden" value="configuration"/>
	  </form>
	  	
  </div>  
 
 
 
  <div class="span3" style="text-align:center;">
	
	
	<table class="table table-striped">
		<th>
			<strong><?php echo JText::_( 'COM_TICKETMASTER_YOUR_TABLE_STRUCTURE' ); ?></strong>
		</th>
		<tr>
			<td>
				<textarea style="width:97%; height:500px; font-size:85%;"><?php echo $local_db; ?></textarea>
			</td>
		</tr>
	</table>
	
  </div>
  
  <div class="span3">
	
	<table class="table table-striped">
		<th>
			<strong><?php echo JText::_( 'COM_TICKETMASTER_ORIGINAL_TABLE_STRUCTURE' ); ?></strong>
		</th>
		<tr>
			<td>
				<textarea style="width:97%; height:500px; font-size:85%;"><?php echo $remote_db; ?></textarea>
			</td>
		</tr>
	</table>
	
  </div>

  <div class="span4">
  
  <?php 
  
	$updater = new dbStructUpdater();
	$res = $updater->getUpdates($local_db, $remote_db);
	
	if(count($res) == 0) { ?>
	
		<div class="alert alert-success">
		  <strong>Great!</strong> <?php echo JText::_( 'COM_TICKETMASTER_TABLE_LOOKS_FINE' ); ?>
		</div>	
	
	
	<?php }else{ ?>
	
		<div class="alert alert-error">
		  <strong>Ooops!</strong> <?php echo JText::_( 'COM_TICKETMASTER_TABLE_LOOKS_BAD' ); ?>
		</div>	

		
		<table class="table table-striped" width="100%" style="font-size:85%;">
			<thead>
			  <th><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_TABLE_FIXES' ); ?></div></th>
			<thead>
			
		  <?php  foreach ($res as $value) { ?>
	
			<tr>
			  <td><div align="left"><?php echo $value; ?>;</div></td>
			</tr>	
					
		   <?php } ?>
			
		</table>
		
		<strong><?php echo JText::_( 'COM_TICKETMASTER_NOTE' ); ?></strong><br/>
		<?php echo JText::_( 'COM_TICKETMASTER_NOTE_DB_CHECKER' ); ?>
	
	<?php } ?>	
		

</div>  