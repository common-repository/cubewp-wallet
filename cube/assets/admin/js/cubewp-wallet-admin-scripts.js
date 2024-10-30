(function($) {
    $(document).on('click', '.cubewp-admin-modal-trigger', function (event) {
        event.preventDefault();
        var $this = $(this),
            target = $($this.attr('data-cubewp-modal'));
        if (target.length > 0) {
            target.addClass('shown').fadeIn();
        }
    });
    $(document).on('click', '.cubewp-admin-modal-close', function (event) {
        event.preventDefault();
        var $this = $(this),
            target = $this.closest('.cubewp-admin-modal');
        target.removeClass('shown').fadeOut();
    });

    var view_withdrawal_details = $('.cubewp-view-withdrawal-details');
    if ( view_withdrawal_details.length > 0 ) {
        view_withdrawal_details.on('click', function(event){
            event.preventDefault();
            var $this = $(this),
                item_id = $this.find('a').attr('data-target-id'),
                modal = $('#cubewp-wallet-withdrawal-modal'),
                content = modal.find('.cubewp-wallet-withdrawal-modal-content');
            modal.addClass('shown').fadeIn();
            content.addClass('cubewp-processing-ajax').empty();
            $.ajax({
                type: 'POST',
                url: cubewp_wallet_admin_scripts_params.ajax_url,
                data: {
                    action: 'cubewp_wallet_withdrawal_details',
                    item_id: item_id,
                    nonce: cubewp_wallet_admin_scripts_params.withdrawal_nonce
                },
                dataType: 'json',
                success: function (response) {
                    content.removeClass('cubewp-processing-ajax');
                    content.html(response.data);
                },
                error: function (error) {
                    console.log(error);
                    modal.removeClass('shown').fadeOut();
                    content.removeClass('cubewp-processing-ajax');
                }
            });
        })
    }

    var view_dispute_details = $('.cubewp-view-dispute-details');
    if ( view_dispute_details.length > 0 ) {
        view_dispute_details.on('click', function(event){
            event.preventDefault();
            var $this = $(this),
                item_id = $this.find('a').attr('data-target-id'),
                modal = $('#cubewp-wallet-dispute-modal'),
                content = modal.find('.cubewp-wallet-dispute-modal-content');
            modal.addClass('shown').fadeIn();
            content.addClass('cubewp-processing-ajax').empty();
            $.ajax({
                type: 'POST',
                url: cubewp_wallet_admin_scripts_params.ajax_url,
                data: {
                    action: 'cubewp_wallet_dispute_details',
                    item_id: item_id,
                    nonce: cubewp_wallet_admin_scripts_params.dispute_nonce
                },
                dataType: 'json',
                success: function (response) {
                    content.removeClass('cubewp-processing-ajax');
                    content.html(response.data);
                },
                error: function (error) {
                    console.log(error);
                    modal.removeClass('shown').fadeOut();
                    content.removeClass('cubewp-processing-ajax');
                }
            });
        })
    }
})(jQuery);