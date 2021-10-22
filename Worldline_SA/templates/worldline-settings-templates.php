<?php

class worldline_Templates
{
    function adminOptions()
    {
        echo
        '<div class="wrap">
                <h2><b>Worldline Payment Gateway</b></h2>';

?><a href="#" target="_blank"><img style="margin-top: 10px; width: 410px;" src="<?php echo plugins_url('images/logo.png', __FILE__); ?>" /></a><?php


                                                                                                                                                echo '<form action="options.php" method="POST">';

                                                                                                                                                settings_fields('worldline_fields');
                                                                                                                                                do_settings_sections('worldline_sections');
                                                                                                                                                submit_button();
                                                                                                                                                settings_errors();

                                                                                                                                                echo
                                                                                                                                                '</form>
                </div>';
                                                                                                                                            }

                                                                                                                                            function displayOptions()
                                                                                                                                            {
                                                                                                                                                add_settings_section('worldline_fields', 'Edit Settings', array($this, 'displayHeader'), 'worldline_sections');

                                                                                                                                                $settings = $this->getSettings();

                                                                                                                                                foreach ($settings as $settingField => $settingName) {
                                                                                                                                                    $displayMethod = $this->getDisplaySettingMethod($settingField);
                                                                                                                                                    add_settings_field(
                                                                                                                                                        $settingField,
                                                                                                                                                        $settingName,
                                                                                                                                                        array(
                                                                                                                                                            $this,
                                                                                                                                                            $displayMethod
                                                                                                                                                        ),
                                                                                                                                                        'worldline_sections',
                                                                                                                                                        'worldline_fields'
                                                                                                                                                    );
                                                                                                                                                    register_setting('worldline_fields', $settingField);
                                                                                                                                                }
                                                                                                                                            }

                                                                                                                                            function displayHeader()
                                                                                                                                            {
                                                                                                                                                $header = '<p>worldline is an online payment gateway</p>';

                                                                                                                                                echo $header;
                                                                                                                                            }

                                                                                                                                            function displayEnabled()
                                                                                                                                            {
                                                                                                                                                $default = get_option('enabled_field');

                                                                                                                                                $selected_true = ($default == 1) ? 'selected' : '';
                                                                                                                                                $selected_false = ($default == 0) ? 'selected' : '';

                                                                                                                                                $display_enable = <<<EOT
        <select style="width:350px;" name="enabled_field" id="display_enable" value="{$default}"/>
            <option value= 1 {$selected_true}>Enable</option>
            <option value= 0 {$selected_false}>Disable</option>
        </select><br>
        EOT;

                                                                                                                                                echo $display_enable;
                                                                                                                                            }

                                                                                                                                            function displayTitle()
                                                                                                                                            {
                                                                                                                                                $default = get_option('title_field', "Enter Title");

                                                                                                                                                $title = <<<EOT
        <input type="text" name="title_field" id="title" size="50" value="{$default}" /><br>
        <label for ="title">Your desired title name will be show during checkout proccess.</label>
        EOT;

                                                                                                                                                echo $title;
                                                                                                                                            }

                                                                                                                                            function displayDescription()
                                                                                                                                            {
                                                                                                                                                $default = get_option('description_field', "Worldline Payment Gateway");

                                                                                                                                                $description = <<<EOT
         <input type="text" name="description_field" id="description" size="150" value="{$default}" />
        EOT;

                                                                                                                                                echo $description;
                                                                                                                                            }

                                                                                                                                            function displayMerchantCode()
                                                                                                                                            {
                                                                                                                                                $default = get_option('merchant_code_field');

                                                                                                                                                $Merchant_code = <<<EOT
        <input type="text" name="merchant_code_field" id="merchant_code" size="50" value="{$default}" autocomplete="off" required /><br>
        EOT;

                                                                                                                                                echo $Merchant_code;
                                                                                                                                            }

                                                                                                                                            function displaySalt()
                                                                                                                                            {
                                                                                                                                                $default = get_option('salt_field');

                                                                                                                                                $salt = <<<EOT
        <input type="text" name="salt_field" id="salt" size="50" value="{$default}" autocomplete="off" required /><br>
        EOT;

                                                                                                                                                echo $salt;
                                                                                                                                            }

