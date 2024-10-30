<?php
/**
 * Plugin Name: CubeWP Wallet
 * Plugin URI: https://cubewp.com/
 * Description: CubeWP Wallet is an extension of the CubeWP framework that allows website owners to accept payments and manage transactions directly from their website.
 * Version: 1.0.4
 * Author: CubeWP
 * Author URI: https://cubewp.com
 * Text Domain: cubewp-wallet
 * Domain Path: /languages/
 *
 * @package cubewp-wallet
 */
defined( 'ABSPATH' ) || exit;

/* CUBEWP_WALLET_PLUGIN_DIR Defines for load Php files */
if ( ! defined( 'CUBEWP_WALLET_PLUGIN_DIR' ) ) {
	define( 'CUBEWP_WALLET_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/* CUBEWP_WALLET_PLUGIN_URL Defines for load JS and CSS files */
if ( ! defined( 'CUBEWP_WALLET_PLUGIN_URL' ) ) {
	define( 'CUBEWP_WALLET_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/* CUBEWP_WALLET_PLUGIN_FILE Defines for file access */
if ( ! defined( 'CUBEWP_WALLET_PLUGIN_FILE' ) ) {
	define( 'CUBEWP_WALLET_PLUGIN_FILE', __FILE__ );
}

/**
 * CUBEWP_WALLET_VERSION is defined for current wallet Plugin version
 */
if ( ! defined( 'CUBEWP_WALLET_VERSION' ) ) {
    define( 'CUBEWP_WALLET_VERSION', '1.0.4' );
}

if ( ! function_exists( 'cubewp_framework_required_for_wallet_notice' ) ) {
    function cubewp_framework_required_for_wallet_notice() {
        if ( ! function_exists( 'CWP' ) ) {
            ?>
            <div class="notice notice-error">
                <p><strong><?php esc_html_e( 'CubeWP Wallet', 'cubewp-wallet' ); ?></strong></p>
                <p><?php echo sprintf( esc_html__( '%sCubeWP Framework%s is required to run CubeWP Wallet.', 'cubewp-wallet' ), '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=cubewp-framework&TB_iframe=true' ) . '" class="thickbox open-plugin-details-modal">', '</a>' ); ?></p>
            </div>
            <?php
        }
    }
    add_action( 'admin_notices', 'cubewp_framework_required_for_wallet_notice' );
}

/**
 * All CubeWP classes files to be loaded automatically.
 *
 * @param string $className Class name.
 */
if ( ! function_exists( 'cubewp_wallet_autoload_classes' ) ) {
	function cubewp_wallet_autoload_classes( $className ) {
		// If class does not start with our prefix (CubeWp), nothing will return.
		if ( false === strpos( $className, 'CubeWp' ) ) {
			return null;
		}

		// Replace _ with - to match the file name.
		$file_name = str_replace( '_', '-', strtolower( $className ) );

		// Calling class file.
		$files = array(
			CUBEWP_WALLET_PLUGIN_DIR . 'cube/classes/class-' . $file_name . '.php'
		);

		// Checking if exists then include.
		foreach ( $files as $file ) {
			if ( file_exists( $file ) ) {
				require $file;
			}
		}

		return $className;
	}

	spl_autoload_register( 'cubewp_wallet_autoload_classes' );
}

/**
 * Method cubewp_wallet_init
 *
 * @since  1.0
 * @return void
 */
function cubewp_wallet_init(){
    
    return new CubeWp_Wallet_Load();
    
}
add_action( 'cubewp_loaded', 'cubewp_wallet_init');