<?php
/**
 * PiedmontGlobal Theme Customizer
 *
 * @package PiedmontGlobal
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function pg_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'pg_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'pg_customize_partial_blogdescription',
			)
		);
	}

	// CyberSource Unified Checkout (donations)
	$wp_customize->add_section(
		'pg_cybersource',
		array(
			'title'       => __( 'CyberSource (Donations)', 'pg' ),
			'description' => __( 'Credentials from CyberSource Business Centre → Key Management. Used for the Donate page payment form.', 'pg' ),
			'priority'    => 160,
		)
	);
	$wp_customize->add_setting( 'cybersource_merchant_id', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_setting( 'cybersource_key_id', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_setting( 'cybersource_shared_secret', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_setting( 'cybersource_env', array(
		'default'           => 'test',
		'sanitize_callback' => function ( $v ) {
			return ( $v === 'production' ) ? 'production' : 'test';
		},
	) );
	$wp_customize->add_control( 'cybersource_merchant_id', array(
		'label'   => __( 'Merchant ID', 'pg' ),
		'section' => 'pg_cybersource',
		'type'    => 'text',
	) );
	$wp_customize->add_control( 'cybersource_key_id', array(
		'label'   => __( 'Key ID (API key serial number)', 'pg' ),
		'section' => 'pg_cybersource',
		'type'    => 'text',
	) );
	$wp_customize->add_control( 'cybersource_shared_secret', array(
		'label'   => __( 'Shared Secret', 'pg' ),
		'section' => 'pg_cybersource',
		'type'    => 'text',
		'input_attrs' => array( 'autocomplete' => 'off' ),
	) );
	$wp_customize->add_control( 'cybersource_env', array(
		'label'   => __( 'Environment', 'pg' ),
		'section' => 'pg_cybersource',
		'type'    => 'select',
		'choices' => array(
			'test'       => __( 'Test (sandbox)', 'pg' ),
			'production' => __( 'Production', 'pg' ),
		),
	) );
}
add_action( 'customize_register', 'pg_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function pg_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function pg_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function pg_customize_preview_js() {
	wp_enqueue_script( 'pg-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), _S_VERSION, true );
}
add_action( 'customize_preview_init', 'pg_customize_preview_js' );