                                                                                                                                            function displayPaymentType()
                                                                                                                                            {
                                                                                                                                                $default = get_option('payment_type_field');

                                                                                                                                                $selected_test = ($default == 'test') ? 'selected' : '';
                                                                                                                                                $selected_live = ($default == 'live') ? 'selected' : '';

                                                                                                                                                $payment_type = <<<EOT
        <select style="width:350px;" name="payment_type_field" id="payment_type" value="{$default}"/>
            <option value="test" {$selected_test}>TEST</option>
            <option value="live" {$selected_live}>LIVE</option>
        </select><br>
        <label for ="payment_type">For TEST mode amount will be charge 1</label>
        EOT;

                                                                                                                                                echo $payment_type;
                                                                                                                                            }

                                                                                                                                            function displayCurrency()
                                                                                                                                            {
                                                                                                                                                $default = get_option('currency_field');

                                                                                                                                                $selected_inr = ($default == 'INR') ? 'selected' : '';
                                                                                                                                                $selected_usd = ($default == 'USD') ? 'selected' : '';
                                                                                                                                                $selected_sgd = ($default == 'SGD') ? 'selected' : '';
                                                                                                                                                $selected_gbp = ($default == 'GBP') ? 'selected' : '';
                                                                                                                                                $selected_omr = ($default == 'OMR') ? 'selected' : '';
                                                                                                                                                $selected_bhd = ($default == 'BHD') ? 'selected' : '';
                                                                                                                                                $selected_aed = ($default == 'AED') ? 'selected' : '';
                                                                                                                                                $selected_eur = ($default == 'EUR') ? 'selected' : '';
                                                                                                                                                $selected_cad = ($default == 'CAD') ? 'selected' : '';
                                                                                                                                                $selected_chf = ($default == 'CHF') ? 'selected' : '';
                                                                                                                                                $selected_thb = ($default == 'THB') ? 'selected' : '';
                                                                                                                                                $selected_lkr = ($default == 'LKR') ? 'selected' : '';
                                                                                                                                                $selected_myr = ($default == 'MYR') ? 'selected' : '';
                                                                                                                                                $selected_qar = ($default == 'QAR') ? 'selected' : '';
                                                                                                                                                $selected_hkd = ($default == 'HKD') ? 'selected' : '';
                                                                                                                                                $selected_kwd = ($default == 'KWD') ? 'selected' : '';
                                                                                                                                                $selected_bdt = ($default == 'BDT') ? 'selected' : '';
                                                                                                                                                $selected_nzd = ($default == 'NZD') ? 'selected' : '';
                                                                                                                                                $selected_aud = ($default == 'AUD') ? 'selected' : '';
                                                                                                                                                $selected_npr = ($default == 'NPR') ? 'selected' : '';
                                                                                                                                                $selected_cny = ($default == 'CNY') ? 'selected' : '';
                                                                                                                                                $selected_kes = ($default == 'KES') ? 'selected' : '';
                                                                                                                                                $selected_mur = ($default == 'MUR') ? 'selected' : '';
                                                                                                                                                $selected_php = ($default == 'PHP') ? 'selected' : '';
                                                                                                                                                $selected_sar = ($default == 'SAR') ? 'selected' : '';
                                                                                                                                                $selected_jpy = ($default == 'JPY') ? 'selected' : '';
                                                                                                                                                $selected_zar = ($default == 'ZAR') ? 'selected' : '';

                                                                                                                                                $currency = <<<EOT
        <select style="width:350px;" name="currency_field" id="currency" value="{$default}" />
            <option value="INR" {$selected_inr}>INR (Indian Rupee)</option>
            <option value="USD" {$selected_usd}>USD (American Dollar)</option>
            <option value="SGD" {$selected_sgd}>SGD (Singapore Dollar)</option>
            <option value="GBP" {$selected_gbp}>GBP (Pound Sterling)</option>
            <option value="OMR" {$selected_omr}>OMR (Omani Riyal)</option>
            <option value="BHD" {$selected_bhd}>BHD (Bahrain Dinar)</option>
            <option value="AED" {$selected_aed}>AED (UAE DIRHAM)</option>
            <option value="EUR" {$selected_eur}>EUR (EURO)</option>
            <option value="CAD" {$selected_cad}>CAD (Canadian Dollar)</option>
            <option value="CHF" {$selected_chf}>CHF (Swiss France)</option>
            <option value="THB" {$selected_thb}>THB (Baht)</option>
            <option value="LKR" {$selected_lkr}>LKR (Sri Lanka Rupee)</option>
            <option value="MYR" {$selected_myr}>MYR (Malaysian Ringgit)</option>
            <option value="QAR" {$selected_qar}>QAR (Qatar Riyal)</option>
            <option value="HKD" {$selected_hkd}>HKD (Hong Kong Dollar)</option>
            <option value="KWD" {$selected_kwd}>KWD (Kuwaiti Dinar)</option>
            <option value="BDT" {$selected_bdt}>BDT (Bangladesh taka)</option>
            <option value="NZD" {$selected_nzd}>NZD (New Zealand Dollar)</option>
            <option value="AUD" {$selected_aud}>AUD (Austrailian dollar)</option>
            <option value="NPR" {$selected_npr}>NPR (Nepalese Rupee)</option>
            <option value="CNY" {$selected_cny}>CNY (Chinese Yuan Renminibi )</option>
            <option value="KES" {$selected_kes}>KES (Kenyan Shilling)</option>
            <option value="MUR" {$selected_mur}>MUR (Maritius Rupee)</option>
            <option value="PHP" {$selected_php}>PHP (Philippine Peso)</option>
            <option value="SAR" {$selected_sar}>SAR (Saudi Riyal)</option>
            <option value="JPY" {$selected_jpy}>JPY (Japanese Yen)</option>
            <option value="ZAR" {$selected_zar}>ZAR (South African Rand)</option>
        </select>
         <label for ="title">For Non-INR currency processing, please reach out to your account manager</label>
        <br>
        EOT;

                                                                                                                                                echo $currency;
                                                                                                                                            }

