<?php

namespace mixartemev\db_rbac\views\user;

use common\components\RbacManager;
use leandrogehlen\treegrid\TreeGrid;
use mixartemev\db_rbac\interfaces\UserRbacInterface;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var UserRbacInterface $user */
/* @var array $user_permit array of direct assigned roles */
/* @var array $roles array of all role */

$this->title = Yii::t('db_rbac', 'Управление ролями пользователя') . ': ' . $user->getUserName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Пользователи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $user->getUserName(), 'url' => ['view', 'id' => $user->getId()]];
$this->params['breadcrumbs'][] = Yii::t('db_rbac', 'Управление ролями пользователя');
?>
<div class="box box-default">
    <div class="box-body">
        <?php ActiveForm::begin(['action' => ["update", 'id' => $user->getId()]]); ?>
        <h4>Роли</h4>
        <?php
        /** @var RbacManager $auth */
        $auth = Yii::$app->authManager;
        /** массив всех родственных связей AuthItem-ов */
        $childList = $auth->childrenRowList();
        /** получаем из него Adjacency List ролей */
        $adjacencyListRoles = [];
        foreach($roles as $name => $description){
            $parent = null;
            foreach($childList as &$r){
                if($name == $r['name']){
                    $parent = $r['parent'];
                    unset($r);
                    $adjacencyListRoles []= ['name' => $name, 'description' => $description, 'parent' => $parent];
                }
            }
            if(!$parent){
                $adjacencyListRoles []= ['name' => $name, 'description' => $description, 'parent' => $parent];
            }
        }
        $myRoles = [];
        foreach($user_permit as $directRole){
            $myRoles = array_merge($myRoles, array_keys($auth->getChildRoles($directRole)));
        }
        $myRoles = array_unique($myRoles);
        echo TreeGrid::widget([
            'dataProvider' => new ArrayDataProvider(['allModels' => $adjacencyListRoles]),
            'keyColumnName' => 'name',
            'parentColumnName' => 'parent',
            'parentRootValue' => null, //first parentId value
            'pluginOptions' => [
                //'initialState' => 'collapsed',
            ],
            'columns' => [
                ['attribute' => 'name', 'header' => 'Разрешение'],
                ['attribute' => 'description', 'header' => 'Описание'],
                ['attribute' => 'parent', 'header' => 'Родитель'],
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'roles',
                    'checkboxOptions' => function($model) use ($myRoles, $user_permit){
                        $checked = ['checked' => in_array($model['name'], $myRoles) ? true : false];//$model['parent']==$directRole //is first-level child
                        //$disabled = ['disabled' => in_array($model['parent'], $myRoles) ? true : false]; //если родитель в массиве всех доступных разрешений - дисейблим. так работает только если юзеру назначена только одна роль
                        $disabled = ['disabled' => in_array($model['name'], $user_permit) || !in_array($model['name'], $myRoles) ? false: true];
                        return array_merge(['value' => $model['name']], $checked, $disabled);
                    },
                ],
                //['class' => 'yii\grid\ActionColumn']
            ]
        ]);
        ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('db_rbac', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>
        <?php
        ActiveForm::end(); ?>
    </div>
</div>
