<?php
function database_install1(){
    global $wpdb;
    $table_name = $wpdb->prefix . "ingenico_orders_sa";
    $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
    if( ! $wpdb->get_var( $query ) == $table_name )
    {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name(
        id int NOT NULL AUTO_INCREMENT,  
        order_id text NOT NULL,
        user_id int NOT NULL,
        product_name text NOT NULL,
        amount int NOT NULL,
        currency_type text NOT NULL,
        merchant_identifier text NOT NULL,   
        ingenico_identifier text NOT NULL,
        response text NOT NULL,
        status text NOT NULL,
        created_dt datetime NOT NULL,
        updated_dt datetime NOT NULL,
        PRIMARY KEY (id)
        )$charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql); 
    }        
}