# AI SEO Master

Plugin WordPress intelligent pour l'automatisation du SEO avec l'intelligence artificielle.

## Fonctionnalités

- Génération automatique des métadonnées par IA
- Balises Schema.org dynamiques
- Balises hreflang pour le multilingue
- Open Graph et Twitter Cards
- Interface d'administration moderne
- Sécurité renforcée

## Installation

1. Téléchargez le plugin
2. Décompressez dans `/wp-content/plugins/ai-seo-master/`
3. Activez le plugin dans l'administration WordPress
4. Configurez votre clé API dans "Réglages > AI SEO Master"

## Configuration

### Provider API supportés

- OpenAI (GPT-3.5, GPT-4)
- Google Gemini
- Mistral AI

### Configuration des langues

Définissez votre langue par défaut et les langues alternatives au format JSON :

```json
["fr", "en", "es"]
```

## Types de Schema supportés

- WebSite (page d'accueil)

- Article (posts)

- WebPage (pages)

- Product (WooCommerce)

## Hooks disponibles

### Actions

- `ai_seo_before_meta_generation` - Avant la génération des métadonnées

- `ai_seo_after_meta_generation` - Après la génération des métadonnées

- `ai_seo_before_schema_output` - Avant l'affichage du Schema

### Filtres

- `ai_seo_meta_title` - Filtrer le titre SEO

- `ai_seo_meta_description` - Filtrer la description SEO

- `ai_seo_schema_data` - Filtrer les données Schema

- `ai_seo_hreflang_urls` - Filtrer les URLs hreflang

## Utilisation avancée

### Génération manuelle

```php
$ai_api = AI_SEO_Master_Core::get_instance()->get_ai_api();
$seo_data = $ai_api->generate_seo_content($post_id, $post);
```

### Récupération des métadonnées

```php
$seo_title = get_post_meta($post_id, '_ai_seo_title', true);
$seo_description = get_post_meta($post_id, '_ai_seo_description', true);
```

## Compatibilité

- WordPress 6.0+

- PHP 7.4+

- WooCommerce (optionnel)

- Compatible avec `Yoast SEO` et `Rank Math`

## Support

Pour toute question ou problème, consultez la documentation ou créez une issue sur GitHub.

## Licence

GPL v2 or later
