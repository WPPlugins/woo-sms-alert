<?php

class WC_Settings_SMS_Send_Ei extends WC_Settings_Page {

    /**
     * Constructor
     */
    public function __construct() {

        $this->id = 'twilio_estatic';

        //$this->account_id = $this->get_option('wc_twilio_estatic_account_id');
        //$this->auth_token = $this->get_option('wc_twilio_estatic_auth_token');

        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
        add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
        add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
        add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
    }

    /**
     * Add plugin options tab
     *
     * @return array
     */
    public function add_settings_tab($settings_tabs) {
        $settings_tabs[$this->id] = __('SMS-EI', 'woocommerce-twilio-estatic');
        return $settings_tabs;
    }

    /**
     * Get sections
     *
     * @return array
     */
    public function get_sections() {

        $sections = array(
            'twilio' => __('Twilio SMS', 'woocommerce-twilio-estatic'),
            'plivo' => __('Plivo SMS', 'woocommerce-twilio-estatic'),
        );

        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    /**
     * Get sections
     *
     * @return array
     */
    public function get_settings($section = null) {

        if ($section == 'twilio' || $section == '') {
            $settings = array(
                array(
                    'name' => __('Twilio - Connection Settings', 'woocommerce-twilio-estatic'),
                    'type' => 'title'
                ),
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woocommerce-twilio-estatic'),
                    'type' => 'checkbox',
                    'label' => __('Enable Twilio SMS', 'woocommerce-twilio-estatic'),
                    'default' => 'yes',
                    'desc_tip' => __('Enable Twilio SMS', 'woocommerce-twilio-estatic'),
                    'id' => 'wc_twilio_estatic_enabled',
                ),
                'account_id' => array(
                    'name' => __('Account SID', 'woocommerce-twilio-estatic'),
                    'type' => 'text',
                    'desc' => __('<br/>Log into your Twilio Account to find your Account SID. &nbsp;<a href="https://www.twilio.com/console" target="_blank">https://www.twilio.com/console<a>', 'woocommerce-twilio-estatic'),
                    'desc_tip' => __('Log into your Twilio Account to find your Account SID.', 'woocommerce-twilio-estatic'),
                    'id' => 'wc_twilio_estatic_account_id',
                    'class' => 'woo_sms_alert_class',
                ),
                'auth_token' => array(
                    'name' => __('Auth Token', 'woocommerce-twilio-estatic'),
                    'type' => 'text',
                    'desc' => __('<br/>Log into your Twilio Account to find your Auth Token. &nbsp;<a href="https://www.twilio.com/console" target="_blank">https://www.twilio.com/console<a>', 'woocommerce-twilio-estatic'),
                    'desc_tip' => __('Log into your Twilio Account to find your Auth Token.', 'woocommerce-twilio-estatic'),
                    'id' => 'wc_twilio_estatic_auth_token',
                    'class' => 'woo_sms_alert_class',
                ),
                'from_number' => array(
                    'name' => __('From Number', 'woocommerce-twilio-estatic'),
                    'type' => 'text',
                    'desc' => __('<br/>Enter the number to send Estatic Twilio Messages from. This must be a purchased number from Twilio.'
                            . '&nbsp;<a href="https://www.twilio.com/console" target="_blank">https://www.twilio.com/console<a>'
                            . '<br/><span style="color:red;">Country code is required with "From Number". i.e. +91XXXXXXXXXX, +1XXXXXXXXXX</span>', 'woocommerce-twilio-estatic'),
                    'desc_tip' => __('Enter the number to send Estatic Twilio Messages from. This must be a purchased number from Twilio.', 'woocommerce-twilio-estatic'),
                    'id' => 'wc_twilio_estatic_from_number',
                    'class' => 'woo_sms_alert_class',
                ),
                array('type' => 'sectionend'),
                array(
                    'name' => __('Twilio - Admin Notifications', 'woocommerce-twilio-estatic'),
                    'type' => 'title'
                ),
                'enable_sms_admin' => array(
                    'name' => sprintf(__('Enable new order estatic twilio admin notifications.', 'woocommerce-twilio-estatic')),
                    'type' => 'checkbox',
                    'desc' => __('<br/>Enable Twilio Admin SMS Alert', 'woocommerce-twilio-estatic'),
                    'id' => 'wc_twilio_estatic_enable_sms_admin',
                ),
                'admin_mobile_number' => array(
                    'name' => __('Admin Mobile Number', 'woocommerce-twilio-estatic'),
                    'type' => 'text',
                    'desc' => sprintf(__('<br/>Enter the mobile number (starting with the country code) where the New Order SMS should be sent. <br/>Send to multiple recipients by separating numbers with commas.'
                                    . '<br/><span style="color:red;">Country code is required with "Admin Mobile Number". i.e. +91XXXXXXXXXX, +1XXXXXXXXXX</span>', 'woocommerce-twilio-estatic')),
                    'desc_tip' => __('Enter the mobile number (starting with the country code) where the New Order SMS should be sent. Send to multiple recipients by separating numbers with commas.', 'woocommerce-twilio-estatic'),
                    'id' => 'wc_twilio_estatic_admin_mobile_number',
                    'class' => 'woo_sms_alert_class',
                ),
                'admin_sms_message' => array(
                    'name' => __('Default Admin Estatic Twilio Message', 'woocommerce-twilio-estatic'),
                    'type' => 'textarea',
                    'desc' => sprintf(__('Use these tags to customize your message: %1$s%%shop_name%%%2$s, %1$s%%order_id%%%2$s, %1$s%%order_count%%%2$s, %1$s%%order_amount%%%2$s, %1$s%%order_status%%%2$s, %1$s%%billing_name%%%2$s, %1$s%%shipping_name%%%2$s, and %1$s%%shipping_method%%%2$s. Remember that Estatic Twilio Message are limited to 160 characters.', 'woocommerce-twilio-estatic'), '<code>', '</code>'),
                    'id' => 'wc_twilio_estatic_admin_sms_message',
                    'default' => __('%shop_name% : Your order (%order_id%) is now %order_status%.', 'woocommerce-twilio-estatic'),
                    'class' => 'woo_sms_alert_class',
                ),
                array('type' => 'sectionend'),
                array(
                    'name' => __('Twilio - Customer Notifications', 'woocommerce-twilio-estatic'),
                    'type' => 'title'
                )
            );
            $order_statuses = wc_get_order_statuses();

            $settings[] = array(
                'id' => 'wc_twilio_sms_send_sms_order_statuses',
                'name' => __('Order statuses to send Estatic Twilio Message for', 'woocommerce-twilio-estatic'),
                'desc' => __('Orders with these statuses will have Estatic Twilio Message sent.', 'woocommerce-twilio-estatic'),
                'desc_tip' => __('Orders with these statuses will have Estatic Twilio Message sent.', 'woocommerce-twilio-estatic'),
                'type' => 'multiselect',
                'options' => $order_statuses,
                'default' => array_keys($order_statuses),
                'class' => 'wc-enhanced-select',
                'css' => 'min-width: 250px',
                'custom_attributes' => array(
                    'data-placeholder' => __('Select statuses to automatically send notifications', 'woocommerce-twilio-estatic'),
                ),
            );

            $settings[] = array(
                'id' => 'wc_twilio_sms_default_sms_template',
                'name' => __('Default Customer Estatic Twilio Message', 'woocommerce-twilio-estatic'),
                /* translators: %1$s is <code>, %2$s is </code> */
                'desc' => sprintf(__('Use these tags to customize your message: %1$s%%shop_name%%%2$s, %1$s%%order_id%%%2$s, %1$s%%order_count%%%2$s, %1$s%%order_amount%%%2$s, %1$s%%order_status%%%2$s, %1$s%%billing_name%%%2$s, %1$s%%shipping_name%%%2$s, and %1$s%%shipping_method%%%2$s. Remember that Estatic Twilio Message are limited to 160 characters.', 'woocommerce-twilio-estatic'), '<code>', '</code>'),
                'css' => '',
                'default' => __('%shop_name% : Your order (%order_id%) is now %order_status%.', 'woocommerce-twilio-estatic'),
                'type' => 'textarea',
                'class' => 'woo_sms_alert_class',
            );

            // Display a textarea setting for each available order status
            foreach ($order_statuses as $slug => $label) {

                $slug = 'wc-' === substr($slug, 0, 3) ? substr($slug, 3) : $slug;

                $settings[] = array(
                    'id' => 'wc_twilio_sms_' . $slug . '_sms_template',
                    'name' => sprintf(__('Order %s - Estatic Twilio Message', 'woocommerce-twilio-estatic'), $label),
                    'desc' => sprintf(__('Use these tags to customize your message: %1$s%%shop_name%%%2$s, %1$s%%order_id%%%2$s, %1$s%%order_count%%%2$s, %1$s%%order_amount%%%2$s, %1$s%%order_status%%%2$s, %1$s%%billing_name%%%2$s, %1$s%%shipping_name%%%2$s, and %1$s%%shipping_method%%%2$s. Remember that Estatic Twilio Message are limited to 160 characters.', 'woocommerce-twilio-estatic'), '<code>', '</code>'),
                    'desc_tip' => sprintf(__('Add a custom Estatic Twilio Message for %s orders or leave blank to use the default message above.', 'woocommerce-twilio-estatic'), $slug),
                    'css' => '',
                    'type' => 'textarea',
                    'class' => 'woo_sms_alert_class',
                );
            }
            $settings = array_merge($settings, array(
                array('type' => 'sectionend'))
            );
        } else if ($section == 'plivo') {
            $settings = array(
                array(
                    'name' => __('Plivo - Connection Settings', 'woocommerce-plivo-estatic'),
                    'type' => 'title'
                ),
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woocommerce-plivo-estatic'),
                    'type' => 'checkbox',
                    'label' => __('Enable Plivo SMS', 'woocommerce-plivo-estatic'),
                    'default' => 'no',
                    'desc_tip' => __('Enable Plivo SMS', 'woocommerce-plivo-estatic'),
                    'id' => 'wc_plivo_estatic_enabled',
                ),
                'account_id' => array(
                    'name' => __('AUTH ID', 'woocommerce-plivo-estatic'),
                    'type' => 'text',
                    'desc' => __('<br/>Log into your Plivo Account to find your Account SID. &nbsp;<a href="https://manage.plivo.com/accounts/login/" target="_blank">https://manage.plivo.com/accounts/login/<a>', 'woocommerce-plivo-estatic'),
                    'desc_tip' => __('Log into your Plivo Account to find your Account SID.', 'woocommerce-plivo-estatic'),
                    'id' => 'wc_plivo_estatic_account_id',
                    'class' => 'woo_sms_alert_class',
                    'required' => ';'
                ),
                'auth_token' => array(
                    'name' => __('AUTH TOKEN', 'woocommerce-plivo-estatic'),
                    'type' => 'text',
                    'desc' => __('<br/>Log into your Plivo Account to find your Auth Token. &nbsp;<a href="https://manage.plivo.com/accounts/login/" target="_blank">https://manage.plivo.com/accounts/login/<a>', 'woocommerce-plivo-estatic'),
                    'desc_tip' => __('Log into your Plivo Account to find your Auth Token.', 'woocommerce-plivo-estatic'),
                    'id' => 'wc_plivo_estatic_auth_token',
                    'class' => 'woo_sms_alert_class',
                ),
                'from_number' => array(
                    'name' => __('From Number', 'woocommerce-plivo-estatic'),
                    'type' => 'text',
                    'desc' => __('<br/>Enter the number to send Estatic Plivo Messages from. This must be a purchased number from Plivo.'
                            . '&nbsp;<a href="https://manage.plivo.com/accounts/login/" target="_blank">https://manage.plivo.com/accounts/login/<a>'
                            . '<br/><span style="color:red;">Country code is required with "From Number". i.e. +91XXXXXXXXXX, +1XXXXXXXXXX</span>', 'woocommerce-plivo-estatic'),
                    'desc_tip' => __('Enter the number to send Estatic Plivo Messages from. This must be a purchased number from Plivo.', 'woocommerce-plivo-estatic'),
                    'id' => 'wc_plivo_estatic_from_number',
                    'class' => 'woo_sms_alert_class',
                ),
                array('type' => 'sectionend'),
                array(
                    'name' => __('Plivo - Admin Notifications', 'woocommerce-plivo-estatic'),
                    'type' => 'title'
                ),
                'enable_sms_admin' => array(
                    'name' => __('Enable new order estatic plivo admin notifications.', 'woocommerce-plivo-estatic'),
                    'type' => 'checkbox',
                    'desc' => __('<br/>Enable Plivo Admin SMS Alert', 'woocommerce-plivo-estatic'),
                    'id' => 'wc_plivo_estatic_enable_sms_admin',
                ),
                'admin_mobile_number' => array(
                    'name' => __('Admin Mobile Number', 'woocommerce-plivo-estatic'),
                    'type' => 'text',
                    'desc' => __('<br/>Enter the mobile number (starting with the country code) where the New Order SMS should be sent. <br/>Send to multiple recipients by separating numbers with commas.'
                            . '<br/><span style="color:red;">Country code is required with "Admin Mobile Number". i.e. +91XXXXXXXXXX, +1XXXXXXXXXX</span>', 'woocommerce-plivo-estatic'),
                    'desc_tip' => __('Enter the mobile number (starting with the country code) where the New Order SMS should be sent. Send to multiple recipients by separating numbers with commas.', 'woocommerce-plivo-estatic'),
                    'id' => 'wc_plivo_estatic_admin_mobile_number',
                    'class' => 'woo_sms_alert_class',
                ),
                'admin_sms_message' => array(
                    'name' => __('Default Admin Estatic Plivo Message', 'woocommerce-plivo-estatic'),
                    'type' => 'textarea',
                    'desc' => sprintf(__('Use these tags to customize your message: %1$s%%shop_name%%%2$s, %1$s%%order_id%%%2$s, %1$s%%order_count%%%2$s, %1$s%%order_amount%%%2$s, %1$s%%order_status%%%2$s, %1$s%%billing_name%%%2$s, %1$s%%shipping_name%%%2$s, and %1$s%%shipping_method%%%2$s. Remember that Estatic Plivo Message are limited to 160 characters.', 'woocommerce-plivo-estatic'), '<code>', '</code>'),
                    'id' => 'wc_plivo_estatic_admin_sms_message',
                    'default' => __('%shop_name% : Your order (%order_id%) is now %order_status%.', 'woocommerce-plivo-estatic'),
                    'class' => 'woo_sms_alert_class',
                ),
                array('type' => 'sectionend'),
                array(
                    'name' => __('Plivo - Customer Notifications', 'woocommerce-plivo-estatic'),
                    'type' => 'title'
                )
            );
            $order_statuses = wc_get_order_statuses();

            $settings[] = array(
                'id' => 'wc_plivo_sms_send_sms_order_statuses',
                'name' => __('Order statuses to send Estatic Plivo Message for', 'woocommerce-plivo-estatic'),
                'desc' => __('Orders with these statuses will have Estatic Plivo Message sent.', 'woocommerce-plivo-estatic'),
                'desc_tip' => __('Orders with these statuses will have Estatic Plivo Message sent.', 'woocommerce-plivo-estatic'),
                'type' => 'multiselect',
                'options' => $order_statuses,
                'default' => array_keys($order_statuses),
                'class' => 'wc-enhanced-select',
                'css' => 'min-width: 250px',
                'custom_attributes' => array(
                    'data-placeholder' => __('Select statuses to automatically send notifications', 'woocommerce-plivo-estatic'),
                ),
            );

            $settings[] = array(
                'id' => 'wc_plivo_sms_default_sms_template',
                'name' => __('Default Customer Estatic Plivo Message', 'woocommerce-plivo-estatic'),
                /* translators: %1$s is <code>, %2$s is </code> */
                'desc' => sprintf(__('Use these tags to customize your message: %1$s%%shop_name%%%2$s, %1$s%%order_id%%%2$s, %1$s%%order_count%%%2$s, %1$s%%order_amount%%%2$s, %1$s%%order_status%%%2$s, %1$s%%billing_name%%%2$s, %1$s%%shipping_name%%%2$s, and %1$s%%shipping_method%%%2$s. Remember that Estatic Plivo Message are limited to 160 characters.', 'woocommerce-plivo-estatic'), '<code>', '</code>'),
                'css' => '',
                'default' => __('%shop_name% : Your order (%order_id%) is now %order_status%.', 'woocommerce-plivo-estatic'),
                'type' => 'textarea',
                'class' => 'woo_sms_alert_class',
            );

            // Display a textarea setting for each available order status
            foreach ($order_statuses as $slug => $label) {

                $slug = 'wc-' === substr($slug, 0, 3) ? substr($slug, 3) : $slug;

                $settings[] = array(
                    'id' => 'wc_plivo_sms_' . $slug . '_sms_template',
                    'name' => sprintf(__('Order %s - Estatic Plivo Message', 'woocommerce-plivo-estatic'), $label),
                    'desc' => sprintf(__('Use these tags to customize your message: %1$s%%shop_name%%%2$s, %1$s%%order_id%%%2$s, %1$s%%order_count%%%2$s, %1$s%%order_amount%%%2$s, %1$s%%order_status%%%2$s, %1$s%%billing_name%%%2$s, %1$s%%shipping_name%%%2$s, and %1$s%%shipping_method%%%2$s. Remember that Estatic Plivo Message are limited to 160 characters.', 'woocommerce-plivo-estatic'), '<code>', '</code>'),
                    'desc_tip' => sprintf(__('Add a custom Estatic Plivo Message for %s orders or leave blank to use the default message above.', 'woocommerce-plivo-estatic'), $slug),
                    'css' => '',
                    'type' => 'textarea',
                    'class' => 'woo_sms_alert_class',
                );
            }
            $settings = array_merge($settings, array(
                array('type' => 'sectionend'))
            );
        }


