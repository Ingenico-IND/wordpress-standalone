<?php
/*
Plugin Name: Worldline_SA
Plugin URI: 
Description: Worldline ePayments is India's leading digital payment solutions company. Being a company with more than 45 years of global payment experience, we are present in India for over 20 years and are powering over 550,000 businesses with our tailored payment solution.
Version: 1.0 
Author: Worldline
Author URI: https://www.worldline.com/
Copyright: 
License: 
License URI: 
*/

if (!defined('ABSPATH'))
    exit;
include_once(dirname(__FILE__) . '/database_worldline.php');
require_once __DIR__ . '/includes/worldline-settings.php';
add_action('wp_enqueue_scripts', 'enqueue_style_script');

function enqueue_style_script()
{
    wp_enqueue_style("worldline_order_style", plugins_url('/assets/In_style.css', __FILE__));
    wp_enqueue_script("worldline_order_script", plugins_url('/assets/In_script.js', __FILE__));
}

include_once(dirname(__FILE__) . '/order.php');
include_once(dirname(__FILE__) . '/reconcilation.php');
add_action('rest_api_init', 'my_S2S_route');
add_action('plugins_loaded', 'wordpressworldlineInit', 0);

function wordpressworldlineInit()
{
    if (!defined('PLUGIN_BASE_NAME')) {
        define('worldline_BASE_NAME', plugin_basename(__FILE__));
    }
    class WP_worldline
    {
        public function __construct()
        {
            $this->icon                             =  plugins_url('images/logo.png', __FILE__);
            $settings = new worldline_Settings();
            $this->enable                           = get_option('enabled_field');
            $this->title                            = get_option('title_field');
            $this->description                      = get_option('description_field');
            $this->worldline_merchant_code           = get_option('merchant_code_field');
            $this->worldline_SALT                    = get_option('salt_field');
            $this->payment_type                     = get_option('payment_type_field');
            $this->currency_type                    = get_option('currency_field');
            $this->worldline_merchant_scheme_code    = get_option('merchant_scheme_code_field');
            $this->worldline_success_msg             = get_option('success_message_field');
            $this->worldline_decline_msg             = get_option('decline_message_field');
            $this->merchant_logo_url                = get_option('merchant_logo_url_field');
            $this->PRIMARY_COLOR_CODE               = get_option('primary_color_code_field');
            $this->SECONDARY_COLOR_CODE             = get_option('secondary_color_code_field');
            $this->BUTTON_COLOR_CODE_1              = get_option('button_color_code_1_field');
            $this->BUTTON_COLOR_CODE_2              = get_option('button_color_code_2_field');
            $this->worldline_payment_mode            = get_option('payment_mode_field');
            $this->enableExpressPay                 = get_option('enable_express_pay_field');
            $this->separateCardMode                 = get_option('seperate_card_mode_field');
            $this->merchantMsg                      = get_option('merchant_message_field');
            $this->disclaimerMsg                    = get_option('disclaimer_message_field');
            $this->enableMerTxnDetails              = get_option('merchant_transaction_details_field');
            $this->enableInstrumentDeRegistration   = get_option('enable_instrumentderegisteration_field');
            $this->hideSavedInstruments             = get_option('hide_save_instrument_field');
            $this->saveInstrument                   = get_option('save_instrument_field');
            $this->txnType                          = get_option('transaction_type_field');
            $this->payment_mode_order               = get_option('payment_mode_order_field');
            $this->checkoutElement                  = get_option('embed_payment_gateway_on_page_field');
            $this->popupwindowcolor                 = get_option('popup_window_color_code_field');
            $this->popupwindowtextcolor             = get_option('popup_window_font_color_code_field');
            $this->showresponsemsg                  = get_option('show_response_message_field');
            if ($this->showresponsemsg == '1') {
                $this->showresponsemsg = 0;
            } else if ($this->showresponsemsg == '0') {
                $this->showresponsemsg = 1;
            }
            $this->liveurl = 'https://www.paynimo.com/Paynimocheckout/server/lib/checkout.js';
            $this->current_page_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            add_shortcode('worldline', array($this, 'checkout'));
            add_filter('plugin_action_links_' . worldline_BASE_NAME, array($this, 'worldlineSettingLinks'));
            add_action('wp_ajax_worldline_response_handle', array($this, 'CheckworldlineResponse'), 10);
            add_action('wp_ajax_nopriv_worldline_response_handle', array($this, 'CheckworldlineResponse'), 10);
            add_action('wp_ajax_save_to_db', array($this, 'worldline_Initial_Response'), 10);
            add_action('wp_ajax_nopriv_save_to_db', array($this, 'worldline_Initial_Response'), 10);
            add_action('init', array($this, 'register_session'));
        }

        function worldlineSettingLinks($links)
        {
            $SettingLinks = array(
                'settings' => '<a href="' . esc_url(admin_url('admin.php?page=worldline')) . '">Settings</a>'
            );
            $links = array_merge($links, $SettingLinks);
            return $links;
        }

        function register_session()
        {
            if (!session_id())
                session_start();
        }

        function checkout()
        {
            $html = $this->generateworldlineOrderForm();
            return $html;
        }

        function create_response_logs($str)
        {
            $dir_path = plugin_dir_path(__FILE__);
            $directoryname = 'logs';
            $dir_name = $dir_path . 'logs/';
            $file_name = 'worldline_logs' . date("Y-m-d") . '.log';
            if (!file_exists($file_name)) {
                $myfile = fopen($dir_name . $file_name, "a");
                $txt =  "\r\n" . "worldline Response:" . $str;
                $write_file = fwrite($myfile, $txt);
            }
        }

        function create_request_logs($str)
        {
            $dir_path = plugin_dir_path(__FILE__);
            $directoryname = 'logs';
            $dir_name = $dir_path . 'logs/';
            $file_name = 'worldline_logs' . date("Y-m-d") . '.log';
            if (!file_exists($file_name)) {
                $myfile = fopen($dir_name . $file_name, "a");
                $txt =  "\r\n" . "worldline Request:" . $str;
                $write_file = fwrite($myfile, $txt);
            }
        }

        function getErrorStatusMessage($code)
        {
            $messages = [
                "0300" => "Successful Transaction",
                "0392" => "Transaction cancelled by user either in Bank Page or in PG Card /PG Bank selection",
                "0396" => "Transaction response not received from Bank, Status Check on same Day",
                "0397" => "Transaction Response not received from Bank. Status Check on next Day",
                "0398" => "Corporate Transaction Initiated",
                "0399" => "Failed response received from bank",
                "0400" => "Refund Initiated Successfully",
                "0401" => "Refund in Progress (Currently not in used)",
                "0402" => "Instant Refund Initiated Successfully(Currently not in used)",
                "0499" => "Refund initiation failed",
                "9999" => "Transaction not found :Transaction not found in PG"
            ];
            if (in_array($code, array_keys($messages))) {
                return $messages[$code];
            }
            return null;
        }

        function generateworldlineOrderForm()
        {
            $pageID = get_the_ID();
            $metadata = get_post_meta($pageID);
            $product_name = $metadata['name'][0];
            if (empty($product_name)) {
                $product_name = "null";
            }
            $ajaxurl = esc_url(admin_url('admin-ajax.php'));
            if ($this->payment_type == "test") {
                $amount = 1;
            } else {
                $amount = isset($metadata['amount'][0]) && is_numeric($metadata['amount'][0]) ? (float) $metadata['amount'][0] : 0.0;
            }
            if (empty($amount)) {
                $amount = 1;
            }

            if (isset($this->worldline_merchant_code) && isset($this->worldline_SALT) && isset($this->worldline_merchant_scheme_code) && $amount != null) {

                $order_id = date("dmy") . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 1, 8);

                date_default_timezone_set("Asia/Kolkata");
                $created_dt  = date("Y-m-d H:i:s");

                $merchantTxnRefNumber = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 1, 15);
                $user_id = get_current_user_id();
                if (!$user_id) {
                    $user_id = rand(1, 1000000);
                } else {
                    $user_id = $user_id;
                }
                $mrctCode                           = $this->worldline_merchant_code;
                $mobNo                              = '';
                $email                              = '';
                $name                               = '';
                $returnUrl                          = '';
                $SALT                               = $this->worldline_SALT;
                $accNo                              = '';
                $debitStartDate                     = '';
                $debitEndDate                       = '';
                $maxAmount                          = '';
                $amountType                         = '';
                $frequency                          = '';
                $cardNumber                         = '';
                $expMonth                           = '';
                $expYear                            = '';
                $cvvCode                            = '';

                if ((int)$this->enableExpressPay == 0) {
                    $this->enableInstrumentDeRegistration = 0;
                    $this->hideSavedInstruments = 0;
                }

                $payment_order_mode_raw =  $this->payment_mode_order;
                $payment_order_mode =  explode(",", $payment_order_mode_raw);

                $payment_order_mode[0] = (isset($payment_order_mode[0])) ? $payment_order_mode[0] : null;
                $payment_order_mode[1] = (isset($payment_order_mode[1])) ? $payment_order_mode[1] : null;
                $payment_order_mode[2] = (isset($payment_order_mode[2])) ? $payment_order_mode[2] : null;
                $payment_order_mode[3] = (isset($payment_order_mode[3])) ? $payment_order_mode[3] : null;
                $payment_order_mode[4] = (isset($payment_order_mode[4])) ? $payment_order_mode[4] : null;
                $payment_order_mode[5] = (isset($payment_order_mode[5])) ? $payment_order_mode[5] : null;
                $payment_order_mode[6] = (isset($payment_order_mode[6])) ? $payment_order_mode[6] : null;
                $payment_order_mode[7] = (isset($payment_order_mode[7])) ? $payment_order_mode[7] : null;
                $payment_order_mode[8] = (isset($payment_order_mode[8])) ? $payment_order_mode[8] : null;
                $payment_order_mode[9] = (isset($payment_order_mode[9])) ? $payment_order_mode[9] : null;

                if (!$payment_order_mode_raw || $payment_order_mode_raw == "all") {
                    $pmo['paymentModeOrder'] = ["netBanking", "wallets", "cards",  "imps", "cashCards", "UPI", "MVISA", "debitPin", "emiBanks", "NEFTRTGS"];
                } else {
                    $pmo['paymentModeOrder'] =  [
                        $payment_order_mode[0],
                        $payment_order_mode[1],
                        $payment_order_mode[2],
                        $payment_order_mode[3],
                        $payment_order_mode[4],
                        $payment_order_mode[5],
                        $payment_order_mode[6],
                        $payment_order_mode[7],
                        $payment_order_mode[8],
                        $payment_order_mode[9]

                    ];
                }
                $paymentModeOrder = $pmo['paymentModeOrder'];

                if ($this->merchant_logo_url && @getimagesize($this->merchant_logo_url)) {
                    $merchant_logo_url = $this->merchant_logo_url;
                } else {
                    $merchant_logo_url = 'https://www.paynimo.com/CompanyDocs/company-logo-md.png';
                }

                if ($this->PRIMARY_COLOR_CODE) {
                    $PRIMARY_COLOR_CODE = $this->PRIMARY_COLOR_CODE;
                } else {
                    $PRIMARY_COLOR_CODE = '#3977b7';
                }

                if ($this->SECONDARY_COLOR_CODE) {
                    $SECONDARY_COLOR_CODE = $this->SECONDARY_COLOR_CODE;
                } else {
                    $SECONDARY_COLOR_CODE = '#FFFFFF';
                }

                if ($this->BUTTON_COLOR_CODE_1) {
                    $BUTTON_COLOR_CODE_1 = $this->BUTTON_COLOR_CODE_1;
                } else {
                    $BUTTON_COLOR_CODE_1 = '#1969bb';
                }

                if ($this->BUTTON_COLOR_CODE_2) {
                    $BUTTON_COLOR_CODE_2 = $this->BUTTON_COLOR_CODE_2;
                } else {
                    $BUTTON_COLOR_CODE_2 = '#FFFFFF';
                }

                $datastring =
                    $mrctCode . "|" . $merchantTxnRefNumber . "|" . $amount . "|" . $accNo . "|" . $user_id . "|" .
                    $mobNo . "|" .  $email . "|" . $debitStartDate . "|" . $debitEndDate . "|" . $maxAmount . "|" .
                    $amountType . "|" . $frequency . "|" . $cardNumber . "|" . $expMonth . "|" . $expYear . "|" .
                    $cvvCode . "|" . $SALT;

                $hashed = hash('sha512', $datastring);
                $data = array(
                    "hash" => $hashed,
                    "data" => array(
                        $mrctCode,                                  //0
                        $merchantTxnRefNumber,                      //1
                        $amount,                                    //2
                        $debitEndDate,                              //3
                        $debitEndDate,                              //4
                        $maxAmount,                                 //5
                        $amountType,                                //6
                        $frequency,                                 //7
                        $user_id,                                   //8
                        $mobNo,                                     //9
                        $email,                                     //10
                        $accNo,                                     //11
                        $returnUrl,                                 //12
                        $name,                                      //13
                        $this->worldline_merchant_scheme_code,       //14
                        $this->currency_type,                       //15
                        $this->checkoutElement,                     //16
                        (int)$this->enableMerTxnDetails,            //17
                        (int)$this->enableExpressPay,               //18
                        (int)$this->enableInstrumentDeRegistration, //19
                        (int)$this->hideSavedInstruments,           //20  
                        (int)$this->separateCardMode,               //21
                        $this->worldline_payment_mode,               //22
                        $merchant_logo_url,                         //23
                        $this->merchantMsg,                        //24
                        $this->disclaimerMsg,                       //25
                        $this->txnType,                             //26
                        $PRIMARY_COLOR_CODE,                        //27
                        $SECONDARY_COLOR_CODE,                      //28
                        $BUTTON_COLOR_CODE_1,                       //29
                        $BUTTON_COLOR_CODE_2,                       //30  
                        (int)$this->saveInstrument,                 //31
                        $paymentModeOrder,                          //32
                        $this->current_page_url,                    //33
                        $order_id,                                  //34
                        $product_name,                              //35
                        $created_dt,                                //36
                        $datastring                                 //37
                    )
                );

                if ((int)$this->enable == 1) {

                    $encryptData = json_encode($data);

                    $buttonHtml = file_get_contents(__DIR__ . '/frontend/checkout.phtml');

                    $keys = array("#liveurl#", "#ajaxurl#", "#encrypt#", "#bgcolor#", "#txtcolor#", "#showresponsemsg#");
                    $values = array($this->liveurl, $ajaxurl, $encryptData, $this->popupwindowcolor, $this->popupwindowtextcolor, (int)$this->showresponsemsg);
                    $html = str_replace($keys, $values, $buttonHtml);

                    return $html;
                }
            }
            return null;
        }

        function worldline_Initial_Response()
        {

            if ($_REQUEST['order_id'] && $_REQUEST['user_id'] && $_REQUEST['product_name'] && $_REQUEST['amount'] && $_REQUEST['merchant_identifier'] && $_REQUEST['created_dt'] && $_REQUEST['currency'] && $_REQUEST['datastring']) {

                $data = $_REQUEST;

                $logs = $this->create_request_logs($data['datastring']);

                global $wpdb;
                $table_name = $wpdb->prefix . 'worldline_orders_sa';
                $wpdb->insert(
                    $table_name,
                    array(
                        'order_id'              =>  $data["order_id"],
                        'user_id'               =>  $data["user_id"],
                        'product_name'          =>  $data["product_name"],
                        'amount'                =>  (int)$data["amount"],
                        'currency_type'         =>  $data["currency"],
                        'merchant_identifier'   =>  $data["merchant_identifier"],
                        'worldline_identifier'   =>  'null',
                        'response'              =>  'null',
                        'status'                =>  'in-process',
                        'created_dt'            =>  $data["created_dt"],
                        'updated_dt'            =>  'null'
                    )
                );
            }
            wp_die();
        }

        function CheckworldlineResponse()
        {
            $identifier = $this->worldline_merchant_code;
            $currency = $this->currency_type;
            if (isset($_REQUEST['msg'])) {
                $response = $_REQUEST;
                if (is_array($response)) {
                    $str = $response['msg'];
                    $logs = $this->create_response_logs($str);
                }
                $response1 = explode('|', $str);
                $status_before_dv = $response1[0];
                $merchant_id = $response1[3];
                if (!empty($status_before_dv)) {
                    $response_date = explode(' ', $response1[8]);
                    $updated_dt = date("Y-m-d", strtotime($response_date[0])) . " " . $response_date[1];

                    $status2 = $response1[7];
                    $response_cart = explode('orderid:', $status2);
                    $oid_1 = $response_cart[1];
                    $oid_2 = explode('}', $oid_1);
                    $order_id = $oid_2[0];
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'worldline_orders_sa';
                    if (!$order_id) {
                        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE merchant_identifier = '$merchant_id'");
                        $order_id = $result[0]->order_id;
                    }

                    $wpdb->update(
                        $table_name,
                        array(
                            'worldline_identifier'   =>  $response1[5],
                            'response'              =>  $str,
                            'status'                =>  $response1[1],
                            'updated_dt'            =>  $updated_dt,
                        ),
                        array('order_id' => $order_id)
                    );

                    $transaction_id = $response1[5];

                    $hashstring = array_pop($response1);
                    $array_without_hash = $response1;
                    $string_without_hash = implode("|", $array_without_hash);
                    $salt_token = $string_without_hash . '|' . $this->worldline_SALT;
                    $hashed_string_token = hash('sha512', $salt_token);
                    if ($hashed_string_token == $hashstring) {
                        if ($status_before_dv == '300') {
                            $status = $this->S_call($identifier, $currency, $transaction_id);
                            if ($status == '300') {
                                $error_status_msg = $this->getErrorStatusMessage($status);
                                echo "<b><center>" . strtoupper($error_status_msg) . "</center></b><br>";
                                echo $this->worldline_success_msg . "<br>";
                                $wpdb->update(
                                    $table_name,
                                    array(
                                        'status'    =>  'success'
                                    ),
                                    array('order_id' => $order_id)
                                );
                            } elseif ($status == '398') {
                                echo "Corporate Transaction Initiated";
                            } else {
                                echo $this->worldline_decline_msg . "<br>";
                                $wpdb->update(
                                    $table_name,
                                    array(
                                        'status'    =>  'failed'
                                    ),
                                    array('order_id' => $order_id)
                                );
                            }
                        } else {
                            $error_status_msg = $this->getErrorStatusMessage($status_before_dv);
                            echo "<b><center>" . strtoupper($error_status_msg) . "</center></b><br>";
                            echo $this->worldline_decline_msg . "<br>";
                        }
                    } else {
                        echo $this->worldline_decline_msg . "<br>" . 'Transaction Error Message from Payment Gateway: Hash Validation Failed<br>';
                        $wpdb->update(
                            $table_name,
                            array(
                                'status'    =>  'Hash Validation Failed'
                            ),
                            array('order_id' => $order_id)
                        );
                    }
                } else {
                    echo $this->worldline_decline_msg . "<br>";
                    $wpdb->update(
                        $table_name,
                        array(
                            'status'                =>  'cancelled'
                        ),
                        array('merchant_identifier' => $merchant_id)
                    );
                }
                echo "<b>Merchant Transaction Id</b> : " . $response1[3] . "<br>";
                echo "<b>Transaction Id</b> : " . $response1[5] . "<br>";
                echo "<b>Amount</b> : " . $this->currency_type . " " . $response1[6];
                wp_die();
            }
        }

        function S_call($identifier, $currency, $transaction_id)
        {
            $request_array = array(
                "merchant" => array(
                    "identifier" => $identifier
                ),
                "transaction" => array(
                    "deviceIdentifier" => "S",
                    "currency" => $currency,
                    "dateTime" => date("Y-m-d"),
                    "token" => $transaction_id,
                    "requestType" => "S"
                )
            );

            $Scall_data = json_encode($request_array);

            $Scall_url = "https://www.paynimo.com/api/paynimoV2.req";
            $options = array(
                'http' => array(
                    'method'  => 'POST',
                    'content' => json_encode($request_array),
                    'header' =>  "Content-Type: application/json\r\n" .
                        "Accept: application/json\r\n"
                )
            );
            $context  = stream_context_create($options);
            $response_array = json_decode(file_get_contents($Scall_url, false, $context));
            $status_code = $response_array->paymentMethod->paymentTransaction->statusCode;
            if ($status_code) {
                return $status_code;
            } else {
                return 'Failed';
            }
        }
    }
    return new WP_worldline();
}

