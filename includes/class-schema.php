<?php

class AI_SEO_Master_Schema
{

    public function init()
    {
        add_action('wp_head', array($this, 'output_schema_markup'), 1);
    }

    public function output_schema_markup()
    {
        if (!get_option('ai_seo_master_enable_schema', true)) {
            return;
        }

        $schema = $this->generate_schema();
        if ($schema) {
            echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
        }
    }

    private function generate_schema()
    {
        if (is_front_page()) {
            return $this->generate_website_schema();
        } elseif (is_singular('post')) {
            return $this->generate_article_schema();
        } elseif (is_page()) {
            return $this->generate_webpage_schema();
        } elseif (function_exists('is_product') && is_product()) {
            return $this->generate_product_schema();
        } elseif (is_singular()) {
            return $this->generate_webpage_schema();
        }

        return null;
    }

    private function generate_website_schema()
    {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'url' => home_url(),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            )
        );

        return $schema;
    }

    private function generate_article_schema()
    {
        global $post;

        $post_content = wp_strip_all_tags($post->post_content);
        $excerpt = wp_trim_words($post_content, 50);

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $this->get_seo_title($post->ID),
            'description' => $this->get_seo_description($post->ID) ?: $excerpt,
            'datePublished' => get_the_date('c', $post->ID),
            'dateModified' => get_the_modified_date('c', $post->ID),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name', $post->post_author)
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $this->get_site_logo()
                )
            ),
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => get_permalink($post->ID)
            )
        );

        $thumbnail = get_the_post_thumbnail_url($post->ID, 'full');
        if ($thumbnail) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $thumbnail,
                'width' => 1200,
                'height' => 630
            );
        }

        return $schema;
    }

    private function generate_webpage_schema()
    {
        global $post;

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $this->get_seo_title($post->ID),
            'description' => $this->get_seo_description($post->ID),
            'url' => get_permalink($post->ID),
            'datePublished' => get_the_date('c', $post->ID),
            'dateModified' => get_the_modified_date('c', $post->ID)
        );

        return $schema;
    }

    private function generate_product_schema()
    {
        global $post;

        $product = wc_get_product($post->ID);
        if (!$product) return null;

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->get_name(),
            'description' => $this->get_seo_description($post->ID) ?: wp_strip_all_tags($product->get_short_description()),
            'sku' => $product->get_sku(),
            'brand' => array(
                '@type' => 'Brand',
                'name' => get_bloginfo('name')
            ),
            'offers' => array(
                '@type' => 'Offer',
                'price' => $product->get_price(),
                'priceCurrency' => get_woocommerce_currency(),
                'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => get_permalink($post->ID)
            )
        );

        $image = wp_get_attachment_image_url($product->get_image_id(), 'full');
        if ($image) {
            $schema['image'] = $image;
        }

        return $schema;
    }

    private function get_seo_title($post_id)
    {
        return get_post_meta($post_id, '_ai_seo_title', true) ?: get_the_title($post_id);
    }

    private function get_seo_description($post_id)
    {
        return get_post_meta($post_id, '_ai_seo_description', true) ?: get_the_excerpt($post_id);
    }

    private function get_site_logo()
    {
        $custom_logo_id = get_theme_mod('custom_logo');
        if ($custom_logo_id) {
            return wp_get_attachment_image_url($custom_logo_id, 'full');
        }
        return '';
    }
}
