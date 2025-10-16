<?php

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

class AI_SEO_Master_Diagnostic_AJAX
{

    public static function init()
    {
        add_action('wp_ajax_ai_seo_run_diagnostic', array(__CLASS__, 'run_diagnostic'));
    }

    public static function run_diagnostic()
    {
        check_ajax_referer('ai_seo_diagnostic', '_ajax_nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Non autorisé');
        }

        $results = self::check_system();
        wp_send_json_success(array('html' => self::format_results($results)));
    }

    private static function check_system()
    {
        $results = array();

        // Vérification API
        $api_provider = get_option('ai_seo_master_api_provider');
        $api_key_exists = !empty(get_option('ai_seo_master_api_key_encrypted'));

        $results['api'] = array(
            'label' => 'Configuration API IA',
            'status' => $api_provider && $api_key_exists,
            'message' => $api_provider ?
                "Provider: {$api_provider}, Clé: " . ($api_key_exists ? '✅ Configurée' : '❌ Manquante') :
                'Aucun provider configuré'
        );

        // Vérification des fonctionnalités
        $results['schema'] = array(
            'label' => 'Schema.org',
            'status' => (bool) get_option('ai_seo_master_enable_schema', true),
            'message' => get_option('ai_seo_master_enable_schema', true) ? 'Activé' : 'Désactivé'
        );

        $results['hreflang'] = array(
            'label' => 'Balises Hreflang',
            'status' => (bool) get_option('ai_seo_master_enable_hreflang', true),
            'message' => get_option('ai_seo_master_enable_hreflang', true) ? 'Activé' : 'Désactivé'
        );

        // Vérification PHP
        $results['php_version'] = array(
            'label' => 'Version PHP',
            'status' => version_compare(PHP_VERSION, '7.4', '>='),
            'message' => 'Version actuelle: ' . PHP_VERSION
        );

        return $results;
    }

    private static function format_results($results)
    {
        $html = '<div class="ai-seo-diagnostic-results">';
        $html .= '<h3>Résultats du diagnostic</h3>';
        $html .= '<table class="widefat">';
        $html .= '<thead><tr><th>Test</th><th>Statut</th><th>Détails</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($results as $result) {
            $status_class = $result['status'] ? 'status-ok' : 'status-error';
            $status_icon = $result['status'] ? '✅' : '❌';

            $html .= '<tr>';
            $html .= '<td>' . esc_html($result['label']) . '</td>';
            $html .= '<td><span class="' . $status_class . '">' . $status_icon . '</span></td>';
            $html .= '<td>' . esc_html($result['message']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</div>';

        return $html;
    }
}

AI_SEO_Master_Diagnostic_AJAX::init();
