<?php

namespace iutbay\yii2kcfinder;

use yii\web\AssetBundle;

/**
 * This declares the asset files required by KCFinder Widget.
 * @author Kevin LEVRON <kevin.levron@gmail.com>
 */
class KCFinderWidgetAsset extends AssetBundle
{

    public $sourcePath = '@vendor/iutbay/yii2-kcfinder/assets';
    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];
    public $css = [
        'kcfinder.css',
    ];
    public $js = [
        'kcfinder.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'iutbay\yii2kcfinder\KCFinderAsset',
    ];

}
