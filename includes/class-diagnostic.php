<?php

class AI_SEO_Master_Diagnostic
{

    public function init()
    {
        add_action('wp_ajax_ai_seo_run_diagnostic', array($this, 'ajax_run_diagnostic'));
    }

    public function ajax_run_diagnostic()
    {
        check_ajax_referer('ai_seo_master_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Non autorisé');
        }

        $results = $this->run_diagnostic();
        wp_send_json_success(array('html' => $this->format_results($results)));
    }

    private function run_diagnostic()
    {
        $results = array();

        // Vérification de la configuration API
        $results['api_configured'] = array(
            'label' => 'API IA configurée',
            'status' => get_option('ai_seo_master_api_provider') && get_option('ai_seo_master_api_key_encrypted'),
            'message' => get_option('ai_seo_master_api_provider') ?
                'Provider: ' . get_option('ai_seo_master_api_provider') :
                'Aucun provider configuré'
        );

        // Vérification des langues
        $results['languages'] = array(
            'label' => 'Configuration des langues',
            'status' => !empty(get_option('ai_seo_master_default_lang')),
            'message' => 'Langue par défaut: ' . get_option('ai_seo_master_default_lang', 'Non définie')
        );

        // Vérification des fonctionnalités activées
        $results['schema'] = array(
            'label' => 'Schema.org activé',
            'status' => get_option('ai_seo_master_enable_schema', true),
            'message' => get_option('ai_seo_master_enable_schema', true) ? 'Activé' : 'Désactivé'
        );

        $results['hreflang'] = array(
            'label' => 'Hreflang activé',
            'status' => get_option('ai_seo_master_enable_hreflang', true),
            'message' => get_option('ai_seo_master_enable_hreflang', true) ? 'Activé' : 'Désactivé'
        );

        return $results;
    }

    private function format_results($results)
    {
        $html = '<div class="ai-seo-diagnostic-results">';
        $html .= '<h3>Résultats du diagnostic SEO</h3>';
        $html .= '<table class="widefat">';
        $html .= '<thead><tr><th>Test</th><th>Statut</th><th>Message</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($results as $result) {
            $status_class = $result['status'] ? 'status-ok' : 'status-error';
            $status_text = $result['status'] ? '✅ OK' : '❌ Erreur';

            $html .= '<tr>';
            $html .= '<td>' . esc_html($result['label']) . '</td>';
            $html .= '<td><span class="' . $status_class . '">' . $status_text . '</span></td>';
            $html .= '<td>' . esc_html($result['message']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</div>';

        return $html;
    }
}
