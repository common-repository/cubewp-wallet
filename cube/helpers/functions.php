<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'cubewp_wallet_price' ) ) {
	function cubewp_wallet_price( $price, $currency = '' ) {
        $price = ! empty( $price ) ? $price : 0;
		if ( empty( $currency ) ) {
			global $cwpOptions;
			if ( empty( $cwpOptions ) ) {
				$cwpOptions = get_option( 'cwpOptions' );
			}
			$currency_symbol = isset( $cwpOptions['cubewp_wallet_currency'] ) && ! empty( $cwpOptions['cubewp_wallet_currency'] ) ? $cwpOptions['cubewp_wallet_currency'] : get_woocommerce_currency();
			return cubewp_get_wallet_currency_symbol( $currency_symbol ) . $price;
		} else {
			if ( ! empty( cubewp_get_wallet_currency_symbol( $currency ) ) ) {
				return cubewp_get_wallet_currency_symbol( $currency ) . $price;
			} else {
				return $currency . $price;
			}
		}
	}
}

if ( ! function_exists( 'cubewp_get_wallet_currencies' ) ) {
	function cubewp_get_wallet_currencies( $symbols = true ) {
		if ( ! $symbols ) {
			return apply_filters( 'cubewp_wallet_currencies', array(
				'AED' => __( 'United Arab Emirates dirham', 'cubewp-wallet' ),
				'AFN' => __( 'Afghan afghani', 'cubewp-wallet' ),
				'ALL' => __( 'Albanian lek', 'cubewp-wallet' ),
				'AMD' => __( 'Armenian dram', 'cubewp-wallet' ),
				'ANG' => __( 'Netherlands Antillean guilder', 'cubewp-wallet' ),
				'AOA' => __( 'Angolan kwanza', 'cubewp-wallet' ),
				'ARS' => __( 'Argentine peso', 'cubewp-wallet' ),
				'AUD' => __( 'Australian dollar', 'cubewp-wallet' ),
				'AWG' => __( 'Aruban florin', 'cubewp-wallet' ),
				'AZN' => __( 'Azerbaijani manat', 'cubewp-wallet' ),
				'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'cubewp-wallet' ),
				'BBD' => __( 'Barbadian dollar', 'cubewp-wallet' ),
				'BDT' => __( 'Bangladeshi taka', 'cubewp-wallet' ),
				'BGN' => __( 'Bulgarian lev', 'cubewp-wallet' ),
				'BHD' => __( 'Bahraini dinar', 'cubewp-wallet' ),
				'BIF' => __( 'Burundian franc', 'cubewp-wallet' ),
				'BMD' => __( 'Bermudian dollar', 'cubewp-wallet' ),
				'BND' => __( 'Brunei dollar', 'cubewp-wallet' ),
				'BOB' => __( 'Bolivian boliviano', 'cubewp-wallet' ),
				'BRL' => __( 'Brazilian real', 'cubewp-wallet' ),
				'BSD' => __( 'Bahamian dollar', 'cubewp-wallet' ),
				'BTC' => __( 'Bitcoin', 'cubewp-wallet' ),
				'BTN' => __( 'Bhutanese ngultrum', 'cubewp-wallet' ),
				'BWP' => __( 'Botswana pula', 'cubewp-wallet' ),
				'BYR' => __( 'Belarusian ruble (old)', 'cubewp-wallet' ),
				'BYN' => __( 'Belarusian ruble', 'cubewp-wallet' ),
				'BZD' => __( 'Belize dollar', 'cubewp-wallet' ),
				'CAD' => __( 'Canadian dollar', 'cubewp-wallet' ),
				'CDF' => __( 'Congolese franc', 'cubewp-wallet' ),
				'CHF' => __( 'Swiss franc', 'cubewp-wallet' ),
				'CLP' => __( 'Chilean peso', 'cubewp-wallet' ),
				'CNY' => __( 'Chinese yuan', 'cubewp-wallet' ),
				'COP' => __( 'Colombian peso', 'cubewp-wallet' ),
				'CRC' => __( 'Costa Rican col&oacute;n', 'cubewp-wallet' ),
				'CUC' => __( 'Cuban convertible peso', 'cubewp-wallet' ),
				'CUP' => __( 'Cuban peso', 'cubewp-wallet' ),
				'CVE' => __( 'Cape Verdean escudo', 'cubewp-wallet' ),
				'CZK' => __( 'Czech koruna', 'cubewp-wallet' ),
				'DJF' => __( 'Djiboutian franc', 'cubewp-wallet' ),
				'DKK' => __( 'Danish krone', 'cubewp-wallet' ),
				'DOP' => __( 'Dominican peso', 'cubewp-wallet' ),
				'DZD' => __( 'Algerian dinar', 'cubewp-wallet' ),
				'EGP' => __( 'Egyptian pound', 'cubewp-wallet' ),
				'ERN' => __( 'Eritrean nakfa', 'cubewp-wallet' ),
				'ETB' => __( 'Ethiopian birr', 'cubewp-wallet' ),
				'EUR' => __( 'Euro', 'cubewp-wallet' ),
				'FJD' => __( 'Fijian dollar', 'cubewp-wallet' ),
				'FKP' => __( 'Falkland Islands pound', 'cubewp-wallet' ),
				'GBP' => __( 'Pound sterling', 'cubewp-wallet' ),
				'GEL' => __( 'Georgian lari', 'cubewp-wallet' ),
				'GGP' => __( 'Guernsey pound', 'cubewp-wallet' ),
				'GHS' => __( 'Ghana cedi', 'cubewp-wallet' ),
				'GIP' => __( 'Gibraltar pound', 'cubewp-wallet' ),
				'GMD' => __( 'Gambian dalasi', 'cubewp-wallet' ),
				'GNF' => __( 'Guinean franc', 'cubewp-wallet' ),
				'GTQ' => __( 'Guatemalan quetzal', 'cubewp-wallet' ),
				'GYD' => __( 'Guyanese dollar', 'cubewp-wallet' ),
				'HKD' => __( 'Hong Kong dollar', 'cubewp-wallet' ),
				'HNL' => __( 'Honduran lempira', 'cubewp-wallet' ),
				'HRK' => __( 'Croatian kuna', 'cubewp-wallet' ),
				'HTG' => __( 'Haitian gourde', 'cubewp-wallet' ),
				'HUF' => __( 'Hungarian forint', 'cubewp-wallet' ),
				'IDR' => __( 'Indonesian rupiah', 'cubewp-wallet' ),
				'ILS' => __( 'Israeli new shekel', 'cubewp-wallet' ),
				'IMP' => __( 'Manx pound', 'cubewp-wallet' ),
				'INR' => __( 'Indian rupee', 'cubewp-wallet' ),
				'IQD' => __( 'Iraqi dinar', 'cubewp-wallet' ),
				'IRR' => __( 'Iranian rial', 'cubewp-wallet' ),
				'IRT' => __( 'Iranian toman', 'cubewp-wallet' ),
				'ISK' => __( 'Icelandic kr&oacute;na', 'cubewp-wallet' ),
				'JEP' => __( 'Jersey pound', 'cubewp-wallet' ),
				'JMD' => __( 'Jamaican dollar', 'cubewp-wallet' ),
				'JOD' => __( 'Jordanian dinar', 'cubewp-wallet' ),
				'JPY' => __( 'Japanese yen', 'cubewp-wallet' ),
				'KES' => __( 'Kenyan shilling', 'cubewp-wallet' ),
				'KGS' => __( 'Kyrgyzstani som', 'cubewp-wallet' ),
				'KHR' => __( 'Cambodian riel', 'cubewp-wallet' ),
				'KMF' => __( 'Comorian franc', 'cubewp-wallet' ),
				'KPW' => __( 'North Korean won', 'cubewp-wallet' ),
				'KRW' => __( 'South Korean won', 'cubewp-wallet' ),
				'KWD' => __( 'Kuwaiti dinar', 'cubewp-wallet' ),
				'KYD' => __( 'Cayman Islands dollar', 'cubewp-wallet' ),
				'KZT' => __( 'Kazakhstani tenge', 'cubewp-wallet' ),
				'LAK' => __( 'Lao kip', 'cubewp-wallet' ),
				'LBP' => __( 'Lebanese pound', 'cubewp-wallet' ),
				'LKR' => __( 'Sri Lankan rupee', 'cubewp-wallet' ),
				'LRD' => __( 'Liberian dollar', 'cubewp-wallet' ),
				'LSL' => __( 'Lesotho loti', 'cubewp-wallet' ),
				'LYD' => __( 'Libyan dinar', 'cubewp-wallet' ),
				'MAD' => __( 'Moroccan dirham', 'cubewp-wallet' ),
				'MDL' => __( 'Moldovan leu', 'cubewp-wallet' ),
				'MGA' => __( 'Malagasy ariary', 'cubewp-wallet' ),
				'MKD' => __( 'Macedonian denar', 'cubewp-wallet' ),
				'MMK' => __( 'Burmese kyat', 'cubewp-wallet' ),
				'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'cubewp-wallet' ),
				'MOP' => __( 'Macanese pataca', 'cubewp-wallet' ),
				'MRU' => __( 'Mauritanian ouguiya', 'cubewp-wallet' ),
				'MUR' => __( 'Mauritian rupee', 'cubewp-wallet' ),
				'MVR' => __( 'Maldivian rufiyaa', 'cubewp-wallet' ),
				'MWK' => __( 'Malawian kwacha', 'cubewp-wallet' ),
				'MXN' => __( 'Mexican peso', 'cubewp-wallet' ),
				'MYR' => __( 'Malaysian ringgit', 'cubewp-wallet' ),
				'MZN' => __( 'Mozambican metical', 'cubewp-wallet' ),
				'NAD' => __( 'Namibian dollar', 'cubewp-wallet' ),
				'NGN' => __( 'Nigerian naira', 'cubewp-wallet' ),
				'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'cubewp-wallet' ),
				'NOK' => __( 'Norwegian krone', 'cubewp-wallet' ),
				'NPR' => __( 'Nepalese rupee', 'cubewp-wallet' ),
				'NZD' => __( 'New Zealand dollar', 'cubewp-wallet' ),
				'OMR' => __( 'Omani rial', 'cubewp-wallet' ),
				'PAB' => __( 'Panamanian balboa', 'cubewp-wallet' ),
				'PEN' => __( 'Sol', 'cubewp-wallet' ),
				'PGK' => __( 'Papua New Guinean kina', 'cubewp-wallet' ),
				'PHP' => __( 'Philippine peso', 'cubewp-wallet' ),
				'PKR' => __( 'Pakistani rupee', 'cubewp-wallet' ),
				'PLN' => __( 'Polish z&#x142;oty', 'cubewp-wallet' ),
				'PRB' => __( 'Transnistrian ruble', 'cubewp-wallet' ),
				'PYG' => __( 'Paraguayan guaran&iacute;', 'cubewp-wallet' ),
				'QAR' => __( 'Qatari riyal', 'cubewp-wallet' ),
				'RON' => __( 'Romanian leu', 'cubewp-wallet' ),
				'RSD' => __( 'Serbian dinar', 'cubewp-wallet' ),
				'RUB' => __( 'Russian ruble', 'cubewp-wallet' ),
				'RWF' => __( 'Rwandan franc', 'cubewp-wallet' ),
				'SAR' => __( 'Saudi riyal', 'cubewp-wallet' ),
				'SBD' => __( 'Solomon Islands dollar', 'cubewp-wallet' ),
				'SCR' => __( 'Seychellois rupee', 'cubewp-wallet' ),
				'SDG' => __( 'Sudanese pound', 'cubewp-wallet' ),
				'SEK' => __( 'Swedish krona', 'cubewp-wallet' ),
				'SGD' => __( 'Singapore dollar', 'cubewp-wallet' ),
				'SHP' => __( 'Saint Helena pound', 'cubewp-wallet' ),
				'SLL' => __( 'Sierra Leonean leone', 'cubewp-wallet' ),
				'SOS' => __( 'Somali shilling', 'cubewp-wallet' ),
				'SRD' => __( 'Surinamese dollar', 'cubewp-wallet' ),
				'SSP' => __( 'South Sudanese pound', 'cubewp-wallet' ),
				'STN' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'cubewp-wallet' ),
				'SYP' => __( 'Syrian pound', 'cubewp-wallet' ),
				'SZL' => __( 'Swazi lilangeni', 'cubewp-wallet' ),
				'THB' => __( 'Thai baht', 'cubewp-wallet' ),
				'TJS' => __( 'Tajikistani somoni', 'cubewp-wallet' ),
				'TMT' => __( 'Turkmenistan manat', 'cubewp-wallet' ),
				'TND' => __( 'Tunisian dinar', 'cubewp-wallet' ),
				'TOP' => __( 'Tongan pa&#x2bb;anga', 'cubewp-wallet' ),
				'TRY' => __( 'Turkish lira', 'cubewp-wallet' ),
				'TTD' => __( 'Trinidad and Tobago dollar', 'cubewp-wallet' ),
				'TWD' => __( 'New Taiwan dollar', 'cubewp-wallet' ),
				'TZS' => __( 'Tanzanian shilling', 'cubewp-wallet' ),
				'UAH' => __( 'Ukrainian hryvnia', 'cubewp-wallet' ),
				'UGX' => __( 'Ugandan shilling', 'cubewp-wallet' ),
				'USD' => __( 'United States (US) dollar', 'cubewp-wallet' ),
				'UYU' => __( 'Uruguayan peso', 'cubewp-wallet' ),
				'UZS' => __( 'Uzbekistani som', 'cubewp-wallet' ),
				'VEF' => __( 'Venezuelan bol&iacute;var', 'cubewp-wallet' ),
				'VES' => __( 'Bol&iacute;var soberano', 'cubewp-wallet' ),
				'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'cubewp-wallet' ),
				'VUV' => __( 'Vanuatu vatu', 'cubewp-wallet' ),
				'WST' => __( 'Samoan t&#x101;l&#x101;', 'cubewp-wallet' ),
				'XAF' => __( 'Central African CFA franc', 'cubewp-wallet' ),
				'XCD' => __( 'East Caribbean dollar', 'cubewp-wallet' ),
				'XOF' => __( 'West African CFA franc', 'cubewp-wallet' ),
				'XPF' => __( 'CFP franc', 'cubewp-wallet' ),
				'YER' => __( 'Yemeni rial', 'cubewp-wallet' ),
				'ZAR' => __( 'South African rand', 'cubewp-wallet' ),
				'ZMW' => __( 'Zambian kwacha', 'cubewp-wallet' ),
			) );
		} else {
			return apply_filters( 'cubewp_wallet_currency_symbols', array(
				'AED' => '&#x62f;.&#x625;',
				'AFN' => '&#x60b;',
				'ALL' => 'L',
				'AMD' => 'AMD',
				'ANG' => '&fnof;',
				'AOA' => 'Kz',
				'ARS' => '&#36;',
				'AUD' => '&#36;',
				'AWG' => 'Afl.',
				'AZN' => 'AZN',
				'BAM' => 'KM',
				'BBD' => '&#36;',
				'BDT' => '&#2547;&nbsp;',
				'BGN' => '&#1083;&#1074;.',
				'BHD' => '.&#x62f;.&#x628;',
				'BIF' => 'Fr',
				'BMD' => '&#36;',
				'BND' => '&#36;',
				'BOB' => 'Bs.',
				'BRL' => '&#82;&#36;',
				'BSD' => '&#36;',
				'BTC' => '&#3647;',
				'BTN' => 'Nu.',
				'BWP' => 'P',
				'BYR' => 'Br',
				'BYN' => 'Br',
				'BZD' => '&#36;',
				'CAD' => '&#36;',
				'CDF' => 'Fr',
				'CHF' => '&#67;&#72;&#70;',
				'CLP' => '&#36;',
				'CNY' => '&yen;',
				'COP' => '&#36;',
				'CRC' => '&#x20a1;',
				'CUC' => '&#36;',
				'CUP' => '&#36;',
				'CVE' => '&#36;',
				'CZK' => '&#75;&#269;',
				'DJF' => 'Fr',
				'DKK' => 'kr.',
				'DOP' => 'RD&#36;',
				'DZD' => '&#x62f;.&#x62c;',
				'EGP' => 'EGP',
				'ERN' => 'Nfk',
				'ETB' => 'Br',
				'EUR' => '&euro;',
				'FJD' => '&#36;',
				'FKP' => '&pound;',
				'GBP' => '&pound;',
				'GEL' => '&#x20be;',
				'GGP' => '&pound;',
				'GHS' => '&#x20b5;',
				'GIP' => '&pound;',
				'GMD' => 'D',
				'GNF' => 'Fr',
				'GTQ' => 'Q',
				'GYD' => '&#36;',
				'HKD' => '&#36;',
				'HNL' => 'L',
				'HRK' => 'kn',
				'HTG' => 'G',
				'HUF' => '&#70;&#116;',
				'IDR' => 'Rp',
				'ILS' => '&#8362;',
				'IMP' => '&pound;',
				'INR' => '&#8377;',
				'IQD' => '&#x62f;.&#x639;',
				'IRR' => '&#xfdfc;',
				'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
				'ISK' => 'kr.',
				'JEP' => '&pound;',
				'JMD' => '&#36;',
				'JOD' => '&#x62f;.&#x627;',
				'JPY' => '&yen;',
				'KES' => 'KSh',
				'KGS' => '&#x441;&#x43e;&#x43c;',
				'KHR' => '&#x17db;',
				'KMF' => 'Fr',
				'KPW' => '&#x20a9;',
				'KRW' => '&#8361;',
				'KWD' => '&#x62f;.&#x643;',
				'KYD' => '&#36;',
				'KZT' => '&#8376;',
				'LAK' => '&#8365;',
				'LBP' => '&#x644;.&#x644;',
				'LKR' => '&#xdbb;&#xdd4;',
				'LRD' => '&#36;',
				'LSL' => 'L',
				'LYD' => '&#x644;.&#x62f;',
				'MAD' => '&#x62f;.&#x645;.',
				'MDL' => 'MDL',
				'MGA' => 'Ar',
				'MKD' => '&#x434;&#x435;&#x43d;',
				'MMK' => 'Ks',
				'MNT' => '&#x20ae;',
				'MOP' => 'P',
				'MRU' => 'UM',
				'MUR' => '&#x20a8;',
				'MVR' => '.&#x783;',
				'MWK' => 'MK',
				'MXN' => '&#36;',
				'MYR' => '&#82;&#77;',
				'MZN' => 'MT',
				'NAD' => 'N&#36;',
				'NGN' => '&#8358;',
				'NIO' => 'C&#36;',
				'NOK' => '&#107;&#114;',
				'NPR' => '&#8360;',
				'NZD' => '&#36;',
				'OMR' => '&#x631;.&#x639;.',
				'PAB' => 'B/.',
				'PEN' => 'S/',
				'PGK' => 'K',
				'PHP' => '&#8369;',
				'PKR' => '&#8360;',
				'PLN' => '&#122;&#322;',
				'PRB' => '&#x440;.',
				'PYG' => '&#8370;',
				'QAR' => '&#x631;.&#x642;',
				'RMB' => '&yen;',
				'RON' => 'lei',
				'RSD' => '&#1088;&#1089;&#1076;',
				'RUB' => '&#8381;',
				'RWF' => 'Fr',
				'SAR' => '&#x631;.&#x633;',
				'SBD' => '&#36;',
				'SCR' => '&#x20a8;',
				'SDG' => '&#x62c;.&#x633;.',
				'SEK' => '&#107;&#114;',
				'SGD' => '&#36;',
				'SHP' => '&pound;',
				'SLL' => 'Le',
				'SOS' => 'Sh',
				'SRD' => '&#36;',
				'SSP' => '&pound;',
				'STN' => 'Db',
				'SYP' => '&#x644;.&#x633;',
				'SZL' => 'E',
				'THB' => '&#3647;',
				'TJS' => '&#x405;&#x41c;',
				'TMT' => 'm',
				'TND' => '&#x62f;.&#x62a;',
				'TOP' => 'T&#36;',
				'TRY' => '&#8378;',
				'TTD' => '&#36;',
				'TWD' => '&#78;&#84;&#36;',
				'TZS' => 'Sh',
				'UAH' => '&#8372;',
				'UGX' => 'UGX',
				'USD' => '&#36;',
				'UYU' => '&#36;',
				'UZS' => 'UZS',
				'VEF' => 'Bs F',
				'VES' => 'Bs.S',
				'VND' => '&#8363;',
				'VUV' => 'Vt',
				'WST' => 'T',
				'XAF' => 'CFA',
				'XCD' => '&#36;',
				'XOF' => 'CFA',
				'XPF' => 'Fr',
				'YER' => '&#xfdfc;',
				'ZAR' => '&#82;',
				'ZMW' => 'ZK',
			) );
		}
	}
}

