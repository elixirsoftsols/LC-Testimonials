<?php
/*
Plugin Name: LC Testimonial Sliders
Description: Sliding client testimonials with SlideJS
Author: Learn Codez
Version: 1.0.1
Author URI: http://learncodez.com
License: GPLv2
*/

//Include the file for testimonial settings page
include_once "inc/settings.php";




add_action( 'init', 'testimonials_init' );

//Creating the custom Client Testimonials type
function testimonials_init() 
	{
	/*≈=====≈=====≈=====≈=====≈=====≈=====≈=====
		Testimonial Post Type
	 ≈=====≈=====≈=====≈=====≈=====≈=====≈=====*/
	 // Setup post labels
		$post_type_labels = array(
			'name' => __( 'LC Testimonials', 'lc-testimonials' ),
			'singular_name' => __( 'Testimonial', 'lc-testimonials' ),
			'add_new' => __( 'Add New', 'lc-testimonials' ),
			'add_new_item' => __( 'Add New Testimonial', 'lc-testimonials' ),
			'edit_item' => __( 'Edit Testimonial', 'lc-testimonials' ),
			'new_item' => __( 'New Testimonial', 'lc-testimonials' ),
			'view_item' => __( 'View Testimonial', 'lc-testimonials' ),
			'search_items' => __( 'Search Testimonials', 'lc-testimonials' ),
			'not_found' =>  __( 'No Testimonials found', 'lc-testimonials' ),
			'not_found_in_trash' => __( 'No Testimonials found in the trash', 'lc-testimonials' ),
			'parent_item_colon' => ''
		);

	$args = array(
		'public' => true,
		'labels' => $post_type_labels,
		'singular_label' => __( 'Testimonial', 'lc-testimonials' ),
		'public' => true,
		'show_ui' => true,
		'_builtin' => false,
		'_edit_link' => 'post.php?post=%d',
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => array( 'slug' => 'testimonial' ),
		'query_var' => 'testimonial',
		'supports' => array( 'title', 'editor', 'thumbnail' ),
		'menu_position' => 10,
		'menu_icon' => 'dashicons-testimonial' ,
		'register_meta_box_cb' => 'testimonials_meta_boxes',		
	);

	register_post_type('testimonials', $args);	
	    
}

	/**
 * Adding the necessary metabox
 *
 * This functions is attached to the 'testimonials_post_type()' meta box callback.
 */
function testimonials_meta_boxes() {
	add_meta_box( 'testimonials_form', 'Client Details', 'testimonials_form', 'testimonials', 'normal', 'high' );
}

/**
 * Adding the necessary metabox
 *
 * This functions is attached to the 'add_meta_box()' callback.
 */
function testimonials_form() {
	$post_id = get_the_ID();
	$testimonial_data = get_post_meta( $post_id, '_testimonial', true );
	$client_name = ( empty( $testimonial_data['client_name'] ) ) ? '' : $testimonial_data['client_name'];
	$company_name = ( empty( $testimonial_data['company_name'] ) ) ? '' : $testimonial_data['company_name'];
	$website_link = ( empty( $testimonial_data['website_link'] ) ) ? '' : $testimonial_data['website_link'];

	wp_nonce_field( 'testimonials', 'testimonials' );
	?>
	<p>
		<label>Client's Name</label><br />
		<input type="text" value="<?php echo $client_name; ?>" name="testimonial[client_name]" size="40" />
		<em><?php _e( 'The name of the client giving this testimonial', 'lc-testimonials' ); ?></em>
	</p>
	<p>
		<label>Company Name</label><br />
		<input type="text" value="<?php echo $company_name; ?>" name="testimonial[company_name]" size="40" />
		<em><?php _e( 'The company which this client represents', 'lc-testimonials' ); ?></em>
	</p>
	<p>
		<label>Website</label><br />
		<input type="text" value="<?php echo $website_link; ?>" name="testimonial[website_link]" size="40" />
		<em><?php _e( 'The website link of the client', 'clea-testimonials' ); ?></em>
	</p>
	<?php
}


