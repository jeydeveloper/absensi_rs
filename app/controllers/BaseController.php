<?php

namespace App\Controllers;

use Gettext\Translator;

class BaseController
{
    protected $t;

    public function __construct()
    {
        $this->t = new Translator();
        $translations = \Gettext\Translations::fromPoFile(__DIR__ . '/../../locales/en_US.po');
        $this->t->loadTranslations($translations);
        $this->t->register();
    }
}
