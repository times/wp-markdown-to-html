<div class="wrap">
<h2><?php echo $this->plugin->name; ?> Settings</h2>

<?php
if(isset($message)) {
	switch($message['type']) {
		case 'success':
			?>
			<div class="updated settings-error"> 
			<p><strong><?php echo $message['message']; ?></strong></p></div>
			<?php
			break;
		case 'error':
		default:
			?>
			<div class="error settings-error"> 
			<p><strong><?php echo $message['message']; ?></strong></p></div>
			<?php
			break;
	}
}
?>

<div id="poststuff">
	<div class="postbox-container wp-postbox-plugin">
		<div class="meta-box-sortables ui-sortable" style="">
			<div class="postbox">
				<h3 class="hndle"><span>Single Post</span></h3>
				
				<div class="inside">
					<form method="post">
						<p><label for="single">Select a single post to convert</label></p>
						<select id="single" name="settings[single]" class="select2">
							<?php
							foreach($postsArray as $types) {
								?>
								<optgroup label="<?php echo $types['name']; ?>">
									<?php
									foreach($types['posts'] as $post) {
										?>
										<option value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
										<?php
									}
									?>
								</optgroup>
								<?php
							}
							?>
						</select>

						<p class="submit">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="Convert Single Post">
						</p>
					</form>
				</div>
			</div>

			<div class="postbox">
				<h3 class="hndle"><span>Post Type or Custom Post Type</span></h3>
				
				<div class="inside">
					<form method="post">
						<p><label for="single">Select an entire post type to convert</label></p>
						<select id="single" name="settings[type]" class="select2">
							<?php
							foreach($postsArray as $types) {
								?>
								<option value="<?php echo $types['slug']; ?>"><?php echo $types['name']; ?></option>
								<?php
							}
							?>
						</select>

						<p class="submit">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="Convert All Posts">
						</p>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

</div>