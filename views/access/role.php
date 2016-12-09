<?php

namespace mixartemev\db_rbac\views\access;

use common\components\RbacManager;
use leandrogehlen\treegrid\TreeGrid;
use Yii;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\grid\DataColumn;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

/* @var array $roles all */

$this->title = Yii::t('db_rbac', 'Управление ролями');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default">
    <div class="box-body">
    <p>
        <?= Html::a(Yii::t('db_rbac', 'Добавить роль'), ['add-role'], ['class' => 'btn btn-success']) ?>
    </p>
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
        $dataProvider = new ArrayDataProvider([
            'allModels' => $adjacencyListRoles,
            'sort' => [
                'attributes' => ['name', 'description'],
            ],
            'pagination' => [
                'pageSize' => 50,
            ]
        ]);
        ?>
        <?= TreeGrid::widget([
            'dataProvider' => $dataProvider,
            'keyColumnName' => 'name',
            'parentColumnName' => 'parent',
            'parentRootValue' => null, //first parentId value
            'pluginOptions' => [
                //'initialState' => 'collapsed',
            ],
            'columns' => [
                ['attribute' => 'name', 'header' => 'Роль'],
                ['attribute' => 'description', 'header' => 'Описание'],
                ['attribute' => 'parent', 'header' => 'Родитель'],
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' =>
                        [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::toRoute(['update-role', 'name' => $model['name']]), [
                                    'title' => Yii::t('yii', 'Update'),
                                    'data-pjax' => '0',
                                ]); },
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['delete-role','name' => $model['name']]), [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                ]);
                            }
                        ]
                ],
            ]
        ]) ?>
    </div>
</div>
