<?php

use yii\helpers\Html;
use speedrunner\widgets\grid\GridView;

use backend\modules\System\models\SystemLanguage;

$this->title = Yii::t('app', 'System languages');
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<h2 class="main-title">
    <?= $this->title ?>
    <?= Html::a(
        Html::tag('i', null, ['class' => 'fas fa-plus-square']) . Yii::t('app', 'Create'),
        ['create'],
        ['class' => 'btn btn-primary btn-icon float-right']
    ) ?>
</h2>

<div class="main-shadow p-3">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'header' => false,
                'format' => 'raw',
                'filter' => false,
                'value' => fn ($model) => Html::img(Yii::$app->services->image->thumb($model->image, [25, 25], 'resize')),
                'headerOptions' => [
                    'style' => 'width: 40px;'
                ],
            ],
            [
                'attribute' => 'id',
                'headerOptions' => [
                    'style' => 'width: 100px;'
                ]
            ],
            'name',
            'code',
            'is_active:boolean',
            'is_main:boolean',
            'created_at',
            'updated_at',
            [
                'class' => 'speedrunner\widgets\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