//Adding the responsiveslides.js script and our script
function testimonials_register_scripts(){
	//Only add these script if we are not in the admin dashboard
	if(!is_admin()){
		wp_register_script('responsiveslides', plugins_url('js/responsiveslides.min.js', __FILE__), array('jquery') );
		wp_enqueue_script('responsiveslides'); 

		wp_register_style( 'responsiveslidescss', plugins_url( 'lc-testimonials/css/responsiveslides.css' ) );
		wp_enqueue_style( 'responsiveslidescss' );

		wp_register_style( 'themecss', plugins_url( 'lc-testimonials/css/themes.css' ) );
		wp_enqueue_style( 'themecss' );
	}
}


add_action('wp_print_scripts', 'testimonials_register_scripts');  





add_action( 'save_post', 'testimonials_save_post' );
/**
 * Data validation and saving
 *
 * This functions is attached to the 'save_post' action hook.
 */
function testimonials_save_post( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( ! empty( $_POST['testimonials'] ) && ! wp_verify_nonce( $_POST['testimonials'], 'testimonials' ) )
		return;

	if ( ! empty( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;
	}

	if ( ! wp_is_post_revision( $post_id ) && 'testimonials' == get_post_type( $post_id ) ) {
		remove_action( 'save_post', 'testimonials_save_post' );

		wp_update_post( array(
			'ID' => $post_id,
			'post_title' => 'Testimonial - ' . $post_id
		) );

		add_action( 'save_post', 'testimonials_save_post' );
	}

	if ( ! empty( $_POST['testimonial'] ) ) {
		$testimonial_data['client_name'] = ( empty( $_POST['testimonial']['client_name'] ) ) ? '' : sanitize_text_field( $_POST['testimonial']['client_name'] );
		$testimonial_data['company_name'] = ( empty( $_POST['testimonial']['company_name'] ) ) ? '' : sanitize_text_field( $_POST['testimonial']['company_name'] );
		$testimonial_data['website_link'] = ( empty( $_POST['testimonial']['website_link'] ) ) ? '' : esc_url( $_POST['testimonial']['website_link'] );

		update_post_meta( $post_id, '_testimonial', $testimonial_data );
	} else {
		delete_post_meta( $post_id, '_testimonial' );
	}
}


//Displaying the testimonials
function dispaly_testimonial_slider(){
	$args = array(  
        'post_type' => 'testimonials', 
        'posts_per_page' => 5  
    ); 
	$the_query = new WP_Query($args);   

 	$result = '<div class="rslides_container"> <ul class="rslides" id="slider1">';

	while ( $the_query->have_posts() ) : $the_query->the_post();
    	$img= get_the_post_thumbnail( $post->ID);    	
		$post_id = get_the_ID();
    	$testimonial_data = get_post_meta( $post_id, '_testimonial', true );    	
		
    	$result .= '<li>
				        <div class="client_image">' . $img . '</div>'
				        .get_the_content().
				        '<div class="client_name">'.$testimonial_data['client_name'] .' </div>
				        <div class="company_name"><a href="'.$testimonial_data['website_link']. '" target="blank" >'.$testimonial_data['company_name'] .'</a></div>
				    </li>';

	endwhile;

    // Reset Post Data
	wp_reset_postdata();

        $result .= '</ul></div>';            

    return $result;
}

add_shortcode('lc_testimonials', 'dispaly_testimonial_slider');  



/* Print the required js script in footer    */
function print_my_inline_script() {  
	$data =  get_option( 'my_option_name' );	
?>

<script>
(function($){
 $("#slider1").responsiveSlides({
        auto: <?php echo  $data['auto_slide']; ?>,
        pager: <?php echo  $data['pager']; ?>,
        nav: false,
        speed: <?php echo  $data['speed']; ?>,   
        namespace: "centered-btns",
        pause: <?php echo  $data['hoverPause']; ?>,       
      });
})(jQuery);
</script>


<?php 
}
add_action( 'wp_footer', 'print_my_inline_script' );

?>
