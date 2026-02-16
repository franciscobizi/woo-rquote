jQuery(document).ready(function($) {
    var rqQuantity = 1;

    var __ = function( text ) {
        if ( typeof wp !== 'undefined' && wp.i18n && wp.i18n.__ ) {
            return wp.i18n.__( text, 'woo-rquote' );
        }
        return text;
    };

     // change counter field
    $(document).on('change', '.swoopopup-counter', function(e) {
        rqQuantity = e.target.value;
    });

    // Add to quote cart
    $(document).on('click', '.rquote-add-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var productId = btn.attr('data-product-id');
        var btnText = btn.find('span span, .elementor-button-text');
        var originalText = btnText.text();
        
        btnText.text( __( 'Adding...', 'woo-rquote' ) );

        $.ajax({
            url: woo_rquote_params.ajax_url,
            type: 'POST',
            data: {
                action: 'rquote_add_to_cart',
                product_id: productId,
                quantity: rqQuantity,
                nonce: woo_rquote_params.nonce
            },
            success: function(response) {
                if (response.success) {
                    btnText.text( __( 'Added to Quote', 'woo-rquote' ) );
                    setTimeout(function() {
                        btnText.text( originalText );
                        let classes = document.querySelector('.woopp-side-basket');
                        let updatedContent = response.data.content.replace('woopp-side-basket', classes.classList);
                        $('.woopp-side-container').html(updatedContent);

                    }, 2000);
                } else {
                    //alert(response.data.message);
                    btnText.text( originalText );
                }
            }
        });
    });

    // Remove from quote cart
    $(document).on('click', '.remove-item-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var productId = btn.attr('data-product-id');
        UpdateShopCart();
        $.ajax({
            url: woo_rquote_params.ajax_url,
            type: 'POST',
            data: {
                action: 'rquote_remove_from_cart',
                product_id: productId,
                nonce: woo_rquote_params.nonce
            },
            beforeSend: function() {
                btn.closest('tr, .woopp-side-products').fadeOut();
                if (btn.hasClass('sidecart-delete') && window.location.pathname.includes('request-quote')) {
                    $('.woo-rquote-table tr[data-product-id="' + productId + '"]')
                    .fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            },
            success: function(response) {
                if (response.success) {
                    //location.reload(); 
                }
            }
        });
    });

    // Update quantity
    $(document).on('click', '.qty-btn', function() {
        var btn = $(this);
        var row = btn.closest('tr');
        var productId = row.attr('data-product-id');
        var input = row.find('.qty-input');
        var currentVal = parseInt(input.val());

        if (btn.hasClass('plus')) {
            currentVal++;
        } else if (btn.hasClass('minus') && currentVal > 1) {
            currentVal--;
        }

        input.val(currentVal);

        UpdateShopCart();

        $.ajax({
            url: woo_rquote_params.ajax_url,
            type: 'POST',
            data: {
                action: 'rquote_update_quantity',
                product_id: productId,
                quantity: currentVal,
                nonce: woo_rquote_params.nonce
            }
        });
    });

    // Submit Request Form
    $('#woo-rquote-submit-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var msgDiv = $('#woo-rquote-msg');
        var submitBtn = $('#woo-rquote-submit-btn');

        msgDiv.hide().removeClass('success error');
        submitBtn.prop('disabled', true).text( __( 'Sending...', 'woo-rquote' ) );

        $.ajax({
            url: woo_rquote_params.ajax_url,
            type: 'POST',
            data: {
                action: 'rquote_submit_request',
                form_data: form.serialize(),
                nonce: woo_rquote_params.nonce
            },
            success: function(response) {
                if (response.success) {
                    msgDiv.addClass('success').text(response.data.message).show();
                    form.slideUp();
                    $('.woo-rquote-table-wrapper').slideUp();
                    $('.woo-rquote-empty-msg').show();
                } else {
                    msgDiv.addClass('error').text(response.data.message).show();
                    submitBtn.prop('disabled', false).text( __( 'Send Request', 'woo-rquote' ) );
                }
            }
        });
    });

    // cart update function to refresh cart content and storage
    
        function UpdateShopCart(){
            setTimeout(function(){
                RefreshCartContent();
                UpdateStorageCart();
            }, 1000);
        }

        function RefreshCartContent(){

            jQuery.ajax({
               url: woo_rquote_params.ajax_url,
               type: 'POST',
               data: {
                    action: 'rquote_load_sidecart',
                    load_sidecart: true,
                    nonce: woo_rquote_params.nonce
               },
               success: function( res ){
                  if(res.data.status){
                    let classes = document.querySelector('.woopp-side-basket');
                    let updatedContent = res.data.content.replace('woopp-side-basket', classes.classList);
                    $('.woopp-side-container').html(updatedContent);
                  }
               },
               complete: function(){
               }
            })
        }

        function UpdateStorageCart(){
            window.localStorage.setItem("storage_cart", Date.now());
        }

        function HandleStorageCart(e)
        {
            if (e.storageArea != localStorage) return;
            if (e.key === 'storage_cart') {
                RefreshCartContent();
            }
        }

        window.addEventListener('storage', HandleStorageCart);
});
