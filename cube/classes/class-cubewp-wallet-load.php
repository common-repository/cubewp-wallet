<?php
/**
 * CubeWP Wallet initializer.
 *
 * @package cubewp-addon-wallet/cube/classes
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Wallet_Load
 */
class CubeWp_Wallet_Load {

	/**
	 * The single instance of the class.
	 *
	 * @var CubeWp_Wallet_Load
	 */
	protected static $Load = null;

	/**
	 * CubeWp_Wallet_Load Constructor.
	 */
	public function __construct() {

		self::init();
	}

	private static function init() {
		require_once CUBEWP_WALLET_PLUGIN_DIR . 'cube/helpers/functions.php';

		add_action( 'init', array( 'CubeWp_Wallet_Enqueue', 'init' ) );
		add_action( 'init', array( 'CubeWp_Wallet_Config', 'init' ) );

		add_action( 'init', array( 'CubeWp_Wallet_Ajax', 'init' ) );

		add_action( 'init', array( 'CubeWp_Wallet_Transactions', 'init' ) );
		add_action( 'init', array( 'CubeWp_Wallet_Withdrawals', 'init' ) );
		add_action( 'init', array( 'CubeWp_Wallet_Disputes', 'init' ) );

		add_action( 'init', array( 'CubeWp_Wallet_Processor', 'init' ) );
		add_action( 'init', array( 'CubeWp_Wallet_Withdrawals_Processor', 'init' ) );
	}

	public static function instance() {
		if ( is_null( self::$Load ) ) {
			self::$Load = new self();
		}

		return self::$Load;
	}
}