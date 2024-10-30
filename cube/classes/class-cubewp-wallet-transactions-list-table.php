<?php
/**
 * CubeWP Wallet Transactions List Table.
 *
 * @package cubewp-addon-wallet/cube/classes
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Wallet_Transactions_List_Table
 */
class CubeWp_Wallet_Transactions_List_Table extends WP_List_Table {

	public static $total_earning = 0;
	public static $on_hold_earning = 0;

	public static $commission_earning = 0;
	public static $available_earning = 0;
	public static $withdraw_earning = 0;
	private $order;

	function __construct() {
		parent::__construct();
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param Array $item Data
	 * @param String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '-';
	}

	public function usort_reorder( $a, $b ) {
		// If no sort, default to title
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field( $_GET['orderby'] ) : 'created_at';
		// If no order, default to asc
		$order = ( ! empty( $_GET['order'] ) ) ? sanitize_text_field( $_GET['order'] ) : 'desc';
		// Determine sort order
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : - $result;
	}

	function column_ID( $item ) {
		$status  = $item['status'];
		$title   = '<strong>' . $item['ID'] . '</strong>';
		$actions = [
			'delete' => sprintf( '<a href="%s">' . esc_html__( 'Delete', 'cubewp-wallet' ) . '</a>', CubeWp_Submenu::_page_action( 'cubewp-wallet', 'delete', '&item_id=' . $item['ID'], '&nonce=' . wp_create_nonce( 'cubewp_wallet_delete_record_nonce' ) ) ),
		];
		if ( $status != 'approved' ) {
			$actions['approve_now'] = sprintf( '<a href="%s">' . esc_html__( 'Approve Now', 'cubewp-wallet' ) . '</a>', CubeWp_Submenu::_page_action( 'cubewp-wallet', 'approve', '&item_id=' . $item['ID'], '&nonce=' . wp_create_nonce( 'cubewp_wallet_approve_record_nonce' ) ) );
		}

		return $title . $this->row_actions( $actions );
	}

	function column_customer_id( $item ) {
		$buyer      = get_userdata( $item['customer_id'] );
		$buyer_name = ( ! empty( $buyer ) ) ? $buyer->display_name : esc_html__( 'User not found', 'cubewp-wallet' );
		if ( isset( $buyer->display_name ) ) {
			echo '<a href="' . get_edit_profile_url( $item['customer_id'] ) . '"><strong>' . esc_html( $buyer_name ) . '</strong></a>';
		} else {
			echo '<strong>' . esc_html( $buyer_name ) . '</strong>';
		}
	}

	function column_vendor_id( $item ) {
		$author      = get_userdata( $item['vendor_id'] );
		$author_name = ( ! empty( $author ) ) ? $author->display_name : esc_html__( 'User not found', 'cubewp-wallet' );
		if ( isset( $author->display_name ) ) {
			echo '<a href="' . get_edit_profile_url( $item['vendor_id'] ) . '"><strong>' . esc_html( $author_name ) . '</strong></a>';
		} else {
			echo '<strong>' . esc_html( $author_name ) . '</strong>';
		}
	}

	function column_total_price( $item ) {
		echo '<strong>' . cubewp_wallet_price( $item['total_price'] ) . '</strong>';
	}

	function column_commission_amount( $item ) {
		echo '<strong>' . cubewp_wallet_price( $item['commission_amount'] ) . '</strong>';
	}

	function column_sub_price( $item ) {
		echo '<strong>' . cubewp_wallet_price( $item['sub_price'] ) . '</strong>';
	}

	public function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/*
		 * First, lets decide how many records per page to show
		 */
		$per_page = 20;

		/*
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		/*
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * three other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/*
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/*
		 * GET THE DATA!
		 *
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our dummy data.
		 *
		 * In a real-world situation, this is probably where you would want to
		 * make your actual database query. Likewise, you will probably want to
		 * use any posted sort or pagination data to build a custom query instead,
		 * as you'll then be able to use the returned query data immediately.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 */

		// $sql     = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cubewp_wallet LIMIT %d,%d", $current_page, $per_page );
		$data        = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cubewp_wallet", ARRAY_A );
		$withdrawals = $wpdb->get_results( "SELECT `amount` FROM {$wpdb->prefix}cubewp_withdraw_requests WHERE `status` != 'rejected'", ARRAY_A );

		foreach ( $data as $item ) {
			if ( $item['status'] == 'approved' ) {
				self::$total_earning      += $item['sub_price'];
				self::$available_earning  += $item['sub_price'];
				self::$commission_earning += $item['commission_amount'];
			} else if ( $item['status'] == 'on-hold' ) {
				self::$total_earning   += $item['sub_price'];
				self::$on_hold_earning += $item['sub_price'];
				self::$commission_earning += $item['commission_amount'];
			}
		}
		if ( ! empty( $withdrawals ) && is_array( $withdrawals ) ) {
			foreach ( $withdrawals as $withdrawal ) {
				$amount                  = $withdrawal['amount'];
				self::$available_earning -= $amount;
				self::$withdraw_earning  += $amount;
			}
		}

		/*
		 * This checks for sorting input and sorts the data in our array of dummy
		 * data accordingly (using a custom usort_reorder() function). It's for
		 * example purposes only.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary. In other words: remove this when
		 * you implement your own query.
		 */
		usort( $data, array( $this, 'usort_reorder' ) );

		/*
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count( $data );

		/*
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to do that.
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/*
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		return array(
			'ID'                => esc_html__( 'Record #', 'cubewp-wallet' ),
			'vendor_id'         => esc_html__( 'Vendor', 'cubewp-wallet' ),
			'customer_id'       => esc_html__( 'Customer', 'cubewp-wallet' ),
			'total_price'       => esc_html__( 'Amount', 'cubewp-wallet' ),
			'commission_amount' => esc_html__( 'Commission', 'cubewp-wallet' ),
			'sub_price'         => esc_html__( 'Total', 'cubewp-wallet' ),
			'status'            => esc_html__( 'Status', 'cubewp-wallet' ),
			'created_at'        => esc_html__( 'Created At', 'cubewp-wallet' ),
		);
	}

	public function get_sortable_columns() {
		return array(
			'ID'                => array( 'ID', false ),
			'vendor_id'         => array( 'vendor_id', false ),
			'customer_id'       => array( 'customer_id', false ),
			'total_price'       => array( 'total_price', false ),
			'commission_amount' => array( 'commission_amount', false ),
			'sub_price'         => array( 'sub_price', false ),
			'status'            => array( 'status', false ),
			'created_at'        => array( 'created_at', true )
		);
	}
}