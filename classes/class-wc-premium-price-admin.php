<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Premium_Price_Admin
 *
 * @class 		WC_Premium_Price_Admin
 * @version		1.0.0
 * @category	Class
 * @author 		cipherx (Bhaskar K C)
 */
class WC_Premium_Price_Admin {

	/**
	 * Singleton self object
	 *
	 * @var self obj
	 */
	public static $instance;

	/**
	 * constructor method
	 */
	public function __construct() {

		if ( ! defined( 'WC_IS_PREMIUM_PRODUCT' ) ) {
			define( 'WC_IS_PREMIUM_PRODUCT', 'wc_is_premium_product' );
		}

		if ( ! defined( 'WC_PREMIUM_PERCENTAGE' ) ) {
			define( 'WC_PREMIUM_PERCENTAGE', 'wc_premium_percentage' );
		}

		if ( is_admin() ) {
			add_action( 'add_meta_boxes', [ $this, 'wc_premium_price_meta_boxes' ] );
		}

		add_action( 'save_post', [ $this, 'wc_premium_save_meta_box' ] );

	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			return new self();
		} else {
			return self::$instance;
		}
	}

	/**
	 * Register meta box(es).
	 */
	function wc_premium_price_meta_boxes() {
		add_meta_box( 'wc-premium-price', __( 'Premium Product', 'woocommerce-product-premium' ), [ $this, 'wc_premium_price_meta_boxes_render' ], 'product', 'side' );
	}


	/**
	 * Meta box display callback.
	 *
	 * @param WP_Post $post Current post object.
	 */
	function wc_premium_price_meta_boxes_render( $post ) {
		wp_nonce_field( 'admin_save_wc_premium_rate', 'wc_premium_rate' );
		?>
		<style>
		#wc-premium-rate{
			display: none;
		}
		</style>

		<label class="selectit">
			<input type="checkbox" <?php echo ( $this->wc_is_premium_product( $post->ID )? "checked='checked'":'' ) ?> name="<?php echo WC_IS_PREMIUM_PRODUCT ?>" id="wc-is-premium" >
			Is premium product
		</label>

		<div id="wc-premium-rate">
			<h4>Premium price percentage</h4>
				<input type="number" min="0" max="100" value="<?php echo ( ! empty( $this->wc_get_premium_percent_rate( $post->ID ) ) ?$this->wc_get_premium_percent_rate( $post->ID ): ''); ?>" name="<?php echo WC_PREMIUM_PERCENTAGE ?>">%
		</div>

		<script>

			toggleRateField( jQuery( '#wc-is-premium' ).is(':checked') );

			jQuery('#wc-is-premium').on( 'click', function() {
				toggleRateField( this.checked );
			});

			// takes bool arg to toggle display
			function toggleRateField( checked ) {
				( checked === true )? jQuery( '#wc-premium-rate' ).show() : jQuery( '#wc-premium-rate' ).hide();
			}
		</script>

		<?php
	}

	/**
	 * Save meta box content.
	 *
	 * @param int $post_id Post ID
	 */
	function wc_premium_save_meta_box() {
		if (
			isset( $_POST[ WC_PREMIUM_PERCENTAGE ] )
			|| wp_verify_nonce( $_POST['wc_premium_rate'], 'admin_save_wc_premium_rate' )
		) {
			if ( ! empty( $_POST[ WC_IS_PREMIUM_PRODUCT ] ) && ( ! empty( $_POST[ WC_PREMIUM_PERCENTAGE ] ) && $_POST[ WC_PREMIUM_PERCENTAGE ] > 0 ) ) {
				update_post_meta( $_POST['post_ID'], WC_IS_PREMIUM_PRODUCT, $_POST[ WC_IS_PREMIUM_PRODUCT ] );
				update_post_meta( $_POST['post_ID'], WC_PREMIUM_PERCENTAGE, $_POST[ WC_PREMIUM_PERCENTAGE ] );
			} else {
				delete_post_meta( $_POST['post_ID'], WC_IS_PREMIUM_PRODUCT );
				delete_post_meta( $_POST['post_ID'], WC_PREMIUM_PERCENTAGE );
			}
		}
	}

	/**
	 * Gets all premium product ids
	 *
	 * @return void
	 */
	function get_premium_product_ids() {

		$args = [
			'post_type' => 'product',
			'post_status' => 'publish',
			'fields' => 'ids',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => WC_IS_PREMIUM_PRODUCT,
					'value' => 'on',
				],
			],
		];

		return get_posts( $args );
	}

	/**
	 * getter function to check if a product is premium
	 *
	 * @param int $post_id
	 * @return boolean
	 */
	function wc_is_premium_product( $post_id ) {
		return ( get_post_meta( $post_id, WC_IS_PREMIUM_PRODUCT, true ) )? true : false;
	}

	/**
	 * getter function for permium_percent_rate
	 *
	 * @param int $post_id
	 * @return int|bool
	 */
	function wc_get_premium_percent_rate( $post_id ) {
		$percent_rate = get_post_meta( $post_id, WC_PREMIUM_PERCENTAGE, true );
		return ( ! empty( $percent_rate ) ) ? intval( $percent_rate ) : false;
	}
}
