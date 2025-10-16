<?php

class AI_SEO_Master_Core
{

    private static $instance = null;
    private $ai_api;
    private $schema;
    private $hreflang;
    private $meta_tags;
    private $admin;
    private $frontend;
    private $diagnostic;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->load_dependencies();
    }

    private function load_dependencies()
    {
        // Charger les classes
        require_once AI_SEO_MASTER_PLUGIN_PATH . 'includes/class-ai-api.php';
        require_once AI_SEO_MASTER_PLUGIN_PATH . 'includes/class-schema.php';
        require_once AI_SEO_MASTER_PLUGIN_PATH . 'includes/class-hreflang.php';
        require_once AI_SEO_MASTER_PLUGIN_PATH . 'includes/class-meta-tags.php';
        require_once AI_SEO_MASTER_PLUGIN_PATH . 'admin/class-admin.php';
        require_once AI_SEO_MASTER_PLUGIN_PATH . 'public/class-frontend.php';
        require_once AI_SEO_MASTER_PLUGIN_PATH . 'includes/class-diagnostic.php';
        require_once AI_SEO_MASTER_PLUGIN_PATH . 'admin/ajax-diagnostic.php';

        // Instancier les composants
        $this->ai_api = new AI_SEO_Master_AI_API();
        $this->schema = new AI_SEO_Master_Schema();
        $this->hreflang = new AI_SEO_Master_Hreflang();
        $this->meta_tags = new AI_SEO_Master_Meta_Tags();
        $this->diagnostic = new AI_SEO_Master_Diagnostic();


        if (is_admin()) {
            $this->admin = new AI_SEO_Master_Admin();
        }

        $this->frontend = new AI_SEO_Master_Frontend();
    }

    public function run()
    {
        // Initialiser les composants
        $this->ai_api->init();
        $this->schema->init();
        $this->hreflang->init();
        $this->meta_tags->init();
        $this->diagnostic->init();

        if ($this->admin) {
            $this->admin->init();
        }

        if ($this->frontend) {
            $this->frontend->init();
        }

        // Hook pour la génération IA lors de la sauvegarde
        add_action('save_post', array($this, 'generate_seo_on_save'), 10, 3);
    }

    public function generate_seo_on_save($post_id, $post, $update)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (wp_is_post_revision($post_id)) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $auto_generate = get_option('ai_seo_master_auto_generate', true);
        $force_manual = get_option('ai_seo_master_force_manual', false);

        if ($auto_generate && !$force_manual && $this->ai_api->is_configured()) {
            $this->ai_api->generate_seo_content($post_id, $post);
        }
    }

    // Getters pour les composants
    public function get_ai_api()
    {
        return $this->ai_api;
    }
    public function get_schema()
    {
        return $this->schema;
    }
    public function get_hreflang()
    {
        return $this->hreflang;
    }
    public function get_meta_tags()
    {
        return $this->meta_tags;
    }
}
