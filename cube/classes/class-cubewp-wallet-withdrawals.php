<?php
/**
 * CubeWp Wallet Withdrawals.
 *
 * @package cubewp-addon-wallet/cube/classes
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Wallet_Withdrawals
 */
class CubeWp_Wallet_Withdrawals {

	public function __construct() {
		add_action( 'cubewp_wallet_withdrawals', array( $this, 'cubewp_wallet_withdrawals' ) );
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

	public function cubewp_wallet_withdrawals() {
		self::cubewp_wallet_transactions_delete();
		self::cubewp_wallet_transactions_approve();
		self::cubewp_wallet_transactions_reject();
		$this->cubewp_wallet_withdrawals_display();
	}

	private function cubewp_wallet_withdrawals_display() {
		$Withdrawals_list_table = new CubeWp_Wallet_Withdrawals_List_Table();
		$Withdrawals_list_table->prepare_items();
		?>
        <div class="wrap cwp-post-type-wrape">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'Withdrawal Requests', 'cubewp-framework' ); ?></h1>
            <hr class="wp-header-end">
            <form method="post">
                <input type="hidden" name="page" value="cubewp-wallet-withdrawal">
				<?php $Withdrawals_list_table->display(); ?>
            </form>
        </div>
        <div class="cubewp-admin-modal" id="cubewp-wallet-withdrawal-modal">
            <div class="cubewp-admin-modal-content">
                <span class="dashicons dashicons-no cubewp-admin-modal-close"></span>
                <div class="cubewp-wallet-withdrawal-modal-content"></div>
            </div>
        </div>
		<?php
	}

	private static function cubewp_wallet_transactions_delete() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) {
			if ( isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_GET['nonce'] ), 'cubewp_wallet_delete_record_nonce' ) ) {
				if ( isset( $_GET['item_id'] ) && ! empty( $_GET['item_id'] ) ) {
					global $wpdb;
					$wpdb->delete( $wpdb->prefix . 'cubewp_withdraw_requests', array( 'ID' => sanitize_text_field( $_GET['item_id'] ) ), array( '%d' ) );
					wp_redirect( esc_url( CubeWp_Submenu::_page_action( 'cubewp-wallet-withdrawals' ) ) );
					exit;
				}
			}
		}
	}

	private static function cubewp_wallet_transactions_approve() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'approve' ) {
			if ( isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_GET['nonce'] ), 'cubewp_wallet_approve_record_nonce' ) ) {
				if ( isset( $_GET['item_id'] ) && ! empty( $_GET['item_id'] ) ) {
					CubeWp_Wallet_Withdrawals_Processor::cubewp_wallet_approve_withdrawal( sanitize_text_field( $_GET['item_id'] ) );
					 wp_redirect( esc_url( CubeWp_Submenu::_page_action( 'cubewp-wallet-withdrawals' ) ) );
					 exit;
				}
			}
		}
	}

	private static function cubewp_wallet_transactions_reject() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'reject' ) {
			if ( isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_GET['nonce'] ), 'cubewp_wallet_reject_record_nonce' ) ) {
				if ( isset( $_GET['item_id'] ) && ! empty( $_GET['item_id'] ) ) {
					CubeWp_Wallet_Withdrawals_Processor::cubewp_wallet_reject_withdrawal( sanitize_text_field( $_GET['item_id'] ) );
					 wp_redirect( esc_url( CubeWp_Submenu::_page_action( 'cubewp-wallet-withdrawals' ) ) );
					 exit;
				}
			}
		}
	}
}