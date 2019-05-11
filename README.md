KCFinder for Yii2
=================

[KCFinder](http://kcfinder.sunhater.com/) for Yii2.

WARNING : I don't have time actually to maintain this repository, but take a [look here](https://github.com/iutbay/mm) (WIP)...

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
You should use extended CKEditor widget ```iutbay\yii2kcfinder\CKEditor```, e.g. :

```php
\iutbay\yii2kcfinder\CKEditor::widget();
```
Widget has enableKCFinder option for enable\disable KCFinder.

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
