<?php
/**
 * Plugin Name: Markdown To HTML
 * Plugin URI: http://github.com/times/wp-markdown-to-html
 * Description: Converts markdown to HTML in the specified post, or all posts of a specified post type
 * Version: 0.0.1
 * Author: Chris Hutchinson
 * Author URI: http://github.com/chrishutchinson
 * Text Domain: wp-markdown-to-html
 * License: GPL2
 */

require_once dirname(__FILE__) . '/vendor/Michelf/Markdown.inc.php';
use \Michelf\Markdown;

class MarkdownToHTML {

	// Initialise
	function MarkdownToHTML() {
		// Setup defaults
		$this->plugin = new stdClass();
		$this->plugin->name = 'Markdown to HTML';
		$this->plugin->version = '0.0.1';
		$this->plugin->folderName = basename(dirname(__FILE__));
		$this->plugin->folderPath = dirname(__FILE__);

		// Hooks
		add_action('admin_menu', array($this, 'pluginMenuItem'));

		add_action('admin_enqueue_scripts', array($this, 'adminScriptsAndStyles'));

		// Filters
	}

	function adminScriptsAndStyles() {
		// JS
		wp_enqueue_script($this->plugin->folderName . '-select2', plugins_url('js/vendor/select2/select2.min.js', __FILE__), array('jquery'), $this->plugin->version, true);
		wp_enqueue_script($this->plugin->folderName . '-admin-js', plugins_url('js/script.js', __FILE__), array('jquery'), $this->plugin->version, true);

		// CSS
		wp_enqueue_style($this->plugin->folderName . '-admin-css', plugins_url('css/style.css', __FILE__), array(), $this->plugin->version);
		wp_enqueue_style($this->plugin->folderName . '-select2', plugins_url('js/vendor/select2/select2.css', __FILE__), array(), $this->plugin->version);
	}

	function pluginMenuItem() {
		add_options_page($this->plugin->name . ' Settings', $this->plugin->name, 'manage_options', $this->plugin->folderName . '.php', array($this, 'pluginMenuPage'));
	}

	function pluginMenuPage() {
		if(isset($_POST['settings'])) {
			if(isset($_POST['settings']['type']) && !empty($_POST['settings']['type'])) {
				// We're converting an entire post type
				// Get the posts in this post type
				$postArgs = array(
					'posts_per_page' => -1,
					'post_type' => $_POST['settings']['type'],
				);
				$posts = get_posts($postArgs);

				// If we find posts, loop through them and update each one
				if(count($posts) > 0) {
					foreach($posts as $post) {
						$this->updatePost($post->ID);
					}
				}

				// Return a success message
				$message = array(
					'type' => 'success',
					'message' => 'All posts in the selected type have been successfully converted.'
				);
			} elseif(isset($_POST['settings']['single']) && !empty($_POST['settings']['single'])) {
				// We're converting a single post
				$this->updatePost($_POST['settings']['single']);

				// Return a success message
				$message = array(
					'type' => 'success',
					'message' => 'The selected post has been successfully converted.'
				);
			} else {
				// Who knows what we've been sent, error.
				$message = array(
					'type' => 'error',
					'message' => 'An unexpected error occurred, please try again.'
				);
			}
		}

		// Get the posts types
		$postTypes = get_post_types(array(
			'public' => true,
		), 'objects');

		// Setup a posts array
		$postsArray = array();

		// Loop through each post type
		foreach($postTypes as $key => $type) {
			// Get posts in each post type
			$postArgs = array(
				'posts_per_page' => -1,
				'post_type' => $key,
			);
			$posts = get_posts($postArgs);

			// Setup a final array to build the <select>s in the template
			if(count($posts) > 0) {
				$postsArray[] = array(
					'name' => $type->labels->name,
					'slug' => $key,
					'posts' => $posts
				);
			}
		}

		// Include the template
		include_once($this->plugin->folderPath . '/partials/settings.php');
	}

	// Run the required functions to update the post
	function updatePost($id) {
		// Get the post
		$post = get_post($id);

		// If we have a post
		if(!is_null($post)) {
			// Store and convert the body
			$oldPostBody = $post->post_content;
			$postBody = Markdown::defaultTransform($oldPostBody);

			// Update the post body
			$updatedPost = wp_update_post(array(
				'ID' => $post->ID,
				'post_content' => $postBody,
			));

			// Store the old markdown in a custom field for reference
			update_post_meta($post->ID, 'markdown_post_content', $oldPostBody);
		}
	}

};

$MarkdownToHTML = new MarkdownToHTML();