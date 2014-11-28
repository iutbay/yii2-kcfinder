<?php

namespace iutbay\yii2kcfinder;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;

use iutbay\yii2fontawesome\FontAwesome;

/**
 * KCFinder Input Widget.
 * @author Kevin LEVRON <kevin.levron@gmail.com>
 */
class KCFinderInputWidget extends InputWidget
{

    /**
     * Multiple selection
     * @var boolean
     */
    public $multiple = false;

    /**
     * KCFinder dynamic settings (using session)
     * @link http://kcfinder.sunhater.com/install#dynamic
     * @var array
     */
    public $kcfOptions = [];

    /**
     * KCFinder default dynamic settings
     * @link http://kcfinder.sunhater.com/install#dynamic
     * @var array
     */
    public $kcfDefaultOptions = [
        'disabled'=>false,
        'denyZipDownload' => true,
        'denyUpdateCheck' => true,
        'denyExtensionRename' => true,
        'theme' => 'default',
        'access' =>[    // @link http://kcfinder.sunhater.com/install#_access
            'files' =>[
                'upload' => false,
                'delete' => false,
                'copy' => false,
                'move' => false,
                'rename' => false,
            ],
            'dirs' =>[
                'create' => false,
                'delete' => false,
                'rename' => false,
            ],
        ],
        'types'=>[  // @link http://kcfinder.sunhater.com/install#_types
            'files' => [
                'type' => '',
            ],
            'images' => [
                'type' => '*img',
            ],
        ],
        'thumbsDir' => '.thumbs',
        'thumbWidth' => 100,
        'thumbHeight' => 100,
    ];

    /**
     * KCFinder client options
     * @var array
     */
    public $clientOptions = [];

    /**
     * KCFinder input parameters
     * @link http://kcfinder.sunhater.com/integrate#input
     * @var array
     */
    public $kcfBrowseOptions = [];

    /**
     * Button label
     * @var string
     */
    public $buttonLabel = 'Add Media';

    /**
     * Button options
     * @var array
     */
    public $buttonOptions = [];

    public $template = '<div class="input-group">{button}</div><div class="input-group">{thumbs}</div>';
    public $thumbTemplate = '<li><img src="{thumbSrc}" /><input type="hidden" name="{inputName}" value="{inputValue}"></li>';

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        
        if (!isset($this->kcfOptions['uploadURL']))
        {
            $this->kcfOptions['uploadURL'] = Yii::getAlias('@web/upload');
            //$this->kcfOptions['uploadDir'] = Yii::getAlias('@app/web/upload');
        }
        
        $this->kcfOptions = array_merge($this->kcfDefaultOptions, $this->kcfOptions);
        Yii::$app->session['KCFINDER'] = $this->kcfOptions;

        $this->clientOptions['browseOptions'] = $this->kcfBrowseOptions;
        $this->clientOptions['uploadURL'] = $this->kcfOptions['uploadURL'];
        $this->clientOptions['multiple'] = $this->multiple;
        $this->clientOptions['inputName'] = $this->getInputName();
        $this->clientOptions['thumbsDir'] = $this->kcfOptions['thumbsDir'];
        $this->clientOptions['thumbsSelector'] = '#'.$this->getThumbsId();
        $this->clientOptions['thumbTemplate'] = $this->thumbTemplate;

        $this->buttonOptions['id'] = $this->getButtonId();

        Html::addCssClass($this->options, 'form-control');
        Html::addCssClass($this->buttonOptions, 'kcf btn btn-default');
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $this->registerClientScript();

        $button = Html::button(FontAwesome::icon('picture-o').' '.$this->buttonLabel, $this->buttonOptions);

        $thumbs = '<ul class="kcf-thumbs" id="'.$this->getThumbsId().'"></ul>';
        
        echo Html::tag('div', strtr($this->template, [
            '{button}' => $button,
            '{thumbs}' => $thumbs,
        ]), ['class'=>'kcf-input-group']);
    }
    
    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        static $kcfAssetPathRegistered = false;

        $view = $this->getView();
        KCFinderWidgetAsset::register($view);

        if (!$kcfAssetPathRegistered)
        {
            $assetPath = Json::encode(Yii::$app->assetManager->getPublishedUrl((new KCFinderAsset)->sourcePath));
            $view->registerJs("var kcfAssetPath = $assetPath;", View::POS_BEGIN);
            $kcfAssetPathRegistered = true;
        }

        $clientOptions = Json::encode($this->clientOptions);
        $view->registerJs("jQuery('#{$this->buttonOptions['id']}').KCFinderInputWidget($clientOptions)");
    }
    
    public function getInputName()
    {
        if ($this->hasModel()) {
            return Html::getInputName($this->model, $this->attribute);
        } else {
            return $this->name;
        }
    }

    public function getButtonId()
    {
        return $this->getId().'-button';
    }

    public function getThumbsId()
    {
        return $this->getId().'-thumbs';
    }

}