<?php
/**
 * CubeWP Wallet Processor.
 *
 * @package cubewp-addon-wallet/cube/classes
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Wallet_Processor
 */
class CubeWp_Wallet_Processor {

	/**
	 * CubeWp_Wallet_Processor Constructor.
	 */
	public function __construct() {
		self::cubewp_wallet_transient_init();
	}

	/**
	 * Method cubewp_wallet_transient_init
	 *
	 * @return void
	 * @since  1.0.0
	 */
	private static function cubewp_wallet_transient_init() {
		$transient = 'cubewp_wallet_transient';
		$duration  = HOUR_IN_SECONDS * 12; // Run transient after every 12 hours.
		if ( false === ( get_transient( $transient ) ) ) {
			$approved = self::cubewp_wallet_release_funds();
			set_transient( $transient, $approved, $duration );
		}
	}

	public static function cubewp_wallet_release_funds() {
		$on_hold_funds   = self::get_wallet_transactions_by( 'status', 'on-hold', '=', '`ID`, `data`, `created_at`' );
		$processed_funds = array();
		if ( ! empty( $on_hold_funds ) && is_array( $on_hold_funds ) ) {
			foreach ( $on_hold_funds as $hold_fund ) {
				$ID = $hold_fund['ID'];
				$data = maybe_unserialize( $hold_fund['data'] );
				$created_at = strtotime( $hold_fund['created_at'] );
				$now = time();
				$differance = $now - $created_at;
				$differance = round($differance / (60 * 60 * 24));
				global $cwpOptions;
				if ( empty( $cwpOptions ) ) {
					$cwpOptions = get_option( 'cwpOptions' );
				}
				if ( isset( $data['hold_period'] ) && ! empty( isset( $data['hold_period'] ) ) ) {
					$on_hold_period = $data['hold_period'];
				}else {
					$on_hold_period = isset( $cwpOptions['cubewp_wallet_hold_period'] ) && ! empty( $cwpOptions['cubewp_wallet_hold_period'] ) ? $cwpOptions['cubewp_wallet_hold_period'] : 7;
				}
				if ( $differance >= $on_hold_period ) {
					if ( self::cubewp_add_funds_to_vendor_wallet( $ID ) ) {
						$processed_funds[] = $ID;
					}
				}
			}
		}

		return $processed_funds;
	}

	public static function get_wallet_transactions_by( $field, $value, $compare = '=', $select = '*', $limit = false ) {
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
		if ( ! $limit ){
			$limit = '';
		}
		$query_results = $wpdb->get_results( "SELECT $select FROM {$wpdb->prefix}cubewp_wallet $condition $limit", ARRAY_A );
		if ( ! empty( $query_results ) && count( $query_results ) > 0 ) {
			return $query_results;
		}

		return array();
	}

	public static function cubewp_add_funds_to_vendor_wallet( $item_id, $is_transient = true ) {
		$return        = false;
		$query_results = self::get_wallet_transactions_by( 'ID', $item_id );
		$query_results = isset( $query_results[0] ) && ! empty( $query_results[0] ) ? $query_results[0] : array();
		if ( ! empty( $query_results ) ) {
			$vendor_id            = $query_results['vendor_id'];
			$amount               = $query_results['total_price'];
			$status               = $query_results['status'];
			$author_funds         = get_user_meta( $vendor_id, 'cubewp_wallet_available_funds', true );
			$author_funds         = ! empty( $author_funds ) ? $author_funds : 0;
			$author_on_hold_funds = get_user_meta( $vendor_id, 'cubewp_wallet_on_hold_funds', true );
			$author_on_hold_funds = ! empty( $author_on_hold_funds ) ? $author_on_hold_funds : 0;
			$author_overall_funds = get_user_meta( $vendor_id, 'cubewp_wallet_overall_funds', true );
			$author_overall_funds = ! empty( $author_overall_funds ) ? $author_overall_funds : 0;
			if ( $is_transient ) {
				if ( $status == 'on-hold' ) {
					$author_funds += $amount;
					update_user_meta( $vendor_id, 'cubewp_wallet_available_funds', $author_funds );
					$author_on_hold_funds = $author_on_hold_funds - $amount;
					update_user_meta( $vendor_id, 'cubewp_wallet_on_hold_funds', $author_on_hold_funds );
					self::update_wallet_payment_status( $item_id, 'approved' );
					$return = true;
				}
			} else {
				if ( $status == 'approved' ) {
					$author_funds += $amount;
					update_user_meta( $vendor_id, 'cubewp_wallet_available_funds', $author_funds );
					$author_overall_funds += $amount;
					$return = true;
				} else if ( $status == 'on-hold' ) {
					$author_on_hold_funds += $amount;
					update_user_meta( $vendor_id, 'cubewp_wallet_on_hold_funds', $author_on_hold_funds );
					$author_overall_funds += $amount;
					$return = true;
				} else if ( $status == 'refunded' ) {
					$author_on_hold_funds -= $amount;
					update_user_meta( $vendor_id, 'cubewp_wallet_on_hold_funds', $author_on_hold_funds );
					$author_overall_funds -= $amount;
					$return = true;
				}
				update_user_meta( $vendor_id, 'cubewp_wallet_overall_funds', $author_overall_funds );
			}
		}

		return $return;
	}

