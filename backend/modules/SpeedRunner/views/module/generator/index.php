<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title = 'Module Generator';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'SpeedRunner'), 'url' => ['/speedrunner/speedrunner']];
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<?php $form = ActiveForm::begin([
    'id' => 'edit-form'
]); ?>

<h2 class="main-title">
    <?= $this->title ?>
    <?= Html::submitButton(
        Html::tag('i', null, ['class' => 'fas fa-file-code']) . Yii::t('app', 'Generate'),
        ['class' => 'btn btn-primary btn-icon float-right']
    ) ?>
</h2>

<div class="row">
    <div class="col-lg-2 col-md-3">
        <ul class="nav flex-column nav-pills main-shadow" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#tab-general">
                    <?= Yii::t('app', 'General') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#tab-controller">
                    <?= Yii::t('app', 'Controller') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#tab-model">
                    <?= Yii::t('app', 'Model') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#tab-view">
                    <?= Yii::t('app', 'View') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#tab-use">
                    <?= Yii::t('app', 'Use') ?>
                </a>
            </li>
        </ul>
    </div>
    
    <div class="col-lg-10 col-md-9 mt-3 mt-md-0">
        <div class="tab-content main-shadow p-3">
            <div id="tab-general" class="tab-pane active">
                <?= $form->field($model, 'module_name', ['enableClientValidation' => false])->textInput() ?>
                
                <?= $form->field($model, 'generate_files')->widget(Select2::classname(), [
                    'data' => $model->generateFiles,
                    'options' => [
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]); ?>
            </div>
            
            <div id="tab-controller" class="tab-pane fade">
                <?= $form->field($model, 'controller_name')->textInput() ?>
                
                <?= $form->field($model, 'controller_actions')->widget(Select2::classname(), [
                    'data' => $model->controllerActions,
                    'options' => [
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ],
                ]); ?>
            </div>
            
            <div id="tab-model" class="tab-pane fade">
                <?= $form->field($model, 'table_name')->dropDownList($tables, [
                    'data-toggle' => 'selectpicker',
                    'prompt' => ' '
                ]) ?>
                
                <?= $form->field($model, 'with_translation', [
                    'checkboxTemplate' => Yii::$app->params['switcher_template'],
                ])->checkbox([
                    'class' => 'custom-control-input'
                ])->label(null, [
                    'class' => 'custom-control-label'
                ]) ?>
                
                <?= $form->field($model, 'has_seo_meta', [
                    'checkboxTemplate' => Yii::$app->params['switcher_template'],
                ])->checkbox([
                    'class' => 'custom-control-input'
                ])->label(null, [
                    'class' => 'custom-control-label'
                ]) ?>
                <hr>
                
                <?= $this->render('_model_relations') ?>
                <hr>
                
                <?= $this->render('_view_relations', ['tables' => $tables]) ?>
            </div>
            
            <div id="tab-view" class="tab-pane fade">
                <div id="generatorform-attrs-result"></div>
            </div>
            
            <div id="tab-use" class="tab-pane fade">
                <?= $this->render('_use') ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>


<script>
    window.onload = function() {
        var el, action, sendData;
        
        $('#generatorform-module_name').on('change', function() {
            $('#generatorform-table_name').val($(this).val()).trigger('change');
            $('#generatorform-controller_name').val($(this).val());
        });
        
//        ------------------------------------------------
        
        function getAttrsFields() {
            action = '<?= Yii::$app->urlManager->createUrl(['speedrunner/module/generator/attrs-fields']) ?>';
            sendData = {
                "table_name": $('#generatorform-table_name').val(),
                "with_translation": $('#generatorform-with_translation').prop('checked') ? 1 : 0,
                "_csrf-backend": $('meta[name=csrf-token]').attr('content')
            };
            
            if (!['', null].includes($('#generatorform-table_name').val())) {
                $.post(action, sendData, function(data) {
                    $('#generatorform-attrs-result').html(data);
                });
            }
        }
        
        $('#generatorform-table_name').on('change', function() {
            getAttrsFields();
        });
        
        $('#generatorform-with_translation').on('change', function() {
            getAttrsFields();
        });
        
//        ------------------------------------------------
        
        $(document).on('change', '.model_relations-name', function() {
            $(this).parents('tr').find('.model_relations-model').val($(this).val());
        });
        
//        ------------------------------------------------
        
        $(document).on('click', '.btn-add[data-table=use]', function(e) {
            el = $(this);
            
            setTimeout(function() {
                el.parents('table').find('.selectpicker-type-2').not('.select2-hidden-accessible').select2({
                    tags: true,
                    closeOnSelect: false
                });
                
                el.parents('table').find('.selectpicker-type-2').on('select2:select', function(e) {
                    $(e.target).data('select2').dropdown.$search.val(e.params.data.text).focus();
                });
            }, 0);
        });
    };
</script>


<style>
    table th,
    table td {
        vertical-align: middle !important;
    }
    
    .attr-mover {
        left: 7px;
    }
</style>