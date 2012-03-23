<?php
/**
 * FlexSlider Widget
 *
 * @since 0.1
 */
class Arconix_FlexSlider_Widget extends WP_Widget {

    /**
     * Holds widget settings defaults, populated in constructor.
     *
     * @var array
     * @since 0.1
     */
    protected $defaults;

    /**
     * Constructor. Set the default widget options, create widget, and load the js
     *
     * @since 0.1
     */
    function __construct() {

	$this->defaults = array(
	    'title'	    => '',
	    'post_type'	    => '',
	    'image_size'    => '',
	    'post_num'	    => 5,
	    'show_caption'  => 0
	);

        $widget_ops = array(
            'classname' => 'flexslider_widget',
            'description' => __( 'Featured Slider', 'acfs' ),
        );

        $control_ops = array(
	    'id_base' => 'arconix-flexslider-widget'
	);

        $this->WP_Widget( 'arconix-flexslider-widget', 'Arconix - FlexSlider', $widget_ops, $control_ops );

        if ( is_active_widget( false, false, $this->id_base ) )
            add_action( 'wp_head', array( $this, 'load_js' ), 99 );
    }

    function load_js() { /*?>
        <script type="text/javascript" charset="utf-8">
	    jQuery(window).load(function() {
		jQuery('.flexslider').flexslider( {
		    pauseOnHover: true,
		    controlsContainer: ".flex-container"
		} );
	    } );
	</script>
	<?php */
    }

    /**
     * Widget Output
     *
     * @param type $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param type $instance The settings for the particular instance of the widget
     * @since 0.1
     */
    function widget( $args, $instance ) {

	extract( $args );

	/** Merge with defaults */
	$instance = wp_parse_args( ( array )$instance, $this->defaults );

	/** Before widget (defined by themes) */
	echo $before_widget;

	/** Title of widget (before and after defined by themes) */
	if( !empty( $instance['title'] ) )
	    echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;


	$query_args = array(
	    'post_type' => $instance['post_type'],
	    'posts_per_page' => $instance['post_num'],
	);

	$flex_posts = new WP_Query( $query_args );

	if ( $flex_posts->have_posts() ) {
	    echo '<div class="flex-container">
                <div class="flexslider">
                <ul class="slides">';
                while ( $flex_posts->have_posts() ) : $flex_posts->the_post();
                    echo '<li><a href="';
                    the_permalink();
                    echo '" rel="bookmark">';
                    the_post_thumbnail( $instance['image_size'] );
                    if ( ! empty( $instance['show_caption'] ) ) {
                        echo '<p class="flex-caption">';
                        the_title();
                        echo '</p>';
                    }
                    echo '</a></li>';
                endwhile;
            echo '</ul></div></div>';
        }
        
        /** After widget (defined by themes) */
        echo $after_widget;


    }

    /**
     * Update a particular instance.
     *
     * @param array $new_instance New settings for this instance as input by the user via form()
     * @param array $old_instance Old settings for this instance
     * @return array Settings to save or bool false to cancel saving
     * @since 0.1
     */
    function update( $new_instance, $old_instance ) {

	$new_instance['title'] = strip_tags( $new_instance['title'] );
	$new_instance['post_num'] = strip_tags( $new_instance['post_num'] );

	return $new_instance;
    }

    /**
     * Widget form
     *
     * @param array $instance Current settings
     * @since 0.1
     */
    function form( $instance ) {

	/** Merge with defaults */
	$instance = wp_parse_args( (array) $instance, $this->defaults );
	?>

	<!-- Title: Input Box -->
	<p>
	    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'acfs' ); ?>:</label>
	    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
	</p>

	<!-- Post Type: Select Box -->
	<p>
	    <label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type', 'acfs' ); ?>:</label>
	    <select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
		<?php
		$types = acfs_get_post_types();
		foreach( $types as $type )
		    echo '<option value="' . esc_attr( $type ) . '" ' . selected( $type, $instance['post_type'], FALSE ) . '>' . esc_html( $type ) . '</option>';
		?>
	    </select>

	<!-- Image Size: Select Box -->
	<p>
	    <label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Image Size', 'acfs' ); ?>:</label>
	    <select id="<?php echo $this->get_field_id( 'image_size' ); ?>" name="<?php echo $this->get_field_name( 'image_size' ); ?>">
		<?php
		$sizes = acfs_get_image_sizes();
		foreach( (array) $sizes as $name => $size )
		    echo '<option value="' . esc_attr( $name ) . '" ' . selected( $name, $instance['image_size'], FALSE ) . '>' . esc_html( $name ).' ( ' . $size['width'] . 'x' . $size['height'] . ' )</option>';
		?>
	    </select>
	</p>

	<!-- Posts Number: Input Box -->
	<p>
	    <label for="<?php echo esc_attr( $this->get_field_id( 'post_num' ) ); ?>"><?php _e( 'Number of posts to show:', 'acfs' ); ?></label>
	    <input id="<?php echo esc_attr( $this->get_field_id( 'post_num' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_num' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['post_num'] ); ?>" size="3" /></p>
	</p>

	<!-- Show Caption: Check Box -->
	<p>
	    <input id="<?php echo $this->get_field_id( 'show_caption' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_caption' ); ?>" value="1" <?php checked( $instance['show_caption'] ); ?>/>
	    <label for="<?php echo $this->get_field_id( 'show_caption' ); ?>"><?php _e( 'Show post title as caption', 'acfs' ); ?></label>
	</p>

	<?php
    }


}

?>