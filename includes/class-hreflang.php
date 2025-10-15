<?php

class AI_SEO_Master_Hreflang
{

    public function init()
    {
        add_action('wp_head', array($this, 'output_hreflang_tags'), 2);
    }

    public function output_hreflang_tags()
    {
        if (!get_option('ai_seo_master_enable_hreflang', true)) {
            return;
        }

        $alternate_urls = $this->get_alternate_urls();

        foreach ($alternate_urls as $lang => $url) {
            echo '<link rel="alternate" hreflang="' . esc_attr($lang) . '" href="' . esc_url($url) . '" />' . "\n";
        }
    }

    private function get_alternate_urls()
    {
        $default_lang = get_option('ai_seo_master_default_lang', 'fr');
        $alternate_langs = $this->get_alternate_languages();

        $urls = array();
        $current_url = $this->get_current_url();

        // URL par défaut
        $urls['x-default'] = $current_url;

        // Langue par défaut
        $urls[$default_lang] = $current_url;

        // Langues alternatives
        foreach ($alternate_langs as $lang) {
            if ($lang !== $default_lang) {
                $urls[$lang] = $this->get_translated_url($current_url, $lang);
            }
        }

        return $urls;
    }

    private function get_alternate_languages()
    {
        $langs_option = get_option('ai_seo_master_alternate_langs', '["fr","en"]');
        $langs = json_decode($langs_option, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($langs)) {
            $langs = array('fr', 'en');
        }

        return $langs;
    }

    private function get_current_url()
    {
        global $wp;
        return home_url($wp->request);
    }

    private function get_translated_url($url, $lang)
    {
        // Méthode basique - à adapter selon votre configuration multilingue
        $parsed_url = parse_url($url);
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';

        // Support pour les sous-domaines
        if (get_option('ai_seo_master_url_strategy') === 'subdomain') {
            $host = $parsed_url['host'];
            $new_host = $lang . '.' . preg_replace('/^[a-z]{2}\./', '', $host);
            return $parsed_url['scheme'] . '://' . $new_host . $path;
        }

        // Support pour les répertoires (par défaut)
        $home_url = home_url();
        $relative_path = str_replace($home_url, '', $url);

        return $home_url . '/' . $lang . $relative_path;
    }
}