function my_S2S_route()
{
    register_rest_route(
        'worldline',
        '/s2sverification',
        array(
            'methods' => 'POST',
            'callback' => 'callback_S2S',
            'permission_callback' => '__return_true',
        )
    );
}

function callback_S2S()
{
    $paynimo_class  = new WP_worldline();
    $response = $_GET;
    if (!$response) {
        return 'No msg parameter in params';
        exit;
    }
    if (!$response['msg']) {
        return 'Empty Response Received';
        exit;
    }
    if (is_array($response)) {
        $str = $response['msg'];
    }
    $response1 = explode('|', $str);
    $response_message = $response1[1];
    $response_message2 = $response1[2];
    $merchantTxnRefNumber = $response1[3];
    $transaction_id = $response1[5];
    $status = $response1[0];

    $status2 = $response1[7];
    $response_cart = explode('orderid:', $status2);
    $oid_1 = $response_cart[1];
    $oid_2 = explode('}', $oid_1);
    $order_id = $oid_2[0];
    global $wpdb;
    $table_name = $wpdb->prefix . 'worldline_orders_sa';
    if (!$order_id) {
        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE merchant_identifier = '$merchant_id'");
        $order_id = $result[0]->order_id;
    }

    $query = $wpdb->query("UPDATE $table_name SET merchant_identifier = '$merchantTxnRefNumber' WHERE order_id = '$order_id'");
    $hashstring = array_pop($response1);
    $array_without_hash = $response1;
    $string_without_hash = implode("|", $array_without_hash);
    $salt_token = $string_without_hash . '|' . $paynimo_class->worldline_SALT;
    $hashed_string_token = hash('sha512', $salt_token);
    $dir_path = plugin_dir_path(__FILE__);
    $directoryname = 'logs';
    $dir_name = $dir_path . 'logs/';
    if ($hashstring == $hashed_string_token) {
        if ($status == '300') {
            $file_name = 'worldline_S2S_logs' . date("Y-m-d") . '.log';
            if (!file_exists($file_name)) {
                $myfile = fopen($dir_name . $file_name, "a");
                $txt =  "\r\n" . "Response_S2S:" . $str;
                $write_file = fwrite($myfile, $txt);
            }
            $wpdb->update(
                $table_name,
                array(
                    'status'                =>  'success'
                ),
                array('order_id' => $order_id)
            );
            $return_string = $merchantTxnRefNumber . "|" . $transaction_id . "|1";
            echo $return_string;
        } else {
            $file_name = 'worldline_S2S_logs' . date("Y-m-d") . '.log';
            if (!file_exists($file_name)) {
                $myfile = fopen($dir_name . $file_name, "a");
                $txt = "\r\n" . "Response_S2S:" . $str;
                $write_file = fwrite($myfile, $txt);
            }
            $wpdb->update(
                $table_name,
                array(
                    'status'                =>  'cancelled'
                ),
                array('order_id' => $order_id)
            );
            $return_string = $merchantTxnRefNumber . "|" . $transaction_id . "|0";
            echo $return_string;
        }
    } else {
        return 0;
    }
}
register_activation_hook(__FILE__, 'database_install1');
