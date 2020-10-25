<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\components\framework\grid\GridView;
use kartik\select2\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<h2 class="main-title">
    <?= $this->title ?>
</h2>

<div class="main-shadow p-3">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $modelSearch,
        'rowOptions' => function ($model) {
            return [
                'class' => 'table-' . ArrayHelper::getValue($model->statuses(), "$model->status.class"),
            ];
        },
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => [
                    'style' => 'width: 100px;'
                ],
            ],
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'filter' => Select2::widget([
                    'model' => $modelSearch,
                    'attribute' => 'user_id',
                    'data' => [$modelSearch->user_id => ArrayHelper::getValue($modelSearch->user, 'username')],
                    'options' => ['placeholder' => ' '],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => Yii::$app->urlManager->createUrl(['items-list/users', 'role' => 'registered']),
                            'dataType' => 'json',
                            'delay' => 300,
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                    ]
                ]),
                'value' => function ($model) {
                    return ArrayHelper::getValue($model->user, 'username');
                },
            ],
            [
                'attribute' => 'full_name',
                'format' => 'raw',
                'value' => function ($model) {
                    $result[] = $model->getAttributeLabel('full_name') . ": $model->full_name";
                    $result[] = $model->getAttributeLabel('email') . ": $model->email";
                    $result[] = $model->getAttributeLabel('phone') . ": $model->phone";
                    
                    return implode('<br>', $result);
                }
            ],
            [
                'attribute' => 'delivery_type',
                'format' => 'raw',
                'filter' => $modelSearch->deliveryTypes(),
                'value' => function ($model) {
                    return ArrayHelper::getValue($model->deliveryTypes(), $model->delivery_type);
                }
            ],
            [
                'attribute' => 'payment_type',
                'format' => 'raw',
                'filter' => $modelSearch->paymentTypes(),
                'value' => function ($model) {
                    return ArrayHelper::getValue($model->paymentTypes(), $model->payment_type);
                }
            ],
            [
                'attribute' => 'total_price',
                'format' => 'raw',
                'value' => function ($model) {
                    $result[] = $model->getAttributeLabel('total_quantity') . ": $model->total_quantity";
                    $result[] = Yii::t('app', 'Products price') . ": $model->total_price";
                    $result[] = $model->getAttributeLabel('delivery_price') . ": $model->delivery_price";
                    $result[] = $model->getAttributeLabel('total_price') . ": " . $model->realTotalPrice();
                    
                    return implode('<br>', $result);
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => ArrayHelper::getColumn($modelSearch->statuses(), 'label'),
                'value' => function ($model) {
                    return ArrayHelper::getValue($model->statuses(), "$model->status.label");
                }
            ],
            'created',
            [
                'class' => 'common\components\framework\grid\ActionColumn',
                'template' => '{link} {view} {delete}',
                'buttons' => [
                    'link' => function($url, $model, $key) {
                        return Html::a(
                            Html::tag('i', null, ['class' => 'fas fa-external-link-alt']),
                            Yii::$app->urlManagerFrontend->createUrl(['order/view', 'key' => $model->key]),
                            [
                                'target' => '_blank',
                                'title' => Yii::t('app', 'Link'),
                                'data-toggle' => 'tooltip',
                                'data-pjax' => 0,
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>