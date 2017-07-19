<?php

ini_set('display_errors', 0);
/**
 * Plugin Name:       Woo Twilio - Plivo SMS Addon
 * Plugin URL:        Woo Twilio - Plivo SMS Addon
 * Description:       Woo Twilio - Plivo SMS Addon is use to send test message notification for order status to user. <br/>WooCommerce Send SMS alert after place order by customers, And admin can update order so customers get notification by text message into customers mobile number, its provied into order details.
 * Version:           1.0.0
 * WC requires at least: 2.3
 * WC tested up to: 2.6.13
 * Requires at least: 4.0+
 * Tested up to: 4.7.1
 * Contributors: wp_estatic
 * Author:            Estatic Infotech Pvt Ltd
 * Author URI:        http://estatic-infotech.com/
 * License:           GPLv3
 * @package WooCommerce
 * @category Woocommerce Order SMS Alert, Notification, Twilio, Plivo
 */
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

require ( 'woo_twilio_sms_alert.php' );
require ( 'woo_plivo_sms_alert.php' );

//echo get_template_part( WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)) . 'admin' );

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    deactivate_plugins(plugin_basename(__FILE__));
    add_action('load-plugins.php', function() {
        add_filter('gettext', 'change_text', 99, 3);
    });

    function change_text($translated_text, $untranslated_text, $domain) {
        $old = array(
            "Plugin <strong>activated</strong>.",
            "Selected plugins <strong>activated</strong>."
        );

        $new = "Please activate <b>Woocommerce</b> Plugin to use WooCommerce SMS Alert - Twilio/Plivo plugin";

        if (in_array($untranslated_text, $old, true)) {
            $translated_text = $new;
            remove_filter(current_filter(), __FUNCTION__, 99);
        }
        return $translated_text;
    }

    return FALSE;
}



/* Plugin Settings link */

function twilio_estatic_sms_plugin_action_links($links) {
    $links[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=wc-settings&tab=twilio_estatic&section=twilio')) . '">Settings</a>';
    return $links;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'twilio_estatic_sms_plugin_action_links');

function twilio_estatic_admin_styles() {
    wp_enqueue_style('twilio-estatic-admin-css', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)) . '/css/twilio_estatic_admin.css');
    wp_enqueue_script( 'estatic-admin-script-main', plugin_dir_url( __FILE__ ) . '/js/ei_sms_main.js' );
    
}

add_action('admin_init', 'twilio_estatic_admin_styles');

add_filter('woocommerce_get_settings_pages', 'ei_add_settings_page');

function ei_add_settings_page($settings) {
    $settings[] = include( 'woo_plugin_settings_sections_ei.php' );
    return $settings;
}

function replace_order_message_variables($order_id, $message, $order_status = '') {
    $order = wc_get_order($order_id);

    if ($order_status == '') {
        $update_order_status = ucfirst($order->get_status());
    } else {
        $update_order_status = ucfirst($order_status);
    }

    $replacements = array(
        '%shop_name%' => get_bloginfo('name'),
        '%order_id%' => $order->get_order_number(),
        '%order_count%' => $order->get_item_count(),
        '%order_amount%' => $order->get_total(),
        '%order_status%' => $update_order_status,
        '%billing_name%' => $order->get_formatted_billing_full_name(),
        '%shipping_name%' => $order->get_formatted_shipping_full_name(),
        '%shipping_method%' => $order->get_shipping_method(),
    );
    return str_replace(array_keys($replacements), $replacements, $message);
}

