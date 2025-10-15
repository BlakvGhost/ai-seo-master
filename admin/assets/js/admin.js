jQuery(document).ready(function($) {
    // Navigation par onglets
    $('.ai-seo-tabs .nav-tab').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this).attr('href');
        
        // Activer l'onglet
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Afficher le contenu
        $('.tab-pane').removeClass('active');
        $(target).addClass('active');
    });
    
    // Diagnostic SEO
    $('#run-seo-diagnostic').on('click', function() {
        var $button = $(this);
        var $results = $('#diagnostic-results');
        
        $button.prop('disabled', true).text('Analyse en cours...');
        $results.html('<div class="notice notice-info"><p>Diagnostic en cours...</p></div>');
        
        $.post(ajaxurl, {
            action: 'ai_seo_run_diagnostic',
            nonce: aiSeoMaster.nonce
        }, function(response) {
            if (response.success) {
                $results.html(response.data.html);
            } else {
                $results.html('<div class="notice notice-error"><p>Erreur: ' + response.data + '</p></div>');
            }
            $button.prop('disabled', false).text('Lancer le diagnostic SEO');
        }).fail(function() {
            $results.html('<div class="notice notice-error"><p>Erreur de connexion</p></div>');
            $button.prop('disabled', false).text('Lancer le diagnostic SEO');
        });
    });
});