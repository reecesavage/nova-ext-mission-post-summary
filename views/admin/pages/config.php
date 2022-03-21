<?php echo text_output($title, 'h1', 'page-head');?>

<?php echo form_open('extensions/nova_ext_mission_post_summary/Manage/config/');?>

<?php foreach($jsons['nova_ext_mission_post_summary'] as $key=>$field){ ?>
			<p>
				<kbd><?=$field['name']?></kbd>
				<input type="text" name="<?=$key?>" value="<?=$field['value']?>">	
			</p>
<?php } ?>

			<p>
				<kbd>Default Summary Field Size</kbd>
				<input type="text" name="rows" onkeypress="return (function(evt)
       			 {
           			var charCode = (evt.which) ? evt.which : event.keyCode
           			if (charCode > 31 &amp;&amp; (charCode < 48 || charCode > 57))
              		return false;
           			return true;
        })(event)" 
        value="<?=isset($jsons['setting']['rows'])?$jsons['setting']['rows']:5?>">	
			</p>


			<p>
				<kbd>Summary Field Email</kbd>
				<input type="checkbox" name="summary_mode" value="1" <?=(isset($jsons['setting']['summary_mode'])&&$jsons['setting']['summary_mode']==1 )?'checked':''?>>	
			</p>


			<br>
			<button name="submit" type="submit" class="button-main" value="Submit"><span>Update Configuration</span></button>
<?php echo form_close(); ?>



<?php if(!empty($fields)){ ?>
<?php echo form_open('extensions/nova_ext_mission_post_summary/Manage/config/');?>
        

			<p>
				<kbd>Database Columns Missing - This is expected if it is the first time you have used this Extension or an update has produced a change. Click the Create Column button below for each missing column or check the README file for manual instructions.</kbd>
				<select name="attribute">
				<?php foreach($fields as $key=>$field){?>
                  <option value="<?=$field?>"><?=$field?></option>
				<?php }?>
				</select>
			</p>

			<br>
			<button name="submit" type="submit" class="button-main" value="Add"><span>Create Column</span></button>
<?php echo form_close(); ?>
<?php } else { ?>
   <div><br>All expected columns found in the database</div>
    
<?php } ?>



<?php if(empty($feed)){ ?>

	<?php echo form_open('extensions/nova_ext_mission_post_summary/Manage/config/');?>
	<br>
	<div>Rss Feed Configuration Missing or Updated - This is expected if it is the first time you have used this Extension or an update has produced a change. Click the button below to modify your application/controlers/feed.php file or check the README file for manual instructions.</div>
	<br>
     
	<button name="submit" type="submit" class="button-main" value="feed"><span>Update Controller Configuration</span></button>


	<?php echo form_close(); ?>
<?php } else { ?>
   <div class="email-message"><br>Rss Feed located, and up to date.</div>
<?php } ?>



