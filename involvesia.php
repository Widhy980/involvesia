<?php
/**
 * Plugin Name:       Invovesia
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Plugin management affiliate involve.asia.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Widhy Pradhana
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       invovesia
 * Domain Path:       /languages
 */
 
/* BEGIN INVOLVE PRICE */
/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function involve_marketplace_price() {
   $screens = array( 'post' );
   foreach ( $screens as $screen ) {
      add_meta_box(
         'involve_marketplace_price', 		   /* ID Metabox */
         esc_html__( 'Marketplace Price List', 'involvesia' ),  /* Metabox Title */
         'involve_marketplace_price_call_back',       /* Print View */
         $screen
       );
   }
}
add_action( 'add_meta_boxes', 'involve_marketplace_price' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function involve_marketplace_price_call_back( $post ) {

   // Add an nonce field so we can check for it later.
   wp_nonce_field( 'involve_marketplace_price_custom_box', 'involve_marketplace_price_custom_box_nonce' );
   /*
      * Use get_post_meta() to retrieve an existing value
      * from the database and use the value for the form.
   */
   $involve_product_title = get_post_meta( $post->ID, 'involve_product_title', true );
   $involve_price_list = get_post_meta( $post->ID, 'involve_price_list', true );
   
   ?>
      <p> 
         <label><?php echo esc_html__('Product Title','involvesia') ?></label>
         <input name="involve_product_title" rows="5" class="widefat" value="<?php echo esc_attr($involve_product_title); ?>"/>
      </p>
	  <table>
		<tr>
			<th>
				<?php echo esc_html__( 'Marketplace', 'involvesia' ) ?>
			</th>
			<th>
				<?php echo esc_html__( 'URL Referall', 'involvesia' ) ?>
			</th>
			<th>
				<?php echo esc_html__( 'Price', 'Price' ) ?>
			</th>
		</tr>
		<?php
			$marketplace_array = array(
				array(
					'name' 	=> esc_html__( 'Shoppe', 'involve' ),
					'id'   	=> 'shoppe',
					'url'	=> isset( $involve_price_list['shoppe']['url'] ) ? $involve_price_list['shoppe']['url'] : '',
					'price'	=> isset( $involve_price_list['shoppe']['price'] ) ? $involve_price_list['shoppe']['price'] : '',
				),
				array(
					'name' 	=> esc_html__( 'Lazada', 'involve' ),
					'id'   	=> 'lazada',
					'url'	=> isset( $involve_price_list['lazada']['url'] ) ? $involve_price_list['lazada']['url'] : '',
					'price'	=> isset( $involve_price_list['lazada']['price'] ) ? $involve_price_list['lazada']['price'] : '',
				),
				array(
					'name' 	=> esc_html__( 'Blibli ', 'involve' ),
					'id'   	=> 'blibli',
					'url'	=> isset( $involve_price_list['blibli']['url'] ) ? $involve_price_list['blibli']['url'] : '',
					'price'	=> isset( $involve_price_list['blibli']['price'] ) ? $involve_price_list['blibli']['price'] : '',
				),
				array(
					'name' 	=> esc_html__( 'Bukalapak ', 'involve' ),
					'id'   	=> 'bukalapak',
					'url'	=> isset( $involve_price_list['bukalapak']['url'] ) ? $involve_price_list['bukalapak']['url'] : '',
					'price'	=> isset( $involve_price_list['bukalapak']['price'] ) ? $involve_price_list['bukalapak']['price'] : '',
				)
			);
			involve_marketplace_input( $marketplace_array );
		?>
	  </table>
   <?php
}

function involve_marketplace_input( $marketplace = false ){
	if( $marketplace ){
		foreach( $marketplace as $mpc ){
			?>
				<tr>
					<td>
						<?php echo esc_html( $mpc['name'] )?>
					</td>
					<td>
						<input name="involve_price_list[<?php echo esc_attr( $mpc['id'] )?>][url]" rows="5" class="widefat" value="<?php echo esc_attr( $mpc['url'] )?>"/>
					</td>
					<td>
						<input name="involve_price_list[<?php echo esc_attr( $mpc['id'] )?>][price]" rows="5" class="widefat" value="<?php echo esc_attr( $mpc['price'] )?>"/>
					</td>
				</tr>
			<?php
		}
	}
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function involve_marketplace_price_save_postdata( $post_id ) {
   /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
   */

   // Check if our nonce is set.
   if ( ! isset( $_POST['involve_marketplace_price_custom_box_nonce'] ) )
      return $post_id;

   $nonce = $_POST['involve_marketplace_price_custom_box_nonce'];

   // Verify that the nonce is valid.
   if ( ! wp_verify_nonce( $nonce, 'involve_marketplace_price_custom_box' ) )
      return $post_id;

   // If this is an autosave, our form has not been submitted, so we don't want to do anything.
   if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;

   // Check the user's permissions.
   if ( 'page' == $_POST['post_type'] ) {
      if ( ! current_user_can( 'edit_page', $post_id ) )
        return $post_id;
   } else {
      if ( ! current_user_can( 'edit_post', $post_id ) )
        return $post_id;
   }

  /* OK, its safe for us to save the data now. */
   if ( empty( $_POST['involve_product_title'] ) ) {
      delete_post_meta( $post_id, 'involve_product_title' );
   } else {  
      update_post_meta( $post_id, 'involve_product_title', $_POST['involve_product_title'] ); 
   }
   
   if ( empty( $_POST['involve_price_list'] ) ) {
      delete_post_meta( $post_id, 'involve_price_list' );
   } else {  
      update_post_meta( $post_id, 'involve_price_list', $_POST['involve_price_list'] ); 
   }
}
add_action( 'save_post', 'involve_marketplace_price_save_postdata' );
/* END INVOLVE PRICE */
?>