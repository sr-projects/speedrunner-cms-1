<?php

namespace common\actions\crud;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use common\services\FileService;


class FileDeleteAction extends Action
{
    public array $allowed_attributes = [];
    
    public function run()
    {
        $attr = Yii::$app->request->get('attr');
        
        if (!in_array($attr, $this->allowed_attributes)) {
            return $this->controller->redirect(Yii::$app->request->referrer);
        }
        
        if (!($model = $this->controller->findModel())) {
            return $this->controller->redirect(Yii::$app->request->referrer);
        }
        
        $images = is_array($model->{$attr}) ? $model->{$attr} : [$model->{$attr}];
        $key = array_search(Yii::$app->request->post('key'), $images);
        
        if ($key !== false) {
            FileService::delete($images[$key]);
            unset($images[$key]);
            
            return $model->updateAttributes([
                $attr => is_array($model->{$attr}) ? array_values($images) : '',
            ]);
        }
    }
}