	private static function update_wallet_payment_status( $ID, $status ) {
		global $wpdb;
		$where = array( 'ID' => $ID );
		$wpdb->update( $wpdb->prefix . "cubewp_wallet", array(
			'status' => $status
		), $where );

		return $ID;
	}

	public static function cubewp_add_funds_to_wallet( $parameters ) {
		if ( self::can_order_add_to_wallet( $parameters ) ) {
			return self::cubewp_add_to_wallet( $parameters );
		}

		return false;
	}

	private static function can_order_add_to_wallet( $parameters ) {
		if ( ! empty( $parameters ) && ( isset( $parameters['amount'] ) && ! empty( $parameters['amount'] ) && is_numeric( $parameters['amount'] ) ) && ( isset( $parameters['post_id'] ) && ! empty( $parameters['post_id'] ) && is_numeric( $parameters['post_id'] ) ) ) {
			return true;
		}

		return false;
	}

	private static function cubewp_wallet_get_value( $field, $parameters ) {
		global $cwpOptions;
		if ( empty( $cwpOptions ) ) {
			$cwpOptions = get_option( 'cwpOptions' );
		}
		switch ( $field ) {
			case 'amount':
				return $parameters['amount'];
			case 'customer_id':
				return $parameters['customer_id'] ?? get_current_user_id();
			case 'post_id':
				return $parameters['post_id'];
			case 'order_id':
				return $parameters['order_id'];
			case 'vendor_id':
				if ( ! isset( $parameters['vendor_id'] ) ) {
					return get_post_field( 'post_author', $parameters['post_id'] );
				}
				return $parameters['vendor_id'];
			case 'currency':
				if ( ! isset( $parameters['currency'] ) ) {
					$currency_symbol = isset( $cwpOptions['cubewp_wallet_currency'] ) && ! empty( $cwpOptions['cubewp_wallet_currency'] ) ? $cwpOptions['cubewp_wallet_currency'] : get_woocommerce_currency();

					return cubewp_get_wallet_currency_symbol( $currency_symbol );
				}
				if ( ! empty( $parameters['currency'] ) ) {
					return $parameters['currency'];
				}
				return false;
			case 'commission':
				if ( ! isset( $parameters['commission'] ) ) {
					return isset( $cwpOptions['cubewp_wallet_commission'] ) && ! empty( $cwpOptions['cubewp_wallet_commission'] ) ? $cwpOptions['cubewp_wallet_commission'] : 0;
				}
				if ( ! empty( $parameters['commission'] ) ) {
					return true;
				}
				return false;
			case 'commission_type':
				if ( ! isset( $parameters['commission']['commission_type'] ) ) {
					return isset( $cwpOptions['cubewp_wallet_commission_type'] ) && ! empty( $cwpOptions['cubewp_wallet_commission_type'] ) ? $cwpOptions['cubewp_wallet_commission_type'] : 0;
				}
				if ( ! empty( $parameters['commission']['commission_type'] ) ) {
					return $parameters['commission']['commission_type'];
				}
				return false;
			case 'commission_value':
				if ( ! isset( $parameters['commission']['commission_value'] ) ) {
					return isset( $cwpOptions['cubewp_wallet_commission_value'] ) && ! empty( $cwpOptions['cubewp_wallet_commission_value'] ) ? $cwpOptions['cubewp_wallet_commission_value'] : 0;
				}
				if ( ! empty( $parameters['commission']['commission_value'] ) ) {
					return $parameters['commission']['commission_value'];
				}
				return false;
			case 'on_hold':
				if ( ! isset( $parameters['on_hold'] ) ) {
					return isset( $cwpOptions['cubewp_wallet_hold_payments'] ) && ! empty( $cwpOptions['cubewp_wallet_hold_payments'] ) ? $cwpOptions['cubewp_wallet_hold_payments'] : 0;
				}
				if ( ! empty( $parameters['on_hold'] ) ) {
					return true;
				}
				return false;
			case 'hold_period':
				if ( ! isset( $parameters['on_hold']['hold_period'] ) ) {
					return isset( $cwpOptions['cubewp_wallet_hold_period'] ) && ! empty( $cwpOptions['cubewp_wallet_hold_period'] ) ? $cwpOptions['cubewp_wallet_hold_period'] : 0;
				}
				if ( ! empty( $parameters['on_hold']['hold_period'] ) ) {
					return $parameters['on_hold']['hold_period'];
				}
				return false;
			default:
				return false;
		}
	}

