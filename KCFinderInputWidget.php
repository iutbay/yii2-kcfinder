<?php

namespace iutbay\yii2kcfinder;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;
use yii\bootstrap\Modal;

use iutbay\yii2fontawesome\FontAwesome;
use iutbay\yii2fontawesome\FontAwesomeAsset;

/**
 * KCFinder Input Widget.
 * @author Kevin LEVRON <kevin.levron@gmail.com>
 */
class KCFinderInputWidget extends KCFinder
{

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

    /**
     * Modal title
     * @var string
     */
    public $modalTitle = 'Media Manager';

    /**
     * Main template
     * @var array
     */
    public $template = '{button}{thumbs}';

    /**
     * Thumb template
     * @var array
     */
    public $thumbTemplate = '<li class="sortable"><div class="remove"><span class="fa fa-trash"></span></div><img src="{thumbSrc}" /><input type="hidden" name="{inputName}" value="{inputValue}"></li>';

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        $this->clientOptions['thumbsDir'] = $this->kcfOptions['thumbsDir'];
        $this->clientOptions['thumbsSelector'] = '#' . $this->getThumbsId();
        $this->clientOptions['thumbTemplate'] = $this->thumbTemplate;

        $this->buttonOptions['id'] = $this->getButtonId();

        Html::addCssClass($this->options, 'form-control');
        Html::addCssClass($this->buttonOptions, 'btn btn-default');
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $this->registerClientScript();

        $button = Html::button(FontAwesome::icon('picture-o') . ' ' . $this->buttonLabel, $this->buttonOptions);
        
        if ($this->iframe) {
            $button.= Modal::widget([
                'id' => $this->getIFrameModalId(),
                'header' => Html::tag('h4', $this->modalTitle, ['class' => 'modal-title']),
                'size' => Modal::SIZE_LARGE,
                'options' => [
                    'class' => 'kcfinder-modal',
                    'style' => 'z-index:1060' // will usable if used together with redactor or another widget that has z-index more than 1040
                ],
            ]);
        }

        $thumbs = '';
        if ($this->hasModel() && !empty($this->model->{$this->attribute})) {
            
            $thumbs.= strtr('<input type="hidden" name="{inputName}" value="{inputValue}">', [                    
                    '{inputName}' => str_replace("[]","",$this->getInputName()),
                    '{inputValue}' => null,
                ]); // trick to ensure model value changed when all files removed
            
            if (is_array($this->model->{$this->attribute}))
            {
				$images = $this->model->{$this->attribute};
			}
			else
			{
				$images	= array($this->model->{$this->attribute}); // this will shown a thumb when multiple set false
			}
			
            foreach ($images as $path) {												
				
				if (str_replace([".jpg",".jpeg",".png",".gif"],'',$path) != $path)
				{
					//$path = $this->getThumbSrc($path);						
				}
				else
				{
					$path = $this->clientOptions['kcfUrl']."/themes/default/img/files/big/".substr($path,strrpos($path,".")+1).".png";					
				}
					
                $thumbs.= strtr($this->thumbTemplate, [
                    '{thumbSrc}' => $this->getThumbSrc($path),
                    '{inputName}' => $this->getInputName(),
                    '{inputValue}' => $path,
                ]);
			}            
        }
        $thumbs = Html::tag('ul', $thumbs, ['id' => $this->getThumbsId(), 'class' => 'kcf-thumbs']);

        echo Html::tag('div', strtr($this->template, [
            '{button}' => $button,
            '{thumbs}' => $thumbs,
        ]), ['class' => 'kcf-input-group']);
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        KCFinderWidgetAsset::register($view);
        FontAwesomeAsset::register($view);        
        
        if (!isset($this->clientOptions['kcfUrl']))
        {
			$this->clientOptions['kcfUrl'] = Yii::$app->assetManager->getPublishedUrl((new KCFinderAsset)->sourcePath);
		}	

        if ($this->iframe) {
             $this->clientOptions['iframeModalId'] = $this->getIFrameModalId();
        }

        $clientOptions = Json::encode($this->clientOptions);
        $view->registerJs("jQuery('#{$this->buttonOptions['id']}').KCFinderInputWidget($clientOptions)");
    }

    public function getButtonId()
    {
        return $this->getId() . '-button';
    }

    public function getThumbsId()
    {
        return $this->getId() . '-thumbs';
    }

    public function getIFrameModalId()
    {
        return $this->getId() . '-iframe';
    }

}
