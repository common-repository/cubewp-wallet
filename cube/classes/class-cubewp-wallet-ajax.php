<?php
/**
 * CubeWP Wallet Ajax.
 *
 * @package cubewp-addon-wallet/cube/classes
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Wallet_Ajax
 */
class CubeWp_Wallet_Ajax {


	/**
	 * CubeWp_Wallet_Ajax Constructor.
	 */
	public function __construct() {
		new CubeWp_Ajax( '', __CLASS__, 'cubewp_ajax_payout_methods' );
		new CubeWp_Ajax( '', __CLASS__, 'cubewp_ajax_request_withdrawal' );
		new CubeWp_Ajax( '', __CLASS__, 'cubewp_wallet_transactions_pagination' );
		new CubeWp_Ajax( '', __CLASS__, 'cubewp_wallet_withdrawals_pagination' );

		// On Admin
		new CubeWp_Ajax( '', __CLASS__, 'cubewp_wallet_withdrawal_details' );
		new CubeWp_Ajax( '', __CLASS__, 'cubewp_wallet_dispute_details' );
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

	public static function cubewp_wallet_withdrawal_details() {
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'cubewp_wallet_withdrawal_nonce' ) ) {
			wp_send_json_error( esc_html__( 'Security Verification Failed.', 'cubewp-wallet' ) );
		}
		if ( ! is_user_logged_in() && ! is_admin() ) {
			wp_send_json_error( esc_html__( 'Authentication Failed.', 'cubewp-wallet' ) );
		}
		$item_id         = sanitize_text_field( $_POST['item_id'] );
		$withdrawal_data = CubeWp_Wallet_Withdrawals_Processor::get_withdrawal_requests_by( 'ID', $item_id );
		if ( isset( $withdrawal_data[0] ) && ! empty( $withdrawal_data[0] ) ) {
			$withdrawal_data = $withdrawal_data[0];
			$message         = $withdrawal_data['message'];
			$payout          = maybe_unserialize( $withdrawal_data['payout'] );
			$output          = '';
			if ( ! empty( $message ) ) {
				$output .= '<h2>' . esc_html__( 'User Message', 'cubewp-wallet' ) . '</h2>';
				$output .= '<div class="cubewp-wallet-code-appearance cubewp-wallet-withdrawal-message">';
				$output .= esc_textarea( $message );
				$output .= '</div>';
			}
			if ( ! empty( $payout ) ) {
				$output .= '<h2>' . esc_html__( 'User Payout Details', 'cubewp-wallet' ) . '</h2>';
				$output .= '<div class="cubewp-wallet-code-appearance cubewp-wallet-withdrawal-payout">';
				$output .= '<h4>' . esc_html( $payout['title'] ) . '</h4>';
				$output .= '<pre>' . esc_textarea( $payout['details'] ) . '</pre>';
				$output .= '</div>';
			}

			wp_send_json_success( $output );
		}
		wp_send_json_error( esc_html__( 'Something went wrong.', 'cubewp-wallet' ) );
	}

	public static function cubewp_wallet_dispute_details() {
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'cubewp_wallet_dispute_nonce' ) ) {
			wp_send_json_error( esc_html__( 'Security Verification Failed.', 'cubewp-wallet' ) );
		}
		if ( ! is_user_logged_in() && ! is_admin() ) {
			wp_send_json_error( esc_html__( 'Authentication Failed.', 'cubewp-wallet' ) );
		}
		$item_id      = sanitize_text_field( $_POST['item_id'] );
		$dispute_data = CubeWp_Wallet_Disputes_Processor::get_dispute_requests_by( 'ID', $item_id );
		if ( isset( $dispute_data[0] ) && ! empty( $dispute_data[0] ) ) {
			$output       = '';
			$dispute_data = $dispute_data[0];
			$message        = $dispute_data['details'];
			$transaction_id = $dispute_data['transaction_id'];
			$transaction = CubeWp_Wallet_Processor::get_wallet_transactions_by( 'ID', $transaction_id );
			$transaction = isset( $transaction[0] ) && ! empty( $transaction[0] ) ? $transaction[0] : array();
			$customer_id = $transaction['customer_id'];
			$vendor_id = $transaction['vendor_id'];
			$vendor = get_userdata( $vendor_id );
			$customer = get_userdata( $customer_id );
			$order_id = $transaction['order_id'];
			$post_id = $transaction['post_id'];

			$output .= '<h2>' . esc_html__( 'Customer', 'cubewp-wallet' ) . '</h2>';
			$output .= '<div class="cubewp-wallet-code-appearance">';
			$output .= '<a href="' . get_edit_profile_url( $customer_id ) . '" target="_blank">';
			$output .= $customer->user_login;
			$output .= '</a>';
			$output .= '</div>';

			$output .= '<h2>' . esc_html__( 'Vendor', 'cubewp-wallet' ) . '</h2>';
			$output .= '<div class="cubewp-wallet-code-appearance">';
			$output .= '<a href="' . get_edit_profile_url( $vendor_id ) . '" target="_blank">';
			$output .= $vendor->user_login;
			$output .= '</a>';
			$output .= '</div>';

			$output .= '<h2>' . esc_html__( 'Post', 'cubewp-wallet' ) . '</h2>';
			$output .= '<div class="cubewp-wallet-code-appearance">';
			$output .= '<a href="' . get_permalink( $post_id ) . '" target="_blank">';
			$output .= get_the_title( $post_id );
			$output .= '</a>';
			$output .= '</div>';

			$output .= '<h2>' . esc_html__( 'Order#', 'cubewp-wallet' ) . '</h2>';
			$output .= '<div class="cubewp-wallet-code-appearance">';
			$output .= esc_html( $order_id );
			$output .= '</div>';

			$output .= '<h2>' . esc_html__( 'Details', 'cubewp-wallet' ) . '</h2>';
			$output .= '<div class="cubewp-wallet-code-appearance">';
			$output .= esc_textarea( $message );
			$output .= '</div>';

			wp_send_json_success( $output );
		}
		wp_send_json_error( esc_html__( 'Something went wrong.', 'cubewp-wallet' ) );
	}

	public static function cubewp_ajax_payout_methods() {
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'cubewp_wallet_payout_nonce' ) ) {
			wp_send_json_error( esc_html__( 'Security Verification Failed.', 'cubewp-wallet' ) );
		}
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( esc_html__( 'Authentication Failed.', 'cubewp-wallet' ) );
		}
		$user_id = get_current_user_id();

		$payout_data = $_POST['cubewp_wallet_payout_method'];
		$data        = array();
		foreach ( $payout_data as $key => $method ) {
			$key     = sanitize_text_field( $key );
			$title   = sanitize_text_field( $method['title'] );
			$details = sanitize_textarea_field( $method['details'] );
			if ( ! empty( $title ) && ! empty( $details ) ) {
				$data[ $key ] = array(
					'title'   => $title,
					'details' => $details,
				);
			}
		}

		$data = array_filter( $data );
		if ( empty( $data ) ) {
			wp_send_json_error( esc_html__( 'Please Add At Least One Payout Method.', 'cubewp-wallet' ) );
		}
		update_user_meta( $user_id, 'cubewp_wallet_payout_methods', $data );
		wp_send_json_success( esc_html__( 'Payout Methods Successfully Saved.', 'cubewp-wallet' ) );
	}

	public static function cubewp_ajax_request_withdrawal() {
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'cubewp_wallet_withdrawal_nonce' ) ) {
			wp_send_json_error( esc_html__( 'Security Verification Failed.', 'cubewp-wallet' ) );
		}
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( esc_html__( 'Authentication Failed.', 'cubewp-wallet' ) );
		}

		global $cwpOption;
		if ( empty( $cwpOption ) ) {
			$cwpOption = get_option( 'cwpOptions' );
		}
		$user_id         = get_current_user_id();
		$amount          = isset( $_POST['cubewp_wallet_withdrawal_amount'] ) ? sanitize_text_field( $_POST['cubewp_wallet_withdrawal_amount'] ) : '';
		$message         = isset( $_POST['cubewp_wallet_withdrawal_message'] ) ? sanitize_textarea_field( $_POST['cubewp_wallet_withdrawal_message'] ) : '';
		$payout          = isset( $_POST['cubewp_wallet_withdrawal_payout'] ) ? sanitize_text_field( $_POST['cubewp_wallet_withdrawal_payout'] ) : '';
		$available_funds = get_user_meta( $user_id, 'cubewp_wallet_available_funds', true );
		$payout_method   = get_user_meta( $user_id, 'cubewp_wallet_payout_methods', true );
		$available_funds = ! empty( $available_funds ) ? $available_funds : 0;
		$payout_method   = ! empty( $payout_method ) ? $payout_method : array();

		if ( isset( $payout_method[ $payout ] ) && ! empty( $payout_method[ $payout ] ) ) {
			$payout_method = $payout_method[ $payout ];
		} else {
			wp_send_json_error( esc_html__( 'Something went wrong with payout method. Please try again later.', 'cubewp-wallet' ) );
		}

		$minimum_request = isset( $cwpOptions['cubewp_wallet_min_payout'] ) && ! empty( $cwpOptions['cubewp_wallet_min_payout'] ) ? $cwpOptions['cubewp_wallet_min_payout'] : 5;
		$maximum_request = isset( $cwpOptions['cubewp_wallet_max_payout'] ) && ! empty( $cwpOptions['cubewp_wallet_max_payout'] ) ? $cwpOptions['cubewp_wallet_max_payout'] : 50000;

		if ( ! $available_funds || $amount > $available_funds ) {
			wp_send_json_error( esc_html__( 'You have insufficient balance for this request.', 'cubewp-wallet' ) );
		}
		if ( $amount < $minimum_request ) {
			wp_send_json_error( sprintf( esc_html__( 'Amount must be greater or equals to %s', 'cubewp-wallet' ), $minimum_request ) );
		}
		if ( $amount > $maximum_request ) {
			wp_send_json_error( sprintf( esc_html__( 'Amount must be less or equals to %s', 'cubewp-wallet' ), $maximum_request ) );
		}

		if ( CubeWp_Wallet_Withdrawals_Processor::cubewp_wallet_create_withdrawal_request( $user_id, $amount, $message, $payout_method ) ) {
			wp_send_json_success( esc_html__( 'Request submitted successfully and currently pending for approval.', 'cubewp-wallet' ) );
		}
		wp_send_json_error( esc_html__( 'Something went wrong. Please try again later.', 'cubewp-wallet' ) );
	}

	public static function cubewp_wallet_transactions_pagination() {
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'pagination_nonce' ) ) {
			wp_send_json_error( esc_html__( 'Security Verification Failed.', 'cubewp-wallet' ) );
		}
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( esc_html__( 'Authentication Failed.', 'cubewp-wallet' ) );
		}
		$page_no = sanitize_text_field( $_POST['current_page'] );
		wp_send_json_success( cubewp_wallet_transactions( $page_no ) );
	}

	public static function cubewp_wallet_withdrawals_pagination() {
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'pagination_nonce' ) ) {
			wp_send_json_error( esc_html__( 'Security Verification Failed.', 'cubewp-wallet' ) );
		}
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( esc_html__( 'Authentication Failed.', 'cubewp-wallet' ) );
		}
		$page_no = sanitize_text_field( $_POST['current_page'] );
		wp_send_json_success( cubewp_wallet_withdrawals( $page_no ) );
	}
}