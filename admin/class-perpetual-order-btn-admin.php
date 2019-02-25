<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://atarimtr.com
 * @since      1.0.0
 *
 * @package    Perpetual_Order_Btn
 * @subpackage Perpetual_Order_Btn/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Perpetual_Order_Btn
 * @subpackage Perpetual_Order_Btn/admin
 * @author     Yehuda Tiram <yehuda@atarimtr.co.il>
 */
class Perpetual_Order_Btn_Admin
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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }
/**
Adding custom user fields
 */

    public function atr_show_extra_profile_fields($user)
    {?>

	<h3><?php echo __('Order again the same predefined order', 'perpetual-order-btn') ?></h3>

	<table id="user_perpetual_order_tbl" class="form-table">

		<tr>
			<th><label for="user_perpetual_order"><?php echo __('Perpetual order number (can be order from other customer!)', 'perpetual-order-btn') ?></label></th>

			<td>

				<input type="text" name="user_perpetual_order" id="user_perpetual_order" value="<?php echo esc_attr(get_user_meta($user->ID, 'user_perpetual_order', true)); ?>" class="regular-text" />
				<br />

				<span class="description"><?php echo __('Write the client\'s perpetual order number. Take it from one of his existing orders.', 'perpetual-order-btn') ?></span>
			</td>
		</tr>

	</table>
<?php }

    public function atr_save_extra_profile_fields($user_id)
    {

        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        if ($this->check_isset_post('user_perpetual_order')) {

            $raw_order_id = $_POST["user_perpetual_order"];

            // order id must be digits only
            if (ctype_digit($raw_order_id) == false) {
                
                add_action('user_profile_update_errors', array($this, 'check_user_perpetual_order_field'), 10, 3);

            } else {
                
                $order_id = intval($raw_order_id);
                
                if ($order_id > 0) {
                    
                    update_user_meta($user_id, 'user_perpetual_order', $order_id);

                } elseif ($order_id == 0) {
                    var_dump($order_id);
                    add_action('user_profile_update_errors', array($this, 'check_user_perpetual_order_field_zero'), 10, 3);

                    delete_user_meta($user_id, 'user_perpetual_order');

                } else {
                    
                    add_action('user_profile_update_errors', array($this, 'check_user_perpetual_order_field'), 10, 3);

                }
            }

        } else {
            
            delete_user_meta($user_id, 'user_perpetual_order');
        }

    }

    public function check_user_perpetual_order_field($errors, $update, $user)
    {
        $errors->add('not_int_order', __('Perpetual order ID must contain digits only'));
    }

    public function check_user_perpetual_order_field_zero($errors, $update, $user)
    {
        $errors->add('order_id_zero', __('Perpetual order ID can\'t be 0. It was saved as empty!'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        //wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/perpetual-order-btn-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        //wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/perpetual-order-btn-admin.js', array('jquery'), $this->version, false);

    }

    /**
     *  Check if value is in $_POST
     * @since    1.0.0
     */
    public function check_isset_post($field_name)
    {
        if (isset($_POST[$field_name]) && !empty($_POST[$field_name])) {return true;} else {return false;}
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
