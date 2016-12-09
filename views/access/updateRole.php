<?php
namespace mixartemev\db_rbac\views\access;

use common\components\RbacManager;
use leandrogehlen\treegrid\TreeGrid;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\rbac\Item;
use yii\rbac\Role;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var Role $role */
/* @var array $permitted_roles */
/* @var array $roles all */
/* @var array $permitted_permissions */
/* @var array $permissions all */

$this->title = Yii::t('db_rbac', 'Редактирование роли: ') . ' ' . $role->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('db_rbac', 'Управление ролями'), 'url' => ['role']];
$this->params['breadcrumbs'][] = Yii::t('db_rbac', 'Редактирование');

$this->registerJs("
    //что бы кликались сразу все одноименные чекбоксы
    /*$('input[type=checkbox]').click(function(){
        $('input[value='+this.value+']').trigger('click')
        console.log(this.value);
    });*/
");
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

        ActiveForm::begin();
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
        $directRoles = $auth->firstChildrenArray($role->name, [Item::TYPE_ROLE]);
        $directPermissions = $auth->firstChildrenArray($role->name, [Item::TYPE_PERMISSION]);
        ?>
        <div class="row">
            <div class="form-group col-xs-5">
                <?= Html::label(Yii::t('db_rbac', 'Название роли')); ?>
                <?= Html::textInput('name', $role->name); ?>
            </div>

            <div class="form-group col-xs-7">
                <?= Html::label(Yii::t('db_rbac', 'Текстовое описание')); ?>
                <?= Html::textInput('description', $role->description); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-7">
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
                        //['attribute' => 'parent', 'header' => 'Родитель'],
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'name' => 'permissions',
                            'checkboxOptions' => function($model) use ($permitted_permissions, $directPermissions){
                                $checked = ['checked' => in_array($model['name'], $permitted_permissions) ? true : false];//$model['parent']==$directRole //is first-level child
                                //$disabled = ['disabled' => in_array($model['parent'], $permitted_permissions) ? true : false]; //если родитель в массиве всех доступных разрешений - дисейблим. так работает только если юзеру назначена только одна роль
                                $disabled = ['disabled' => in_array($model['name'], $directPermissions) || !in_array($model['name'], $permitted_permissions) ? false: true];
                                $hidden = ['hidden' => $model['type'] == 1 ? true : false];
                                return array_merge(['value' => $model['name']], $checked, $disabled, $hidden);
                            },
                        ],
                        //['class' => 'yii\grid\ActionColumn']
                    ]
                ]);
                ?>
            </div>
            <?= ''//Html::checkboxList('permissions', $permitted_permissions, $permissions, ['separator' => '<br>']); ?>

            <div class="form-group col-lg-5">
                <h4>Роли</h4>
                <?= TreeGrid::widget([
                    'dataProvider' => new ArrayDataProvider(['allModels' => $adjacencyListRoles]),
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
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'name' => 'roles',
                            'checkboxOptions' => function($model) use ($permitted_roles, $directRoles){
                                $checked = ['checked' => in_array($model['name'], $permitted_roles) ? true : false];//$model['parent']==$directRole //is first-level child
                                //$disabled = ['disabled' => in_array($model['parent'], $permitted_roles) ? true : false]; //если родитель в массиве всех доступных разрешений - дисейблим. так работает только если юзеру назначена только одна роль
                                $disabled = ['disabled' => in_array($model['name'], $directRoles) || !in_array($model['name'], $permitted_roles) ? false: true];
                                return array_merge(['value' => $model['name']], $checked, $disabled);
                            },
                        ],
                        //['class' => 'yii\grid\ActionColumn']
                    ]
                ]);
                ?>
                <?= ''//Html::checkboxList('permissions', $permitted_roles, $roles, ['separator' => '<br>']); ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('db_rbac', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
