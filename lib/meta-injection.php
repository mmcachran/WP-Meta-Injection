<?php
class WP_Meta_Injection {

	const VERSION = '1.0.1';

	public static 
		$url,
		$path,
		$name;

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.3
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.3
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Sets up our plugin
	 * @since  0.1.0
	 */
	private function __construct() {
		// Useful variables
		self::$url  = trailingslashit( plugin_dir_url( __FILE__ ) );
		self::$path = trailingslashit( dirname( __FILE__ ) );
		self::$name = __( 'WP Meta Injection', 'wp_meta_injection' );

		add_action( 'init', array( $this, 'init' ) );
		
		// check that CMB2 is active
		if( class_exists( 'cmb2_bootstrap_200beta' ) ) {
			// Add the metaboxes
			add_filter( 'cmb2_meta_boxes', array( $this, 'cmb_meta_injection_metaboxes' ) );
		} else {
			add_action( 'add_meta_boxes', array( $this->meta_box(), 'meta_box_add' ) );
			add_action( 'save_post', array( $this->meta_box(), 'meta_box_save' ) );
		}
		
		// Add whatever is in the metabox field to <head>
		add_action( 'wp_head', array( $this, 'do_meta_injection' ), 1 );
	}

	/**
	 * Init hooks
	 * @since  0.1.0
	 * @return null
	 */
	public function init() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp_meta_injection' );
		load_textdomain( 'wp_meta_injection', WP_LANG_DIR . '/wp-meta-injection/wp_meta_injection-' . $locale . '.mo' );
		load_plugin_textdomain( 'wp_meta_injection', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Define the metabox and field configurations.
	 *
	 * @param  array $meta_boxes
	 * @return array
	 */
	public function cmb_meta_injection_metaboxes( array $meta_boxes ) {
		global $current_user;

		if ( current_user_can( 'manage_options' ) ) {
			// Start with an underscore to hide fields from custom fields list
			$prefix = '_wp_meta_injection_';

			$label = isset( $_GET['post'] ) ? get_post_type( $_GET['post'] ) : 'page/post';
			$label = isset( $_GET['post_type'] ) ? esc_attr( $_GET['post_type'] ) : $label;

			$meta_boxes['rss_metabox'] = array(
				'id'            => 'rss_metabox',
				'title'         => __( 'Meta Injection Content (Arbitrary Tags for &lt;head&gt;)', 'wp_meta_injection' ),
				'object_types'  => array( 'page', 'post' ), // Post types
				'context'       => 'normal',
				'priority'      => 'high',
				'show_names'    => false,
				'fields'        => array(
					array(
						'name' => '',
						'desc' => sprintf( __( 'Can be used (with caution) to place link, script, or css tags in the head of this %s.', 'wp_meta_injection' ), $label ),
						'id'   => $prefix . 'content',
						'type' => 'textarea_code',
						'attributes' => array(
							'style' => 'width: 100%;',
						),
					),
				),
			);

			return $meta_boxes;
		}
	}
	
	public function meta_box() {
		if ( isset( $this->meta_box ) ) {
			return $this->meta_box;
		}
		
		require_once( self::$path . 'meta-box.php' );
		$this->meta_box = new WP_Meta_Injection_Meta_Box( $this );
		return $this->meta_box;
	}

	/**
	 * Output content from metabox defined above
	 */
	public function do_meta_injection() {
		global $post;

		if ( ! is_admin() && isset( $post->ID ) ) {
			echo get_post_meta( $post->ID, '_wp_meta_injection_content', true );
		}
	}
}