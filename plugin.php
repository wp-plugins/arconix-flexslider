<?php
/*
  Plugin Name: Arconix FlexSlider
  Plugin URI: http://www.arconixpc.com/plugins/arconix-flexslider
  Description: A featured slider using WooThemes FlexSlider script.
  Author: John Gardner
  Author URI: http://www.arconixpc.com

  Version: 0.2

  License: GNU General Public License v2.0
  License URI: http://www.opensource.org/licenses/gpl-license.php
 */


define( 'ACFS_VERSION', '0.2' );
define( 'ACFS_URL', plugin_dir_url( __FILE__ ) );

add_action( 'after_setup_theme', 'acfs_setup', 15 );

/**
 * Loads required files and functions
 *
 * @since 0.1
 */
function acfs_setup() {

    add_action( 'wp_enqueue_scripts', 'acfs_scripts' );
    add_action( 'widgets_init', 'acfs_register_widget' );

}

/**
 * Load required CSS and JavaScript
 *
 * @since 0.1
 */
function acfs_scripts() {

    wp_register_script( 'flexslider', ACFS_URL . 'js/jquery.flexslider-min.js', array( 'jquery' ), '1.8', true );

    /** Allow user to override css by including his own */
    if( file_exists( get_stylesheet_directory() . "/arconix-flexslider.css" ) ) {
	wp_enqueue_style( 'arconix-flexslider', get_stylesheet_directory_uri() . '/arconix-flexslider.css', array( ), ACFS_VERSION );
    }
    elseif( file_exists( get_template_directory() . "/arconix-flexslider.css" ) ) {
	wp_enqueue_style( 'arconix-flexslider', get_template_directory_uri() . '/arconix-flexslider.css', array( ), ACFS_VERSION );
    }
    else {
	wp_enqueue_style( 'arconix-flexslider', ACFS_URL . 'flexslider.css', array( ), ACFS_VERSION );
    }

    /** Allow user to override javascript by including his own */
    if( file_exists( get_stylesheet_directory() . "/arconix-flexslider.js" ) ) {
	wp_register_script( 'arconix-flexslider-js', get_stylesheet_directory_uri() . '/arconix-flexslider.js', array( 'flexslider' ), ACFS_VERSION, true );
    }
    elseif( file_exists( get_template_directory() . "/arconix-flexslider.js" ) ) {
	wp_register_script( 'arconix-flexslider-js', get_template_directory_uri() . '/arconix-flexslider.js', array( 'flexslider' ), ACFS_VERSION, true );
    }
    else {
	wp_register_script( 'arconix-flexslider-js', ACFS_URL . 'js/flexslider.js', array( 'flexslider' ), ACFS_VERSION, true );
    }

    /** Only load the script if the widget is active */
    /*if( is_active_widget( '', '', 'arconix-flexslider-widget' ) )*/
        wp_enqueue_script( 'arconix-flexslider-js' );

}

/**
 * Register the Slider Widget
 *
 * @since 0.1
 */
function acfs_register_widget() {
    register_widget( 'Arconix_FlexSlider_Widget' );
}

require_once( dirname( __FILE__ ) . '/flexslider-widget.php' );

/**
 * Returns registered image sizes.
 *
 * @global array $_wp_additional_image_sizes Additionally registered image sizes
 * @return array Two-dimensional, with width, height and crop sub-keys
 * @since 0.1
 */
function acfs_get_image_sizes() {

    global $_wp_additional_image_sizes;
    $additional_sizes = array();

    $builtin_sizes = array(
	'thumbnail' => array(
	    'width' => get_option( 'thumbnail_size_w' ),
	    'height' => get_option( 'thumbnail_size_h' ),
	    'crop' => get_option( 'thumbnail_crop' ),
	),
        'medium' => array(
	    'width' => get_option( 'medium_size_w' ),
	    'height' => get_option( 'medium_size_h' ),
	),
        'large' => array(
	    'width' => get_option( 'large_size_w' ),
	    'height' => get_option( 'large_size_h' ),
	)
    );

    if( $_wp_additional_image_sizes )
	$additional_sizes = $_wp_additional_image_sizes;

    return array_merge( $builtin_sizes, $additional_sizes );
}

/**
 * Return a modified list of Post Types
 *
 * @return type array Post Types
 * @since 0.1
 * @version 0.2
 */
function acfs_get_post_types() {

    $post_types = get_post_types( '', 'names' );
    
    /** 
     * List of post types we don't want to show in the select box
     * This list can be added to by putting a filter in your functions file
     */
    $excl_post_types = apply_filters( 'acfs_exclude_post_types',
        array( 
            'revision', 
            'nav_menu_item', 
            'attachment', 
            'wpcf7_contact_form' 
        )
    );
    
    /** Loop through and exclude the items in the list */
    foreach( $excl_post_types as $excl_post_type ) {
        if( isset( $post_types[$excl_post_type] ) ) unset( $post_types[$excl_post_type] );
    }

    return $post_types;
}

/**
 * Register the dashboard widget
 * 
 * @since 0.1 
 */
function acfs_register_dashboard_widget() {
    wp_add_dashboard_widget( 'ac-flexslider', 'Arconix FlexSlider', array( $this, 'dashboard_widget_output' ) );
}

/**
 * Output for the dashboard widget
 *
 * @since 0.1
 */
function acfs_dashboard_widget_output() {
    echo '<div class="rss-widget">';

    wp_widget_rss_output( array(
        'url' => 'http://arconixpc.com/tag/arconix-flexslider/feed', // feed url
        'title' => 'Arconix FlexSlider Posts', // feed title
        'items' => 3, // how many posts to show
        'show_summary' => 1, // display excerpt
        'show_author' => 0, // display author
        'show_date' => 1 // display post date
    ) );

    echo '<div class="acfs-widget-bottom"><ul>'; ?>
                <li><img src="<?php echo ACFS_URL . 'images/page-16x16.png'; ?>"><a href="http://arcnx.co/afswiki">Wiki Page</a></li>
                <li><img src="<?php echo ACFS_URL . 'images/help-16x16.png'; ?>"><a href="http://arcnx.co/afshelp">Support Forum</a></li>
    <?php
    echo '</ul></div>';
    echo '</div>';

    // handle the styling
    echo '<style type="text/css">
            #ac-flexslider .rsssummary { display: block; }
            #ac-flexslider .acfs-widget-bottom { border-top: 1px solid #ddd; padding-top: 10px; text-align: center; }
            #ac-flexslider .acfs-widget-bottom ul { list-style: none; }
            #ac-flexslider .acfs-widget-bottom ul li { display: inline; padding-right: 9%; }
            #ac-flexslider .acfs-widget-bottom img { padding-right: 3px; vertical-align: middle; }
        </style>';
}

?>