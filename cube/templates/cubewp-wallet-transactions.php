<?php
/**
 * CubeWP Wallet Transactions Template.
 *
 * @package cubewp-addon-wallet/cube/template
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();

$items_per_page = 10;
$page_no = $page_no ?? 1;
$offset = ($page_no * $items_per_page) - $items_per_page;
$transactions_count = count( CubeWp_Wallet_Processor::get_wallet_transactions_by( 'vendor_id', $user_id, '=', 'ID' ) );
$transactions = CubeWp_Wallet_Processor::get_wallet_transactions_by( 'vendor_id', $user_id, '=', '*', "LIMIT $offset,$items_per_page" );
?>
<div class="cubewp-all-payments">
    <table class="payment-main-table">
        <thead>
        <tr class="payment-table-rows headings">
            <th class="payment-table-headings"><?php esc_html_e( 'Post', 'cubewp-wallet' ); ?></th>
            <th class="payment-table-headings"><?php esc_html_e( 'Customer', 'cubewp-wallet' ); ?></th>
            <th class="payment-table-headings"><?php esc_html_e( 'Amount', 'cubewp-wallet' ); ?></th>
            <th class="payment-table-headings"><?php esc_html_e( 'Web Fee', 'cubewp-wallet' ); ?></th>
            <th class="payment-table-headings"><?php esc_html_e( 'Total Received', 'cubewp-wallet' ); ?></th>
            <th class="payment-table-headings"><?php esc_html_e( 'Status', 'cubewp-wallet' ); ?></th>
            <th class="payment-table-headings"><?php esc_html_e( 'Date', 'cubewp-wallet' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ( ! empty( $transactions ) && is_array( $transactions ) ) {
            foreach ( $transactions as $transaction ) {
                $post_id = $transaction['post_id'];
                $customer_id = $transaction['customer_id'];
                $sub_price = $transaction['sub_price'];
                $commission_amount = isset( $transaction['commission_amount'] ) && $transaction['commission_amount'] > 0 ? $transaction['commission_amount'] : 0;
                $total_price = $transaction['total_price'];
                $status = $transaction['status'];
                $created_at = strtotime( $transaction['created_at'] );
                $customer = get_userdata( $customer_id );
                $customer = $customer->display_name ?? esc_html__( 'N/A', 'cubewp_wallet' );
                $data = maybe_unserialize( $transaction['data'] );
                $currency = $data['currency'] ?? '';
                $commission = isset( $data['commission'] ) && ! empty( $data['commission'] ) ? $data['commission'] : array();
                ?>
                <tr class="payment-table-rows">
                    <td class="payment-table-details"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" target="_blank"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></td>
                    <td class="payment-table-details"><?php echo esc_html( $customer ); ?></td>
                    <td class="payment-table-details"><?php echo cubewp_wallet_price( $sub_price, $currency ); ?></td>
                    <td class="payment-table-details"><?php echo cubewp_wallet_price( $commission_amount, $currency ); ?></td>
                    <td class="payment-table-details"><?php echo cubewp_wallet_price( $total_price, $currency ); ?></td>
                    <td class="payment-table-details"><?php echo esc_html( $status ); ?></td>
                    <td class="payment-table-details"><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $created_at ) ); ?></td>
                </tr>
                <?php
            }
        }else {
            ?>
            <tr class="payment-table-rows">
                <td colspan="8" class="payment-table-details"><?php esc_html_e( 'No Transaction Found.', 'cubewp-wallet' ); ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
<?php
echo cubewp_wallet_sql_pagination( $items_per_page, $transactions_count, $page_no, 'cubewp-wallet-transactions-pagination' );
?>