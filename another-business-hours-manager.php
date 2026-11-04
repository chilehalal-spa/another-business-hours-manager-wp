<?php
/**
 * Plugin Name: Another Business Hours Manager
 * Description: Gestor de horarios comercial con detección automática de feriados y widget de estado.
 * Version: 1.1.0
 * Author: Zumito
 * Text Domain: another-business-hours
 * Requires at least: 5.8
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: https://github.com/chilehalal-spa/another-business-hours-manager-wp
 */

if (!defined('ABSPATH')) exit;

// Actualizaciones desde las releases de GitHub
require_once __DIR__ . '/includes/class-chilehalal-github-updater.php';
Chilehalal_GitHub_Updater_V1::register( __FILE__, 'chilehalal-spa/another-business-hours-manager-wp' );

// Constantes de rutas
define('ABH_PATH', plugin_dir_path(__FILE__));
define('ABH_URL', plugin_dir_url(__FILE__));

// Cargar módulos
require_once ABH_PATH . 'includes/class-abh-logic.php';
require_once ABH_PATH . 'includes/class-abh-settings.php';
require_once ABH_PATH . 'includes/class-abh-shortcode.php';
require_once ABH_PATH . 'includes/class-abh-widget.php';

class AnotherBusinessHoursPlugin {

    public function __construct() {
        // Inicializar admin y shortcode
        new ABH_Settings();
        new ABH_Shortcode();

        // Registrar Widget
        add_action('widgets_init', function() {
            register_widget('ABH_Hours_Widget');
        });

        // Cargar estilos
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'abh-styles', 
            ABH_URL . 'assets/css/style.css', 
            array(), 
            '1.0'
        );
    }
}

// Arrancar el plugin
new AnotherBusinessHoursPlugin();