<?php

class AI_SEO_Master_Frontend
{

    private $core;

    public function init()
    {
        $this->core = AI_SEO_Master_Core::get_instance();

        // Les hooks sont déjà initialisés dans les classes spécifiques
        // Cette classe sert de point d'entrée pour les extensions futures
    }
}
