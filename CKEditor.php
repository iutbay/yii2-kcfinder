<?php
/**
 * @author Yuriy Mamaev
 */

namespace iutbay\yii2kcfinder;


use yii\helpers\ArrayHelper;

class CKEditor extends \dosamigos\ckeditor\CKEditor
{

    public $enableKCFinder = true;

    /**
     * Registers CKEditor plugin
     */
    protected function registerPlugin()
    {
        if ($this->enableKCFinder)
        {
            $this->registerKCFinder();
        }

        parent::registerPlugin();
    }

    /**
     * Registers KCFinder
     */
    protected function registerKCFinder()
    {
        $register = \iutbay\yii2kcfinder\KCFinderAsset::register($this->view);
        $kcfinderUrl = $register->baseUrl;

        $browseOptions = [
            'filebrowserBrowseUrl' => $kcfinderUrl . '/browse.php?opener=ckeditor&type=files&lang=' . \yii::$app->language,
            'filebrowserUploadUrl' => $kcfinderUrl . '/upload.php?opener=ckeditor&type=files&lang=' . \yii::$app->language,
        ];

        $this->clientOptions = ArrayHelper::merge($browseOptions, $this->clientOptions);
    }
}