<?php
/**
 * @package Postcountdown
 */

/*
Plugin Name: Postcountdown
Plugin URI: http://github.com/arildm/postcountdown
Description:
Version: 0.0.1
Author: Arla <arild@klavaro.se>
Author URI: http://klavaro.se
License: GPLv2 or later
Text Domain: postcountdown
*/

/**
 * Registers the Postcountdown widget with WordPress.
 */
function postcountdown_register_widget() {
	register_widget( 'Postcountdown_Widget' );
}
add_action( 'widgets_init', 'postcountdown_register_widget' );

/**
 * Returns the number of posts in a category.
 */
function postcountdown_count( $cat ) {
	$query = new WP_Query( "cat=$cat" );
	return $query->found_posts;
}

/**
 * Enqueues the default CSS.
 */
function postcountdown_enqueue_scripts() {
	wp_enqueue_style( 'postcountdown', plugins_url( 'postcountdown.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'postcountdown_enqueue_scripts' );

/**
 * Loads the textdomain for internationalization.
 */
function postcountdown_textdomain() {
	load_plugin_textdomain( 'postcountdown', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	#load_plugin_textdomain( 'postcountdown', false, plugins_url( '/languages/', __FILE__ ) );
}
add_action( 'init', 'postcountdown_textdomain' );

class Postcountdown_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'postcountdown',
			__( 'Postcountdown', 'postcountdown' ),
			array( 'description' => __( 'A countdown for the number of post in a category', 'postcountdown' ) )
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$goal = '<span class="goal">' . $instance['goal'] . '</span>';
		?>
		<div class="postcountdown">
			<p>
				<span class="before"><?php printf( __( $instance['before'] ), $goal ); ?></span>
				<span class="count"><?php echo postcountdown_count( $instance['category'] ); ?></span>
				<span class="after"><?php printf( __( $instance['after'] ), $goal ); ?></span>
			</p>
		</div>
		<?php
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$category = isset( $instance['category'] ) ? $instance['category'] : '';
		$goal = isset( $instance['goal'] ) ? $instance['goal'] : '10';
		$before = isset( $instance['before'] ) ? $instance['before'] : __( 'So far,', 'postcountdown' );
		$after = isset( $instance['after'] ) ? $instance['after'] : __( 'of %s posts have been published', 'postcountdown' );
		$categories = get_categories();
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>">
				<?php _e( 'Category:', 'postcountdown' ); ?>
			</label>
			<select class="widefat"
				id="<?php echo $this->get_field_id( 'category' ); ?>"
				name="<?php echo $this->get_field_name( 'category' ); ?>"
				type="text" value="<?php echo esc_attr( $category ); ?>">
				<?php foreach ($categories as $cat): ?>
					<option value="<?php echo $cat->term_id; ?>" <?php selected( $instance['category'], $cat->term_id ); ?>><?php _e( $cat->name ); ?> (<?php echo $cat->count; ?>)</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'goal' ); ?>">
				<?php _e( 'Goal:', 'postcountdown' ); ?>
			</label>
			<input class="widefat"
				id="<?php echo $this->get_field_id( 'goal' ); ?>"
				name="<?php echo $this->get_field_name( 'goal' ); ?>"
				type="text" value="<?php echo esc_attr( $goal ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'before' ); ?>">
				<?php _e( 'Before:', 'postcountdown' ); ?>
			</label>
			<input class="widefat"
				id="<?php echo $this->get_field_id( 'before' ); ?>"
				name="<?php echo $this->get_field_name( 'before' ); ?>"
				type="text" value="<?php echo esc_attr( $before ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'after' ); ?>">
				<?php _e( 'After:', 'postcountdown' ); ?>
			</label>
			<input class="widefat"
				id="<?php echo $this->get_field_id( 'after' ); ?>"
				name="<?php echo $this->get_field_name( 'after' ); ?>"
				type="text" value="<?php echo esc_attr( $after ); ?>">
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? $new_instance['category'] : '0';
		$instance['goal'] = ( ! empty( $new_instance['goal'] ) ) ? $new_instance['goal'] : '0';
		$instance['before'] = ( ! empty( $new_instance['before'] ) ) ? $new_instance['before'] : '';
		$instance['after'] = ( ! empty( $new_instance['after'] ) ) ? $new_instance['after'] : '';
		return $instance;
	}
}
