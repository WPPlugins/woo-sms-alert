<?php

/* Send New ORDER Message Code Start */
$plugin_dir_path = str_replace('\\', '/', plugin_dir_path(__FILE__));
require_once $plugin_dir_path . "twilio_vendor/autoload.php";
/* WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)) */

use Twilio\Rest\Client;

/* woocommerce_checkout_order_processed */
/* woocommerce_thankyou */

add_action('woocommerce_thankyou', 'send_twolio_sms_to_customers_ei', 1, 1);

function send_twolio_sms_to_customers_ei($order_id) {

    $AccountSid = get_option('wc_twilio_estatic_account_id');
    $AuthToken = get_option('wc_twilio_estatic_auth_token');
    $from_number = get_option('wc_twilio_estatic_from_number');

    $client = new Client($AccountSid, $AuthToken);

    $twilio_estatic_enabled = get_option('wc_twilio_estatic_enabled');
    if ($twilio_estatic_enabled == 'yes') {
        $order = new WC_Order($order_id);
        /* You can do here whatever you want */

        $cur_order_stat = $order->post->post_status;
        $cur_order_statu = explode('wc-', $cur_order_stat);
        $cur_order_status = end($cur_order_statu);

        $message_ei = get_option('wc_twilio_sms_' . $cur_order_status . '_sms_template');

        if ($message_ei == '') {
            $message_ei = get_option('wc_twilio_sms_default_sms_template');
        }
        $send_message = replace_order_message_variables($order_id, $message_ei);

        $order_meta = get_post_meta($order_id);
        $billing_phone = $order_meta['_billing_phone'][0];
        if ($billing_phone != '') {

            $twilio_ei_enable_admin = get_option('wc_twilio_estatic_enable_sms_admin');

            if ($twilio_ei_enable_admin == 'yes') {
                $twilio_ei_admin_mobile_number = get_option('wc_twilio_estatic_admin_mobile_number');
                $twilio_ei_admin_sms_message = get_option('wc_twilio_estatic_admin_sms_message');
                $send_admin_message = replace_order_message_variables($order_id, $twilio_ei_admin_sms_message, $post_order_status);

                /* Send twilio SMS to ADMIN, code HRER */
                $response = $client->messages->create(
                        $twilio_ei_admin_mobile_number, array(
                    'from' => $from_number,
                    'body' => $send_admin_message,)
                );
                if ($response) {
                    $order->add_order_note(__($send_message, 'woocommerce'));
                } else {
                    $order->add_order_note(__('Twilio Order SMS Sending to Customer Fail!!!.', 'woocommerce'));
                }
            }
            /*  Send a message */

            /* Send twilio SMS to Customer, code HRER */
            $response = $client->messages->create(
                    $billing_phone, array(
                'from' => $from_number,
                'body' => $send_message,)
            );
            if ($response) {
                $order->add_order_note(__($send_message, 'woocommerce'));
            } else {
                $order->add_order_note(__('Twilio Order SMS Sending to Customer Fail!!!.', 'woocommerce'));
            }
        }
    }
}

add_action('woocommerce_process_shop_order_meta', 'send_twilio_order_sms_to_customers');

function send_twilio_order_sms_to_customers($order_id) {

    $AccountSid = get_option('wc_twilio_estatic_account_id');
    $AuthToken = get_option('wc_twilio_estatic_auth_token');

    $from_number = get_option('wc_twilio_estatic_from_number');

    $client = new Client($AccountSid, $AuthToken);

    $twilio_estatic_enabled = get_option('wc_twilio_estatic_enabled');

    if ($twilio_estatic_enabled == 'yes') {
        $order = new WC_Order($order_id);
        /* You can do here whatever you want */

        $cur_order_stat = $order->post->post_status;
        $cur_order_statu = explode('wc-', $cur_order_stat);
        $cur_order_status = end($cur_order_statu);

        $post_order_status = '';
        if (isset($_POST['order_status'])) {
            $post_order_stat = $_POST['order_status'];
            $post_order_statu = explode('wc-', $post_order_stat);
            $post_order_status = end($post_order_statu);
            $get_order_status_msg = $post_order_status;
        } else {
            $get_order_status_msg = $cur_order_status;
        }

        /* $message_ei = nl2br(get_option('wc_twilio_sms_' . $cur_order_status . '_sms_template')); */
        $message_ei = get_option('wc_twilio_sms_' . $get_order_status_msg . '_sms_template');

        if ($message_ei == '') {
            $message_ei = get_option('wc_twilio_sms_default_sms_template');
        }
        $send_message = replace_order_message_variables($order_id, $message_ei, $post_order_status);

        $order_meta = get_post_meta($order_id);
        $billing_phone = $order_meta['_billing_phone'][0];

        if ($billing_phone != '' && $post_order_stat != $cur_order_stat) {

            $twilio_ei_enable_admin = get_option('wc_twilio_estatic_enable_sms_admin');

            if ($twilio_ei_enable_admin == 'yes') {
                $twilio_ei_admin_mobile_number = get_option('wc_twilio_estatic_admin_mobile_number');
                $twilio_ei_admin_sms_message = get_option('wc_twilio_estatic_admin_sms_message');
                $send_admin_message = replace_order_message_variables($order_id, $twilio_ei_admin_sms_message, $post_order_status);

                /* Send twilio SMS to ADMIN, code HRER */
                $response = $client->messages->create(
                        $twilio_ei_admin_mobile_number, array(
                    'from' => $from_number,
                    'body' => $send_admin_message,)
                );
                if ($response) {
                    $order->add_order_note(__($send_message, 'woocommerce'));
                } else {
                    $order->add_order_note(__('Twilio Order SMS Sending to Customer Fail!!!.', 'woocommerce'));
                }
            }
            /*  Send a message */

            /* Send twilio SMS to Customer, code HRER */
            //$sms = $client->account->sms_messages->create($from_number, $billing_phone,$send_message, array());
            $response = $client->messages->create(
                    $billing_phone, array(
                'from' => $from_number,
                'body' => $send_message,
                    )
            );
            if ($response) {
                $order->add_order_note(__($send_message, 'woocommerce'));
            } else {
                $order->add_order_note(__('Twilio Order SMS Sending to Customer Fail!!!.', 'woocommerce'));
            }
        }
    }
}
