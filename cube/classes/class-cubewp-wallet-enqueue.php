<?php
/**
 * Enqueue class to register and enqueue script/styles.
 *
 * @package cubewp-addon-wallet/cube/template
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Wallet_Enqueue
 */
class CubeWp_Wallet_Enqueue {

	public function __construct() {
		add_filter( 'frontend/style/register', array( $this, 'register_frontend_styles' ) );
		add_filter( 'frontend/script/register', array( $this, 'register_frontend_scripts' ) );

		add_filter( 'admin/script/register', array( $this, 'register_admin_scripts' ) );
		add_filter( 'admin/style/register', array( $this, 'register_admin_style' ) );

		add_filter( 'admin/script/enqueue', array( $this, 'load_admin_scripts' ) );

		add_filter( 'get_frontend_script_data', array( $this, 'get_frontend_script_data' ), 12, 2 );
		add_filter( 'cubewp_get_admin_script', array( $this, 'get_admin_script_data' ), 10, 2 );
	}

	/**
	 * Method register_frontend_styles
	 *
	 * @param array $styles contains already registered styles for frontend
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_frontend_styles( $styles ) {
		$register_styles = array(
			'cubewp-wallet-styles' => array(
				'src'     => CUBEWP_WALLET_PLUGIN_URL . 'cube/assets/frontend/css/cubewp-wallet.css',
				'deps'    => array(),
				'version' => CUBEWP_WALLET_VERSION,
				'has_rtl' => false,
			)
		);

		return array_merge( $register_styles, $styles );
	}

	public static function load_admin_scripts( $data ) {
		if ( CWP()->is_admin_screen( 'cubewp_wallet_withdrawals' ) || CWP()->is_admin_screen( 'cubewp_wallet_disputes' ) ) {
			CubeWp_Enqueue::enqueue_script( 'cubewp-wallet-admin-scripts' );
			CubeWp_Enqueue::enqueue_style( 'cubewp-wallet-admin-styles' );
		}
		if ( CWP()->is_admin_screen( 'cubewp_wallet' ) ) {
			CubeWp_Enqueue::enqueue_style( 'cubewp-wallet-admin-styles' );
		}
	}

	/**
	 * Method register_frontend_scripts
	 *
	 * @param array $script contains already registered scripts for frontend
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_frontend_scripts( $script ) {
		$register_scripts = array(
			'cubewp-wallet-scripts' => array(
				'src'     => CUBEWP_WALLET_PLUGIN_URL . 'cube/assets/frontend/js/cubewp-wallet.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_WALLET_VERSION,
			)
		);

		return array_merge( $register_scripts, $script );
	}

	/**
	 * Method register_admin_scripts
	 *
	 * @param array $script contains already registered scripts for admin
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_admin_scripts( $script ) {
		$register_scripts = array(
			'cubewp-wallet-admin-scripts' => array(
				'src'     => CUBEWP_WALLET_PLUGIN_URL . 'cube/assets/admin/js/cubewp-wallet-admin-scripts.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_WALLET_VERSION,
			)
		);

		return array_merge( $script, $register_scripts );
	}

	/**
	 * Method register_admin_style
	 *
	 * @param array $styles contains already registered styles for admin
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_admin_style( $styles ) {
		$register_style = array(
			'cubewp-wallet-admin-styles' => array(
				'src'     => CUBEWP_WALLET_PLUGIN_URL . 'cube/assets/admin/css/cubewp-wallet-admin-styles.css',
				'deps'    => array(),
				'version' => CUBEWP_WALLET_VERSION,
				'has_rtl' => false,
			)
		);

		return array_merge( $register_style, $styles );
	}

	/**
	 * Method get_frontend_script_data
	 *
	 * @param string $data
	 * @param string $handle contains script handles
	 *
	 * @return mixed
	 * @since  1.0.0
	 */
	public static function get_frontend_script_data( $data, $handle ) {
		if ( $handle == 'cubewp-wallet-scripts' ) {
			return array(
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'withdrawal_nonce' => wp_create_nonce( 'cubewp_wallet_withdrawal_nonce' ),
				'payout_nonce'     => wp_create_nonce( 'cubewp_wallet_payout_nonce' ),
				'pagination_nonce' => wp_create_nonce( 'pagination_nonce' ),
				'error_msg'        => esc_html__( 'Something Went Wrong. Try Again Later.', 'cubewp-frontend' )
			);
		}

		return $data;
	}

	/**
	 * Method get_admin_script_data
	 *
	 * @param string $data
	 * @param string $handle contains script handles
	 *
	 * @return mixed
	 * @since  1.0.0
	 */
	public static function get_admin_script_data( $data, $handle ) {
		if ( $handle == 'cubewp-wallet-admin-scripts' ) {
			return array(
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'withdrawal_nonce' => wp_create_nonce( "cubewp_wallet_withdrawal_nonce" ),
				'dispute_nonce'    => wp_create_nonce( "cubewp_wallet_dispute_nonce" ),
			);
		}

		return $data;
	}
	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}

}