                                                                                                                                            function displayMerchantSchemeCode()
                                                                                                                                            {
                                                                                                                                                $default = get_option('merchant_scheme_code_field', "");

                                                                                                                                                $merchant_scheme_code = <<<EOT
        <input type="text" name="merchant_scheme_code_field" id="merchant_scheme_code" size="50" value="{$default}" autocomplete="off" required/><br>
        EOT;

                                                                                                                                                echo $merchant_scheme_code;
                                                                                                                                            }

                                                                                                                                            function displaySuccessMessage()
                                                                                                                                            {
                                                                                                                                                $default = get_option('success_message_field', "Thank you for shopping with us. Your account has been charged and your transaction is successful.");

                                                                                                                                                $success_message = <<<EOT
        <input type="text" name="success_message_field" id="success_message" size="150"  value="{$default}" /></textarea>
        EOT;

                                                                                                                                                echo $success_message;
                                                                                                                                            }

                                                                                                                                            function displayDeclineMessage()
                                                                                                                                            {
                                                                                                                                                $default = get_option('decline_message_field', "Thank you for shopping with us. However, the transaction has been declined.");

                                                                                                                                                $decline_message = <<<EOT
        <input type="text" name="decline_message_field" id="decline_message" size="150"  value="{$default}" /></textarea>
        EOT;

                                                                                                                                                echo $decline_message;
                                                                                                                                            }

                                                                                                                                            function displayMerchantLogoUrl()
                                                                                                                                            {
                                                                                                                                                $default = get_option('merchant_logo_url_field', "https://www.paynimo.com/CompanyDocs/company-logo-md.png");

                                                                                                                                                $merchant_logo_url = <<<EOT
        <input type="text" name="merchant_logo_url_field" id="merchant_logo_url" size="150" value="{$default}" ><br>
        <label for ="merchant_logo_url">An absolute URL pointing to a logo image of merchant which will show on checkout popup.</label>
        EOT;

                                                                                                                                                echo $merchant_logo_url;
                                                                                                                                            }

                                                                                                                                            function displayPrimaryColorCode()
                                                                                                                                            {
                                                                                                                                                $default = get_option('primary_color_code_field', "#3977b7");

                                                                                                                                                $primary_color_code = <<<EOT
        <input type="text" name="primary_color_code_field" id="primary_color_code" size="50" value="{$default}" ><br>
        <label for ="primary_color_code">Color value can be hex, rgb or actual color name</label>
        EOT;

                                                                                                                                                echo $primary_color_code;
                                                                                                                                            }

