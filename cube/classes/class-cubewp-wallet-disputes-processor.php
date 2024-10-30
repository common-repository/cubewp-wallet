<?php
/**
 * CubeWP Wallet Disputes.
 *
 * @package cubewp-addon-wallet/cube/classes
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Wallet_Disputes_Processor
 */
class CubeWp_Wallet_Disputes_Processor {
	public static function cubewp_wallet_create_dispute_request( $order_id, $user_id, $details, $status = 'pending' ) {
		global $wpdb;
		$where = array( 'order_id' => $order_id );
		$wpdb->update( $wpdb->prefix . "cubewp_wallet", array(
			'status' => 'disputed'
		), $where );
		$transaction = $wpdb->get_row( "SELECT `ID` FROM {$wpdb->prefix}cubewp_wallet WHERE `order_id` = {$order_id}", ARRAY_A );
		if ( ! empty( $transaction ) && is_array( $transaction ) ) {
			$id = self::insert_dispute_request( array(
				'transaction_id' => $transaction['ID'],
				'user_id'  => $user_id,
				'details' => $details,
				'status'  => $status,
			), array( '%s', '%s', '%s', '%s' ) );
			if ( $id ) {
				return true;
			}
		}

		return false;
	}

	private static function insert_dispute_request( $data, $format ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . "cubewp_dispute_requests", $data, $format );

		return $wpdb->insert_id;
	}

	public static function cubewp_wallet_dispute_status( $id, $status ) {
		global $wpdb;
		$transaction = $wpdb->get_row( "SELECT `transaction_id` FROM {$wpdb->prefix}cubewp_dispute_requests WHERE `ID` = {$id}", ARRAY_A );
		$transaction_id = $transaction['transaction_id'];

		if ( $status == 'approve' ) {
			$is_transient = false;
			$_status = 'refunded';
		}else if ( $status == 'reject' ) {
			$_status = 'on-hold';
			$is_transient = true;
		}else {
			return false;
		}

		if ( ! empty( $transaction_id ) ) {
			$where = array( 'ID' => $transaction_id );
			$wpdb->update( $wpdb->prefix . "cubewp_wallet", array(
				'status' => $_status
			), $where );
			CubeWp_Wallet_Processor::cubewp_add_funds_to_vendor_wallet( $transaction_id, $is_transient );
		}

		$where = array( 'ID' => $id );
		$wpdb->update( $wpdb->prefix . "cubewp_dispute_requests", array(
			'status' => $status
		), $where );

		return true;
	}

	public static function get_dispute_requests_by( $field, $value, $compare = '=', $select = '*', $limit = false ) {
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
		$query_results = $wpdb->get_results( "SELECT $select FROM {$wpdb->prefix}cubewp_dispute_requests $condition $limit", ARRAY_A );
		if ( ! empty( $query_results ) && count( $query_results ) > 0 ) {
			return $query_results;
		}

		return array();
	}
}