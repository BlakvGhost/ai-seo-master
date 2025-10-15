<?php

class AI_SEO_Master_Meta_Tags
{

    public function init()
    {
        add_action('wp_head', array($this, 'output_meta_tags'), 3);
        add_filter('pre_get_document_title', array($this, 'filter_document_title'), 20);
    }

    public function filter_document_title($title)
    {
        if (is_singular()) {
            global $post;
            $seo_title = get_post_meta($post->ID, '_ai_seo_title', true);
            if (!empty($seo_title)) {
                return $seo_title;
            }
        }

        return $title;
    }

    public function output_meta_tags()
    {
        $this->output_standard_meta();
        $this->output_open_graph();
        $this->output_twitter_cards();
    }

    private function output_standard_meta()
    {
        if (!get_option('ai_seo_master_auto_titles', true)) {
            return;
        }

        global $post;

        // Description
        $description = $this->get_seo_description();
        if ($description) {
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        }

        // Keywords
        $keywords = $this->get_seo_keywords();
        if ($keywords) {
            echo '<meta name="keywords" content="' . esc_attr($keywords) . '">' . "\n";
        }

        // Robots
        echo '<meta name="robots" content="index, follow">' . "\n";
    }

    private function output_open_graph()
    {
        global $post;

        $og_title = $this->get_og_title();
        $og_description = $this->get_og_description();
        $og_image = $this->get_og_image();
        $og_url = $this->get_og_url();

        echo '<!-- Open Graph -->' . "\n";
        echo '<meta property="og:type" content="' . $this->get_og_type() . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($og_description) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($og_url) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";

        if ($og_image) {
            echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
            echo '<meta property="og:image:width" content="1200">' . "\n";
            echo '<meta property="og:image:height" content="630">' . "\n";
        }

        echo '<meta property="og:locale" content="' . $this->get_locale() . '">' . "\n";
    }

    private function output_twitter_cards()
    {
        $twitter_title = $this->get_twitter_title();
        $twitter_description = $this->get_twitter_description();
        $twitter_image = $this->get_twitter_image();

        echo '<!-- Twitter Card -->' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($twitter_title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($twitter_description) . '">' . "\n";

        if ($twitter_image) {
            echo '<meta name="twitter:image" content="' . esc_url($twitter_image) . '">' . "\n";
        }
    }

    private function get_seo_description()
    {
        global $post;

        if (is_singular()) {
            $description = get_post_meta($post->ID, '_ai_seo_description', true);
            if (!empty($description)) {
                return $description;
            }

            if ($post->post_excerpt) {
                return $post->post_excerpt;
            }

            return wp_trim_words(wp_strip_all_tags($post->post_content), 30);
        }

        if (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();
            return $term->description ?: wp_trim_words(wp_strip_all_tags(term_description()), 30);
        }

        return get_bloginfo('description');
    }

    private function get_seo_keywords()
    {
        global $post;

        if (is_singular()) {
            return get_post_meta($post->ID, '_ai_seo_keywords', true);
        }

        return '';
    }

    private function get_og_title()
    {
        global $post;

        if (is_singular()) {
            $seo_title = get_post_meta($post->ID, '_ai_seo_title', true);
            if (!empty($seo_title)) {
                return $seo_title;
            }
        }

        return wp_get_document_title();
    }

    private function get_og_description()
    {
        return $this->get_seo_description();
    }

    private function get_og_image()
    {
        global $post;

        if (is_singular()) {
            $thumbnail = get_the_post_thumbnail_url($post->ID, 'full');
            if ($thumbnail) {
                return $thumbnail;
            }
        }

        // Image par défaut
        return get_option('ai_seo_master_default_og_image', '');
    }

    private function get_og_url()
    {
        if (is_singular()) {
            return get_permalink();
        }

        global $wp;
        return home_url($wp->request);
    }

    private function get_og_type()
    {
        if (is_front_page()) {
            return 'website';
        } elseif (is_singular('post')) {
            return 'article';
        } else {
            return 'website';
        }
    }

    private function get_locale()
    {
        $locale = get_locale();
        return str_replace('_', '-', $locale);
    }

    private function get_twitter_title()
    {
        return $this->get_og_title();
    }

    private function get_twitter_description()
    {
        return $this->get_og_description();
    }

    private function get_twitter_image()
    {
        return $this->get_og_image();
    }
}
