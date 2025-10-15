<?php

class AI_SEO_Master_Admin
{

    private $core;

    public function init()
    {
        $this->core = AI_SEO_Master_Core::get_instance();

        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_ai_seo_generate_content', array($this, 'ajax_generate_seo_content'));
    }

    public function add_admin_menu()
    {
        add_options_page(
            'AI SEO Master',
            'AI SEO Master',
            'manage_options',
            'ai-seo-master',
            array($this, 'admin_page')
        );
    }

    public function admin_page()
    {
?>
        <div class="wrap ai-seo-master-admin">
            <h1>AI SEO Master</h1>

            <div class="ai-seo-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#general" class="nav-tab nav-tab-active">Général</a>
                    <a href="#ai-seo" class="nav-tab">SEO par IA</a>
                    <a href="#advanced" class="nav-tab">Avancé</a>
                    <a href="#help" class="nav-tab">Aide</a>
                </nav>

                <div class="tab-content">
                    <div id="general" class="tab-pane active">
                        <?php $this->render_general_tab(); ?>
                    </div>
                    <div id="ai-seo" class="tab-pane">
                        <?php $this->render_ai_seo_tab(); ?>
                    </div>
                    <div id="advanced" class="tab-pane">
                        <?php $this->render_advanced_tab(); ?>
                    </div>
                    <div id="help" class="tab-pane">
                        <?php $this->render_help_tab(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    private function render_general_tab()
    {
    ?>
        <form method="post" action="options.php">
            <?php settings_fields('ai_seo_master_general'); ?>
            <?php do_settings_sections('ai_seo_master_general'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">Langue par défaut</th>
                    <td>
                        <input type="text" name="ai_seo_master_default_lang"
                            value="<?php echo esc_attr(get_option('ai_seo_master_default_lang', 'fr')); ?>"
                            class="regular-text">
                        <p class="description">Code langue ISO (ex: fr, en, es)</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Langues alternatives</th>
                    <td>
                        <input type="text" name="ai_seo_master_alternate_langs"
                            value="<?php echo esc_attr(get_option('ai_seo_master_alternate_langs', '["fr","en"]')); ?>"
                            class="regular-text">
                        <p class="description">JSON array des langues (ex: ["fr","en","es"])</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Stratégie d'URL</th>
                    <td>
                        <select name="ai_seo_master_url_strategy">
                            <option value="directory" <?php selected(get_option('ai_seo_master_url_strategy', 'directory'), 'directory'); ?>>Répertoires (/fr/)</option>
                            <option value="subdomain" <?php selected(get_option('ai_seo_master_url_strategy', 'directory'), 'subdomain'); ?>>Sous-domaines (fr.)</option>
                        </select>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    <?php
    }

    private function render_ai_seo_tab()
    {
    ?>
        <form method="post" action="options.php">
            <?php settings_fields('ai_seo_master_ai'); ?>
            <?php do_settings_sections('ai_seo_master_ai'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">Provider API</th>
                    <td>
                        <select name="ai_seo_master_api_provider">
                            <option value="">-- Sélectionner --</option>
                            <option value="openai" <?php selected(get_option('ai_seo_master_api_provider'), 'openai'); ?>>OpenAI</option>
                            <option value="gemini" <?php selected(get_option('ai_seo_master_api_provider'), 'gemini'); ?>>Google Gemini</option>
                            <option value="mistral" <?php selected(get_option('ai_seo_master_api_provider'), 'mistral'); ?>>Mistral AI</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Clé API</th>
                    <td>
                        <input type="password" name="ai_seo_master_api_key"
                            value="" class="regular-text">
                        <p class="description">Laisser vide pour ne pas modifier la clé actuelle</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Génération automatique</th>
                    <td>
                        <label>
                            <input type="checkbox" name="ai_seo_master_auto_generate" value="1"
                                <?php checked(get_option('ai_seo_master_auto_generate', true)); ?>>
                            Générer automatiquement le SEO à la sauvegarde
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Mode manuel forcé</th>
                    <td>
                        <label>
                            <input type="checkbox" name="ai_seo_master_force_manual" value="1"
                                <?php checked(get_option('ai_seo_master_force_manual', false)); ?>>
                            Désactiver la génération IA (mode manuel uniquement)
                        </label>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    <?php
    }

    private function render_advanced_tab()
    {
    ?>
        <form method="post" action="options.php">
            <?php settings_fields('ai_seo_master_advanced'); ?>
            <?php do_settings_sections('ai_seo_master_advanced'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">Schema Markup</th>
                    <td>
                        <label>
                            <input type="checkbox" name="ai_seo_master_enable_schema" value="1"
                                <?php checked(get_option('ai_seo_master_enable_schema', true)); ?>>
                            Activer le balisage Schema.org
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Balises Hreflang</th>
                    <td>
                        <label>
                            <input type="checkbox" name="ai_seo_master_enable_hreflang" value="1"
                                <?php checked(get_option('ai_seo_master_enable_hreflang', true)); ?>>
                            Activer les balises hreflang
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Titres & Descriptions</th>
                    <td>
                        <label>
                            <input type="checkbox" name="ai_seo_master_auto_titles" value="1"
                                <?php checked(get_option('ai_seo_master_auto_titles', true)); ?>>
                            Générer automatiquement les titres et descriptions
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Image Open Graph par défaut</th>
                    <td>
                        <input type="text" name="ai_seo_master_default_og_image"
                            value="<?php echo esc_attr(get_option('ai_seo_master_default_og_image')); ?>"
                            class="regular-text">
                        <p class="description">URL de l'image par défaut pour les partages sociaux</p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    <?php
    }

    private function render_help_tab()
    {
    ?>
        <div class="ai-seo-help-content">
            <h2>Guide d'utilisation</h2>

            <h3>Configuration de base</h3>
            <ol>
                <li>Configurez votre provider API et votre clé dans l'onglet "SEO par IA"</li>
                <li>Définissez les langues dans l'onglet "Général"</li>
                <li>Activez les fonctionnalités souhaitées dans l'onglet "Avancé"</li>
            </ol>

            <h3>Génération de contenu SEO</h3>
            <p>Pour générer le SEO d'un article :</p>
            <ol>
                <li>Éditez un article existant ou créez-en un nouveau</li>
                <li>Remplissez le contenu de l'article</li>
                <li>Cliquez sur "Générer avec l'IA" dans la meta-box "AI SEO Master"</li>
                <li>Enregistrez l'article</li>
            </ol>

            <h3>Diagnostic SEO</h3>
            <div id="ai-seo-diagnostic">
                <button type="button" class="button button-primary" id="run-seo-diagnostic">
                    Lancer le diagnostic SEO
                </button>
                <div id="diagnostic-results" style="margin-top: 20px;"></div>
            </div>
        </div>
    <?php
    }

    public function register_settings()
    {
        // Général
        register_setting('ai_seo_master_general', 'ai_seo_master_default_lang');
        register_setting('ai_seo_master_general', 'ai_seo_master_alternate_langs');
        register_setting('ai_seo_master_general', 'ai_seo_master_url_strategy');

        // IA
        register_setting('ai_seo_master_ai', 'ai_seo_master_api_provider');
        register_setting('ai_seo_master_ai', 'ai_seo_master_api_key', array(
            'sanitize_callback' => array($this, 'sanitize_api_key')
        ));
        register_setting('ai_seo_master_ai', 'ai_seo_master_auto_generate');
        register_setting('ai_seo_master_ai', 'ai_seo_master_force_manual');

        // Avancé
        register_setting('ai_seo_master_advanced', 'ai_seo_master_enable_schema');
        register_setting('ai_seo_master_advanced', 'ai_seo_master_enable_hreflang');
        register_setting('ai_seo_master_advanced', 'ai_seo_master_auto_titles');
        register_setting('ai_seo_master_advanced', 'ai_seo_master_default_og_image');
    }

    public function sanitize_api_key($api_key)
    {
        if (!empty($api_key)) {
            $ai_api = $this->core->get_ai_api();
            $ai_api->set_api_key($api_key);
        }
        return ''; // Ne pas stocker en clair
    }

    public function add_meta_boxes()
    {
        $post_types = get_post_types(array('public' => true), 'names');

        foreach ($post_types as $post_type) {
            add_meta_box(
                'ai_seo_master_meta_box',
                'AI SEO Master',
                array($this, 'render_meta_box'),
                $post_type,
                'normal',
                'high'
            );
        }
    }

    public function render_meta_box($post)
    {
        wp_nonce_field('ai_seo_master_meta_box', 'ai_seo_master_nonce');

        $seo_title = get_post_meta($post->ID, '_ai_seo_title', true);
        $seo_description = get_post_meta($post->ID, '_ai_seo_description', true);
        $seo_keywords = get_post_meta($post->ID, '_ai_seo_keywords', true);
    ?>
        <div class="ai-seo-meta-box">
            <p>
                <button type="button" class="button button-secondary" id="ai-seo-generate">
                    Générer avec l'IA
                </button>
                <span id="ai-seo-status" style="margin-left: 10px;"></span>
            </p>

            <div class="ai-seo-fields">
                <p>
                    <label for="ai_seo_title"><strong>Titre SEO</strong></label>
                    <input type="text" id="ai_seo_title" name="ai_seo_title"
                        value="<?php echo esc_attr($seo_title); ?>" class="widefat"
                        placeholder="Titre optimisé pour le SEO (max 60 caractères)">
                    <span class="char-count" id="title-char-count">0/60</span>
                </p>

                <p>
                    <label for="ai_seo_description"><strong>Description SEO</strong></label>
                    <textarea id="ai_seo_description" name="ai_seo_description"
                        class="widefat" rows="3"
                        placeholder="Description optimisée pour le SEO (max 160 caractères)"><?php echo esc_textarea($seo_description); ?></textarea>
                    <span class="char-count" id="desc-char-count">0/160</span>
                </p>

                <p>
                    <label for="ai_seo_keywords"><strong>Mots-clés</strong></label>
                    <input type="text" id="ai_seo_keywords" name="ai_seo_keywords"
                        value="<?php echo esc_attr($seo_keywords); ?>" class="widefat"
                        placeholder="Mots-clés séparés par des virgules">
                </p>
            </div>
        </div>

        <script>
            jQuery(document).ready(function($) {
                // Compteur de caractères
                function updateCharCount() {
                    $('#title-char-count').text($('#ai_seo_title').val().length + '/60');
                    $('#desc-char-count').text($('#ai_seo_description').val().length + '/160');
                }

                $('#ai_seo_title, #ai_seo_description').on('input', updateCharCount);
                updateCharCount();

                // Génération IA
                $('#ai-seo-generate').on('click', function() {
                    var $button = $(this);
                    var $status = $('#ai-seo-status');

                    $button.prop('disabled', true);
                    $status.text('Génération en cours...');

                    $.post(ajaxurl, {
                        action: 'ai_seo_generate_content',
                        post_id: <?php echo $post->ID; ?>,
                        nonce: '<?php echo wp_create_nonce('ai_seo_generate'); ?>'
                    }, function(response) {
                        if (response.success) {
                            if (response.data.title) {
                                $('#ai_seo_title').val(response.data.title);
                            }
                            if (response.data.description) {
                                $('#ai_seo_description').val(response.data.description);
                            }
                            if (response.data.keywords) {
                                $('#ai_seo_keywords').val(response.data.keywords);
                            }
                            $status.text('Généré avec succès!');
                            updateCharCount();
                        } else {
                            $status.text('Erreur: ' + response.data);
                        }
                        $button.prop('disabled', false);
                    }).fail(function() {
                        $status.text('Erreur de connexion');
                        $button.prop('disabled', false);
                    });
                });
            });
        </script>
<?php
    }

    public function enqueue_admin_scripts($hook)
    {
        if ($hook === 'settings_page_ai-seo-master' || in_array($hook, array('post.php', 'post-new.php'))) {
            wp_enqueue_style(
                'ai-seo-master-admin',
                AI_SEO_MASTER_PLUGIN_URL . 'admin/assets/css/admin.css',
                array(),
                AI_SEO_MASTER_VERSION
            );

            wp_enqueue_script(
                'ai-seo-master-admin',
                AI_SEO_MASTER_PLUGIN_URL . 'admin/assets/js/admin.js',
                array('jquery'),
                AI_SEO_MASTER_VERSION,
                true
            );
        }
    }

    public function ajax_generate_seo_content()
    {
        check_ajax_referer('ai_seo_generate', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_die('Unauthorized');
        }

        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);

        if (!$post) {
            wp_send_json_error('Post non trouvé');
        }

        $ai_api = $this->core->get_ai_api();
        $result = $ai_api->generate_seo_content($post_id, $post);

        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error('Erreur lors de la génération');
        }
    }
}
