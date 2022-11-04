<?php
/**
 * Sanida
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Sanida
 * @since 1.0
 */

define('SANIDA_DIR', get_template_directory_uri());

include 'includes'.DIRECTORY_SEPARATOR.'templates' . DIRECTORY_SEPARATOR . 'dashboards' .DIRECTORY_SEPARATOR. 'dashboard-lte.php';
include 'includes' . DIRECTORY_SEPARATOR . 'sanida-load.php';
include 'includes' . DIRECTORY_SEPARATOR . 'sanida-controller.php';

class Sanida{

    static function run(){
        return new self();
    }

    function __construct(){

        // register_activation_hook(__FILE__, array($this, 'create_table'));
        
        add_action('after_switch_theme', array($this, 'create_table'));
        add_action('after_setup_theme', array($this, 'sanida_roles'));
        add_action( 'wp_enqueue_scripts', array($this, 'sanida_init_scripts'), 10 );
        add_action('init', array($this, 'sanida_init'));
        add_shortcode( 'sanida', array($this, 'sanida_actions'));

        

        add_action( 'rest_api_init', function () {
            $cntrlr = new WP_Sanida_Controller();
            $cntrlr->register_routes();
        });

    }

    function sanida_init_scripts(){


        wp_enqueue_style( 'bootstrap', SANIDA_DIR . '/thirdparty/assets/bootstrap/css/bootstrap.min.css', array(), '5.2' );
        wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback', array(), '' );

        wp_enqueue_script("jquery");
        wp_enqueue_script( 'sanida-login', SANIDA_DIR . '/thirdparty/assets/custom/login.js', array(), '5.2' );

        wp_localize_script( 'sanida-login', 'sanida_login', array(
            'url' => admin_url( 'admin-ajax.php' ),
            'login_token' => wp_create_nonce("this_is_my_login123"),
            'google_auth' => wp_create_nonce("this_is_my_google123"),
        ));

    }


    function sanida_roles(){

        add_role( 'sanida_user', __('Sanida User'), array( 'read' => false ) );

        $role = get_role('sanida_user');

        $role->add_cap('guess_role', true);
        
    }


    function sanida_init(){

        if(!session_id()){
            session_start();
        }

        $sanida = new Sanida_Load();
        
        $sanida->action_post();
    }


    function sanida_actions($atts){

        ob_start();

        $sanida = new Sanida_Load();

        $sanida->init();

        return ob_get_clean();
    }

    function create_table(){

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `sanida_token` (
              id bigint(50) NOT NULL AUTO_INCREMENT,
              access_path text NOT NULL,
              user_id bigint(20),
              date_created datetime,              
              PRIMARY KEY (id)             
              ) $charset_collate;";

      require_once ABSPATH . 'wp-admin/includes/upgrade.php';

      dbDelta( $sql );
      $is_error = empty( $wpdb->last_error );
      print_r($is_error);
      return $is_error;


    }

    function unregister(){
        remove_role('sanida_user');
    }
}

Sanida::run();