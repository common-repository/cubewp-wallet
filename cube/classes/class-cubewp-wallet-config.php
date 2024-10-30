<?php
/**
 * CubeWP Wallet Config.
 *
 * @package cubewp-addon-wallet/cube/classes
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Wallet_Config
 */
class CubeWp_Wallet_Config {

	/**
	 * CubeWp_Wallet_Config Constructor.
	 */
	public function __construct() {
		$this->create_database_tables();
		add_filter( 'cubewp/options/sections', array( $this, 'cubewp_wallet_settings' ) );
		add_filter( 'cubewp-submenu', array( $this, 'register_menu_pages' ) );
		add_filter( 'user/dashboard/content/types', array( $this, 'cubewp_wallet_user_dashboard' ) );
		add_shortcode( 'cubewp_wallet', array( $this, 'cubewp_wallet_shortcode_callback' ) );
		add_filter( 'cwp/dashboard/single/tab/content/output', array( $this, 'cubewp_wallet_user_dashboard_output' ), 10, 2 );
	}

	public function cubewp_wallet_shortcode_callback() {
		load_template(CUBEWP_WALLET_PLUGIN_DIR . 'cube/templates/cubewp-wallet.php');
	}

	public function cubewp_wallet_user_dashboard_output($output, $args) {
		if ( isset( $args['content_type'] ) && $args['content_type'] == 'cubewp_wallet' ) {
			ob_start();
			echo do_shortcode( '[cubewp_wallet]' );
			$output = ob_get_clean();
		}

		return $output;
	}

	public function cubewp_wallet_user_dashboard( $types ) {
		$types['cubewp_wallet'] = esc_html__("CubeWP Wallet", "cubewp-wallet");

		return $types;
	}

	public function register_menu_pages($pages) {
		global $wpdb;
		$query_results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cubewp_withdraw_requests WHERE `status` = 'pending'", ARRAY_A );
		$withdrawal_title = esc_html__('Withdrawals', 'cubewp-framework');
		if ( ! empty( $query_results ) && count( $query_results ) > 0 ) {
			$withdrawal_title = sprintf( esc_html__('Withdrawals %s', 'cubewp-framework'), '<span class="awaiting-mod"></span>' );
		}
		$query_results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cubewp_dispute_requests WHERE `status` = 'pending'", ARRAY_A );
		$disputes_title = esc_html__('Disputes', 'cubewp-framework');
		if ( ! empty( $query_results ) && count( $query_results ) > 0 ) {
			$disputes_title = sprintf( esc_html__('Disputes %s', 'cubewp-framework'), '<span class="awaiting-mod"></span>' );
		}

		$new_pages[] = array(
			'id'       => 'cubewp-wallet',
			'title'    => esc_html__('CubeWP Wallet', 'cubewp-framework'),
			'icon'     => CWP_PLUGIN_URI .'cube/assets/admin/images/cubewp-admin.svg',
			'callback' => 'cubewp_wallet',
			'position' => 100
		);
		$new_pages[] = array(
			'id'       => 'cubewp-wallet-withdrawals',
			'title'    => $withdrawal_title,
			'icon'     => CWP_PLUGIN_URI .'cube/assets/admin/images/cubewp-admin.svg',
			'callback' => 'cubewp-wallet-withdrawals',
			'parent'   => 'cubewp_wallet'
		);
		$new_pages[] = array(
			'id'       => 'cubewp-wallet-disputes',
			'title'    => $disputes_title,
			'icon'     => CWP_PLUGIN_URI .'cube/assets/admin/images/cubewp-admin.svg',
			'callback' => 'cubewp-wallet-disputes',
			'parent'   => 'cubewp_wallet'
		);

		return array_merge( $pages, $new_pages );
	}

