<?php
/**
 * Graphy Theme Customizer
 *
 * @package Graphy
 */


/**
 * Implement Theme Customizer additions and adjustments.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function graphy_customize_register( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	$wp_customize->add_setting( 'graphy_hide_blogdescription', array(
		'default'           => '',
		'sanitize_callback' => 'graphy_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'graphy_hide_blogdescription', array(
		'label'    => __( 'Hide Tagline', 'graphy' ),
		'section'  => 'title_tagline',
		'settings' => 'graphy_hide_blogdescription',
		'type'     => 'checkbox',
	) );

	$wp_customize->add_section( 'graphy_logo', array(
		'title'    => __( 'Logo', 'graphy' ),
		'priority' => 20,
	) );
	$wp_customize->add_setting( 'graphy_logo', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw'
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control(	$wp_customize, 'graphy_logo', array(
		'label'    => __( 'Upload Logo', 'graphy' ),
		'section'  => 'graphy_logo',
		'settings' => 'graphy_logo',
	) ) );
	$wp_customize->add_setting( 'graphy_top_margin', array(
		'default'           => '0',
		'sanitize_callback' => 'graphy_sanitize_margin',
	) );
	$wp_customize->add_control( 'graphy_top_margin', array(
		'label'    => __( 'Top Margin (px)', 'graphy' ),
		'section'  => 'graphy_logo',
		'settings' => 'graphy_top_margin',
		'type'     => 'text',
	));
	$wp_customize->add_setting( 'graphy_bottom_margin', array(
		'default'           => '0',
		'sanitize_callback' => 'graphy_sanitize_margin',
	) );
	$wp_customize->add_control( 'graphy_bottom_margin', array(
		'label'    => __( 'Bottom Margin (px)', 'graphy' ),
		'section'  => 'graphy_logo',
		'settings' => 'graphy_bottom_margin',
		'type'     => 'text',
	));
	$wp_customize->add_setting( 'graphy_replace_blogname', array(
		'default'           => '',
		'sanitize_callback' => 'graphy_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'graphy_replace_blogname', array(
		'label'    => __( 'Replace Title', 'graphy' ),
		'section'  => 'graphy_logo',
		'settings' => 'graphy_replace_blogname',
		'type'     => 'checkbox',
	) );
	$wp_customize->add_setting( 'graphy_add_border_radius', array(
		'default'           => '',
		'sanitize_callback' => 'graphy_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'graphy_add_border_radius', array(
		'label'    => __( 'Add Border Radius', 'graphy' ),
		'section'  => 'graphy_logo',
		'settings' => 'graphy_add_border_radius',
		'type'     => 'checkbox',
	) );

	$wp_customize->add_setting( 'graphy_link_color' , array(
		'default'           => '#a62425',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'graphy_link_color', array(
		'label'    => __( 'Link Color', 'graphy' ),
		'section'  => 'colors',
		'settings' => 'graphy_link_color',
	) ) );
	$wp_customize->add_setting( 'graphy_link_hover_color' , array(
		'default'           => '#b85051',
		'transport'         => 'refresh',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'graphy_link_hover_color', array(
		'label'    => __( 'Link Hover Color', 'graphy' ),
		'section'  => 'colors',
		'settings' => 'graphy_link_hover_color',
	) ) );

	$wp_customize->add_setting( 'graphy_hide_search', array(
		'default'           => '',
		'sanitize_callback' => 'graphy_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'graphy_hide_search', array(
		'label'    => __( 'Hide Serach', 'graphy' ),
		'section'  => 'nav',
		'settings' => 'graphy_hide_search',
		'type'     => 'checkbox',
	) );

	$wp_customize->add_section( 'graphy_article', array(
		'title'    => __( 'Article', 'graphy' ),
		'priority' => 100,
	) );
	$wp_customize->add_setting( 'graphy_content', array(
		'default'           => 'summary',
		'sanitize_callback' => 'graphy_sanitize_content'
	) );
	$wp_customize->add_control( 'graphy_content', array(
		'label'    => __( 'Display', 'graphy' ),
		'section'  => 'graphy_article',
		'settings' => 'graphy_content',
		'type'     => 'select',
		'choices'  => array(
			'summary' => __( 'Summary', 'graphy' ),
			'content' => __( 'Full text', 'graphy' ),
		),
	) );
	$wp_customize->add_setting( 'graphy_hide_author', array(
		'default'           => '',
		'sanitize_callback' => 'graphy_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'graphy_hide_author', array(
		'label'    => __( 'Hide Author', 'graphy' ),
		'section'  => 'graphy_article',
		'settings' => 'graphy_hide_author',
		'type'     => 'checkbox',
	) );
	$wp_customize->add_setting( 'graphy_hide_category', array(
		'default'           => '',
		'sanitize_callback' => 'graphy_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'graphy_hide_category', array(
		'label'    => __( 'Hide Categories', 'graphy' ),
		'section'  => 'graphy_article',
		'settings' => 'graphy_hide_category',
		'type'     => 'checkbox',
	) );

}
add_action( 'customize_register', 'graphy_customize_register' );

/**
 * Sanitize user inputs.
 */
function graphy_sanitize_checkbox( $value ) {
	if ( $value == 1 ) {
		return 1;
	} else {
		return '';
	}
}
function graphy_sanitize_margin( $value ) {
	if ( preg_match("/^-?[0-9]+$/", $value) ) {
		return $value;
	} else {
		return '0';
	}
}
function graphy_sanitize_content( $value ) {
	$valid = array(
		'summary' => __( 'Summary', 'graphy' ),
		'content' => __( 'Full text', 'graphy' ),
	);

	if ( array_key_exists( $value, $valid ) ) {
		return $value;
	} else {
		return 'summary';
	}
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function graphy_customize_preview_js() {
	wp_enqueue_script( 'graphy_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20140207', true );
}
add_action( 'customize_preview_init', 'graphy_customize_preview_js' );
