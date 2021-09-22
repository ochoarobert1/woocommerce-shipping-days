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
        add_action('admin_enqueue_scripts', array($this, 'custom_enqueue_scripts'));
    }

    public function wooshipdays_settings()
    {
        register_setting('wooshipdays-settings-group', 'dias');
        register_setting('wooshipdays-settings-group', 'checkout_message');
    }

    public function custom_enqueue_scripts()
    {
        wp_enqueue_style('admin_styles', plugins_url('css/admin-style.css', __FILE__), array(), null, 'all');
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
                <?php $checked = (in_array('lunes', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="lunes" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Martes</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('martes', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="martes" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Miércoles</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('miercoles', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="miercoles" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Jueves</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('jueves', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="jueves" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Viernes</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('viernes', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="viernes" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Sábado</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('sabado', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="sabado" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Domingo</th>
                <?php $checked = ''; ?>
                <?php $checked = (in_array('domingo', get_option('dias'))) ? 'checked="checked"' : ''; ?>
                <td><input type="checkbox" name="dias[]" value="domingo" <?php echo $checked; ?> /></td>
            </tr>

            <tr valign="top">
                <th colspan="2" scope="row">Mensaje en el Carrito/Checkout</th>
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
}

new WooShipDays;
