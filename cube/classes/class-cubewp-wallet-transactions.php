<?php
/**
 * CubeWp Wallet Transactions.
 *
 * @package cubewp-addon-wallet/cube/classes
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Wallet_Transactions
 */
class CubeWp_Wallet_Transactions {

	public function __construct() {
		add_action( 'cubewp_wallet', array( $this, 'cubewp_wallet_transactions' ) );
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

	public function cubewp_wallet_transactions() {
		self::cubewp_wallet_transactions_approve();
		self::cubewp_wallet_transactions_delete();
		$this->cubewp_wallet_transactions_display();
	}

	private static function cubewp_wallet_transactions_approve() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'approve' ) {
			if ( isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_GET['nonce'] ), 'cubewp_wallet_approve_record_nonce' ) ) {
				if ( isset( $_GET['item_id'] ) && ! empty( $_GET['item_id'] ) ) {
					CubeWp_Wallet_Processor::cubewp_add_funds_to_vendor_wallet( sanitize_text_field( $_GET['item_id'] ) );
					wp_redirect( esc_url( CubeWp_Submenu::_page_action( 'cubewp-wallet' ) ) );
					exit;
				}
			}
		}
	}

	private static function cubewp_wallet_transactions_delete() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) {
			if ( isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_GET['nonce'] ), 'cubewp_wallet_delete_record_nonce' ) ) {
				if ( isset( $_GET['item_id'] ) && ! empty( $_GET['item_id'] ) ) {
					global $wpdb;
					$wpdb->delete( $wpdb->prefix . 'cubewp_wallet', array( 'ID' => sanitize_text_field( $_GET['item_id'] ) ), array( '%d' ) );
					wp_redirect( esc_url( CubeWp_Submenu::_page_action( 'cubewp-wallet' ) ) );
					exit;
				}
			}
		}
	}

	private function cubewp_wallet_transactions_display() {
		$transactions_list_table = new CubeWp_Wallet_Transactions_List_Table();
		$transactions_list_table->prepare_items();
		?>
        <div class="wrap cwp-post-type-wrape">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'CubeWP Wallet', 'cubewp-framework' ); ?></h1>
            <hr class="wp-header-end">
            <div class="cube-earning-grids">
                <div class="cwp-payment-balance-grids payment-balance">
                    <div class="cwp-payment-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-cash-coin" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z"/>
                            <path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1h-.003zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195l.054.012z"/>
                            <path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083c.058-.344.145-.678.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1H1z"/>
                            <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 5.982 5.982 0 0 1 3.13-1.567z"/>
                        </svg>
                    </div>
                    <div class="cwp-payment-cards-earnings">
                        <h3><?php echo cubewp_wallet_price( $transactions_list_table::$available_earning ); ?></h3>
                    </div>
                    <div class="cwp-payment-cards-title">
                        <h4><?php esc_html_e( 'Available Balance', 'cubewp-wallet' ); ?></h4>
                    </div>
                </div>
                <div class="cwp-payment-balance-grids payment-hold">
                    <div class="cwp-payment-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             viewBox="0 0 16 16">
                            <path d="M0 3a2 2 0 0 1 2-2h13.5a.5.5 0 0 1 0 1H15v2a1 1 0 0 1 1 1v8.5a1.5 1.5 0 0 1-1.5 1.5h-12A2.5 2.5 0 0 1 0 12.5V3zm1 1.732V12.5A1.5 1.5 0 0 0 2.5 14h12a.5.5 0 0 0 .5-.5V5H2a1.99 1.99 0 0 1-1-.268zM1 3a1 1 0 0 0 1 1h12V2H2a1 1 0 0 0-1 1z"/>
                        </svg>
                    </div>
                    <div class="cwp-payment-cards-earnings">
                        <h3><?php echo cubewp_wallet_price( $transactions_list_table::$on_hold_earning ); ?></h3>
                    </div>
                    <div class="cwp-payment-cards-title">
                        <h4><?php esc_html_e( 'On-Hold Balance', 'cubewp-wallet' ); ?></h4>
                    </div>
                </div>

                <div class="cwp-payment-balance-grids payment-commission">
                    <div class="cwp-payment-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-cash-coin" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z"/>
                            <path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1h-.003zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195l.054.012z"/>
                            <path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083c.058-.344.145-.678.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1H1z"/>
                            <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 5.982 5.982 0 0 1 3.13-1.567z"/>
                        </svg>
                    </div>
                    <div class="cwp-payment-cards-earnings">
                        <h3><?php echo cubewp_wallet_price( $transactions_list_table::$commission_earning ); ?></h3>
                    </div>
                    <div class="cwp-payment-cards-title">
                        <h4><?php esc_html_e( 'Commission', 'cubewp-wallet' ); ?></h4>
                    </div>
                </div>

                <div class="cwp-payment-balance-grids payment-Withdraw">
                    <div class="cwp-payment-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-arrow-up-right-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.854 10.803a.5.5 0 1 1-.708-.707L9.243 6H6.475a.5.5 0 1 1 0-1h3.975a.5.5 0 0 1 .5.5v3.975a.5.5 0 1 1-1 0V6.707l-4.096 4.096z"/>
                        </svg>
                    </div>
                    <div class="cwp-payment-cards-earnings">
                        <h3><?php echo cubewp_wallet_price( $transactions_list_table::$withdraw_earning ); ?></h3>
                    </div>
                    <div class="cwp-payment-cards-title">
                        <h4><?php esc_html_e( 'Total Withdrawn', 'cubewp-wallet' ); ?></h4>
                    </div>
                </div>
                <div class="cwp-payment-balance-grids payment-grids">
                    <div class="cwp-payment-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-arrow-down-left-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-5.904-2.854a.5.5 0 1 1 .707.708L6.707 9.95h2.768a.5.5 0 1 1 0 1H5.5a.5.5 0 0 1-.5-.5V6.475a.5.5 0 1 1 1 0v2.768l4.096-4.097z"/>
                        </svg>
                    </div>
                    <div class="cwp-payment-cards-earnings">
                        <h3><?php echo cubewp_wallet_price( $transactions_list_table::$total_earning ); ?></h3>
                    </div>
                    <div class="cwp-payment-cards-title">
                        <h4><?php esc_html_e( 'Lifetime Balance', 'cubewp-wallet' ); ?></h4>
                    </div>
                </div>
            </div>
            <form method="post">
                <input type="hidden" name="page" value="cubewp-wallet-transactions">
				<?php $transactions_list_table->display(); ?>
            </form>
        </div>
		<?php
	}

}