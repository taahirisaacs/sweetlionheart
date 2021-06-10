<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '2.2.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_load_textdomain', [ true ], '2.0', 'hello_elementor_load_textdomain' );
		if ( apply_filters( 'hello_elementor_load_textdomain', $hook_result ) ) {
			load_theme_textdomain( 'hello-elementor', get_template_directory() . '/languages' );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_menus', [ true ], '2.0', 'hello_elementor_register_menus' );
		if ( apply_filters( 'hello_elementor_register_menus', $hook_result ) ) {
			register_nav_menus( array( 'menu-1' => __( 'Primary', 'hello-elementor' ) ) );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_theme_support', [ true ], '2.0', 'hello_elementor_add_theme_support' );
		if ( apply_filters( 'hello_elementor_add_theme_support', $hook_result ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				array(
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				)
			);
			add_theme_support(
				'custom-logo',
				array(
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				)
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'editor-style.css' );

			/*
			 * WooCommerce.
			 */
			$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_woocommerce_support', [ true ], '2.0', 'hello_elementor_add_woocommerce_support' );
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', $hook_result ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$enqueue_basic_style = apply_filters_deprecated( 'elementor_hello_theme_enqueue_style', [ true ], '2.0', 'hello_elementor_enqueue_style' );
		$min_suffix          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', $enqueue_basic_style ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_elementor_locations', [ true ], '2.0', 'hello_elementor_register_elementor_locations' );
		if ( apply_filters( 'hello_elementor_register_elementor_locations', $hook_result ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = \Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * Wrapper function to deal with backwards compatibility.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		} else {
			do_action( 'wp_body_open' );
		}
	}
}
// Remove anchor jump on Gravity Forms
add_filter( 'gform_confirmation_anchor', '__return_true' );

//Send user straight to checkout
// add_filter( 'woocommerce_add_to_cart_redirect', 'skip_woo_cart' );
 
// function skip_woo_cart() {
//    return wc_get_checkout_url();
// } 

add_filter( 'woocommerce_product_single_add_to_cart_text', 'cw_btntext_cart' );
add_filter( 'woocommerce_product_add_to_cart_text', 'cw_btntext_cart' );
function cw_btntext_cart() {
    return __( 'Purchase', 'woocommerce' );
}

add_action( 'wp_head', function(){
    ?>
    <meta name="p:domain_verify" content="6e15907dd45d109643f4bbbd47e901b1"/>
    <!-- Facebook Pixel Code -->
	<script>
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
	n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
	document,'script','//connect.facebook.net/en_US/fbevents.js');

	fbq('init', '755488865292476');
	fbq('track', 'PageView');</script>
	<noscript><img height='1' width='1' style='display:none'
	src='https://www.facebook.com/tr?id=755488865292476/&ev=PageView&noscript=1'
	/></noscript>
	<!-- End Facebook Pixel Code -->
	<?php
});

add_action( 'init', 'cake_flavours' );
function cake_flavours() {
	$args = [
		'label'  => esc_html__( 'Flavours', 'flavour' ),
		'labels' => [
			'menu_name'          => esc_html__( 'Flavours', 'flavour' ),
			'name_admin_bar'     => esc_html__( 'Flavour', 'flavour' ),
			'add_new'            => esc_html__( 'Add Flavour', 'flavour' ),
			'add_new_item'       => esc_html__( 'Add new Flavour', 'flavour' ),
			'new_item'           => esc_html__( 'New Flavour', 'flavour' ),
			'edit_item'          => esc_html__( 'Edit Flavour', 'flavour' ),
			'view_item'          => esc_html__( 'View Flavour', 'flavour' ),
			'update_item'        => esc_html__( 'View Flavour', 'flavour' ),
			'all_items'          => esc_html__( 'All Flavours', 'flavour' ),
			'search_items'       => esc_html__( 'Search Flavours', 'flavour' ),
			'parent_item_colon'  => esc_html__( 'Parent Flavour', 'flavour' ),
			'not_found'          => esc_html__( 'No Flavours found', 'flavour' ),
			'not_found_in_trash' => esc_html__( 'No Flavours found in Trash', 'flavour' ),
			'name'               => esc_html__( 'Flavours', 'flavour' ),
			'singular_name'      => esc_html__( 'Flavour', 'flavour' ),
		],
		'public'              => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'show_in_rest'        => true,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite_no_front'    => false,
		'show_in_menu'        => true,
		'supports' => [
			'title',
			'editor',
			'thumbnail',
		],
		
		'rewrite' => true
	];

	register_post_type( 'flavour', $args );
}

/**
 * Custom Taxonomy for Offers/Campaigns
 */
add_action( 'init', 'create_my_taxonomies', 0 );
function create_my_taxonomies() {
	register_taxonomy(
		'category_flavours',
		'flavour',
		array(
			'labels' => array(
				'name' => 'Category',
				'add_new_item' => 'Add New Category',
				'new_item_name' => "New Category"
			),
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true
		)
	);
}

// add_filter( 'gform_pre_render', 'populate_posts' );
// add_filter( 'gform_pre_validation', 'populate_posts' );
// add_filter( 'gform_pre_submission_filter', 'populate_posts' );
// add_filter( 'gform_admin_pre_render', 'populate_posts' );
// function populate_posts( $form ) {
 
//     foreach ( $form['fields'] as $field ) {
 
//         if ( $field->type != 'product' || $field->inputName == 'populate' || strpos( $field->cssClass, 'green' ) === false ) {
//             // you can add additional parameters here to alter the posts that are retrieved
// 		// more info: http://codex.wordpress.org/Template_Tags/get_posts
// 		$options = array(
// 			'post_type' => 'flavour',
// 			'numberposts' => -1,
// 			'post_status' => 'publish',
// 			'tax_query' => array(
// 				array(
// 					'taxonomy' => 'category_flavours', // Here I have set dummy taxonomy name like "taxonomy_cat" but you must be set current taxonomy name of annoucements post type. 
// 					'field' => 'name',
// 					'terms' => 'green'
// 				)
// 			)
// 		);

//         $posts = get_posts( $options );
 
//         $choices = array();
 
//         foreach ( $posts as $post ) {
//             $choices[] = array( 'text' => $post->post_title, 'value' => $post->post_title, 'price' => $post->post_content);
//         }
 
//         // update 'Select a Post' to whatever you'd like the instructive option to be
//         $field->placeholder = 'Select a Flavour';
//         $field->choices = $choices;
//         }
 
//     }
 
//     return $form;
// }
/**
 * Filter to change the Rank Math schema data for Product.
 * @param array $entity
 * @return array
 */
add_filter('rank_math/snippet/rich_snippet_product_entity', function ($entity) {
    $entity['@id'] = get_permalink().'#product';
    return $entity;
});
