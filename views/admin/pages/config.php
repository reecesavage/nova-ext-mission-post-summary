<?php
	$stateLabels = array(
		'current'      => 'Installed and up to date',
		'outdated'     => 'Installed but outdated - update available',
		'legacy'       => 'Older unmarked version present - update available',
		'missing'      => 'Not installed',
		'missing_file' => 'Feed controller file not found',
		'deferred'     => 'Handled by Ordered Mission Posts extension',
	);

	$allMissingColumns = array_merge($missing_columns['posts'], $missing_columns['missions']);
?>

<?php echo text_output($title, 'h1', 'page-head');?>


<?php /* ---------- Status ---------- */ ?>

<?php echo text_output('Status', 'h3', 'page-subhead');?>

<table class="table100 zebra">
	<tbody>
		<tr>
			<td class="cell-label">Database columns</td>
			<td class="cell-spacer"></td>
			<td>
				<?php if (empty($allMissingColumns)): ?>
					All present
				<?php else: ?>
					<?php echo count($allMissingColumns);?> missing
					(<?php echo implode(', ', $allMissingColumns);?>)
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="cell-label">RSS feed code</td>
			<td class="cell-spacer"></td>
			<td><?php echo $stateLabels[$feed_state];?></td>
		</tr>
	</tbody>
</table>

<br>


<?php /* ---------- Database setup ---------- */ ?>

<?php echo text_output('Database', 'h3', 'page-subhead');?>

<?php if ( ! $db_ready): ?>
	<p>
		One click will add the missing columns to <code><?php echo $this->db->dbprefix;?>posts</code> and
		<code><?php echo $this->db->dbprefix;?>missions</code>. Safe to re-run.
	</p>
	<?php echo form_open('extensions/nova_ext_mission_post_summary/Manage/config/');?>
		<button name="action" type="submit" class="button-main" value="setup_database"><span>Set Up Database</span></button>
	<?php echo form_close();?>
<?php else: ?>
	<p>All required columns are present.</p>
<?php endif; ?>

<br>


<?php /* ---------- Feed code ---------- */ ?>

<?php echo text_output('RSS feed code', 'h3', 'page-subhead');?>

<?php if ($feed_state === 'current'): ?>
	<p>The RSS feed code in <code>application/controllers/Feed.php</code> is up to date.</p>

<?php elseif ($feed_state === 'deferred'): ?>
	<p>
		The Ordered Mission Posts extension's feed shim is already installed in
		<code>application/controllers/Feed.php</code>. That extension's feed builder already includes the post
		summary, so there's nothing to install here. Manage the feed code from the Ordered Mission Posts
		admin page.
	</p>

<?php elseif ($feed_state === 'missing_file'): ?>
	<p>
		<code>application/controllers/Feed.php</code> was not found. Restore the file from your Nova install before continuing.
	</p>

<?php else: ?>
	<p>
		<?php if ($feed_state === 'outdated'): ?>
			The injected feed code in <code>application/controllers/Feed.php</code> is out of date and will be replaced.
		<?php elseif ($feed_state === 'legacy'): ?>
			An older, unmarked version of <code>posts()</code> is present in <code>application/controllers/Feed.php</code>
			and will be replaced with the current shim.
		<?php else: ?>
			Inject the summary-aware RSS feed shim into <code>application/controllers/Feed.php</code> so
			<code>/feed/posts</code> includes the post summary.
		<?php endif; ?>
	</p>
	<?php echo form_open('extensions/nova_ext_mission_post_summary/Manage/config/');?>
		<button name="action" type="submit" class="button-main" value="install_feed">
			<span><?php echo ($feed_state === 'missing') ? 'Install Feed Code' : 'Update Feed Code';?></span>
		</button>
	<?php echo form_close();?>
<?php endif; ?>

<br>


<?php /* ---------- Labels + configuration ---------- */ ?>

<?php echo text_output('Labels and configuration', 'h3', 'page-subhead');?>

<?php echo form_open('extensions/nova_ext_mission_post_summary/Manage/config/');?>
	<?php foreach ($jsons['nova_ext_mission_post_summary'] as $key => $field): ?>
		<p>
			<kbd><?php echo $field['name'];?></kbd>
			<input type="text" name="<?php echo $key;?>" value="<?php echo htmlspecialchars($field['value'], ENT_QUOTES);?>">
		</p>
	<?php endforeach; ?>

	<p>
		<kbd>Default Summary Field Size (rows)</kbd>
		<input type="text" name="rows"
			onkeypress="return (function(evt){var charCode=(evt.which)?evt.which:event.keyCode;if(charCode>31 && (charCode<48||charCode>57)) return false; return true;})(event)"
			value="<?php echo isset($jsons['setting']['rows']) ? (int) $jsons['setting']['rows'] : 5;?>">
	</p>

	<p>
		<kbd>Include summary in post emails</kbd>
		<input type="checkbox" name="summary_mode" value="1" <?php echo (isset($jsons['setting']['summary_mode']) && $jsons['setting']['summary_mode'] == 1) ? 'checked' : '';?>>
	</p>

	<br>
	<button name="action" type="submit" class="button-main" value="save_config"><span>Save Configuration</span></button>
<?php echo form_close();?>
