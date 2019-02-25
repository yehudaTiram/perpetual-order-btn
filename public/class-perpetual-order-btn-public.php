<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://atarimtr.com
 * @since      1.0.0
 *
 * @package    Perpetual_Order_Btn
 * @subpackage Perpetual_Order_Btn/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Perpetual_Order_Btn
 * @subpackage Perpetual_Order_Btn/public
 * @author     Yehuda Tiram <yehuda@atarimtr.co.il>
 */
class Perpetual_Order_Btn_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Shortcode that renders button .
     *
     * @since    1.0.0
     */
    public function perpetual_order_sc($atts)
    {
        $a = shortcode_atts(array(

            // 'taxonomy_t'            => 'product_cat',
            // 'post_type_t'            => 'product',
            // 'uncollapse_all'            => 'true'

        ), $atts);
        $user_ID = get_current_user_id();
        $user_perpetual_order_number = esc_attr(get_user_meta($user_ID, 'user_perpetual_order', true));
        if ($user_perpetual_order_number) {
            ?>
		<form id="user_perpetual_order_form" name="user_perpetual_order_form"  method="post">
        
          <input id="user_perpetual_order_number" name="user_perpetual_order_number" type="hidden" value="<?php echo $user_perpetual_order_number; ?>" />
          <?php wp_nonce_field( '_pob_wpnonce', '_wpnonce' ) ?>
		  <button class="user-perpetual-order-btn" type="submit"  onclick="return confirm('<?php echo __("Are you sure? The perpetual order items will replace everything in you cart!", "perpetual-order-btn") ?>')"><?php echo __('Add my perpetual order to the cart', 'perpetual-order-btn') ?></button>
		</form>
		<?php
}

    }
    /**
     * Add all predefined order items to cart
     */
    public function add_product_to_cart()
    {
        if ( ($this->check_isset_post('user_perpetual_order_number')) && (  wp_verify_nonce( $_POST['_wpnonce'], '_pob_wpnonce' )  ) ) {



            global $woocommerce;

            $user = wp_get_current_user();

            $order_id = intval( esc_attr($_POST["user_perpetual_order_number"]) );

            if ( esc_attr(get_user_meta($user->ID, 'user_perpetual_order', true)) != $order_id){
                wp_die( __("There was a problem! Please contact shop admin.", "perpetual-order-btn") , __("Didn\'t work!", "perpetual-order-btn") );
            }

            if ($order_id > 0) {
                //check if product already in cart
                if (sizeof(WC()->cart->get_cart()) > 0) {

                    // First, empty cart
                    WC()->cart->empty_cart();

                }
                //$order = wc_get_order( $order_id );
                $original_order = wc_get_order($order_id);
                foreach ($original_order->get_items() as $item_id => $item_product) {
                    //Get the product ID
                    $product = $item_product->get_product();

                    $product_id = $item_product->get_product_id();

                    $quantity = $item_product->get_quantity();

                    $variation_id = $item_product->get_variation_id();

                    $variation = '';

                    if ($variation_id > 0) {

                        $variation = $product->get_variation_attributes();

                    }

                    // Then, add the order products to the cart
                    WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);

                }
                $redirect_after = 'Location: ' . wc_get_cart_url();
                header($redirect_after);
            } else {
                
                // Alert if order ID not integer. Loaded in wp_head due to window.onload
                add_action('wp_head', array($this, 'not_int_order'), 10);

            }

        }

    }

    /**
     * Notice when order ID is not integer
     */
    public function not_int_order()
    {
        ?>
        <script type="text/javascript">
            function not_int_order_js() {
                alert("<?php echo __('Nothing changed! Order ID must be an Integer!', 'perpetual-order-btn'); ?>");
            }
            window.onload = not_int_order_js;
        </script>

        <?php
}

    /**
     * Clone order
     */

    public function clone_order_cta($order)
    {

        if ($this->check_isset_post('user_perpetual_order_number')) {

            global $woocommerce, $post;

            $order_id = $_POST["user_perpetual_order_number"];

            //$order = wc_get_order( $order_id );
            $original_order = wc_get_order($order_id);

            $this->debug($original_order->get_customer_id());

            $customer_id = $original_order->get_customer_id();

            $address_billing = $original_order->get_address();

            $address_shipping = $original_order->get_address('shipping');

            $order_data = array('customer_id' => $customer_id);

            $new_order = wc_create_order($order_data);

            //Get the order items from the WC_Order object:

            $original_order_products_WC_Order = [];

            foreach ($original_order->get_items() as $item_id => $item_product) {
                //Get the product ID
                $product_id = $item_product->get_product_id();
                $quantity = $item_product->get_quantity();
                $variation_id = $item_product->get_variation_id();
                //Get the WC_Product object
                $product = $item_product->get_product();
                //Get the product SKU (using WC_Product method)
                $sku = $product->get_sku();
                $original_order_products_WC_Order['item_id'] = $item_product;
                $this->debug($original_order_products_WC_Order['item_id']);
                $args = array(
                    'variation' => array('attribute_color' => 'red'),
                );
                $new_order->add_product(wc_get_product($product_id), $quantity, array(
                    'variation' => $variation_id,
                ));

            }

            // //Accessing and unprotecting WC_Order_Item_Product data:

            // // The loop to get the order items which are WC_Order_Item_Product objects since WC 3+

            // $original_order_WC_Order_Item_Product_data = [];

            // // $original_order_WC_Order_Item_Product_meta_data = [];

            // // $original_order_WC_Order_Item_Product_formatted_meta_data = [];

            // foreach ($original_order->get_items() as $item_id => $item_product) {

            //     // Get the common data in an array:
            //     $item_product_data_array = $item_product->get_data();

            //     // Get the special meta data in an array:
            //     $item_product_meta_data_array = $item_product->get_meta_data();

            //     // get only additional meta data (formatted in an unprotected array)
            //     $formatted_meta_data = $item_product->get_formatted_meta_data();

            //     $original_order_WC_Order_Item_Product_data['id'] =  $item_product_data_array;

            //     $this->debug($original_order_WC_Order_Item_Product_data['id']);

            //     // $original_order_WC_Order_Item_Product_meta_data['id'] = $item_product_meta_data_array;

            //     // $original_order_WC_Order_Item_Product_formatted_meta_data['id'] = $formatted_meta_data;

            //     // $this->debug($original_order_WC_Order_Item_Product_meta_data['id']);

            //     // $this->debug($original_order_WC_Order_Item_Product_formatted_meta_data['id']);

            // }

            $new_order->set_address($address_billing, 'billing');

            $new_order->set_address($address_shipping, 'shipping');

            //return wc_create_order($new_order);

        }

        //return $actions;

    }

    /**
     *  Check if value is in $_POST
     * @since    1.0.0
     */
    public function check_isset_post($field_name)
    {
        if (isset($_POST[$field_name]) && !empty($_POST[$field_name])) {return true;} else {return false;}
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/perpetual-order-btn-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/perpetual-order-btn-public.js', array('jquery'), $this->version, false);

    }

    public function debug()
    {
        $trace = debug_backtrace();
        $rootPath = dirname(dirname(__FILE__));
        $file = str_replace($rootPath, '', $trace[0]['file']);
        $line = $trace[0]['line'];
        $var = $trace[0]['args'][0];
        $lineInfo = sprintf('<div><strong>%s</strong> (line <strong>%s</strong>)</div>', $file, $line);
        $debugInfo = sprintf('<pre>%s</pre>', print_r($var, true));
        print_r($lineInfo . $debugInfo);
    }

}
