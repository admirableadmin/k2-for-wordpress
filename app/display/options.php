<?php

	global $wpdb;

	if ( K2_USING_STYLES ) {
		// Get the current K2 Style
		$style_name = get_option('k2scheme');
		$style_title = $style_name !== false ? $style_name : __('No Style', 'k2_domain');
		$style_info = get_option('k2styleinfo');

		// Check that the styles folder exists
		$is_styles_dir = is_dir(K2_STYLES_PATH);

		// Get the scheme files
		$style_files = K2::get_styles();
	}

	// Check that the K2 folder has no spaces
	$dir_has_spaces = (strpos(TEMPLATEPATH, ' ') !== false);

	// Get the sidebar
	$column_number = get_option('k2columns');
	$column_options = array(
		1 => __('Single Column', 'k2_domain'),
		__('Two Columns', 'k2_domain'),
		__('Three Columns', 'k2_domain')
	);

	// Get the asides category
	$asides_id = get_option('k2asidescategory');

	// Get the categories we might use for asides
	$asides_cats = get_categories('get=all');

	// Get the current header picture
	$current_header_image = get_theme_mod('header_image');

	// Get the header pictures
	$header_images = K2Header::get_header_images();
?>

<script>
	jQuery(document).scroll(function() { smartPosition('.configstuff') });
</script>


<?php if(isset($_POST['submit']) or isset($_GET['updated'])) { ?>
<div id="message2" class="updated fade">
	<p><?php _e('K2 Options have been updated', 'k2_domain'); ?></p>
</div>
<?php } ?>

<?php if(isset($_POST['configela'])) { ?>
<div id="message2" class="updated fade">
	<p><?php _e('The Extended Live Archives plugin has been setup for use with K2', 'k2_domain'); ?></p>
</div>
<?php } ?>

