<?php
if (!defined('ABSPATH')) {
  exit;
}
global $product;
?>
<div class="price_wrap">
  <p class="<?php echo esc_attr(apply_filters('woocommerce_product_price_class', 'price')); ?> align-items-center d-flex fs-6 gap-3 mb-0"><?php if ($product->is_on_sale()) : ?><span class="opacity-50 text-decoration-line-through text-muted"><?php echo PG_WC_Helper::getPrice($product, 'regular'); ?></span><?php endif; ?><?php if ($product->is_on_sale()) : ?><span class="fs-3 fw-bold text-danger"><?php echo PG_WC_Helper::getPrice($product, 'sale'); ?></span><?php endif; ?><?php if (!$product->is_on_sale()) : ?><span class="fs-3 fw-bold text-dark"><?php echo PG_WC_Helper::getPrice($product, 'regular'); ?></span><?php endif; ?></p>
  <?php if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'false') : ?>
    <p class="eilat_price">
      <span class="">מחיר אילת:</span>
      <span>
        <?php
        global $product;
        $regular_price = wc_get_price_to_display($product, array('price' => $product->get_regular_price())); // Regular price
        $sale_price = wc_get_price_to_display($product); // Sale price which could be the same as regular if no sale

        // Check if the product is on sale
        if ($product->is_on_sale()) {
          echo '<span class="text-decoration-line-through text-muted">' . wc_price(wc_get_price_excluding_tax($product, array('price' => $regular_price))) . '</span>';
          echo '<span class="fs-5 fw-bold text-danger me-2">' . wc_price(wc_get_price_excluding_tax($product, array('price' => $sale_price))) . '</span>';
        } else {
          echo wc_price(wc_get_price_excluding_tax($product, array('price' => $regular_price)));
        }

        // if ($product->is_type('bundle')) {
        //     global $product;
        //     if ($product->is_type('bundle') && is_single()) {
        //         $price = 0;
        //         foreach ($product->get_bundled_items() as $bundled_item) {
        //             $price += $bundled_item->get_price();
        //         }
        //         echo wc_price($price / 1.17);
        //     }
        // } else {
        //     if ($product->is_on_sale()) {
        //         echo wc_price($product->get_sale_price() / 1.17);
        //     } else {
        //         echo wc_price($product->get_price() / 1.17);
        //     }
        // }

        ?>
      </span>
    </p>
  <?php endif; ?>
</div>