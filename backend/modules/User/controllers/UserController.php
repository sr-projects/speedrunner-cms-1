<?php

namespace backend\modules\User\controllers;

use Yii;
use speedrunner\controllers\CrudController;
use speedrunner\actions as Actions;
use yii\helpers\ArrayHelper;

use backend\modules\User\models\User;


class UserController extends CrudController
{
    public function init()
    {
        $this->model = new User();
        return parent::init();
    }
    
    public function actions()
    {
        $actions = ArrayHelper::filter(parent::actions(), ['index', 'create', 'update', 'delete']);
        
        return ArrayHelper::merge($actions, [
            'file-delete' => [
                'class' => Actions\crud\FileDeleteAction::className(),
                'allowed_attributes' => ['image'],
            ],
            'profile-update' => [
                'class' => Actions\web\FormAction::className(),
                'model' => Yii::$app->user->identity,
                'render_view' => 'profile_update',
                'run_method' => 'save',
                'success_message' => 'Profile has been updated',
                'redirect_route' => ['profile-update'],
            ],
        ]);
    }
}
