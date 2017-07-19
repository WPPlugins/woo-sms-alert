<?php

/* Send New ORDER Message Code Start */
$plugin_dir_path = str_replace('\\', '/', plugin_dir_path(__FILE__));
require $plugin_dir_path . 'plivo_vendor/autoload.php';

use Plivo\RestAPI;

/* woocommerce_checkout_order_processed */
/* woocommerce_thankyou */

add_action('woocommerce_thankyou', 'send_plivo_sms_to_customers_ei', 1, 1);

function send_plivo_sms_to_customers_ei($order_id) {

    $auth_id = get_option('wc_plivo_estatic_account_id');
    $auth_token = get_option('wc_plivo_estatic_auth_token');
    $from_number = get_option('wc_plive_estatic_from_number');
    $plivo_auth = new RestAPI($auth_id, $auth_token);

    $plivo_estatic_enabled = get_option('wc_plivo_estatic_enabled');
    if ($plivo_estatic_enabled == 'yes') {
        $order = new WC_Order($order_id);
        /* You can do here whatever you want */

        $cur_order_stat = $order->post->post_status;
        $cur_order_statu = explode('wc-', $cur_order_stat);
        $cur_order_status = end($cur_order_statu);

        /* $message_ei = nl2br(get_option('wc_plive_sms_' . $cur_order_status . '_sms_template')); */
        $message_ei = get_option('wc_plive_sms_' . $cur_order_status . '_sms_template');

        if ($message_ei == '') {
            $message_ei = get_option('wc_plive_sms_default_sms_template');
        }
        $send_message = replace_order_message_variables($order_id, $message_ei);

        $order_meta = get_post_meta($order_id);
        $billing_phone = $order_meta['_billing_phone'][0];
        if ($billing_phone != '') {

            $plivo_ei_enable_admin = get_option('wc_plivo_estatic_enable_sms_admin');

            if ($plivo_ei_enable_admin == 'yes') {
                $plivo_ei_admin_mobile_number = get_option('wc_plivo_estatic_admin_mobile_number');
                $plivo_ei_admin_sms_message = get_option('wc_plivo_estatic_admin_sms_message');
                $send_admin_message = replace_order_message_variables($order_id, $plivo_ei_admin_sms_message, $post_order_status);

                $params_admin = array(
                    'src' => $from_number, /* Sender's phone number with country code */
                    'dst' => $plivo_ei_admin_mobile_number, /* $_POST['billing_phone'], //'+919979573472', // Receiver's phone number with country code */
                    'text' => $send_admin_message /* Your SMS text message */
                );
                $admin_response = $plivo_auth->send_message($params_admin);
                /* $response = array(); */
                if ($admin_response['status'] == 202) {
                    /* /* Update Order Note */
                    $order->add_order_note(__($send_admin_message, 'woocommerce'));
                } else {
                    $order->add_order_note(__('Plivo Order Admin SMS Sending Fail!!!.', 'woocommerce'));
                }
            }

            /* Send a message */
            $params = array(
                'src' => $from_number, /* Sender's phone number with country code */
                'dst' => $billing_phone, /* $_POST['billing_phone'], //'+919979573472', // Receiver's phone number with country code */
                'text' => $send_message /* Your SMS text message */
            );
            /* Send message */
            $response = $plivo_auth->send_message($params);
            if ($response['status'] == 202) {
                /* Update Order Note */
                $order->add_order_note(__($send_message, 'woocommerce'));
            } else {
                $order->add_order_note(__('Plivo Order SMS Sending Fail!!!.', 'woocommerce'));
            }
        }
    }
}

add_action('woocommerce_process_shop_order_meta', 'send_plivo_order_sms_to_customers');

function send_plivo_order_sms_to_customers($order_id) {

    $auth_id = get_option('wc_plivo_estatic_account_id');
    $auth_token = get_option('wc_plivo_estatic_auth_token');
    $from_number = get_option('wc_plive_estatic_from_number');
    $plivo_auth = new RestAPI($auth_id, $auth_token);

    $plivo_estatic_enabled = get_option('wc_plivo_estatic_enabled');
    if ($plivo_estatic_enabled == 'yes') {
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

        /* $message_ei = nl2br(get_option('wc_plive_sms_' . $cur_order_status . '_sms_template')); */
        $message_ei = get_option('wc_plive_sms_' . $get_order_status_msg . '_sms_template');

        if ($message_ei == '') {
            $message_ei = get_option('wc_plive_sms_default_sms_template');
        }
        $send_message = replace_order_message_variables($order_id, $message_ei, $post_order_status);

        $order_meta = get_post_meta($order_id);


        $billing_phone = $order_meta['_billing_phone'][0];

        if ($billing_phone != '' && $post_order_stat != $cur_order_stat) {

            $plivo_ei_enable_admin = get_option('wc_plivo_estatic_enable_sms_admin');

            if ($plivo_ei_enable_admin == 'yes') {
                $plivo_ei_admin_mobile_number = get_option('wc_plivo_estatic_admin_mobile_number');
                $plivo_ei_admin_sms_message = get_option('wc_plivo_estatic_admin_sms_message');
                $send_admin_message = replace_order_message_variables($order_id, $plivo_ei_admin_sms_message, $post_order_status);

                $params_admin = array(
                    'src' => $from_number, /* Sender's phone number with country code */
                    'dst' => $plivo_ei_admin_mobile_number, /* $_POST['billing_phone'], //'+919979573472', // Receiver's phone number with country code */
                    'text' => $send_admin_message /* Your SMS text message */
                );
                $admin_response = $plivo_auth->send_message($params_admin);
                /* $response = array(); */
                if ($admin_response['status'] == 202) {
                    /* /* Update Order Note */
                    $order->add_order_note(__($send_admin_message, 'woocommerce'));
                } else {
                    $order->add_order_note(__('Plivo Order Admin SMS Sending Fail!!!.', 'woocommerce'));
                }
            }
            /* Send a message */
            $params = array(
                'src' => $from_number, /* Sender's phone number with country code */
                'dst' => $billing_phone, /* $_POST['billing_phone'], //'+919979573472', // Receiver's phone number with country code */
                'text' => $send_message /* Your SMS text message */
            );
            /* Send message */
            $response = $plivo_auth->send_message($params);
            /* $response = array(); */
            if ($response['status'] == 202) {
                /* Update Order Note */
                $order->add_order_note(__($send_message, 'woocommerce'));
            } else {
                $order->add_order_note(__('Plivo Order SMS Sending Fail!!!.', 'woocommerce'));
            }
        }
    }
}
