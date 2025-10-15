<?php

// Sécurité : empêcher l'accès direct
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Supprimer les options
$options = array(
    'ai_seo_master_default_lang',
    'ai_seo_master_alternate_langs',
    'ai_seo_master_url_strategy',
    'ai_seo_master_api_provider',
    'ai_seo_master_api_key_encrypted',
    'ai_seo_master_auto_generate',
    'ai_seo_master_force_manual',
    'ai_seo_master_enable_schema',
    'ai_seo_master_enable_hreflang',
    'ai_seo_master_auto_titles',
    'ai_seo_master_default_og_image'
);

foreach ($options as $option) {
    delete_option($option);
}

// Supprimer les métadonnées des posts
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_ai_seo_%'");
