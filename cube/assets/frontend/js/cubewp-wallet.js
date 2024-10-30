(function($) {
    var payout_method_form = $('#cubewp_wallet_payout_methods'),
        switch_payout_form = $('.cubewp-wallet-payout-method'),
        remove_payout_form = $('.cubewp-wallet-payout-remove-method'),
        request_withdrawal = $('#cubewp_wallet_withdrawal'),
        transactions_links = $('#cubewp-wallet-transactions-pagination a.page-numbers'),
        withdrawals_links  = $('#cubewp-wallet-withdrawals-pagination a.page-numbers'),
        wallet_modal_close = $('.cubewp-modal-close');

    if (wallet_modal_close.length > 0) {
        wallet_modal_close.on('click', function (event) {
            event.preventDefault();
            var $this = jQuery(this),
                target = $this.closest('.cubewp-modal');
            if (target.attr('id') === 'cubewp-wallet-withdraw-modal') {
                target.find('form')[0].reset();
            }
        });
    }

    if (payout_method_form.length > 0) {
        payout_method_form.on('submit', function (e) {
            e.preventDefault();
            var $this = $(this),
                button = $this.find('[type=submit]');
            if ( ! button.hasClass('cubewp-processing-ajax')) {
                button.addClass('cubewp-processing-ajax');
                var form = $this.serialize();
                form += '&action=cubewp_ajax_payout_methods&nonce=' + cubewp_wallet_scripts_params.payout_nonce;
                $.ajax({
                    type: 'POST',
                    url: cubewp_wallet_scripts_params.ajax_url,
                    data: form,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success === true) {
                            cwp_notification_ui('success', response.data);
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }else {
                            cwp_notification_ui('error', response.data);
                        }
                        button.removeClass('cubewp-processing-ajax');
                    },
                    error: function (error) {
                        console.log(error);
                        button.removeClass('cubewp-processing-ajax');
                        cwp_notification_ui('error', cubewp_wallet_scripts_params.error_msg)
                    }
                });
            }
        });
    }

    if (switch_payout_form.length > 0) {
        switch_payout_form.on('click', function (e) {
            e.preventDefault();
            var $this = $(this),
                target = $('#' + $this.attr('data-cubewp-wallet-payout-method'));
            switch_payout_form.removeClass('cwp-active');
            $this.addClass('cwp-active');
            if (target.length > 0) {
                $('.cubewp-wallet-payout-method-form').removeClass('cwp-active');
                target.addClass('cwp-active');
            }
        })
    }

    if (remove_payout_form.length > 0) {
        remove_payout_form.on('click', function (e) {
            e.preventDefault();
            var $this = $(this).closest('.cubewp-wallet-payout-method'),
                target = $('#' + $this.attr('data-cubewp-wallet-payout-method'));
            $this.remove();
            if (target.length > 0) {
                target.remove();
            }
            $('.cubewp-wallet-payout-method').first().trigger('click');
        })
    }

    if (request_withdrawal.length > 0) {
        request_withdrawal.on('submit', function (e) {
            e.preventDefault();
            var $this = $(this),
                button = $this.find('[type=submit]');
            if ( ! button.hasClass('cubewp-processing-ajax')) {
                button.addClass('cubewp-processing-ajax');
                var form = $this.serialize();
                form += '&action=cubewp_ajax_request_withdrawal&nonce=' + cubewp_wallet_scripts_params.withdrawal_nonce;
                $.ajax({
                    type: 'POST',
                    url: cubewp_wallet_scripts_params.ajax_url,
                    data: form,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success === true) {
                            cwp_notification_ui('success', response.data);
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }else {
                            cwp_notification_ui('error', response.data);
                        }
                        button.removeClass('cubewp-processing-ajax');
                    },
                    error: function (error) {
                        console.log(error);
                        button.removeClass('cubewp-processing-ajax');
                        cwp_notification_ui('error', cubewp_wallet_scripts_params.error_msg)
                    }
                });
            }
        });
    }

    if (transactions_links.length > 0) {
        $(document).on('click', '#cubewp-wallet-transactions-pagination a.page-numbers', function (e) {
            e.preventDefault();
            var $this = $(this),
                transactions = $this.closest('.cubewp-wallet-all-transactions'),
                page_no;
            if ($this.hasClass('next')) {
                $this = transactions.find('.current').closest('li').next().find('a');
            }else if ($this.hasClass('prev')) {
                $this = transactions.find('.current').closest('li').prev().find('a');
            }
            page_no = $this.text();
            transactions.addClass('cubewp-wallet-processing-ajax').empty();
            $.ajax({
                type: 'POST',
                url: cubewp_wallet_scripts_params.ajax_url,
                data: {
                    action: 'cubewp_wallet_transactions_pagination',
                    current_page: page_no,
                    nonce: cubewp_wallet_scripts_params.pagination_nonce,
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success === true) {
                        transactions.html(response.data);
                    }else {
                        cwp_notification_ui('error', response.data);
                    }
                    transactions.removeClass('cubewp-wallet-processing-ajax');
                },
                error: function (error) {
                    console.log(error);
                    transactions.removeClass('cubewp-wallet-processing-ajax');
                    cwp_notification_ui('error', cubewp_wallet_scripts_params.error_msg)
                }
            });
        });
    }

    if (withdrawals_links.length > 0) {
        $(document).on('click', '#cubewp-wallet-withdrawals-pagination a.page-numbers', function (e) {
            e.preventDefault();
            var $this = $(this),
                withdrawals = $this.closest('.cubewp-wallet-withdrawals-container'),
                page_no;
            if ($this.hasClass('next')) {
                $this = withdrawals.find('.current').closest('li').next().find('a');
            }else if ($this.hasClass('prev')) {
                $this = withdrawals.find('.current').closest('li').prev().find('a');
            }
            page_no = $this.text();
            withdrawals.addClass('cubewp-wallet-processing-ajax').empty();
            $.ajax({
                type: 'POST',
                url: cubewp_wallet_scripts_params.ajax_url,
                data: {
                    action: 'cubewp_wallet_withdrawals_pagination',
                    current_page: page_no,
                    nonce: cubewp_wallet_scripts_params.pagination_nonce,
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success === true) {
                        withdrawals.html(response.data);
                    }else {
                        cwp_notification_ui('error', response.data);
                    }
                    withdrawals.removeClass('cubewp-wallet-processing-ajax');
                },
                error: function (error) {
                    console.log(error);
                    withdrawals.removeClass('cubewp-wallet-processing-ajax');
                    cwp_notification_ui('error', cubewp_wallet_scripts_params.error_msg)
                }
            });
        });
    }
})(jQuery);