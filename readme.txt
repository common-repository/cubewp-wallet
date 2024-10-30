=== CubeWP Wallet ===
Contributors: cubewp1211, teamcubewp
Donate link: NA
Tags: wallet, payment, e-commerce, online transactions, payment gateway
Requires at least: 5.0
Requires PHP: 7.0
Tested up to: 6.6.1
Stable tag: 1.0.4
License: GPLv2 or later

A Digital wallet plugin for websites that allows customers to make payments using their stored funds.


== Description ==

CubeWP Wallet is a feature-rich plugin designed primarily for developers and themes, allowing them to integrate a wallet system into their websites. With CubeWP Wallet, you can enable transactions to be added to a user's wallet, provide withdrawal functionality, and even implement commission charges. This plugin provides a flexible and customizable solution for managing virtual wallets on your WordPress site.

Features:

Insert Transactions into Wallet: Developers and theme designers can easily incorporate the ability to add transactions into a user's wallet using the provided coding examples. This feature enables users to deposit funds into their wallet for future use.

Withdrawal Functionality: Users can conveniently withdraw funds from their wallet as needed. This provides them with the flexibility to utilize their wallet balance for various purposes within your website.

Commission Charging: Admins have the option to apply commission charges on wallet transactions. This feature allows you to monetize the wallet system by deducting a percentage or fixed amount as a commission from each transaction.

Dispute Request for Refund: Customers can create dispute requests for refunding a specific transaction. This functionality ensures that users have recourse in case of any issues or discrepancies with their wallet transactions.

Insert Transaction into Wallet Example Code:

$parameters = array(
	'amount'     => 100, // Amount to add into the wallet { numeric } [ Required ]
	'post_id'    => 1, // Add a post ID to this record { Post ID } [ Required ]
	'order_id'   => 1, // Add an order ID to this record { Order ID } [ Optional ( Cannot be null ) ]
	'vendor_id'  => 1, // Identify the user who will receive this amount { User ID } [ Optional ( post author will be used as a replacement ) ]
	'currency'   => 'Rs', // Specify the currency symbol { String } [ Optional ( CubeWP settings will be used as a replacement ) ]
	'commission' => array( // Get a commission on the amount { array | false } [ Optional ( CubeWP settings will be used as a replacement ) ]
		'commission_type'  => 'percentage', // Commission type { percentage | fixed } [ Optional ( CubeWP settings will be used as a replacement ) ]
		'commission_value' => '25' // Commission value { numeric } [ Optional ( CubeWP settings will be used as a replacement ) ]
	),
	'on_hold' => array( // Hold the amount for a specific number of days before it is available for withdrawal { Array | False } [ Optional ( CubeWP settings will be used as a replacement ) ]
		'hold_period'  => '7' // Specify the hold amount period in days { numeric } [ Optional ( CubeWP settings will be used as a replacement ) ]
	)
);
CubeWp_Wallet_Processor::cubewp_add_funds_to_wallet( $parameters );

Please note that integrating CubeWP Wallet into your website requires coding knowledge as shown in the example above. Developers and theme designers can utilize this code to add transaction functionality to the wallet system.

We hope you find CubeWP Wallet Plugin helpful in managing virtual wallets on your website. For any questions, feedback, or assistance, please contact our support team at [support email or contact form].

Note: CubeWP Wallet Plugin may require additional setup and configuration based on your specific requirements.

== Changelog ==

= 1.0.3 2023-06-27

* Fixed: Currency Settings In CubeWP Options fixed.

= 1.0.2 2023-06-14

* Structure setup for Wordpress repo