	/**
	 * Method create_cube_order_table
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function create_database_tables() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$wpdb->query( "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "cubewp_wallet` (
            `ID` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		    `post_id` int(20) NOT NULL DEFAULT '0',
		    `order_id` int(20) NOT NULL DEFAULT '0',
            `vendor_id` int(20) DEFAULT NULL,
            `customer_id` int(20) DEFAULT NULL,
            `sub_price` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `commission_amount` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `total_price` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate" );

		$wpdb->query( "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "cubewp_withdraw_requests` (
            `ID` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` int(20) DEFAULT NULL,
            `amount` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `payout` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate" );

		$wpdb->query( "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "cubewp_dispute_requests` (
            `ID` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		    `transaction_id` int(20) NOT NULL DEFAULT '0',
            `user_id` int(20) DEFAULT NULL,
            `details` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate" );
	}

	/**
	 * Method init
	 *
	 * @return void
	 */
	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}

	public function cubewp_wallet_settings( $sections ) {
		$settings['cubewp_wallet'] = array(
			'title'  => __( 'Wallet', 'cubewp' ),
			'id'     => 'cubewp_wallet',
			'icon'   => 'dashicons-money',
			'fields' => array(
				array(
					'id'      => 'cubewp_wallet_currency',
					'type'    => 'select',
					'title'   => __( 'Currency', 'cubewp-wallet' ),
					'options' => cubewp_get_wallet_currencies( false ),
					'default' => 0,
					'desc'    => __( 'Choose the currency for CubeWP Wallet, By default Woocommerce Setting will be used.', 'cubewp-wallet' ),
				),
				array(
					'id'      => 'cubewp_wallet_commission',
					'type'    => 'switch',
					'title'   => __( 'Commission', 'cubewp-wallet' ),
					'default' => '0',
					'desc'    => __( 'Want to take commission on vendor\'s payment? Turn this option on.', 'cubewp-wallet' ),
				),
				array(
					'id'       => 'cubewp_wallet_commission_type',
					'type'     => 'select',
					'title'    => __( 'Commission Type', 'cubewp-wallet' ),
					'desc'     => __( 'Please select the type of commission you want.', 'cubewp-wallet' ),
					'options'  => array(
						'percentage' => __( 'Percentage', 'cubewp-wallet' ),
						'fixed'      => __( 'Fixed', 'cubewp-wallet' )
					),
					'default'  => 'percentage',
					'required' => array(
						array( 'cubewp_wallet_commission', 'equals', '1' )
					)
				),
				array(
					'id'       => 'cubewp_wallet_commission_value',
					'title'    => __( 'Commission Value', 'cubewp-framework' ),
					'desc'     => __( 'Please enter the commission value, Percentage or Fixed.', 'cubewp-framework' ),
					'type'     => 'text',
					'default'  => '15',
					'required' => array(
						array( 'cubewp_wallet_commission', 'equals', '1' )
					)
				),
				array(
					'id'      => 'cubewp_wallet_hold_payments',
					'type'    => 'switch',
					'title'   => __( 'Hold Payments', 'cubewp-wallet' ),
					'default' => '0',
					'desc'    => __( 'Want to hold vendor\'s payment? Turn this option on.', 'cubewp-wallet' ),
				),
				array(
					'id'       => 'cubewp_wallet_hold_period',
					'title'    => __( 'Hold Payments Period', 'cubewp-framework' ),
					'desc'     => __( 'Please enter the number of days you want to hold vendor\'s payment. After this period fund will be added to vendor\'s wallet automatically.', 'cubewp-framework' ),
					'type'     => 'text',
					'default'  => '7',
					'required' => array(
						array( 'cubewp_wallet_hold_payments', 'equals', '1' )
					)
				),
				array(
					'id'       => 'cubewp_wallet_min_payout',
					'title'    => __( 'Minimum Payout', 'cubewp-framework' ),
					'desc'     => __( 'Please Enter the minimum amount for withdrawal.', 'cubewp-framework' ),
					'type'     => 'text',
					'default'  => '5'
				),
				array(
					'id'       => 'cubewp_wallet_max_payout',
					'title'    => __( 'Maximum Payout', 'cubewp-framework' ),
					'desc'     => __( 'Please Enter the maximum amount for withdrawal.', 'cubewp-framework' ),
					'type'     => 'text',
					'default'  => '50000'
				),
			)
		);

		return array_merge( $sections, $settings );
	}
}