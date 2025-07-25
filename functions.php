<?php
/**
 * Functions for the Powder WordPress theme.
 *
 * @package	Powder
 * @author	Brian Gardner
 * @license	GNU General Public License v3
 * @link	https://powder.design/
 */

if ( ! function_exists( 'powder_setup' ) ) {

	/**
	 * Initialize theme styles and support.
	 */
	function powder_setup() {

		// Enqueue editor style sheet.
		add_editor_style( get_template_directory_uri() . '/style.css' );

		// Remove core block patterns.
		remove_theme_support( 'core-block-patterns' );

	}
}
add_action( 'after_setup_theme', 'powder_setup' );

/**
 * Enqueue theme style sheet.
 */
function powder_enqueue_style_sheet() {

	wp_enqueue_style( 'powder', get_template_directory_uri() . '/style.css', array(), wp_get_theme( 'powder' )->get( 'Version' ) );

}
add_action( 'wp_enqueue_scripts', 'powder_enqueue_style_sheet' );

/**
 * Register block styles.
 */
function powder_register_block_styles() {

	$block_styles = array(
		'core/group' => array(
			'fadeinup' => __( 'Fade In Up', 'powder' ),
		),
		'core/list' => array(
			'no-style' => __( 'No Style', 'powder' ),
		),
		'core/social-links' => array(
			'outline' => __( 'Outline', 'powder' ),
		),
	);

	foreach ( $block_styles as $block => $styles ) {
		foreach ( $styles as $style_name => $style_label ) {
			register_block_style(
				$block,
				array(
					'name'  => $style_name,
					'label' => $style_label,
				)
			);
		}
	}

}
add_action( 'init', 'powder_register_block_styles' );

/**
 * Enqueue assets for Fade In Up block style.
 */
function powder_fadeinup_assets() {

	if (
		is_singular()
		&& has_block( 'core/group', get_post() )
		&& strpos( get_post()->post_content, 'is-style-fadeinup' ) !== false
	) {
		wp_enqueue_script( 'powder-fadeinup', get_template_directory_uri() . '/assets/js/fadeinup.js', [], '1.0', true );
	}

}
add_action( 'wp_enqueue_scripts', 'powder_fadeinup_assets' );

/**
 * Register pattern category.
 */
function powder_register_pattern_category( $slug, $label, $description ) {
	register_block_pattern_category(
		'powder-' . $slug,
		array(
			'label'       => __( $label, 'powder' ),
			'description' => __( $description, 'powder' ),
		)
	);
}

/**
 * Register pattern categories.
 */
function powder_register_pattern_categories() {
	$categories = array(
		'about'          => array( __( 'About', 'powder' ), __( 'A collection of about patterns for Powder.', 'powder' ) ),
		'call-to-action' => array( __( 'Call to Action', 'powder' ), __( 'A collection of call to action patterns for Powder.', 'powder' ) ),
		'content'        => array( __( 'Content', 'powder' ), __( 'A collection of content patterns for Powder.', 'powder' ) ),
		'faq'            => array( __( 'FAQs', 'powder' ), __( 'A collection of FAQ patterns for Powder.', 'powder' ) ),
		'featured'       => array( __( 'Featured', 'powder' ), __( 'A collection of featured patterns for Powder.', 'powder' ) ),
		'footer'         => array( __( 'Footers', 'powder' ), __( 'A collection of footer patterns for Powder.', 'powder' ) ),
		'gallery'        => array( __( 'Gallery', 'powder' ), __( 'A collection of gallery patterns for Powder.', 'powder' ) ),
		'header'         => array( __( 'Headers', 'powder' ), __( 'A collection of header patterns for Powder.', 'powder' ) ),
		'hero'           => array( __( 'Hero', 'powder' ), __( 'A collection of hero patterns for Powder.', 'powder' ) ),
		'posts'          => array( __( 'Posts', 'powder' ), __( 'A collection of posts patterns for Powder.', 'powder' ) ),
		'pricing'        => array( __( 'Pricing', 'powder' ), __( 'A collection of pricing patterns for Powder.', 'powder' ) ),
		'team'           => array( __( 'Team', 'powder' ), __( 'A collection of team patterns for Powder.', 'powder' ) ),
		'template'       => array( __( 'Template', 'powder' ), __( 'A collection of template patterns for Powder.', 'powder' ) ),
		'testimonials'   => array( __( 'Testimonials', 'powder' ), __( 'A collection of testimonials patterns for Powder.', 'powder' ) ),
	);

	foreach ( $categories as $slug => $details ) {
		powder_register_pattern_category( $slug, $details[0], $details[1] );
	}
}
add_action( 'init', 'powder_register_pattern_categories' );

/**
 * Check for theme updates.
 */
function powder_theme_updates( $transient ) {
    $update_url = 'https://powder.design/powder-updates.json';

    $response = wp_remote_get( $update_url );
    if ( is_wp_error( $response ) ) {
        return $transient;
    }

    $data = json_decode( wp_remote_retrieve_body( $response ) );
    if ( ! $data ) {
        return $transient;
    }

    $theme = wp_get_theme( 'powder' );
    $current_version = $theme->get( 'Version' );

    if ( version_compare( $current_version, $data->version, '<' ) ) {
        $transient->response['powder'] = array(
            'theme'       => 'powder',
            'new_version' => $data->version,
            'url'         => 'https://powder.design/changelog/',
            'package'     => $data->download_url,
        );
    }

    return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'powder_theme_updates' );
