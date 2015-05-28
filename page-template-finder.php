<?php
/**
 * Plugin Name: Page Template Finder
 * Plugin URI:  https://github.com/AaronHolbrook/page-template-finder
 * Description: Get the ID of a page based on a template slug
 * Version:     0.1.0
 * Author:      Aaron Holbrook, 10up
 * Author URI:  http://aaronjholbrook.com
 * License:     GPLv2+
 * Text Domain: ptf
 * Domain Path: /languages
 */

namespace Page_Template_Finder;

/**
 * Upon post transition, update our site option for tracking which post IDs have which page templates
 *
 * @param $new_status
 * @param $old_status
 * @param $post
 */
function update_ptf_index( $new_status, $old_status, $post ) {

	if ( 'page' !== get_post_type( $post ) ) {
		return;
	}

	if ( 'publish' !== $new_status || 'publish' !== $old_status ) {
		return;
	}

	$page_template = get_page_template_slug( $post );

	if ( empty( $page_template ) ) {
		return;
	}

	$ptf_index = get_option( 'page_template_finder_index' );

	// If this is empty, then we haven't yet set anything in our index, so let's go ahead and create it properly
	if ( empty( $ptf_index ) ) {
		$ptf_index = array();
	}

	// Currently only supports one page template for this
	$ptf_index[ $page_template ] = $post->ID;

	update_option( 'page_template_finder_index', $ptf_index );
}
add_action( 'transition_post_status', __NAMESPACE__ . '\update_ptf_index', 10, 3 );

/**
 * Given a template name, return the page ID it's related to
 *
 * @param $template_name
 *
 * @return int
 */
function get_page_id_by_template( $template_name ) {
	$ptf_index = get_option( 'page_template_finder_index' );

	if ( ! empty( $ptf_index[ $template_name ] ) ) {
		return absint( $ptf_index[ $template_name ] );
	}
}