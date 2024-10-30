<?php
/**
 * CubeWP Wallet Template.
 *
 * @package cubewp-addon-wallet/cube/template
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $cwpOption;
if ( empty( $cwpOption ) ) {
	$cwpOption = get_option( 'cwpOptions' );
}

if ( ! is_user_logged_in() ) {
    echo cwp_alert_ui( esc_html__( 'You must be logged-in to access the wallet.', 'cubewp-wallet' ), 'warning' );
    return false;
}

CubeWp_Enqueue::enqueue_style( 'cubewp-wallet-styles' );
CubeWp_Enqueue::enqueue_script( 'cubewp-wallet-scripts' );

$user_id = get_current_user_id();
$available = get_user_meta( $user_id, 'cubewp_wallet_available_funds', true );
$on_hold = get_user_meta( $user_id, 'cubewp_wallet_on_hold_funds', true );
$withdrawn = get_user_meta( $user_id, 'cubewp_wallet_withdrawn_funds', true );
$overall = get_user_meta( $user_id, 'cubewp_wallet_overall_funds', true );

$payout = get_user_meta( $user_id, 'cubewp_wallet_payout_methods', true );

$minimum_payout = isset( $cwpOption['cubewp_wallet_min_payout'] ) && ! empty( $cwpOption['cubewp_wallet_min_payout'] ) ? $cwpOption['cubewp_wallet_min_payout'] : 5;
$maximum_payout = isset( $cwpOption['cubewp_wallet_max_payout'] ) && ! empty( $cwpOption['cubewp_wallet_max_payout'] ) ? $cwpOption['cubewp_wallet_max_payout'] : 50000;

if ( $available < $maximum_payout ) {
	$maximum_payout = $available;
}
?>
<div class="cubewp-payment-earning">
    <div class="cwp-container">
        <div class="cwp-row">
            <div class="cwp-col-md-9">
                <div class="cube-earning-grids">
                    <div class="cwp-payment-balance-grids payment-balance">
                        <div class="cwp-payment-balance">
                            <div class="cwp-payment-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z"/><path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1h-.003zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195l.054.012z"/><path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083c.058-.344.145-.678.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1H1z"/><path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 5.982 5.982 0 0 1 3.13-1.567z"/></svg>
                            </div>
                            <div class="cwp-payment-cards-title">
                                <h4><?php esc_html_e( 'Available Balance', 'cubewp-wallet' ); ?></h4>
                            </div>
                        </div>
                        <div class="cwp-payment-cards-earnings">
                            <h3><?php echo cubewp_wallet_price( $available ); ?></h3>
                        </div>
                        <div class="cwp-payment-withdraw-button">
                            <button class="payment-withdraw cubewp-modal-trigger" data-cubewp-modal="#cubewp-wallet-withdraw-modal" id="payment" <?php if ( $available < $minimum_payout ) echo esc_attr('disabled'); ?>>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.854 10.803a.5.5 0 1 1-.708-.707L9.243 6H6.475a.5.5 0 1 1 0-1h3.975a.5.5 0 0 1 .5.5v3.975a.5.5 0 1 1-1 0V6.707l-4.096 4.096z"/></svg>
                                <?php esc_html_e( 'Withdraw', 'cubewp-wallet' ); ?>
                            </button>
                        </div>
                    </div>
                    <div class="cwp-payment-balance-grids  payment-hold">
                        <div class="cwp-payment-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M0 3a2 2 0 0 1 2-2h13.5a.5.5 0 0 1 0 1H15v2a1 1 0 0 1 1 1v8.5a1.5 1.5 0 0 1-1.5 1.5h-12A2.5 2.5 0 0 1 0 12.5V3zm1 1.732V12.5A1.5 1.5 0 0 0 2.5 14h12a.5.5 0 0 0 .5-.5V5H2a1.99 1.99 0 0 1-1-.268zM1 3a1 1 0 0 0 1 1h12V2H2a1 1 0 0 0-1 1z"/></svg>
                        </div>
                        <div class="cwp-payment-cards-earnings">
                            <h3><?php echo cubewp_wallet_price( $on_hold ); ?></h3>
                        </div>
                        <div class="cwp-payment-cards-title">
                            <h4><?php esc_html_e( 'On Hold', 'cubewp-wallet' ); ?></h4>
                        </div>
                    </div>
                    <div class="cwp-payment-balance-grids payment-Withdraw">
                        <div class="cwp-payment-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.854 10.803a.5.5 0 1 1-.708-.707L9.243 6H6.475a.5.5 0 1 1 0-1h3.975a.5.5 0 0 1 .5.5v3.975a.5.5 0 1 1-1 0V6.707l-4.096 4.096z"/></svg>
                        </div>
                        <div class="cwp-payment-cards-earnings">
                            <h3><?php echo cubewp_wallet_price( $withdrawn ); ?></h3>
                        </div>
                        <div class="cwp-payment-cards-title">
                            <h4><?php esc_html_e( 'Total Withdrawn', 'cubewp-wallet' ); ?></h4>
                        </div>
                    </div>
                    <div class="cwp-payment-balance-grids payment-grids">
                        <div class="cwp-payment-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-5.904-2.854a.5.5 0 1 1 .707.708L6.707 9.95h2.768a.5.5 0 1 1 0 1H5.5a.5.5 0 0 1-.5-.5V6.475a.5.5 0 1 1 1 0v2.768l4.096-4.097z"/></svg>
                        </div>
                        <div class="cwp-payment-cards-earnings">
                            <h3><?php echo cubewp_wallet_price( $overall ); ?></h3>
                        </div>
                        <div class="cwp-payment-cards-title">
                            <h4><?php esc_html_e( 'Lifetime Earnings', 'cubewp-wallet' ); ?></h4>
                        </div>
                    </div>
                </div>
                <div class="cubewp-all-payments-details">
                    <h4><?php esc_html_e( 'Transactions', 'cubewp-wallet' ); ?></h4>
                    <p><?php esc_html_e( 'Your recent wallet transactions history.', 'cubewp-wallet' ); ?></p>
                </div>
                <div class="cubewp-wallet-all-transactions">
	                <?php echo cubewp_wallet_transactions( 1 ); ?>
                </div>
            </div>
            <div class="cwp-col-md-3">
                <div class="cwp-payment-balance-grids payment-hold sidebar-change-method">
                    <div class="cwp-payment-balance">
                        <div class="cwp-payment-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/><path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/></svg>
                        </div>
                        <div class="cwp-payment-cards-title">
                            <h3><?php esc_html_e( 'Payout Setting', 'cubewp-wallet' ); ?></h3>
                            <h4><?php esc_html_e( 'Change Your Payout Method From Here.', 'cubewp-wallet' ); ?></h4>
                            <a href="#" class="cubewp-modal-trigger" data-cubewp-modal="#cubewp-wallet-payout-modal"><?php esc_html_e( 'Change', 'cubewp-wallet' ); ?></a>
                        </div>
                    </div>
                </div>
                <div class="cwp-payment-recent-withdraw">
                    <div class="header-withdraw">
                        <h3><?php esc_html_e( 'Recent Withdrawals', 'cubewp-wallet' ); ?></h3>
                    </div>
                    <div class="cubewp-wallet-withdrawals-container">
	                    <?php echo cubewp_wallet_withdrawals( 1 ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="cubewp-modal" id="cubewp-wallet-withdraw-modal">
    <div class="cubewp-modal-content">
        <span class="dashicons dashicons-no cubewp-modal-close"></span>
        <form method="post" id="cubewp_wallet_withdrawal">
            <div class="cubewp-wallet-inputs">
                <label for="cubewp_wallet_withdrawal_payout"><?php esc_html_e( 'Payout Method *', 'cubewp-wallet' ); ?></label>
                <select name="cubewp_wallet_withdrawal_payout" id="cubewp_wallet_withdrawal_payout" required>
                    <option value=""><?php esc_html_e( 'Select Payout Method', 'cubewp-wallet' ); ?></option>
                    <?php
                    if ( ! empty( $payout ) && is_array( $payout ) ) {
                        foreach ( $payout as $key => $method ) {
                            ?>
                            <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $method['title'] ); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
                <p><?php echo sprintf( esc_html__( '%sChange / Add New%s', 'cubewp-wallet' ), '<a href="#" class="cubewp-modal-trigger" data-cubewp-modal="#cubewp-wallet-payout-modal">', '</a>' ); ?></p>
            </div>
            <div class="cubewp-wallet-inputs">
                <label for="cubewp_wallet_withdrawal_amount"><?php esc_html_e( 'Withdrawal Amount *', 'cubewp-wallet' ); ?></label>
                <input type="number" required name="cubewp_wallet_withdrawal_amount" id="cubewp_wallet_withdrawal_amount" min="<?php echo esc_attr( $minimum_payout ); ?>" max="<?php echo esc_attr( $maximum_payout ); ?>">
                <p><?php echo sprintf( esc_html__( 'Must be a numeric value without any currency sign and between %s to %s', 'cubewp-wallet' ), '<strong>' . cubewp_wallet_price( $minimum_payout ) . '</strong>', '<strong>' . cubewp_wallet_price( $maximum_payout )  . '</strong>'); ?></p>
            </div>
            <div class="cubewp-wallet-inputs">
                <label for="cubewp_wallet_withdrawal_message"><?php esc_html_e( 'Additional Note.', 'cubewp-wallet' ); ?></label>
                <textarea name="cubewp_wallet_withdrawal_message" id="cubewp_wallet_withdrawal_message"></textarea>
            </div>
            <input type="submit" value="<?php esc_html_e( 'Send Request', 'cubewp-wallet' ); ?>">
        </form>
    </div>
</div>

<div class="cubewp-modal" id="cubewp-wallet-payout-modal">
    <div class="cubewp-modal-content">
        <span class="dashicons dashicons-no cubewp-modal-close"></span>
        <form method="post" id="cubewp_wallet_payout_methods">
            <?php
            $rand = rand(00000, 99999);
            $new_name = 'cubewp_wallet_payout_method[' . $rand . ']';
            $payout_method_form_ui = '';
            if ( ! empty( $payout ) && is_array( $payout ) ) {
                ?>
                <div class="cubewp-wallet-payout-methods">
                    <?php
                    $counter = 0;
                    foreach ( $payout as $key => $method ) {
                        $method_title = $method['title'];
                        $method_details = $method['details'];
                        $class = '';
                        if ( $counter == 0 ) {
                            $class = 'cwp-active';
                        }
                        $counter++;
                        ?>
                        <div class="cubewp-wallet-payout-method <?php echo esc_attr( $class ); ?>" data-cubewp-wallet-payout-method="<?php echo esc_attr( 'cubewp-wallet-payout-form-' . $key ); ?>">
                            <span><?php echo esc_html( $method_title ); ?></span>
                            <span class="dashicons dashicons-no-alt cubewp-wallet-payout-remove-method"></span>
                        </div>
                        <?php
                        $payout_method_form_ui .= '<div class="cubewp-wallet-payout-method-form ' . esc_attr( $class ) . '" id="' . esc_attr( 'cubewp-wallet-payout-form-' . $key ) . '">';
                        $payout_method_form_ui .= '<input type="hidden" name="cubewp_wallet_payout_method[' . $key . '][title]" value="' . esc_attr( $method_title ) . '">';
                        $payout_method_form_ui .= '<div class="cubewp-wallet-inputs">';
                        $payout_method_form_ui .= '<label for="' . esc_attr($key) . '">' . esc_html__( 'Account Details *', 'cubewp-wallet' ) . '</label>';
                        $payout_method_form_ui .= '<textarea name="cubewp_wallet_payout_method[' . $key . '][details]" id="' . esc_attr($key) . '" placeholder="' .  esc_html__( 'EG: Account Title, Account Number and etc.', 'cubewp-wallet' ) . '">' . esc_textarea( $method_details ) . '</textarea>';
                        $payout_method_form_ui .= '<p>' . esc_html__( 'Please enter all the necessary details so you do no face and disruption in receiving funds.', 'cubewp-wallet' ) . '</p>';
                        $payout_method_form_ui .= '</div>';
                        $payout_method_form_ui .= '</div>';
                    }
                    ?>
                    <div class="cubewp-wallet-payout-method" data-cubewp-wallet-payout-method="<?php echo esc_attr( 'cubewp-wallet-payout-form-' . $rand ); ?>">
                        <span><?php esc_html_e( 'Add New Method', 'cubewp-wallet' ); ?></span>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="cubewp-wallet-payout-method-form-container">
                <?php echo cubewp_core_data( $payout_method_form_ui ); ?>
                <div class="cubewp-wallet-payout-method-form <?php if ( empty( trim( $payout_method_form_ui ) ) ) echo 'cwp-active'; ?>" id="<?php echo esc_attr( 'cubewp-wallet-payout-form-' . $rand ); ?>">
                    <div class="cubewp-wallet-inputs">
                        <label for="<?php echo esc_attr( $new_name ) ?>[title]"><?php esc_html_e( 'Account Type *', 'cubewp-wallet' ) ?></label>
                        <input type="text" name="<?php echo esc_attr( $new_name ) ?>[title]" id="<?php echo esc_attr( $new_name ) ?>[title]" placeholder="<?php esc_html_e( 'EG: Paypal, Stripe, Payoneer and etc.', 'cubewp-wallet' ) ?>">
                    </div>
                    <div class="cubewp-wallet-inputs">
                        <label for="<?php echo esc_attr( $new_name ) ?>[details]"><?php esc_html_e( 'Account Details *', 'cubewp-wallet' ) ?></label>
                        <textarea name="<?php echo esc_attr( $new_name ) ?>[details]" id="<?php echo esc_attr( $new_name ) ?>[details]" placeholder="<?php esc_html_e( 'EG: Account Title, Account Number and etc.', 'cubewp-wallet' ) ?>"></textarea>
                        <p><?php esc_html_e( 'Please enter all the necessary details so you do no face and disruption in receiving funds.', 'cubewp-wallet' ) ?></p>
                    </div>
                </div>
            </div>
            <input type="submit" value="<?php esc_html_e( 'Save Changes', 'cubewp-wallet' ); ?>">
        </form>
    </div>
</div>