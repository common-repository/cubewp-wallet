<?php
/**
 * CubeWP Wallet Withdrawals Template.
 *
 * @package cubewp-addon-wallet/cube/template
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();

$items_per_page            = 5;
$page_no                   = $page_no ?? 1;
$offset                    = ( $page_no * $items_per_page ) - $items_per_page;
$withdrawal_requests_count = count( CubeWp_Wallet_Withdrawals_Processor::get_withdrawal_requests_by( 'user_id', $user_id, '=', 'ID' ) );
$withdrawal_requests       = CubeWp_Wallet_Withdrawals_Processor::get_withdrawal_requests_by( 'user_id', $user_id, '=', '*', "LIMIT $offset,$items_per_page" );
?>
<div class="cwp-payment-recent-withdraw-container">
    <?php
    if ( ! empty( $withdrawal_requests ) && is_array( $withdrawal_requests ) ) {
        foreach ( $withdrawal_requests as $request ) {
            $status       = $request['status'];
            $amount       = $request['amount'];
            $saved_payout = maybe_unserialize( $request['payout'] );
            $date         = strtotime( $request['created_at'] );
            $status_class = 'pending';
            $status_svg   = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/></svg>';
            if ( $status == 'approved' ) {
                $status_class = 'successful';
                $status_svg   = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8.49 10.92C19.412 3.382 11.28-2.387 8 .986 4.719-2.387-3.413 3.382 7.51 10.92l-.234.468a.25.25 0 1 0 .448.224l.04-.08c.009.17.024.315.051.45.068.344.208.622.448 1.102l.013.028c.212.422.182.85.05 1.246-.135.402-.366.751-.534 1.003a.25.25 0 0 0 .416.278l.004-.007c.166-.248.431-.646.588-1.115.16-.479.212-1.051-.076-1.629-.258-.515-.365-.732-.419-1.004a2.376 2.376 0 0 1-.037-.289l.008.017a.25.25 0 1 0 .448-.224l-.235-.468ZM6.726 1.269c-1.167-.61-2.8-.142-3.454 1.135-.237.463-.36 1.08-.202 1.85.055.27.467.197.527-.071.285-1.256 1.177-2.462 2.989-2.528.234-.008.348-.278.14-.386Z"/></svg>';
            } else if ( $status == 'rejected' ) {
                $status_class = 'fail';
                $status_svg   = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/></svg>';
            }
            ?>
            <div class="cwp-payment-recent-content">
                <div class="cwp-payment-withdraw-box">
                    <div class="payment-amount">
                        <div class="cwp-price">
                            <span><?php esc_html_e( 'Requested Amount', 'cubewp-wallet' ); ?></span>
                            <h4><?php echo sprintf( esc_html__( '%s/-', 'cubewp-wallet' ), cubewp_wallet_price( esc_html( $amount ) ) ); ?></h4>
                        </div>
                        <div class="cwp-again-successful <?php echo esc_attr( $status_class ); ?>">
                            <?php
                            echo cubewp_core_data( $status_svg );
                            echo strtoupper( esc_html( $status ) );
                            ?>
                        </div>
                    </div>
                    <div class="payment-method">
                        <ul class="list-payment">
                            <li><?php esc_html_e( 'Payout', 'cubewp-wallet' ); ?></li>
                            <li><?php echo esc_html( $saved_payout['title'] ); ?></li>
                        </ul>
                        <ul class="list-payment">
                            <li><?php esc_html_e( 'Date', 'cubewp-wallet' ); ?></li>
                            <li><?php echo date_i18n( get_option( 'date_format' ), esc_html( $date ) ); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
        }
    }else {
        echo '<p class="cubewp-wallet-not-found">' . esc_html__("No Withdrawal Request Found", "cubewp-wallet") . '</p>';
    }
    ?>
</div>
<?php
if ( $withdrawal_requests_count > $items_per_page ) {
    echo cubewp_wallet_sql_pagination( $items_per_page, $withdrawal_requests_count, $page_no, 'cubewp-wallet-withdrawals-pagination' );
}
?>