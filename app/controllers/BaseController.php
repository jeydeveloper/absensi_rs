<?php

namespace App\Controllers;

use Gettext\Translator;
use App\Models\SettingModel as Setting;

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

    public function getSettingDb() {
      $arrData = [];
      $setting = Setting::getAllNonVoid();
      foreach ($setting as $key => $value) {
        $arrData[$value->sett_name] = $value->sett_value;
      }
      return $arrData;
    }

    public function getYearFilterRange() {
      $arrData = [];
      $setting = $this->getSettingDb();
      $yearBefore = !empty($setting['year_filter_range_before']) ? $setting['year_filter_range_before'] : 0;
      $yearAfter = !empty($setting['year_filter_range_after']) ? $setting['year_filter_range_after'] : 0;
      $year = date('Y');
      $startYear = $year - $yearBefore;
      $endYear = $year + $yearAfter;
      for ($i=$startYear; $i <= $endYear; $i++) {
        $arrData[$i] = $i;
      }
      return $arrData;
    }
}
