KCFinder for Yii2
=================

[KCFinder](http://kcfinder.sunhater.com/) for Yii2.

WIP...

Installation
------------
The preferred way to install this helper is through [composer](http://getcomposer.org/download/).

Since kcfinder package do not have stable release on packagist, you should use these settings in your `composer.json` file :

```json
"minimum-stability": "dev",
"prefer-stable": true,
```

After, either run

```
php composer.phar require "iutbay/yii2-kcfinder" "dev-master"
```

or add

```json
"iutbay/yii2-kcfinder" : "dev-master"
```

to the require section of your application's `composer.json` file.

Widget Use
----------

Without model :
```php
use iutbay\yii2kcfinder\KCFinderInputWidget;

echo KCFinderInputWidget::widget([
	'name' => 'image',
]);
```

With model and ActiveForm :
```php
use iutbay\yii2kcfinder\KCFinderInputWidget;

echo $form->field($model, 'images')->widget(KCFinderInputWidget::className(), [
	'multiple' => true,
]);
```

Use with 2amigos/yii2-ckeditor-widget
-------------------------------------
You should extend ```\dosamigos\ckeditor\CKEditor```, e.g. :

```php
namespace app\widgets;

use yii\helpers\ArrayHelper;

use iutbay\yii2kcfinder\KCFinderAsset;

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
		$register = KCFinderAsset::register($this->view);
		$kcfinderUrl = $register->baseUrl;

		$browseOptions = [
			'filebrowserBrowseUrl' => $kcfinderUrl . '/browse.php?opener=ckeditor&type=files',
			'filebrowserUploadUrl' => $kcfinderUrl . '/upload.php?opener=ckeditor&type=files',
		];

		$this->clientOptions = ArrayHelper::merge($browseOptions, $this->clientOptions);
	}

}
```

You should then set KCFinder options using session var, e.g. :

```php
// kcfinder options
// http://kcfinder.sunhater.com/install#dynamic
$kcfOptions = array_merge(KCFinder::$kcfDefaultOptions, [
	'uploadURL' => Yii::getAlias('@web').'/upload',
	'access' => [
		'files' => [
			'upload' => true,
			'delete' => false,
			'copy' => false,
			'move' => false,
			'rename' => false,
		],
		'dirs' => [
			'create' => true,
			'delete' => false,
			'rename' => false,
		],
	],
]);

// Set kcfinder session options
Yii::$app->session->set('KCFINDER', $kcfOptions);
```