                                                                                                                                            function displaySecondaryColorCode()
                                                                                                                                            {
                                                                                                                                                $default = get_option('secondary_color_code_field', "#FFFFFF");

                                                                                                                                                $secondary_color_code = <<<EOT
        <input type="text" name="secondary_color_code_field" id="secondary_color_code" size="50" value="{$default}" ><br>
        <label for ="secondary_color_code">Color value can be hex, rgb or actual color name</label>
        EOT;

                                                                                                                                                echo $secondary_color_code;
                                                                                                                                            }

                                                                                                                                            function displayButtonColorCode1()
                                                                                                                                            {
                                                                                                                                                $default = get_option('button_color_code_1_field', "#1969bb");

                                                                                                                                                $button_color_code_1 = <<<EOT
        <input type="text" name="button_color_code_1_field" id="button_color_code_1" size="50" value="{$default}" ><br>
        <label for ="button_color_code_1">Color value can be hex, rgb or actual color name</label>
        EOT;

                                                                                                                                                echo $button_color_code_1;
                                                                                                                                            }

                                                                                                                                            function displayButtonColorCode2()
                                                                                                                                            {
                                                                                                                                                $default = get_option('button_color_code_2_field', "#FFFFFF");

                                                                                                                                                $button_color_code_2 = <<<EOT
        <input type="text" name="button_color_code_2_field" id="button_color_code_2" size="50" value="{$default}" ><br>
        <label for ="button_color_code_2">Color value can be hex, rgb or actual color name</label>
        EOT;

                                                                                                                                                echo $button_color_code_2;
                                                                                                                                            }

                                                                                                                                            function displayPaymentMode()
                                                                                                                                            {
                                                                                                                                                $default = get_option('payment_mode_field');

                                                                                                                                                $selected_all = ($default == 'all') ? 'selected' : '';
                                                                                                                                                $selected_cards = ($default == 'cards') ? 'selected' : '';
                                                                                                                                                $selected_netBanking = ($default == 'netBanking') ? 'selected' : '';
                                                                                                                                                $selected_UPI = ($default == 'UPI') ? 'selected' : '';
                                                                                                                                                $selected_imps = ($default == 'imps') ? 'selected' : '';
                                                                                                                                                $selected_wallets = ($default == 'wallets') ? 'selected' : '';
                                                                                                                                                $selected_cashCards = ($default == 'cashCards') ? 'selected' : '';
                                                                                                                                                $selected_NEFTRTGS = ($default == 'NEFTRTGS') ? 'selected' : '';
                                                                                                                                                $selected_emiBanks = ($default == 'emiBanks') ? 'selected' : '';

                                                                                                                                                $payment_mode = <<<EOT
        <select style="width:350px;" name="payment_mode_field" id="payment_mode" value="{$default}"/>
            <option value="all" {$selected_all}>all</option>
            <option value="cards" {$selected_cards}>cards</option>
            <option value="netBanking" {$selected_netBanking}>netBanking</option>
            <option value="UPI" {$selected_UPI}>UPI</option>
            <option value="imps" {$selected_imps}>imps</option>
            <option value="wallets" {$selected_wallets}>wallets</option>
            <option value="cashCards" {$selected_cashCards}>cashCards</option>
            <option value="NEFTRTGS" {$selected_NEFTRTGS}>NEFTRTGS</option>
            <option value="emiBanks" {$selected_emiBanks}>emiBanks</option>
        </select><br>
        <label for ="payment_mode">If Bank selection is at worldline ePayments India Pvt. Ltd. end then select all, if bank selection at Merchant end then pass appropriate mode respective to selected option</label>
        EOT;

                                                                                                                                                echo $payment_mode;
                                                                                                                                            }

