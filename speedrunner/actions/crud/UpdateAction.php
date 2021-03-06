<?php

namespace speedrunner\actions\crud;

use Yii;
use speedrunner\actions\web\FormAction;
use yii\helpers\ArrayHelper;


class UpdateAction extends FormAction
{
    public string $render_view = 'update';
    
    public string $run_method = 'save';
    public ?string $success_message = 'Record has been saved';
    public $redirect_route = ['index'];
    
    public function run($id = null)
    {
        if (!($model = $this->controller->findModel($id))) {
            return $this->controller->redirect(Yii::$app->request->referrer);
        }
        
        $this->model = $model;
        return parent::run();
    }
}
