<?php

namespace common\forms;

use Yii;
use yii\base\Model;
use backend\modules\User\models\User;


class LoginForm extends Model
{
    public $username;
    public $password;
    public $remember_me = true;
    
    private $_user;
    
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['remember_me'], 'boolean'],
            [['password'], 'validatePassword'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'remember_me' => Yii::t('app', 'Remember me'),
        ];
    }
    
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Incorrect password'));
            }
        }
    }
    
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->remember_me ? 3600 * 24 * 30 : 0);
        }
        
        return false;
    }
    
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }
        
        return $this->_user;
    }
}