                                                                                                                                            function displayEnableExpressPay()
                                                                                                                                            {
                                                                                                                                                $default = get_option('enable_express_pay_field');

                                                                                                                                                $selected_true = ($default == 1) ? 'selected' : '';
                                                                                                                                                $selected_false = ($default == 0) ? 'selected' : '';

                                                                                                                                                $enable_express_pay = <<<EOT
        <select style="width:350px;" name="enable_express_pay_field" id="enable_express_pay" value="{$default}"/>
            <option value= 1 {$selected_true}>Enable</option>
            <option value= 0 {$selected_false}>Disable</option>
        </select><br>
        <label for ="enable_express_pay">To enable saved payments set its value to Enable</label>
        EOT;

                                                                                                                                                echo $enable_express_pay;
                                                                                                                                            }

                                                                                                                                            function displaySeperateCardMode()
                                                                                                                                            {
                                                                                                                                                $default = get_option('seperate_card_mode_field');

                                                                                                                                                $selected_true = ($default == 1) ? 'selected' : '';
                                                                                                                                                $selected_false = ($default == 0) ? 'selected' : '';

                                                                                                                                                $seperate_card_mode = <<<EOT
        <select style="width:350px;" name="seperate_card_mode_field" id="seperate_card_mode" value="{$default}"/>
            <option value= 1 {$selected_true}>Enable</option>
            <option value= 0 {$selected_false}>Disable</option>
        </select><br>
        <label for ="seperate_card_mode">If this feature is enabled checkout shows two separate payment mode(Credit Card and Debit Card)</label>
        EOT;

                                                                                                                                                echo $seperate_card_mode;
                                                                                                                                            }

                                                                                                                                            function displayMerchantMessage()
                                                                                                                                            {
                                                                                                                                                $default = get_option('merchant_message_field');

                                                                                                                                                $merchant_message = <<<EOT
        <input type="text" name="merchant_message_field" id="merchant_message" size="50" value="{$default}" ><br>
        <label for ="merchant_message">Customize Merchant message from merchant which will be shown to customer in checkout page</label>
        EOT;

                                                                                                                                                echo $merchant_message;
                                                                                                                                            }

                                                                                                                                            function displayDisclaimerMessage()
                                                                                                                                            {
                                                                                                                                                $default = get_option('disclaimer_message_field');

                                                                                                                                                $disclaimer_message = <<<EOT
        <input type="text" name="disclaimer_message_field" id="disclaimer_message" size="50" value="{$default}" ><br>
        <label for ="disclaimer_message">Customize disclaimer message from merchant which will be shown to customer in checkout page</label>
        EOT;

                                                                                                                                                echo $disclaimer_message;
                                                                                                                                            }


                                                                                                                                            function displayMerchantTransactionDetails()
                                                                                                                                            {
                                                                                                                                                $default = get_option('merchant_transaction_details_field');

                                                                                                                                                $selected_true = ($default == 1) ? 'selected' : '';
                                                                                                                                                $selected_false = ($default == 0) ? 'selected' : '';

                                                                                                                                                $merchant_transaction_details = <<<EOT
        <select style="width:350px;" name="merchant_transaction_details_field" id="merchant_transaction_details" value="{$default}"/>
            <option value= 1 {$selected_true}>Enable</option>
            <option value= 0 {$selected_false}>Disable</option>
        </select><br>
        EOT;

                                                                                                                                                echo $merchant_transaction_details;
                                                                                                                                            }

                                                                                                                                            function displayEnableInstrumentderegisteration()
                                                                                                                                            {
                                                                                                                                                $default = get_option('enable_instrumentderegisteration_field');

                                                                                                                                                $selected_true = ($default == 1) ? 'selected' : '';
                                                                                                                                                $selected_false = ($default == 0) ? 'selected' : '';

                                                                                                                                                $enable_instrumentderegisteration = <<<EOT
        <select style="width:350px;" name="enable_instrumentderegisteration_field" id="enable_instrumentderegisteration" value="{$default}"/>
            <option value= 1 {$selected_true}>Enable</option>
            <option value= 0 {$selected_false}>Disable</option>
        </select><br>
        <label for ="enable_instrumentderegisteration">If this feature is enabled, you will have an option to delete saved cards</label>
        EOT;

                                                                                                                                                echo $enable_instrumentderegisteration;
                                                                                                                                            }