if ( ! function_exists( 'cubewp_get_wallet_currency_symbol' ) ) {
	function cubewp_get_wallet_currency_symbol( $currency ) {
		$symbols = cubewp_get_wallet_currencies();

		return $symbols[ $currency ] ?? '';
	}
}


if ( ! function_exists( 'cubewp_wallet_sql_pagination' ) ) {
	function cubewp_wallet_sql_pagination( $maxPostsPerPage = 0, $totalPosts = 0, $current = 1, $container_id = '' ) {
		$pages     = ceil( $totalPosts / $maxPostsPerPage );
		$return    = null;
		$args = array(
			'add_args'  => false,
			'current'   => max( 1, $current ),
			'total'     => $pages,
			'type'      => 'list',
			'prev_next' => false,
		);
		if ( 1 != $pages ) {
			$return .= '<div class="cubewp-wallet-pagination" id="' . esc_html( $container_id ) . '">';
			$return .= '<div class="pagination">';
			$return .= paginate_links( $args );
			$return .= '</div>';
			$return .= '</div>';
		}

		return $return;
	}
}

if ( ! function_exists( 'cubewp_wallet_transactions' ) ) {
	function cubewp_wallet_transactions( $page_no ) {
		set_query_var( 'page_no', $page_no );
		ob_start();
		load_template(CUBEWP_WALLET_PLUGIN_DIR . 'cube/templates/cubewp-wallet-transactions.php');
		return ob_get_clean();
	}
}

if ( ! function_exists( 'cubewp_wallet_withdrawals' ) ) {
	function cubewp_wallet_withdrawals( $page_no ) {
		set_query_var( 'page_no', $page_no );
		ob_start();
		load_template(CUBEWP_WALLET_PLUGIN_DIR . 'cube/templates/cubewp-wallet-withdrawals.php');
		return ob_get_clean();
	}
}