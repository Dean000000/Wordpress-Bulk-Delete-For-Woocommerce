<?php
/*
Plugin Name: WooCommerce Category Bulk Delete
Plugin URI: https://deanhattingh.co.za/
Description: Delete products in bulk from selected WooCommerce category originally made for qqfabrics .
Version: 1.0
Author: Dean Hattingh
Author URI: https://deanhattingh.co.za/
License: GPL2
*/

// Add menu item in the admin dashboard
add_action('admin_menu', 'woocommerce_category_bulk_delete_menu');
function woocommerce_category_bulk_delete_menu()
{
    add_menu_page(
        'WooCommerce Category Bulk Delete',
        'Category Bulk Delete',
        'manage_options',
        'woocommerce_category_bulk_delete',
        'woocommerce_category_bulk_delete_page',
        'dashicons-trash',
        85
    );
}

// Callback function for the plugin's menu page
function woocommerce_category_bulk_delete_page()
{
    if (isset($_POST['category_id'])) {
        $category_id = absint($_POST['category_id']);
        $deleted_count = woocommerce_bulk_delete_products($category_id);
        echo '<div class="notice notice-success is-dismissible"><p>' . $deleted_count . ' products successfully deleted.</p></div>';
    }
?>
    <div class="wrap">
        <h1>WooCommerce Category Bulk Delete</h1>
        <form method="post" action="">
            <?php
            $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
            ));
            if (!empty($categories)) {
                echo '<select name="category_id">';
                foreach ($categories as $category) {
                    echo '<option value="' . $category->term_id . '">' . $category->name . '</option>';
                }
                echo '</select>';
                echo '<button type="submit" class="button button-primary">Delete Products</button>';
            } else {
                echo '<p>No categories found.</p>';
            }
            ?>
        </form>
    </div>
<?php
}

// Function to bulk delete products from a WooCommerce category
function woocommerce_bulk_delete_products($category_id)
{
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category_id,
            ),
        ),
    );

    $products = get_posts($args);
    $deleted_count = 0;

    foreach ($products as $product) {
        wp_delete_post($product->ID, true);
        $deleted_count++;
    }

    return $deleted_count;
}