                                                                                                                                            function displayHideSaveInstrument()
                                                                                                                                            {
                                                                                                                                                $default = get_option('hide_save_instrument_field');

                                                                                                                                                $selected_true = ($default == 1) ? 'selected' : '';
                                                                                                                                                $selected_false = ($default == 0) ? 'selected' : '';

                                                                                                                                                $hide_save_instrument = <<<EOT
        <select style="width:350px;" name="hide_save_instrument_field" id="hide_save_instrument" value="{$default}"/>
            <option value= 1 {$selected_true}>Enable</option>
            <option value= 0 {$selected_false}>Disable</option>
        </select><br>
        <label for ="hide_save_instrument">If enabled checkout hides saved payment options even in case of enableExpressPay is enabled.</label>
        EOT;

                                                                                                                                                echo $hide_save_instrument;
                                                                                                                                            }

                                                                                                                                            function displaySaveInstrument()
                                                                                                                                            {
                                                                                                                                                $default = get_option('save_instrument_field');

                                                                                                                                                $selected_true = ($default == 1) ? 'selected' : '';
                                                                                                                                                $selected_false = ($default == 0) ? 'selected' : '';

                                                                                                                                                $save_instrument = <<<EOT
        <select style="width:350px;" name="save_instrument_field" id="save_instrument" value="{$default}"/>
            <option value= 1 {$selected_true}>Enable</option>
            <option value= 0 {$selected_false}>Disable</option>
        </select><br>
        <label for ="save_instrument">Enable this feature to vault instrument</label>
        EOT;

                                                                                                                                                echo $save_instrument;
                                                                                                                                            }

                                                                                                                                            function displayTransactionType()
                                                                                                                                            {
                                                                                                                                                $default = get_option('transaction_type_field');

                                                                                                                                                $selected_sale = ($default == 'sale') ? 'selected' : '';

                                                                                                                                                $transaction_type = <<<EOT
        <select style="width:350px;" name="transaction_type_field" id="transaction_type" value="{$default}"/>
            <option value="sale" {$selected_sale}>SALE</option>
        </select><br>
        EOT;

                                                                                                                                                echo $transaction_type;
                                                                                                                                            }

                                                                                                                                            function displayPaymentModeOrder()
                                                                                                                                            {
                                                                                                                                                $default = get_option('payment_mode_order_field');

                                                                                                                                                $payment_mode_order = <<<EOT
        <input type="text" name="payment_mode_order_field" id="payment_mode_order" size="150" value="{$default}" />
         <label for ="payment_mode_order">Place order in this format: cards,netBanking,imps,wallets,cashCards,UPI,MVISA,debitPin,NEFTRTGS,emiBanks</label>
        EOT;

                                                                                                                                                echo $payment_mode_order;
                                                                                                                                            }

                                                                                                                                            function displayEmbedPaymentGatewayOnPage()
                                                                                                                                            {
                                                                                                                                                $default = get_option('embed_payment_gateway_on_page_field');

                                                                                                                                                $selected_true = ($default == '#worldline_payment_form') ? 'selected' : '';
                                                                                                                                                $selected_false = ($default == "") ? 'selected' : '';

                                                                                                                                                $embed_payment_gateway_on_page = <<<EOT
        <select style="width:350px;" name="embed_payment_gateway_on_page_field" id="embed_payment_gateway_on_page" value="{$default}"/>
            <option value="#worldline_payment_form" {$selected_true}>Enable</option>
            <option value = "" {$selected_false}>Disable</option>
        </select><br>
        EOT;

                                                                                                                                                echo $embed_payment_gateway_on_page;
                                                                                                                                            }

                                                                                                                                            function displayPopupwindowcolorcode()
                                                                                                                                            {
                                                                                                                                                $default = get_option('popup_window_color_code_field');

                                                                                                                                                $button_color_code_2 = <<<EOT
        <input type="text" name="popup_window_color_code_field" id="button_color_code_2" size="50" value="{$default}" ><br>
        <label for ="button_color_code_2">Color value can be hex, rgb or actual color name</label>
        EOT;

                                                                                                                                                echo $button_color_code_2;
                                                                                                                                            }

