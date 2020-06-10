<?php

namespace backend\modules\SpeedRunner\forms\module;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;
use yii\db\Schema;
use yii\db\QueryBuilder;


class DuplicatorForm extends Model
{
    public $duplicate_types = ['files', 'db_tables'];
    
    public $module_name_from;
    public $module_name_to;
    
    public function rules()
    {
        return [
            [['duplicate_types', 'module_name_from', 'module_name_to'], 'required'],
            [['duplicate_types'], 'in', 'range' => array_keys($this->duplicateTypes), 'allowArray' => true],
            [['module_name_from'], 'in', 'range' => array_keys($this->modulesList)],
            [['module_name_to'], 'in', 'range' => $this->modulesList, 'not' => true],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'duplicate_types' => Yii::t('speedrunner', 'Duplicate types'),
            'module_name_from' => Yii::t('speedrunner', 'Module name (from)'),
            'module_name_to' => Yii::t('speedrunner', 'Module name (to)'),
        ];
    }
    
    static function getDuplicateTypes()
    {
        return [
            'files' => 'Files',
            'db_tables' => 'DB tables',
        ];
    }
    
    static function getModulesList()
    {
        foreach (Yii::$app->modules as $key => $m) {
            if (!in_array($key, ['rbac', 'debug', 'gii', 'speedrunner'])) {
                $result[ucfirst($key)] = ucfirst($key);
            }
        }
        
        return $result;
    }
    
    public function duplicate()
    {
        //      FILES
        
        if (in_array('files', $this->duplicate_types)) {
            $name_from = Yii::getAlias("@backend/modules/$this->module_name_from");
            $name_to = Yii::getAlias("@backend/modules/$this->module_name_to");
            
            $replace_arr_from = [$this->module_name_from, strtolower($this->module_name_from)];
            $replace_arr_to = [$this->module_name_to, strtolower($this->module_name_to)];
            
            FileHelper::createDirectory($name_to);
            
            $folders = FileHelper::findDirectories($name_from);
            $files = FileHelper::findFiles($name_from);
            $folders_files = ArrayHelper::merge($folders, $files);
            
            foreach ($folders_files as $f_f) {
                $f_f_new = str_replace($replace_arr_from, $replace_arr_to, $f_f);
                
                if (is_file($f_f)) {
                    copy($f_f, $f_f_new);
                    
                    $file_old_content = file_get_contents($f_f_new);
                    $file_new_content = str_replace($replace_arr_from, $replace_arr_to, $file_old_content);
                    
                    $file_new = fopen($f_f_new, 'w');
                    fwrite($file_new, $file_new_content);
                    fclose($file_new);
                } else {
                    FileHelper::createDirectory($f_f_new);
                }
            }
        }
        
        //      DB
        
        if (in_array('db_tables', $this->duplicate_types)) {
            $connection = Yii::$app->db;
            $queryBuilder = $connection->queryBuilder;
            $dbSchema = $connection->schema;
            $tables = $dbSchema->getTableNames();
            
            foreach ($tables as $t) {
                if (strpos($t, $this->module_name_from) === 0) {
                    $new_table_name = str_replace($this->module_name_from, $this->module_name_to, $t);
                    $columns_tmp = $dbSchema->getTableSchema($t)->columns;
                    $columns = [];
                    
                    if ($dbSchema->getTableSchema($new_table_name, true) !== null) {
                        $sql = $queryBuilder->dropTable($new_table_name);
                        $connection->createCommand($sql)->execute();
                    }
                    
                    foreach ($columns_tmp as $key => $c_t) {
                        if ($c_t->isPrimaryKey) {
                            $columns[$key] = 'pk';
                        } else {
                            $columns[$key] = $c_t->dbType;
                            $columns[$key] .= $c_t->allowNull ? ' NULL' : ' NOT NULL';
                        }
                    }
                    
                    $sql = $queryBuilder->createTable($new_table_name, $columns);
                    $connection->createCommand($sql)->execute();
                }
            }
        }
        
        return true;
    }
}