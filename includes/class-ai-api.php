<?php

class AI_SEO_Master_AI_API
{

    private $api_provider;
    private $api_key;
    private $is_configured = false;

    public function init()
    {
        $this->api_provider = get_option('ai_seo_master_api_provider', '');
        $this->api_key = $this->get_encrypted_api_key();
        $this->is_configured = !empty($this->api_provider) && !empty($this->api_key);
    }

    public function is_configured()
    {
        return $this->is_configured;
    }

    private function get_encrypted_api_key()
    {
        $encrypted = get_option('ai_seo_master_api_key_encrypted');
        if ($encrypted) {
            // Décrypter la clé (méthode basique)
            return $this->decrypt_api_key($encrypted);
        }
        return '';
    }

    public function set_api_key($api_key)
    {
        if (!empty($api_key)) {
            $encrypted = $this->encrypt_api_key($api_key);
            update_option('ai_seo_master_api_key_encrypted', $encrypted);
        } else {
            delete_option('ai_seo_master_api_key_encrypted');
        }
    }

    private function encrypt_api_key($key)
    {
        // Méthode de cryptage simple (à renforcer en production)
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($key, 'AES-256-CBC', AUTH_KEY, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    private function decrypt_api_key($encrypted)
    {
        $data = base64_decode($encrypted);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', AUTH_KEY, 0, $iv);
    }

    public function generate_seo_content($post_id, $post)
    {
        $content = $post->post_content;
        $title = $post->post_title;
        $post_type = $post->post_type;

        $prompt = $this->build_seo_prompt($title, $content, $post_type);

        try {
            $response = $this->call_ai_api($prompt);
            $seo_data = $this->parse_ai_response($response);

            // Sauvegarder les métadonnées
            $this->save_seo_metadata($post_id, $seo_data);

            return $seo_data;
        } catch (Exception $e) {
            error_log('AI SEO Master Error: ' . $e->getMessage());
            return false;
        }
    }

    private function build_seo_prompt($title, $content, $post_type)
    {
        $clean_content = wp_strip_all_tags($content);
        $clean_content = substr($clean_content, 0, 3000); // Limiter la longueur

        return "En tant qu'expert SEO, génère des métadonnées optimisées pour cet article.
        
Titre: $title
Type: $post_type
Contenu: $clean_content

Retourne UNIQUEMENT un JSON valide avec cette structure:
{
    \"title\": \"Titre SEO optimisé (max 60 caractères)\",
    \"description\": \"Description meta optimisée (max 160 caractères)\",
    \"keywords\": \"mot-clé1, mot-clé2, mot-clé3\",
    \"schema_description\": \"Description pour le schema.org (2-3 phrases)\"
}";
    }

    private function call_ai_api($prompt)
    {
        switch ($this->api_provider) {
            case 'openai':
                return $this->call_openai($prompt);
            case 'gemini':
                return $this->call_gemini($prompt);
            case 'mistral':
                return $this->call_mistral($prompt);
            default:
                throw new Exception('Provider API non supporté');
        }
    }

    private function call_openai($prompt)
    {
        $url = 'https://api.openai.com/v1/chat/completions';

        $body = array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array('role' => 'user', 'content' => $prompt)
            ),
            'max_tokens' => 500,
            'temperature' => 0.7
        );

        return $this->make_api_request($url, $body);
    }

    private function call_gemini($prompt)
    {
        $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent';
        $url .= '?key=' . $this->api_key;

        $body = array(
            'contents' => array(
                array('parts' => array(array('text' => $prompt)))
            )
        );

        return $this->make_api_request($url, $body);
    }

    private function make_api_request($url, $body)
    {
        $args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key
            ),
            'body' => json_encode($body),
            'timeout' => 30
        );

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            throw new Exception($data['error']['message']);
        }

        return $data;
    }

    private function parse_ai_response($response)
    {
        $content = '';

        if ($this->api_provider === 'openai' && isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
        } elseif ($this->api_provider === 'gemini' && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            $content = $response['candidates'][0]['content']['parts'][0]['text'];
        }

        // Extraire le JSON de la réponse
        preg_match('/\{.*\}/s', $content, $matches);
        if (isset($matches[0])) {
            return json_decode($matches[0], true);
        }

        throw new Exception('Réponse AI non valide');
    }

    private function save_seo_metadata($post_id, $seo_data)
    {
        if (isset($seo_data['title'])) {
            update_post_meta($post_id, '_ai_seo_title', sanitize_text_field($seo_data['title']));
        }
        if (isset($seo_data['description'])) {
            update_post_meta($post_id, '_ai_seo_description', sanitize_text_field($seo_data['description']));
        }
        if (isset($seo_data['keywords'])) {
            update_post_meta($post_id, '_ai_seo_keywords', sanitize_text_field($seo_data['keywords']));
        }
        if (isset($seo_data['schema_description'])) {
            update_post_meta($post_id, '_ai_seo_schema_desc', sanitize_text_field($seo_data['schema_description']));
        }
    }
}