                                                                                                                                            function displayPopupwindowfontcolorcode()
                                                                                                                                            {
                                                                                                                                                $default = get_option('popup_window_font_color_code_field');

                                                                                                                                                $button_color_code_2 = <<<EOT
        <input type="text" name="popup_window_font_color_code_field" id="button_color_code_2" size="50" value="{$default}" ><br>
        <label for ="button_color_code_2">Color value can be hex, rgb or actual color name</label>
        EOT;

                                                                                                                                                echo $button_color_code_2;
                                                                                                                                            }

                                                                                                                                            function displayShowresponsemessage()
                                                                                                                                            {
                                                                                                                                                $default = get_option('show_response_message_field');

                                                                                                                                                $selected_true = ($default == 1) ? 'selected' : '';
                                                                                                                                                $selected_false = ($default == 0) ? 'selected' : '';

                                                                                                                                                $show_response_message = <<<EOT
        <select style="width:350px;" name="show_response_message_field" id="response_message" value="{$default}"/>
            <option value= 1 {$selected_true}>Enable</option>
            <option value= 0 {$selected_false}>Disable</option>
        </select><br>
        EOT;

                                                                                                                                                echo $show_response_message;
                                                                                                                                            }


                                                                                                                                            protected function getSettings()
                                                                                                                                            {
                                                                                                                                                $settings = array(
                                                                                                                                                    'enabled_field'                                 => 'Enabled/Disabled',
                                                                                                                                                    'title_field'                                   => 'Title',
                                                                                                                                                    'description_field'                             => 'Description',
                                                                                                                                                    'merchant_code_field'                           => 'Merchant Code *',
                                                                                                                                                    'salt_field'                                    => 'SALT *',
                                                                                                                                                    'merchant_scheme_code_field'                    => 'Merchant Scheme Code *',
                                                                                                                                                    'payment_type_field'                            => 'Payment Type',
                                                                                                                                                    'currency_field'                                => 'Currency',
                                                                                                                                                    'success_message_field'                         => 'Success Message',
                                                                                                                                                    'decline_message_field'                         => 'Decline Message',
                                                                                                                                                    'merchant_logo_url_field'                       => 'Merchant Logo URL',
                                                                                                                                                    'primary_color_code_field'                      => 'Primary Color Code',
                                                                                                                                                    'secondary_color_code_field'                    => 'Secondary Color Code',
                                                                                                                                                    'button_color_code_1_field'                     => 'Button Color Code 1',
                                                                                                                                                    'button_color_code_2_field'                     => 'Button Color Code 2',
                                                                                                                                                    'payment_mode_field'                            => 'Payment Mode',
                                                                                                                                                    'payment_mode_order_field'                      => 'Payment Mode Order',
                                                                                                                                                    'enable_express_pay_field'                      => 'Enable Express Pay',
                                                                                                                                                    'seperate_card_mode_field'                      => 'Separate Card Mode',
                                                                                                                                                    'merchant_message_field'                        => 'Merchant Message',
                                                                                                                                                    'disclaimer_message_field'                      => 'Disclaimer Message',
                                                                                                                                                    'merchant_transaction_details_field'            => 'Merchant Details',
                                                                                                                                                    'enable_instrumentderegisteration_field'        => 'Enable InstrumentDeRegistration',
                                                                                                                                                    'hide_save_instrument_field'                    => 'Hide Saved Instrument',
                                                                                                                                                    'save_instrument_field'                         => 'Save Instrument',
                                                                                                                                                    'transaction_type_field'                        => 'Transaction Type',
                                                                                                                                                    'embed_payment_gateway_on_page_field'           => 'Embed Payment Gateway On Page',
                                                                                                                                                    'popup_window_color_code_field'                 => 'Popup window color code',
                                                                                                                                                    'popup_window_font_color_code_field'            => 'Popup window font color code',
                                                                                                                                                    'show_response_message_field'                   => 'Show Customized Message'
                                                                                                                                                );

                                                                                                                                                return $settings;
                                                                                                                                            }
                                                                                                                                            protected function getDisplaySettingMethod($settingsField)
                                                                                                                                            {
                                                                                                                                                $settingsField = ucwords($settingsField);

                                                                                                                                                $fieldWords = explode('_', $settingsField);

                                                                                                                                                array_pop($fieldWords);

                                                                                                                                                return 'display' . implode('', $fieldWords);
                                                                                                                                            }
                                                                                                                                        }

                                                                                                                                        return new worldline_Templates();
