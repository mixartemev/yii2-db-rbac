<?php
namespace mixartemev\db_rbac\views\access;
use common\components\RbacManager;
use leandrogehlen\treegrid\TreeGrid;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\rbac\Permission;
use yii\widgets\ActiveForm;
/* @var $this \yii\web\View */
/* @var $form \yii\widgets\ActiveForm */
/* @var Permission $permit */
/* @var array $direct_permitted_permissions */
/* @var array $all_permitted_permissions */
$this->title = Yii::t('db_rbac', 'Редактирование разрешения: ') . ' ' . $permit->description;
$this->params['breadcrumbs'][] = ['label' => Yii::t('db_rbac', 'Разрешения'), 'url' => ['permission']];
$this->params['breadcrumbs'][] = Yii::t('db_rbac', 'Редактирование разрешения');
?>
<div class="box box-default">
    <div class="box-body">
        <?php
        if (!empty($error)) {
            ?>
            <div class="error-summary">
                <?php
                echo implode('<br>', $error);
                ?>
            </div>
        <?php
        }
        ?>

        <?php ActiveForm::begin();
        /** @var RbacManager $auth */
        $auth = Yii::$app->authManager;
        $all_permitted_permissions []= $permit->name;
        ?>
        <div class="row">
            <div class="form-group col-xs-5">
                <?= Html::label(Yii::t('db_rbac', 'Название')); ?>
                <?= Html::textInput('name', $permit->name); ?>
            </div>
            <div class="form-group col-xs-5">
                <?= Html::label(Yii::t('db_rbac', 'Текстовое описание')); ?>
                <?= Html::textInput('description', $permit->description); ?>
            </div>
        </div>
        <div class="form-group">
            <h4>Разрешения</h4>
            <?= TreeGrid::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $auth->allItems(),//$adjacencyListPermissions,
                    'pagination' => [
                        'pageSize' => 50,
                    ]
                ]),
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
                        'name' => 'permissions',
                        'checkboxOptions' => function($model) use ($all_permitted_permissions, $direct_permitted_permissions){
                            $checked = ['checked' => in_array($model['name'], $all_permitted_permissions) ? true : false];//$model['parent']==$directRole //is first-level child
                            //$disabled = ['disabled' => in_array($model['parent'], $all_permitted_permissions) ? true : false]; //если родитель в массиве всех доступных разрешений - дисейблим. так работает только если юзеру назначена только одна роль
                            $disabled = ['disabled' => in_array($model['name'], $direct_permitted_permissions) || !in_array($model['name'], $all_permitted_permissions) ? false: true];
                            $hidden = ['hidden' => $model['type'] == 1 ? true : false];
                            return array_merge(['value' => $model['name']], $checked, $disabled, $hidden);
                        },
                    ],
                    //['class' => 'yii\grid\ActionColumn']
                ]
            ]);
            ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('db_rbac', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
