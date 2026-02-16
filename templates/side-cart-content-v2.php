<?php
    // Refactored to use Woo RQuote session instead of YITH
    $items = [];
    if ( class_exists( 'Woo_RQuote_Session' ) ) {
        $items = Woo_RQuote_Session::get_cart();
        if(count($items) === 0 && isset($product_id) && isset($quantity)){
             $items = [
                [
                    'product_id' => $product_id,
                    'quantity' => $quantity
                ]
            ];
        }
    }
?>
<div class="woopp-side-basket">
      <span class="woopp-side-items-count"><?php echo count($items);?></span>
      <span class="woopp-side-bki">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-basket3-fill" viewBox="0 0 16 16">
        <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H.5a.5.5 0 0 1-.5-.5v-1A.5.5 0 0 1 .5 6h1.717L5.07 1.243a.5.5 0 0 1 .686-.172zM2.468 15.426.943 9h14.114l-1.525 6.426a.75.75 0 0 1-.729.574H3.197a.75.75 0 0 1-.73-.574z"/>
        </svg>
      </span>
   </div>
   <div class="woopp-side-header">
      <div class="woopp-sideh-top">
         <div class="woopp-side-notice-container"></div>
         <div class="woopp-sideh-basket">
            <span class="woopp-sideb-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-bag" viewBox="0 0 16 16">
                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"/>
                </svg>
            </span>
            <span class="woopp-sideb-count"><?php echo count($items );?></span>
         </div>
         <span class="woopp-sideh-close">&times;</span>
      </div>
   </div>
   <div class="woopp-side-body">
    <?php
      if ( count( $items ) === 0 ) :
    ?>
        <p style="text-align: center;"><?php esc_html_e( 'אין מוצרים בהצעת המחיר', 'woo-rquote' ); ?></p>
    <?php 
      else :
        $total = 0;
        foreach ( $items as $key => $raq ) :
            $product_id = $raq['product_id'];
            $_product   = wc_get_product( (int)$product_id );
            if ( ! isset( $_product ) || ! is_object( $_product ) ) {
                continue;
            } 
    ?>
      <div class="woopp-side-products">
         <div data-product-key="<?php echo $key?>" class="woopp-side-product">
            <div>
                <?php
                    $thumbnail = $_product->get_image(array(50,50));
                    echo $thumbnail;
                ?>
            </div>
            <div class="woopp-side-pname">
                    <?php
                       $price = (float)$_product->get_price(); 
                       $total += $price * (int)$raq['quantity'];
                       $subtotal = $price * (int)$raq['quantity'];
                    ?>
                    <span style="color: #CC3366;"><?php echo wp_kses_post( $_product->get_title() ); ?></span>
                    <?php if($price > 0):?>
                    <p><?php echo ' x ' .$raq['quantity'] . ' ' . wc_price($price)  . ' = ' . wc_price($subtotal); ?></p>
                    <?php endif;?>
            </div>
            <div>
                <a href="#" class="remove-item-btn sidecart-delete" data-product-id="<?php echo esc_attr( $product_id ); ?>" title="<?php esc_attr_e( 'Remove this item', 'woo-rquote' ); ?>">&times;</a>
            </div>
         </div>
      </div>
      <?php 
           endforeach;
        endif;
      ?>
   </div>
   <div class="woopp-side-footer">
        <div class="woopp-sidecart-total" style="text-align: center;">
        <?php
            if(count( $items ) > 0){
              echo 'סך הכל:' . ' '. wc_price($total);
            }
        ?>    
        </div>
        <a href="/request-quote/" class="woopp-side-ft-btn">המשך להזמנה</a>
   </div>