        return apply_filters('wc_settings_tab_sms_alert_ei_settings', $settings, $section);
    }

    /**
     * Output the settings
     */
    public function output() {
        global $current_section;
        $settings = $this->get_settings($current_section);
        WC_Admin_Settings::output_fields($settings);
    }

    /**
     * Save settings
     */
    public function save() {
        global $current_section;
        $settings = $this->get_settings($current_section);

        $wc_twilio_estatic_enabled = get_option('wc_twilio_estatic_enabled');
        $wc_plivo_estatic_enabled = get_option('wc_plivo_estatic_enabled');

        $wc_twilio_estatic_enabled_post = $_POST['wc_twilio_estatic_enabled'];
        $wc_plivo_estatic_enabled_post = $_POST['wc_plivo_estatic_enabled'];

        if ($wc_twilio_estatic_enabled == 'yes' && $wc_plivo_estatic_enabled_post == 1) {
            WC_Admin_Settings::add_error(__('Active only one SMS alert service at a time. - Twilio Activated.', 'woocommerce-' . $_GET['section'] . '-estatic'));
        } else if ($wc_plivo_estatic_enabled == 'yes' && $wc_twilio_estatic_enabled_post == 1) {
            WC_Admin_Settings::add_error(__('Active only one SMS alert service at a time. - Plivo Activated.', 'woocommerce-' . $_GET['section'] . '-estatic'));
        } else {
            WC_Admin_Settings::save_fields($settings);
        }



        /*
         * if ($_GET['section'] == 'twilio' &&
          $_GET['section'] != 'plivo' &&
          $_POST['wc_twilio_estatic_enabled'] == 1 &&
          $_POST['wc_twilio_estatic_account_id'] == '' &&
          $_POST['wc_twilio_estatic_auth_token'] == '' &&
          $_POST['wc_twilio_estatic_from_number'] == '') {
          WC_Admin_Settings::add_error(__('Twilio - Account SID, Auth Token and From Number, all three fields are required!!!', 'woocommerce-' . $_GET['section'] . '-estatic'));
          }
         */
    }

}

return new WC_Settings_SMS_Send_Ei();
