<?php 
/*
 Plugin Name: BWT Ecommerce Solution Pro Posttype
 Plugin URI: https://www.buywptemplates.com/
 Description: Creating new post type for BWT Ecommerce Solution Pro Theme
 Author: BuyWpTemplates
 Version: 1.0
 Author URI: https://www.buywptemplates.com/
*/

define( 'BWT_ECOMMERCE_SOLUTION_POSTTYPE_VERSION', '1.0' );

add_action( 'init', 'bwt_ecommerce_solution_pro_posttype_create_post_type' );

function bwt_ecommerce_solution_pro_posttype_create_post_type() {
 
  register_post_type( 'testimonials',
	array(
		'labels' => array(
			'name' => __( 'Testimonials','bwt-ecommerce-solution-pro-posttype-pro' ),
			'singular_name' => __( 'Testimonials','bwt-ecommerce-solution-pro-posttype-pro' )
			),
		'capability_type' => 'post',
		'menu_icon'  => 'dashicons-businessman',
		'public' => true,
		'supports' => array(
			'title',
			'editor',
			'thumbnail'
			)
		)
	);
  
}

/* Testimonial section */
/* Adds a meta box to the Testimonial editing screen */
function bwt_ecommerce_solution_pro_posttype_bn_testimonial_meta_box() {
	add_meta_box( 'bwt-ecommerce-solution-pro-posttype-pro-testimonial-meta', __( 'Enter Designation', 'bwt-ecommerce-solution-pro-posttype-pro' ), 'bwt_ecommerce_solution_pro_posttype_bn_testimonial_meta_callback', 'testimonials', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'bwt_ecommerce_solution_pro_posttype_bn_testimonial_meta_box');
}

/* Adds a meta box for custom post */
function bwt_ecommerce_solution_pro_posttype_bn_testimonial_meta_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'bwt_ecommerce_solution_pro_posttype_posttype_testimonial_meta_nonce' );
  $bn_stored_meta = get_post_meta( $post->ID );
	$designation = get_post_meta( $post->ID, 'meta-desig', true );
	?>
	<div id="testimonials_custom_stuff">
		<table id="list">
			<tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-1">
          <td class="left">
            <?php esc_html_e( 'Designation', 'bwt-ecommerce-solution-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-desig" id="meta-desig" value="<?php echo esc_attr( $designation ); ?>" />
          </td>
        </tr>
      </tbody>
		</table>
	</div>
	<?php
}

/* Saves the custom meta input */
function bwt_ecommerce_solution_pro_posttype_bn_metadesig_save( $post_id ) {
	if (!isset($_POST['bwt_ecommerce_solution_pro_posttype_posttype_testimonial_meta_nonce']) || !wp_verify_nonce($_POST['bwt_ecommerce_solution_pro_posttype_posttype_testimonial_meta_nonce'], basename(__FILE__))) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	// Save desig.
	if( isset( $_POST[ 'meta-desig' ] ) ) {
		update_post_meta( $post_id, 'meta-desig', sanitize_text_field($_POST[ 'meta-desig']) );
	}
}

add_action( 'save_post', 'bwt_ecommerce_solution_pro_posttype_bn_metadesig_save' );

/* Testimonials shortcode */
function bwt_ecommerce_solution_pro_posttype_testimonial_func( $atts ) {
	$testimonial = '';
	$testimonial = '<div class="row">';
	$query = new WP_Query( array( 'post_type' => 'testimonials') );

    if ( $query->have_posts() ) :

	$k=1;
	$new = new WP_Query('post_type=testimonials');

	while ($new->have_posts()) : $new->the_post();
        $custom_url = '';
      	$post_id = get_the_ID();
      	$excerpt = wp_trim_words(get_the_excerpt(),25);
        $course= get_post_meta($post_id,'meta-desig',true);
      	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
		    if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        $testimonial .= '
          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="testimonial_box">
              <div class="image-box">
                <img class="testi-img" src="'.esc_url($thumb_url).'" />
                <div class="testimonial-box">    
                    <p class="desig-name"> - '.esc_html($course).'</p>
                </div>
              </div>
              <div class="content_box">
                 <h4 class="testimonial_name"><a href="'.get_permalink().'">'.esc_html(get_the_title()) .'</a></h4>
              </div>
              <div class="short_text pt-1"><p>'.$excerpt.'</p></div>
            </div>
          </div>';
		if($k%3 == 0){
			$testimonial.= '<div class="clearfix"></div>';
		}
      $k++;
  endwhile;
  else :
  	$testimonial = '<h2 class="center">'.esc_html__('Post Not Found','bwt-ecommerce-solution-pro-posttype-pro').'</h2>';
  endif;
  $testimonial .= '</div>';
  return $testimonial;
}
add_shortcode( 'testimonials', 'bwt_ecommerce_solution_pro_posttype_testimonial_func' );





