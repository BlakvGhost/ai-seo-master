<?php

/**
 * Plugin Name: AI SEO Master
 * Plugin URI: https://github.com/BlakvGhost/ai-seo-master
 * Description: Automatise le SEO avec l'intelligence artificielle - Balises meta, Schema.org, Hreflang et Open Graph.
 * Version: 1.0.0
 * Author: Kabirou ALASSANE
 * License: GPL v2 or later
 * Text Domain: ai-seo-master
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Définir les constantes
define('AI_SEO_MASTER_VERSION', '1.0.0');
define('AI_SEO_MASTER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AI_SEO_MASTER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AI_SEO_MASTER_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Vérifier les dépendances
register_activation_hook(__FILE__, 'ai_seo_master_activation_check');
function ai_seo_master_activation_check()
{
    if (version_compare(get_bloginfo('version'), '6.0', '<')) {
        deactivate_plugins(basename(__FILE__));
        wp_die(__('AI SEO Master nécessite WordPress 6.0 ou supérieur.', 'ai-seo-master'));
    }
}

// Charger les classes principales
require_once AI_SEO_MASTER_PLUGIN_PATH . 'includes/class-core.php';
require_once AI_SEO_MASTER_PLUGIN_PATH . 'includes/class-ai-api.php';
require_once AI_SEO_MASTER_PLUGIN_PATH . 'includes/class-schema.php';
require_once AI_SEO_MASTER_PLUGIN_PATH . 'includes/class-hreflang.php';
require_once AI_SEO_MASTER_PLUGIN_PATH . 'includes/class-meta-tags.php';
require_once AI_SEO_MASTER_PLUGIN_PATH . 'admin/class-admin.php';
require_once AI_SEO_MASTER_PLUGIN_PATH . 'public/class-frontend.php';

// Initialiser le plugin
function ai_seo_master_init()
{
    $core = AI_SEO_Master_Core::get_instance();
    $core->run();
}
add_action('plugins_loaded', 'ai_seo_master_init');

// Internationalisation
function ai_seo_master_load_textdomain()
{
    load_plugin_textdomain('ai-seo-master', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'ai_seo_master_load_textdomain');
