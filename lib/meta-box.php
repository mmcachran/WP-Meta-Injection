<?php
if( ! class_exists( 'WP_Meta_Injection_Meta_Box' ) ):
class WP_Meta_Injection_Meta_Box {
	public function __construct( $wp_meta_injection ) {
		$this->wp_meta_injection = $wp_meta_injection;
	}
	
	public function meta_box_add() {
		add_meta_box( 'wp-meta-injection-meta-box', 'WP Meta Injection Content', array( $this, 'meta_box_cb' ), 'post', 'normal', 'high' );
	}
	
	public function meta_box_cb() {
	    // $post is already set, and contains an object: the WordPress post
	    global $post;
	    $values = get_post_custom( $post->ID );
	     
	    // We'll use this nonce field later on when saving.
	    wp_nonce_field( 'wp_meta_injection_meta_box_nonce', 'meta_box_nonce' );
	    ?>     
	    <p>
		    <textarea id="_wp_meta_injection_content" name="_wp_meta_injection_content" cols="60" rows="8"><?php echo isset( $values['_wp_meta_injection_content'] ) ? trim($values['_wp_meta_injection_content'][0]) : null; ?></textarea>
	    </p>
	    <?php  
	}
	
	public function meta_box_save( $post_id ) {
		// bail if we're autosaving
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		// bail if our nounce if not verified
		if( ! isset( $_POST['meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['meta_box_nonce'], 'wp_meta_injection_meta_box_nonce' ) ) {
		    return;
		}
		   
	     
		// bail if our current user can't edit this post
		if( ! current_user_can( 'edit_post' ) ) {
			return;
		}
	         
		// save the post meta
		$value = isset( $_POST['_wp_meta_injection_content'] ) ? $_POST['_wp_meta_injection_content'] : '';
		
		
		update_post_meta( $post_id, '_wp_meta_injection_content', $value );
	}
}
endif;