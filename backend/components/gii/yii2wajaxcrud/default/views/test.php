<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \wodrow\wajaxcrud\generators\crud\Generator */
/* @var $model \yii\db\ActiveRecord */

$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form \kartik\form\ActiveForm */
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-test">

    <div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

        <?= "<?php " ?>$form = ActiveForm::begin(); ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>

        <?='<?php if (!Yii::$app->request->isAjax){ ?>'."\n"?>
        <div class="form-group">
            <?= "<?= " ?>Html::submitButton("test", ['class' => 'btn btn-primary']) ?>
        </div>
        <?="<?php } ?>\n"?>

        <?= "<?php " ?>ActiveForm::end(); ?>

    </div>

</div>