<div class="k2wrap">
	<?php if ( K2_USING_STYLES and !$is_styles_dir ) { ?>
		<div class="error"><small>
		<?php printf(__('<p>The directory: <code>%s</code>, needed to store custom styles is missing.</p><p>For you to be able to use custom styles, you need to add this directory.</p>', 'k2_domain'), K2_STYLES_PATH ); ?>
		</small></div>
	<?php } ?>

	<?php if ($dir_has_spaces) { ?>
		<div class="error"><small>
		<?php printf( __('<p>The K2 directory: <code>%s</code>, contains spaces. For K2 to function properly, you will need to remove the spaces from the directory name.</p>', 'k2_domain'), TEMPLATEPATH ); ?>
		</small></div>
	<?php } ?>


	<form name="dofollow" action="<?php echo attribute_escape($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('k2options'); ?>

		<div class="configstuff">

			<div class="savebutton">
				<input type="submit" id="save" name="save" value="<?php echo attribute_escape(__('Save', 'k2_domain')); ?>" />
			</div><!-- .savebutton -->

			<div class="container">
				<h3><label for="k2-sidebarmanager"><?php _e('Sidebar Manager', 'k2_domain'); ?></label></h3>

				<p class="checkboxelement"><input id="k2-sidebarmanager" name="k2[sidebarmanager]" type="checkbox" value="1" <?php checked('1', get_option('k2sidebarmanager')); ?> />
				<!--<label for="k2-sidebarmanager"><?php _e('Enable K2\'s Sidebar Manager', 'k2_domain'); ?></label>--></p>

				<p class="description"><?php printf(__('K2 has a neat sidebar system. If disabled, K2 reverts to WordPress widgets.', 'k2_domain'), $column_options[1]); ?></p>

				<?php if (get_option('k2sidebarmanager') == 0) { /* Only show column dropdown if SBM is disabled */ ?>
				<p>
					<select id="k2-columns" name="k2[columns]">
					<?php foreach ($column_options as $option => $label) { ?>
						<option value="<?php echo $option; ?>" <?php selected($column_number, $option); ?>><?php echo $label; ?></option>
					<?php } ?>
					</select>
				</p>
				<?php } ?>
			</div><!-- .container -->


			<div class="container">
				<h3><label for="k2-advnav"><?php _e('Advanced Navigation','k2_domain'); ?></label></h3>

				<p class="checkboxelement"><input id="k2-advnav" name="k2[advnav]" type="checkbox" value="1" <?php checked('1', get_option('k2livesearch')); ?> />
				<!--<label for="k2-advnav"><?php _e('Enable Advanced Navigation','k2_domain'); ?></label>--></p>

				<p class="description"><?php _e('Seamlessly search and navigate old posts.','k2_domain'); ?></p>
			</div><!-- .container -->


			<div class="container">
				<h3><label for="k2-archives"><?php _e('Archives Page', 'k2_domain'); ?></label></h3>

				<p class="checkboxelement"><input id="k2-archives" name="k2[archives]" type="checkbox" value="add_archive" <?php checked('add_archive', get_option('k2archives')); ?> />
				<!--<label for="k2-archives"><?php _e('Enable Archives Page', 'k2_domain'); ?></label>--></p>

				<p class="description"><?php _e('Installs a pre-made archives page.', 'k2_domain'); ?></p>

				<?php if (!function_exists('af_ela_set_config') && ($wp_version > 2.2)) { ?>
					<?php printf(__('We recommend you install %s for maximum archival pleasure.','k2_domain'), '<a href="http://www.sonsofskadi.net/index.php/extended-live-archive/">' . __('Arnaud Froment\'s Extended Live Archives', 'k2_domain') . '</a>'); ?></p>
				<?php } else if (function_exists('af_ela_set_config')) { ?>
					</p><p class="configelap"><input id="configela" name="configela" type="submit" value="<?php echo attribute_escape(__('Configure Extended Live Archives for K2', 'k2_domain')); ?>" /></p>
				<?php } ?>
			</div><!-- .container -->


			<div class="container">
				<h3><label for="k2-livecommenting"><?php _e('Live Commenting', 'k2_domain'); ?></label></h3>

				<p class="checkboxelement"><input id="k2-livecommenting" name="k2[livecommenting]" type="checkbox" value="1" <?php checked('1', get_option('k2livecommenting')); ?> />
				<!--<label for="k2-livecommenting"><?php _e('Enable Live Commenting', 'k2_domain'); ?></label>--></p>
				
				<p class="description"><?php _e('Submit comments without reloading the page.', 'k2_domain'); ?></p>
			</div><!-- .container -->


			<div class="container">
				<h3><?php _e('Asides', 'k2_domain'); ?></h3>

				<select id="k2-asidescategory" name="k2[asidescategory]">
					<option value="0" <?php selected($asides_id, '0'); ?>><?php _e('Off', 'k2_domain'); ?></option>

					<?php foreach ($asides_cats as $cat) { ?>
					<option value="<?php echo attribute_escape($cat->cat_ID); ?>" <?php selected($asides_id, $cat->cat_ID); ?>><?php echo($cat->cat_name); ?></option>
					<?php } ?>
				</select>

				<p class="description"><?php _e('Aside posts are styled differently and can be placed on the sidebar.', 'k2_domain'); ?></p>

			</div><!-- .container -->


			<?php if (K2_USING_STYLES and $is_styles_dir) { ?>
			<div class="container">
				<h3><?php _e('Style', 'k2_domain'); ?></h3>

				<select id="k2-scheme" name="k2[scheme]">
					<option value="" <?php selected($style_name, ''); ?>><?php _e('Off', 'k2_domain'); ?></option>

					<?php foreach($style_files as $style_file) { ?>
					<option value="<?php echo attribute_escape($style_file); ?>" <?php selected($style_name, $style_file); ?>><?php echo($style_file); ?></option>
					<?php } ?>
				</select>

				<p class="description"><?php printf(__('No need to edit core files, K2 is highly customizable using only CSS. %s', 'k2_domain'), '<a href="http://code.google.com/p/kaytwo/wiki/K2CSSandCustomCSS">' . __('Read&nbsp;more', 'k2_domain') . '</a>.'  ); ?></p>
			</div><!-- .container -->
			<?php } ?>


			<div class="container headercontainer">
				<h3><?php _e('Header', 'k2_domain'); ?></h3>

				<p class="description"><?php
					printf(
						__('The current header size is <strong>%1$s px by %2$s px</strong>. Use %3$s to customize the header.', 'k2_domain'),
						empty($style_info['header_width'])? K2_HEADER_WIDTH : $style_info['header_width'],
						empty($style_info['header_height'])? K2_HEADER_HEIGHT : $style_info['header_height'],
						'<a href="themes.php?page=custom-header">' . __('Custom Image Header', 'k2_domain') . '</a>'
					); ?></p>

				<div class="headerwrap">
					<div>
						<span class="span1"><p><?php _e('Select an Image', 'k2_domain'); ?></p></span>

						<span class="span2">
							<select id="k2-header-picture" name="k2[header_picture]">
								<option value="" <?php selected($current_header_image, ''); ?>><?php _e('Off', 'k2_domain'); ?></option>
								<option value="random" <?php selected($current_header_image, 'random'); ?>><?php _e('Random', 'k2_domain'); ?></option>
								<?php foreach($header_images as $picture_file): ?>
								<option value="<?php echo attribute_escape($picture_file); ?>" <?php selected($current_header_image, $picture_file); ?>><?php echo basename($picture_file); ?></option>
								<?php endforeach; ?>
							</select>
						</span>
					</div>

					<div>
						<span class="span1"><p><?php _e('Rename the \'Blog\' tab', 'k2_domain'); ?></p></span>

						<span class="span2"><input id="k2-blogornoblog" name="k2[blogornoblog]" value="<?php echo attribute_escape(get_option('k2blogornoblog')); ?>" /></span>
					</div>
				</div>

			</div><!-- .container .headercontainer -->
				
		</div><!-- .configstuff -->

</div>

<div class="k2wrap uninstall">


		<div class="configstuff">
			<h3><?php _e('Uninstall K2', 'k2_domain'); ?></h3>

			<script type="text/javascript">
			function confirmUninstall() {
				if (confirm("<?php _e('Delete your K2 settings?', 'k2_domain'); ?>") == true) {
					return true;
				} else {
					return false;
				}
			}
			</script>


			<p class="description"><?php _e('Remove all K2 settings and revert WordPress to its default theme. No files are deleted.', 'k2_domain'); ?></p>
			<p style="text-align: center;"><input id="uninstall" name="uninstall" type="submit" onClick="return confirmUninstall()" value="<?php echo attribute_escape(__('Reset and Uninstall K2', 'k2_domain')); ?>" /></p>
		</div>

</div>

	</form>
</div>
