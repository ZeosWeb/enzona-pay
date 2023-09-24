<?php
/**
 *Plugin Name: EnzonaPay
 *Plugin URI: https://www.zeosweb.cu/enzonaPay
 *Description:  Módulo para procesar pagos a través de la plataforma Enzona usando WooCommerce.
 *Version: 1.0.18
 *Author: Zeosweb
 *Author URI: https://www.zeosweb.cu

 *Copyright: © 2021-2023 ZeosWeb
 *License: GNU General Public License v3.0
 *License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

require 'update/plugin-update-checker.php';
use ZeosWeb\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/ZeosWeb/enzona-pay',
	__FILE__,
	'enzona-pay'
);

$myUpdateChecker->getVcsApi()->enableReleaseAssets();

require_once plugin_dir_path(__FILE__) . 'enzonaApi.php';
require_once plugin_dir_path(__FILE__) . 'licencia.php';

if (! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

add_action('plugins_loaded','enzona_payment_init', 11);

function enzona_payment_init(){

    if(class_exists('WC_Payment_Gateway')){

        class WC_Enzona_Pay_Gateway extends WC_Payment_Gateway{

            const ENZONA_LOGO = "data:image/jpg;base64,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";

            public function __construct(){

                $logo = self::ENZONA_LOGO;
                global $woocommerce;
                $plugin_dir = plugin_dir_url(__FILE__);

                $this->id = 'enzona';
                $this->icon = apply_filters('woocommerce_enzona_icon', $logo);
                $this->has_fields = false;
                $this->supports = array(
                    'products',
                    'refunds'
                );
                $this->init_form_fields();
                $this->init_settings();
                $this->title = $this->get_option('title');
                $this->enzona_url = $this->get_option('enzona_url');
                $this->merchant_id = $this->get_option('merchant_id');
                $this->method_title = $this->get_option('title');
                $this->method_description = 'Permita a sus clientes pagar con la plataforma Enzona';
                $this->description = $this->get_option('description');
                $this->return_url = home_url('wc-api/enz_accept');
                $this->error_url = home_url($this->get_option('error_url'));
                $this->cancel_url = home_url('wc-api/enz_cancel');
                $this->stage_modo = $this->get_option('stage_modo');
                $this->customer_key = $this->get_option('customer_key');
                $this->customer_secret = $this->get_option('customer_secret');

                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'enz_save_option' ) );
                add_action('woocommerce_api_enz_accept', array($this, 'accept_ipn_response'));
                add_action('woocommerce_api_enz_cancel', array($this, 'cancel_ipn_response'));
                
                $values = array(
                    'enabled' => 'yes',
                    'license'=>$this->get_option('license'),
                    'title'=>$this->get_option('title'),
                    'description'=>$this->get_option('description'),
                    'merchant_id'=>$this->get_option('merchant_id'),
                    'return_url'=>$this->get_option('return_url'),
                    'error_url'=>$this->get_option('error_url'),
                    'stage_modo'=>$this->get_option('stage_modo'),
                    'customer_key'=>$this->get_option('customer_key'),
                    'customer_secret'=>$this->get_option('customer_secret')
                );

                if($this->is_valid_for_use()!=true){
                    $this->enabled=false;
                    $values['enabled'] = 'no';
                }
                update_option('woocommerce_enzona_settings', $values);
            }
            function is_valid_for_use(){
                return (bool)Val::call_api($value=$this->get_option("license"))['return'];
                //return true;
            }
            public function admin_options(){
                ?>
                <h3><?php _e('Enzona', 'woocommerce'); ?></h3>
                <p><?php _e('Configuración de Pago Electrónico con Enzona.', 'woocommerce'); ?></p>

                <?php if ( $this->is_valid_for_use() ) : ?>
                    <table class="form-table">
                        <?php $this->generate_settings_html(); ?>
                    </table>

                <?php else : ?>
                    <div class="inline error">
                        <p>
                            <strong><?php _e('Licencia no Valida o aun no ha sido habilitada', 'woocommerce'); ?></strong>:
                            <?php _e('No tiene una licencia valida por favor compre una o contacte a los administadores.', 'woocommerce' ); ?>
                        </p>
                        <table class="form-table">
                            <?php $this->generate_settings_html(); ?>
                        </table>
                    </div>
                <?php
                endif;
            }
            public function init_form_fields(){
                $this->form_fields = array(
                    'enabled' => array(
                        'title'       => __( 'Habilitar/Deshabilitar', 'enzonaPay' ),
                        'label'       => __( 'Habilitar ENZONA Gateway', 'enzonaPay' ),
                        'type'        => 'checkbox',
                        'description' => __( 'Habilita el Gateway de ENZONA para recibir pagos.', 'enzonaPay' ),
                        'default'     => 'no',
                        'desc_tip'    => true
                    ),
                    'license' => array(
                        'title'       => __( 'Licencia', 'enzonaPay' ),
                        'label'       => __( 'Activar la Licencia', 'enzonaPay' ),
                        'type'        => 'textarea',
                        'description' => __( 'Compre una licencia y péguela en el campo', 'enzonaPay' )
                    ),
                    'title' => array(
                        'title'       => __( 'Título', 'enzonaPay'),
                        'type'        => 'text',
                        'description' => __( 'Titulo que los clientes verán en el momento de seleccionar el método de pago.', 'enzonaPay' ),
                        'default'     => __( 'Enzona', 'enzonaPay' ),
                        'desc_tip'    => true
                    ),
                    'description' => array(
                        'title'       => __( 'Descripción', 'enzonaPay' ),
                        'type'        => 'textarea',
                        'description' => __( 'Este texto será mostrado a los clientes en el momento de seleccionar el método de pago.', 'enzonaPay' ),
                        'default'     => __( 'Paga todo con enzona Fácil y Rápido!!!', 'enzonaPay' )
                    ),
                    'merchant_id' => array(
                        'title'       => __( 'Merchant uuid', 'enzonaPay' ),
                        'type'        => 'text',
                        'description' => __( 'se encuentra accediendo a enzona y luego ir al comercio -> Comercios-> detalles del Comercio ', 'enzonaPay' ),
                        'desc_tip'    => true,
                        'placeholder' => __('63818b1ef7264f68a613c4b1ce9ce44a')
                    ),
                    'return_url' => array(
                        'title'       => __( 'url return', 'enzonaPay' ),
                        'label'       => __( 'Callback', 'enzonaPay' ),
                        'type'        => 'text',
                        'description' => sprintf(__( 'Cree una página para pagos correcto que usará tu sitio ej: %1s <br> o déjelo en blanco para ir después del pago a %2s ', 'enzonaPay' ),home_url('/success'),home_url()),
                        'placeholder' => sprintf(__('success','enzonaPay'))
                    ),
                    'error_url' => array(
                        'title'       => __( 'url error', 'enzonaPay' ),
                        'label'       => __( 'URL de error', 'enzonaPay' ),
                        'type'        => 'text',
                        'description' => sprintf(__( 'Url de Error que usará tu página<br> %s/error ', 'enzonaPay' ),home_url()),
                        'placeholder' => sprintf(__('error','enzonaPay'))
                    ),
                    'stage_modo' => array(
                        'title'       => __( 'Modo', 'enzonaPay' ),
                        'label'       => __( 'Sandbox/Producción', 'enzonaPay' ),
                        'type'        => 'select',
                        'options' => array(
                            '1' => 'Sandbox',
                            '2' => 'Producción'
                        ),
                        'description' => __( 'Escoja el modo sandbox para hacer Prueba o el modo Producción para cobrar de forma real', 'enzonaPay' ),
                        'desc_tip'    => true
                    ),
                    'customer_key' => array(
                        'title'       => __( 'Customer Key', 'enzonaPay' ),
                        'type'        => 'text',
                        'description' => __( 'se encuentra en <a href= "https://api.enzona.net/store">api enzona</a>', 'enzonaPay' ),
                        'placeholder' => 'asdli54asdf12as54dfa1sd'
                    ),
                    'customer_secret' => array(
                        'title'       => __( 'Customer Secret', 'enzonaPay' ),
                        'type'        => 'password',
                        'description' => __( 'se encuentra en <a href= "https://api.enzona.net/store">api enzona</a>', 'enzonaPay' ),
                        'placeholder' => '***********************'
                    )
                );

            }
            function payment_fields(){
                if ($this->description)
                {
                    echo wpautop(wptexturize($this->description));
                }
            }

            public function sanitizaString($texto){
                $texto=preg_replace('/[^a-zA-Z0-9 ]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $texto));
                return $texto;
            }
            public function process_payment($order_id){
                //obtenemos los detalles de la orden
                $blogName= $this->sanitizaString(get_bloginfo('name'));
                $order =new WC_Order( $order_id );
                $descripcion = sprintf(__('%1$s Factura %2$s', 'woocomerce'), $blogName, $order->get_id());
                $currency=$order->get_currency();
                $total= number_format($order->get_total(),2);
                $shipping = $order->get_shipping_total() !=0? number_format($order->get_shipping_total(),2):number_format(0.00,2);
                $tax = $order->get_total_tax()!=0? number_format($order->get_total_tax(),2,'.',''):number_format(0.00,2);
                $discount =$order->get_discount_total()!=0? number_format($order->get_discount_total(),2,'.',''):number_format(0.00,2);
                $tip = number_format(0.00,2);
                $invoice_number=$order_id;
                $merchant_op_id=123456789012;
                $merchant_id=$this->get_option('merchant_id');
                $terminal_id = 12121;
                $items="";

                //obtenemos los item y los organizamos, puede ser que encontremos una forma mejor de hacer ese array..
                foreach ( $order->get_items() as $item_id => $item ) {
                    if($items==""){
                        $com="";
                    }else{
                        $com=",";
                    }
                    //calculamos los precios teniendo en cuenta el subtotal y la cantidad..
                    $price = $item->get_subtotal() / $item->get_quantity();
                    //Escribimos manualmente el json, podemos buscar una mejor forma para hacerlo.
                    $items=$items.$com.'{
                        "quantity": '.$item->get_quantity().',
                        "price": '.number_format($price,2).',
                        "name": "'.$item->get_name().'",
                        "description": "'.$item->get_name().'",
                        "tax": '.number_format($item->get_total_tax(),2).'
                    }';
                }
                //Escribo manual el json que va a la petición enzona
                $values='{
                    "description": "'.$descripcion.'",
                    "currency": "'.$currency.'",
                    "amount": {
                        "total": '.$total.',
                        "details": {
                            "shipping": '.$shipping.',
                            "tax": '.$tax.',
                            "discount": '.$discount.',
                            "tip": '.$tip.'
                        }
                    },
                    "items": [
                    '.$items.'
                    ],
                    "merchant_uuid": "'.$merchant_id.'",
                    "merchant_op_id": '.$merchant_op_id.',
                    "invoice_number": '.$invoice_number.',
                    "return_url": "'.$this->return_url.'",
                    "cancel_url": "'.$this->cancel_url.'",
                    "terminal_id": '.$terminal_id.'
                }';
                // LLamo al constructor de la Clase de  EnzonaAPi de enzona para crear el pago le paso el Key, el secret la url de error y el modo.
               $api= new enzonaApi($this->customer_key,$this->customer_secret,$this->error_url,$this->stage_modo);
                //solicitams un token de acceso.
                $req=$api->requestAccessToken();
                $tk=json_decode($req);
                //Validamos que no tengamos errores a la hora de pedir el token
                if($tk->access_token == 'Error'){
                    wc_add_notice($tk->message,'error');
                    wp_die();
                }else{
                    //Generamos el Pago pasandole el token y los valores.
                    $p= $api->generatePayment($tk->access_token, $values);
                    $pago= json_decode($p);
                    //Si todo esta correcto
                    if ($pago->status == "ok"){
                        //ponemos el id de transacción de enzona en la orden de Wordpress.
                        $order->set_transaction_id($pago->uuid);
                        $order->save();
                        //Redireccionamos a la URL de Enzona para que se emita el pago.
                        //y quedamos pendiente al call back de enzona con la función (accept_ipn_response) o (cancel_ipn_response).
                        return[
                            'result' => 'success',
                            'redirect' => $pago->url
                        ];

                    } else {
                        //Si surge algun error escribimos en la nota interna del pedido.
                        wc_add_notice($pago->status.' : No se realizó el Pago... '.$pago->message, 'error');
                        $customer_order->add_order_note( 'Error: '. $r['response_reason_text'] );
                    }
                }
            }

            //Enzona Debuelve el Call Back
            function accept_ipn_response(){
                global $woocommerce;
                //Cuando llegamos a este punto, es porque el usuario completó el pago y enzona nos devuelve el call back con 
                //el id de transacción interna de enzona. Volvemos a llamar el constructor de la clase de EnzonaApi, solicitamos
                //tambien el token de acceso Tal vez encontramos una forma de llamar el token una sola vez de algún modo.
                $api= new enzonaApi($this->customer_key,$this->customer_secret,$this->error_url,$this->stage_modo);
                $req=$api->requestAccessToken();
                $tk=json_decode($req);
                if($tk->access_token == 'Error'){
                    //Aqui deberiamos mandar un correo al admin o algo así porque el usuario pagó pero si a la hora de pedir el 
                    //token no somos capaces de obtener uno por cualquier motivo(enzona se colgo por ejemplo) entonces el usuario no tiene 
                    //idea de que pasó con su compra. 
                    wc_add_notice($tk->message,'error');
                    wp_die();
                }else{
                    //En el caso de que todo siga su curso. obtenemos la transacción que nos da en la url enzona y 
                    //hacemos una llamada a la api de enzona /CompletePayment de la plataforma con la transacción y el token.
                    $transaction_uuid=$_GET['transaction_uuid'];
                    $result=$api->completePayment($tk->access_token, $transaction_uuid);
                    $response= json_decode($result);
                    //Si resulta correcto llamamos a la orden(factura) que hizo el pago
                    if ($response->status == "ok"){
                        $order_id= (int)$response->order_id;
                        $order = new WC_Order( $order_id );
                        //y le cambiamos el estado de la orden a completado y escribimos en la nota interna los detalles
                        //como transacción id, usuario que pagó y el metodo de pago que usó.
                        $order->update_status('completed', sprintf(__('Pago Completado -> Método de Pago: %1s, transacción: %2s, Usuario que ha pagado: %3s', 'enzona'),$order->get_payment_method(), $transaction_uuid, $response->user));;
                        //completamos el pago en wordpress.
                        $order->payment_complete();
                        //reducimos el Stock.
                        $order->reduce_order_stock();
                        //Vaciamos el carrito del Cliente.
                        $woocommerce->cart->empty_cart();
                        //obtenemos la url de retorno que da el usuario para cuando realiza la venta, en caso de no tener usa el home del sitio
                        //Damos notificación al usuario de que se realizó correcto su compra.
                        $url_ret = $this->get_option('return_url') !="" ? home_url($this->get_option('return_url')):home_url();
                        wc_add_notice('Pago Completado Correctamente', 'success');
                        //y redirigimos a la url de retorno. con la operación finalizada.
                        wp_redirect($url_ret);
                    }
                    else{
                        //en caso que algo salga mal con la completación del pago da notificación al usuario y redirige a la pagina de error.
                        wc_add_notice($response->status.' : No se pudo completar la orden, en el caso de que su pago se haya realizado, contanta con el administrador de este sitio... '.$response->message, 'error');
                        wp_redirect(home_url($this->get_option('error_url')));
                    }
                }

            }

            //Devolución de ventas, nunca la devolución va a ser mayor que el monto total de la venta.
            public function process_refund( $order_id, $amount = null, $reason = '' ) {
                //Escribimos los valores para la devolución
                $values='{
                    "amount": {
                      "total": '.$amount.'
                    },
                    "commerce_refund_id": "",
                    "username": "",
                    "description": "'.$reason.'"
                  }';
                  //llamamos y obtenemos el identificador de la orden o(factura) y obtenemos el id de transacción que en algun momento
                  //escribimos cuando se realizó la venta.
                $order = new WC_Order( $order_id );
                $transaction_uuid=$order->get_transaction_id();
                //llamamos al constructor de nuestra clase enzonaApi y el respectivo token de acceso.
                $api= new enzonaApi($this->customer_key,$this->customer_secret,$this->error_url,$this->stage_modo);
                $req=$api->requestAccessToken();
                $tk=json_decode($req);

                if($tk->access_token == 'Error'){
                    //Si falla la petición del token podemos decirle al usuario que lo vuelva a intentar en otro momento.
                    //lo que hacemos es pasar el error que nos devuelve enzona.
                    wc_add_notice($tk->message,'error');
                    wp_die();
                }else{
                    //Si procede la devolución, Se llama a la api y se procede a devolver(parcial o total)
                    $result=$api->refundPayment($tk->access_token, $transaction_uuid, $values);
                    $response= json_decode($result);
                    //Si se accepta la devolución  en la nota de la orden queda como devuelto  y el estado de la orden tambien
                    if ($response->status == "Aceptada"){
                        $order->update_status('refunded', sprintf(__('Pago Devuelto <br>Método de Pago: %1s <br>Tipo de devolución: %2s <br>Monto devuelto: %3s <br>Transacción: %4s <br>Devuelto a: %5s <br> ', 'enzona'),$order->get_payment_method(), $response->devolucion,$response->total_devuelto,$response->refund_uuid,$response->user_refund));
                        return true;
                    }
                    else{
                        //En caso de fallos devuelve que no se pudo hacer la devolución.
                        wc_add_notice($response->status.' : No se pudo hacer la devolución... '.$response->message, 'error');
                        return false;
                    }
                }

            }
            //El usuario cancela el pedido desde la plataforma de enzona.
            function cancel_ipn_response(){
                //llamamos al constructor de la clase y el acceso necesario.
                $api= new enzonaApi($this->customer_key,$this->customer_secret,$this->error_url,$this->stage_modo);
                $req=$api->requestAccessToken();
                $tk=json_decode($req);
                if($tk->access_token == 'Error'){
                    //Si falla la petición del token podemos decirle al usuario que lo vuelva a intentar en otro momento.
                    //lo que hacemos es pasar el error que nos devuelve enzona.
                    wc_add_notice($tk->message,'error');
                    wp_die();
                }else{
                    //obtenemos la transacción que nos devuelve enzona en su url de retorno. llamamos a la clase enzonaApi para cancelar el pago.
                    $transaction_uuid=$_GET['transaction_uuid'];
                    $result=$api->cancelPayment($tk->access_token, $transaction_uuid);
                    $response= json_decode($result);

                    //Si se cancela Escribimos en la orden que se cancelo y le damos el estatus de cancelado

                    //Debo Revisarr Esto aqui
                    if ($response->status == "cancelado"){
                        /*$order_id= (int)get_option('ez_order_id');
                        $order = new WC_Order( $order_id );*/

                        //$order->update_status('cancelled', __('Enzona: Pago Cancelado.', 'enzona'));
                        wc_add_notice(' La orden fue cancelada', 'success');
                        wp_redirect(wc_get_cart_url());
                    }
                    else{
                        wc_add_notice($response->status.' : No se pudo cancelar el Pago... '.$response->message, 'error');
                        wp_redirect(get_home_url($this->get_option('error_url'),'http'));
                    }
                }

            }

            //Esta función se dispara cuando se aplica el boton Guardar dentro de la configuración del plugin en woocommerce
            //verifica si no esta activo el hook, y se cumple entonces crea la tarea cada minuto y ejecuta el acction llamando la
            //función bl_cron_exec() y corriendo todo lo que se encuentre dentro.
            function enz_save_option(){
                if ( ! wp_next_scheduled( 'bl_cron_hook' ) ) {
                    wp_schedule_event( time(), 'daily', 'bl_cron_hook' );// Cambiado el tiempo de every_minute => daily
                }

                //post_data trae todos los campos del formulario.
                $post_data=$this->get_post_data();
                //Si la licencia no viene en post_data entonces coge la anterior que tenia en la BD
                if($this->get_field_value("license",$post_data)===FALSE){
                    $value=$this->get_option("license");
                }else{
                    $value=$this->get_field_value("license",$post_data);
                }

                //Mandamos a comprobar la validez de la licencia.

                $d=Val::call_api($value);
                 if($d['return']){
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php _e('Activado Correctamente', 'enzona' ); ?></p>
                    </div>
                    <?php
                }else{
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php _e($d['message'], 'enzona' ); ?></p>
                    </div>
                    <?php
                }
                return $this->process_admin_options();
            }
        }
    }
}

add_filter(
    'plugin_action_links_'.plugin_basename(__FILE__),
    static function ($links) {
        $settings = [
            '<a href="'.admin_url('admin.php?page=wc-settings&tab=checkout&section=enzona').'">'.__('Settings', 'enzonapay').'</a>',
        ];

        return array_merge($settings, $links);
    }
);

add_filter('woocommerce_payment_gateways','add_to_woo_enzona_payment_gateway');

function add_to_woo_enzona_payment_gateway($gateways){
    $gateways[]= 'WC_Enzona_Pay_Gateway';
    return $gateways;
}