<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title = 'Module duplicator';
$this->params['breadcrumbs'][] = ['label' => 'Speedrunner', 'url' => ['/speedrunner/speedrunner']];
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<?php $form = ActiveForm::begin([
    'id' => 'update-form'
]); ?>

<h2 class="main-title">
    <?= $this->title ?>
    <?= Html::submitButton(
        Html::tag('i', null, ['class' => 'fas fa-file-code']) . 'Duplicate',
        ['class' => 'btn btn-primary btn-icon float-right']
    ) ?>
</h2>

<div class="row">
    <div class="col-lg-2 col-md-3">
        <ul class="nav flex-column nav-pills main-shadow" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#tab-information">
                    Information
                </a>
            </li>
        </ul>
    </div>
    
    <div class="col-lg-10 col-md-9 mt-3 mt-md-0">
        <div class="tab-content main-shadow p-3">
            <div id="tab-information" class="tab-pane active">
                <?= $form->field($model, 'duplicate_types')->widget(Select2::classname(), [
                    'data' => $model->duplicateTypes(),
                    'options' => [
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]); ?>
                
                <?= $form->field($model, 'module_name_from')->dropDownList($model->modulesList(), [
                    'class' => 'form-control',
                    'data-toggle' => 'select2',
                    'prompt' => ' ',
                ]) ?>
                
                <?= $form->field($model, 'module_name_to')->textInput() ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
