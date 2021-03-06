<?php
namespace mixartemev\db_rbac\views\access;

use common\components\RbacManager;
use leandrogehlen\treegrid\TreeGrid;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Yii::t('db_rbac', 'Разрешения');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default">
    <div class="box-body">
    <p>
        <?= Html::a(Yii::t('db_rbac', 'Добавить новое правило'), ['add-permission'], ['class' => 'btn btn-success']) ?>
    </p>
<?php
/** @var RbacManager $auth */
$auth = Yii::$app->authManager;

$dataProvider = new ArrayDataProvider([
    'allModels' => $auth->allItems(),
    'sort' => [
        'attributes' => ['name', 'description'],
    ],
    'pagination' => [
        'pageSize' => 5000,
    ]
]);
?>
<?= TreeGrid::widget([
    'dataProvider' => $dataProvider,
    'keyColumnName' => 'name',
    'parentColumnName' => 'parent',
    'parentRootValue' => null, //first parentId value
    'pluginOptions' => [
        'initialState' => 'collapsed',
    ],
    'columns' => [
        ['attribute' => 'name', 'header' => 'Разрешение'/*, 'headerOptions' => ['width' => '35%']*/],
        ['attribute' => 'description', 'header' => 'Описание'],
        ['attribute' => 'parent', 'header' => 'Родитель'],
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'buttons' =>
                [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::toRoute(['update-permission', 'name' => $model['name']]), [
                            'title' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                        ]); },
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['delete-permission','name' => $model['name']]), [
                            'title' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    }
                ]
        ]
    ]
]) ?>
    </div>
</div>
