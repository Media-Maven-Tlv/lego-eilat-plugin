<?php
defined('ABSPATH') || exit;

global $product;

if (!$product->is_purchasable()) {
  return;
}
$cart_quantity_settings = PG_WC_Helper::getQuantityFieldSettings($product);

if ($product->is_in_stock()) :
?>

  <?php do_action('woocommerce_before_add_to_cart_form'); ?>

  <form class="add-to-cart-form mb-3" method="post" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" enctype="multipart/form-data" data-product-id="<?php echo absint($product->get_id()); ?>">
    <div class="d-flex flex-column-reverse flex-md-row flex-wrap mb-5 row">
      <label for="inputQty" class="col-form-label col-lg-3 col-md-4 col-sm-3 col-xl-2 hidden text-dark">כמות</label>
      <div class="d-flex">
        <?php do_action('woocommerce_before_add_to_cart_quantity');

        woocommerce_quantity_input(
          array(
            'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
            'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
            'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(),
          )
        );

        do_action('woocommerce_after_add_to_cart_quantity'); ?>
      </div>
      <div class="align-items-center cart_button_single_prod col-md-12 d-flex justify-content-between mt-2 pt-md-2">

        <?php if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true' && $product->get_meta('eilat_stock') > 0) :
          do_action('eilat_add_to_cart_button');
        ?>

        <?php else : ?>
          <button type="submit" class="ajax_add_to_cart add_to_cart_button btn btn-danger col-10 fw-bold pb-3 pe-5 ps-5 pt-3 rounded rounded-1 single_add_to_cart_button text-md-center text-start wp-block-button__link" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" data-product_id="<?php echo get_the_ID(); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()) ?>" data-quantity="1">
            <?php echo esc_html($product->single_add_to_cart_text()); ?>
          </button>
        <?php endif; ?>


        <?php do_action('woocommerce_after_add_to_cart_button'); ?>
        <div class="wishlist_product_page">
          <?php //echo do_shortcode( '[yith_wcwl_add_to_wishlist]' ); 
          ?>
        </div>
      </div>
    </div>
  </form>

  <?php do_action('woocommerce_after_add_to_cart_form'); ?>

<?php else : ?>
  <?php if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true' && $product->get_meta('eilat_stock') > 0) :
    do_action('eilat_add_to_cart_button');
  endif; ?>
<?php endif; ?>
<?php if ($product->get_meta('eilat_stock') > 0) : ?>
  <div class="mb-4">
    <a href="#"><span class="fa fa-share" data-toggle="tooltip" data-original-title="Share"></span></a>
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" role="switch" id="toggleEilatMode" name="toggleEilatMode" aria-checked="<?php echo isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true' ? 'true' : 'false'; ?>" <?php echo isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true' ? 'checked' : ''; ?>>
      <label class="form-check-label" for="toggleEilatMode">
        הזמנה מחוץ לאילת
      </label>
    </div>
  </div>
<?php endif; ?>