	private static function cubewp_add_to_wallet( $parameters ) {
		$status           = 'on-hold';
		$commission       = self::cubewp_wallet_get_value( 'commission', $parameters );
		$commission_type  = self::cubewp_wallet_get_value( 'commission_type', $parameters );
		$commission_value = self::cubewp_wallet_get_value( 'commission_value', $parameters );
		$on_hold          = self::cubewp_wallet_get_value( 'on_hold', $parameters );
		$on_hold_period   = self::cubewp_wallet_get_value( 'hold_period', $parameters );
		$post_id          = self::cubewp_wallet_get_value( 'post_id', $parameters );
		$order_id         = self::cubewp_wallet_get_value( 'order_id', $parameters );
		$amount           = self::cubewp_wallet_get_value( 'amount', $parameters );
		$vendor_id        = self::cubewp_wallet_get_value( 'vendor_id', $parameters );
		$currency         = self::cubewp_wallet_get_value( 'currency', $parameters );
		$customer_id      = self::cubewp_wallet_get_value( 'customer_id', $parameters );
		if ( $commission ) {
			if ( ! $commission_type || ! $commission_value || ! is_numeric( $commission_value ) ) {
				$commission = 0;
			}
		}
		if ( $on_hold ) {
			if ( ! $on_hold_period || ! is_numeric( $on_hold_period ) ) {
				$on_hold = 0;
			}
		}
		if ( ! $on_hold ) {
			$status = 'approved';
		}
		$total_price       = $amount;
		$commission_amount = 0;
		$commission_data   = array();
		if ( $commission ) {
			if ( $commission_type == 'percentage' ) {
				$commission_amount = ( $commission_value / 100 ) * $total_price;
				$total_price       = $total_price - $commission_amount;
			} else if ( $commission_type == 'fixed' ) {
				$commission_amount = $commission_value;
				$total_price       = $total_price - $commission_amount;
			}
			$commission_amount        = round( $commission_amount, 2 );
			$total_price              = round( $total_price, 2 );
			$commission_data['type']  = $commission_type;
			$commission_data['value'] = $commission_value;
		}
		$data = array();
		if ( ! empty( $commission_data ) ) {
			$data['commission'] = $commission_data;
		}
		$data['currency'] = $currency;
		$data['hold_period'] = $on_hold_period;
		$item_id = self::insert_payment_into_wallet( array(
			'post_id'           => $post_id,
			'order_id'          => $order_id,
			'vendor_id'         => $vendor_id,
			'customer_id'       => $customer_id,
			'sub_price'         => $amount,
			'commission_amount' => $commission_amount,
			'total_price'       => $total_price,
			'status'            => $status,
			'data'              => maybe_serialize( $data ),
		), array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );

		return self::cubewp_add_funds_to_vendor_wallet( $item_id, false );
	}

	private static function insert_payment_into_wallet( $data, $format ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . "cubewp_wallet", $data, $format );

		return $wpdb->insert_id;
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
}
/*
Insert Into Wallet Example Code

$parameters = array(
	'amount'     => 100, // Amount to add into wallet { numeric } [ Required ]
	'post_id'    => 1, // Add a post id to this record { Post ID } [ Required ]
	'order_id'   => 1, // Add a order id to this record { Order ID } [ Optional ( Cannot be null ) ]
	'vendor_id'  => 1, // Which user is receiving this amount { User ID } [ Optional ( post author will be use as replacement ) ]
	'currency'   => 'Rs', // Give currency symbol { String } [ Optional ( CubeWP settings will be use as replacement ) ]
	'commission' => array( // Get commission on amount { array | false } [ Optional ( CubeWP settings will be use as replacement ) ]
		'commission_type'  => 'percentage', // Commission type { percentage | fixed } [ Optional ( CubeWP settings will be use as replacement ) ]
		'commission_value' => '25' // Commission value { numeric } [ Optional ( CubeWP settings will be use as replacement ) ]
	),
	'on_hold' => array( // Hold the amount for specific days before it is available for withdrawal { Array | False } [ Optional ( CubeWP settings will be use as replacement ) ]
		'hold_period'  => '7' // Specify the hold amount period in days { numeric } [ Optional ( CubeWP settings will be use as replacement ) ]
	)
);
$insert_into_wallet = CubeWp_Wallet_Processor::cubewp_add_funds_to_wallet( $parameters );
*/