<?php
/**
 * CubeWP Wallet Withdrawal.
 *
 * @package cubewp-addon-wallet/cube/classes
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Wallet_Withdrawal
 */
class CubeWp_Wallet_Withdrawals_Processor {

	/**
	 * CubeWp_Wallet_Withdrawal Constructor.
	 */
	public function __construct() {
		$this->cubewp_wallet_withdrawals_transient_init();
	}

	/**
	 * Method cubewp_wallet_withdrawals_transient_init
	 *
	 * @return void
	 * @since  1.0.0
	 */
	private function cubewp_wallet_withdrawals_transient_init() {
		$transient = 'cubewp_wallet_withdrawals';
		$duration  = DAY_IN_SECONDS; // Run transient after every 24 hours.
		if ( false === ( get_transient( $transient ) ) ) {
			$this->cubewp_wallet_withdrawals_send_reminder();
			set_transient( $transient, true, $duration );
		}
	}

	/**
	 * Method cubewp_wallet_withdrawals_send_reminder
	 *
	 * @return void
	 * @since  1.0.0
	 */
	private function cubewp_wallet_withdrawals_send_reminder() {
		global $wpdb;
		$query_results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cubewp_withdraw_requests WHERE `status` = 'pending'", ARRAY_A );
		if ( ! empty( $query_results ) && count( $query_results ) > 0 ) {
			$admin_email = get_option( 'admin_email' );
			if ( ! empty( $admin_email ) ) {
				$message = '<h3>' . esc_html__( 'Withdrawal request\'s', 'cubewp-wallet' ) . '</h3>';
				$message .= '<p>' . esc_html__( 'You have pending request in the wallet current waiting for approval.', 'cubewp-wallet' ) . '</p>';
				$message .= '<a href="' . esc_url( CubeWp_Submenu::_page_action( 'cubewp-wallet-withdrawals' ) ) . '">' . esc_html__( 'View Withdrawal Request\'s', 'cubewp-wallet' ) . '</a>';
				add_filter( 'wp_mail_content_type', array( $this, 'cubewp_wallet_mail_content_type' ) );
				wp_mail( $admin_email, esc_html__( 'Pending Wallet Requests', 'cubewp-wallet' ), $message );
				remove_filter( 'wp_mail_content_type', array( $this, 'cubewp_wallet_mail_content_type' ) );
			}
		}
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

	public static function cubewp_wallet_create_withdrawal_request( $user_id, $amount, $message, $payout_method ) {
		$available_funds = get_user_meta( $user_id, 'cubewp_wallet_available_funds', true );
		$withdrawn_funds = get_user_meta( $user_id, 'cubewp_wallet_withdrawn_funds', true );

		$available_funds = ! empty( $available_funds ) ? $available_funds : 0;
		$withdrawn_funds = ! empty( $withdrawn_funds ) ? $withdrawn_funds : 0;

		$available_funds = ( $available_funds - $amount );
		$withdrawn_funds = ( $withdrawn_funds + $amount );

		update_user_meta( $user_id, 'cubewp_wallet_available_funds', ( $available_funds > 0 ) ? $available_funds : 0 );
		update_user_meta( $user_id, 'cubewp_wallet_withdrawn_funds', ( $withdrawn_funds > 0 ) ? $withdrawn_funds : 0 );

		$item_id = self::insert_withdrawal_request( array(
			'user_id' => $user_id,
			'amount'  => $amount,
			'message' => $message,
			'payout'  => maybe_serialize( $payout_method ),
			'status'  => 'pending',
		), array( '%s', '%s', '%s', '%s', '%s' ) );

		if ( $item_id ) {
			return true;
		}

		return false;
	}

	private static function insert_withdrawal_request( $data, $format ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . "cubewp_withdraw_requests", $data, $format );

		return $wpdb->insert_id;
	}

	public static function cubewp_wallet_approve_withdrawal( $item_id ) {
		global $wpdb;
		$where = array( 'ID' => esc_html( $item_id ) );
		$wpdb->update( $wpdb->prefix . "cubewp_withdraw_requests", array(
			'status' => 'approved'
		), $where );
	}

	public static function cubewp_wallet_reject_withdrawal( $item_id ) {
		$data = self::get_withdrawal_requests_by( 'ID', esc_html( $item_id ) );
		$data = isset( $data[0] ) && ! empty( $data[0] ) ? $data[0] : array();
		if ( ! empty( $data ) ) {
			$user_id = $data['user_id'];
			$amount  = $data['amount'];

			$available_funds = get_user_meta( $user_id, 'cubewp_wallet_available_funds', true );
			$withdrawn_funds = get_user_meta( $user_id, 'cubewp_wallet_withdrawn_funds', true );

			$available_funds = ! empty( $available_funds ) ? $available_funds : 0;
			$withdrawn_funds = ! empty( $withdrawn_funds ) ? $withdrawn_funds : 0;

			$available_funds = ( $available_funds + $amount );
			$withdrawn_funds = ( $withdrawn_funds - $amount );

			update_user_meta( $user_id, 'cubewp_wallet_available_funds', ( $available_funds > 0 ) ? $available_funds : 0 );
			update_user_meta( $user_id, 'cubewp_wallet_withdrawn_funds', ( $withdrawn_funds > 0 ) ? $withdrawn_funds : 0 );

			global $wpdb;
			$where = array( 'ID' => esc_html( $data['ID'] ) );
			$wpdb->update( $wpdb->prefix . "cubewp_withdraw_requests", array(
				'status' => 'rejected'
			), $where );
		}
	}

	public static function get_withdrawal_requests_by( $field, $value, $compare = '=', $select = '*', $limit = false ) {
		global $wpdb;
		$condition = '';
		if ( is_array( $field ) && is_array( $value ) ) {
			$condition .= "WHERE ";
			$count     = count( $field );
			for ( $i = 0; $i < $count; $i ++ ) {
				if ( $i != 0 ) {
					$condition .= " AND ";
				}
				$condition .= "`$field[$i]` $compare[$i] '$value[$i]'";
			}
		} else {
			$condition .= "WHERE `$field` $compare '$value'";
		}
		if ( ! $limit ) {
			$limit = '';
		}
		$query_results = $wpdb->get_results( "SELECT $select FROM {$wpdb->prefix}cubewp_withdraw_requests $condition $limit", ARRAY_A );
		if ( ! empty( $query_results ) && count( $query_results ) > 0 ) {
			return $query_results;
		}

		return array();
	}

	/**
	 * Method cubewp_wallet_mail_content_type
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function cubewp_wallet_mail_content_type() {
		return 'text/html';
	}
}