<?php
/**
 * Plugin Name: WooCommerce Dias de Despacho
 * Plugin URI: https://robertochoaweb.com/
 * Description: Plugin para seleccion de dias de despacho.
 * Version: 1.0.0
 * Author: Robert Ochoa
 * Author URI: https://robertochoaweb.com
 * Text Domain: woo_shipping_days
 * Domain Path: /languages/
 * Requires at least: 5.5
 * Requires PHP: 7.0
 *
 * @package WooShipDays
 */

class WooShipDays
{
    const SLUG = 'woo_shipping_days';
    const PREFIX = 'wsd_';

    public function __construct()
    {
        add_action('admin_menu', array($this, 'custom_submenu'));
        add_action('admin_init', array($this, 'wooshipdays_settings'));
        add_action('admin_enqueue_scripts', array($this, 'custom_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'custom_frontend_scripts'));
        add_action('woocommerce_proceed_to_checkout', array($this, 'custom_cart_message'), 15);
        add_action('woocommerce_review_order_before_submit', array($this, 'custom_cart_message'), 10);
        add_action('woocommerce_thankyou_cod', array($this, 'custom_cart_message'), 15);
        add_action('woocommerce_email_after_order_table', array($this, 'custom_cart_message'), 10);
    }

    public function wooshipdays_settings()
    {
        register_setting('wooshipdays-settings-group', 'dias');
        register_setting('wooshipdays-settings-group', 'hora');
        register_setting('wooshipdays-settings-group', 'checkout_message');
    }

    public function custom_admin_scripts()
    {
        wp_enqueue_style('admin_styles', plugins_url('css/admin-style.css', __FILE__), array(), null, 'all');
    }

    public function custom_frontend_scripts()
    {
        wp_enqueue_style('frontend_styles', plugins_url('css/front-style.css', __FILE__), array(), null, 'all');
    }

    public function custom_submenu()
    {
        add_submenu_page('woocommerce', __('Dias de Despacho', self::SLUG), __('Dias de Despacho', self::SLUG), 'manage_options', 'woocommerce-shipping-days', array($this, 'shipping_days_dashboard'), null);
    }

    public function shipping_days_dashboard()
    {
        ob_start(); ?>
<header class="woocommerce-main-header-panel">
    <h1><?php echo get_admin_page_title(); ?></h1>
</header>
<div class="woocommerce-main-content-panel">
    <form method="post" action="options.php">
        <?php settings_fields('wooshipdays-settings-group'); ?>
        <?php do_settings_sections('wooshipdays-settings-group'); ?>
        <h2>Seleccione los dias de despacho</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Lunes</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('Monday', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="Monday" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Martes</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('Tuesday', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="Tuesday" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Miércoles</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('Wednesday', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="Wednesday" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Jueves</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('Thursday', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="Thursday" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Viernes</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('Friday', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="Friday" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Sábado</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('Saturday', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="Saturday" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Domingo</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('Sunday', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="Sunday" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th colspan="2" scope="row">
                    <h2>Hora máxima a recibir pedidos del mismo día</h2>
                </th>
            </tr>

            <tr valign="top">
                <td colspan="2"><input type="time" name="hora" value="<?php echo esc_attr(get_option('hora')); ?>" /></td>
            </tr>

            <tr valign="top">
                <th colspan="2" scope="row">
                    <h2>Mensaje en el Carrito/Checkout</h2>
                    <small>Nota: Deja siempre el texto {dia} para que el sistema pueda reemplazar esto con el cálculo del día a despachar. Puedes usar HTML</small>
                </th>
            </tr>

            <tr valign="top">
                <th colspan="2"><textarea name="checkout_message" id="" cols="60" rows="4" placeholder="Escribe aquí el mensaje a mostrar al cliente"><?php echo get_option('checkout_message'); ?></textarea></th>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
<?php
        $content = ob_get_clean();
        echo $content;
    }

    public function get_closest_day()
    {
        $startDate = DateTime::createFromFormat('U', current_time('timestamp'));
        $closest_day = 0;
        $closest_day_text = '';
        $closest_day_format = '';
        $shipping_days = get_option('dias');

        foreach ($shipping_days as $item) {
            $endDate  = DateTime::createFromFormat('U', current_time('timestamp'));

            $endDate->modify('next ' . $item);
            $difference = $endDate->diff($startDate);

            if ($closest_day == 0) {
                $closest_day = intval($difference->d);
                $closest_day_text = $endDate->format("l");
                $closest_day_format = $endDate->format("d/m/Y");
            } else {
                if ($closest_day > intval($difference->d)) {
                    $closest_day = intval($difference->d);
                    $closest_day_text = $endDate->format("l");
                    $closest_day_format = $endDate->format("d/m/Y");
                }
            }
        }

        switch ($closest_day_text) {
            case 'Monday':
                $closest_day_text = 'Lunes';
                break;

            case 'Tuesday':
                $closest_day_text = 'Martes';
                break;

            case 'Wednesday':
                $closest_day_text = 'Miércoles';
                break;

            case 'Thursday':
                $closest_day_text = 'Jueves';
                break;

            case 'Friday':
                $closest_day_text = 'Viernes';
                break;

            case 'Saturday':
                $closest_day_text = 'Sábado';
                break;

            case 'Sunday':
                $closest_day_text = 'Domingo';
                break;
        }

        $response = array(
            'dia' =>  $closest_day_text,
            'formato' => $closest_day_format
        );

        return $response;
    }

    public function custom_cart_message()
    {
        $shipping_days = get_option('dias');
        $current_day = date("l");

        ob_start();
        if (!in_array($current_day, $shipping_days)) {
            $closest_day_arr = $this->get_closest_day();

            $custom_message = get_option('checkout_message');
            $custom_message = str_replace([
                '{dia}',
            ], [
                $closest_day_arr['dia'] . ' ' . $closest_day_arr['formato']
            ], $custom_message); ?>
<div class="woocommerce-custom-message">
    <?php echo $custom_message ?>
</div>
<?php
        } else {
            $startDate = DateTime::createFromFormat('U', current_time('timestamp'));
            $limit_hour_raw = get_option('hora');
            $limit_hour = explode(':', $limit_hour_raw);
            if (intval($startDate->format('H')) > intval($limit_hour[0])) {
                $closest_day_arr = $this->get_closest_day();

                $custom_message = get_option('checkout_message');
                $custom_message = str_replace([
                    '{dia}',
                ], [
                    $closest_day_arr['dia'] . ' ' . $closest_day_arr['formato']
                ], $custom_message); ?>
<div class="woocommerce-custom-message">
    <?php echo $custom_message ?>
</div>
<?php
            }
        }
        $content = ob_get_clean();
        echo $content;
    }
}

new WooShipDays;
