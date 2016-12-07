<?php
namespace mixartemev\db_rbac\views\access;

use leandrogehlen\treegrid\TreeGrid;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\rbac\DbManager;
use yii\rbac\Item;
use yii\rbac\Role;
use yii\widgets\ActiveForm;

/* @var Role $role */
/* @var array $role_permit array of permitted permissions */
/* @var array $permissions array of all permissions */

$this->title = Yii::t('db_rbac', 'Редактирование роли: ') . ' ' . $role->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('db_rbac', 'Управление ролями'), 'url' => ['role']];
$this->params['breadcrumbs'][] = Yii::t('db_rbac', 'Редактирование');
?>
<div class="news-index">
    <div class="links-form">
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
        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;
        $userId = Yii::$app->user->id;
        $myRoles = $auth->getChildRoles($role->name);
        $myPermissions = $auth->getPermissionsByRole($role->name);

        #$roles = \yii\helpers\ArrayHelper::map($auth->getRoles(), 'name', 'description');
        #$child_roles = array_keys($auth->getChildRoles($role->name));

        $permissionsProvider = new ArrayDataProvider([
            'allModels' => $auth->childrenList() //todo метод не из стандартного менеджера
        ]);
        $rolesProvider = new ArrayDataProvider([
            'allModels' => $auth->childrenList([Item::TYPE_ROLE])
        ]);
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
            <h4>Доступные разрешения</h4>
            <?= TreeGrid::widget([
                'dataProvider' => $permissionsProvider,
                'keyColumnName' => 'name',
                'parentColumnName' => 'parent',
                'parentRootValue' => $role->name,//'admin', //first parentId value
                'columns' => [
                    [
                        'attribute' => 'name',
                        'header' => 'Роль',
                    ],
                    [
                        'attribute' => 'description',
                        'header' => 'Описание',
                    ],
                    [
                        'attribute' => 'parent',
                        'header' => 'Родитель',
                    ],
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'name' => 'permissions',
                        'checkboxOptions' => function($model) use ($myPermissions, $role){
                            $opt = $model['type'] == 2 && $model['parent']==$role->name //is permission and first-level child
                                ? ['checked' => in_array($model['name'], array_keys($myPermissions)) ? true : false]
                                : ['hidden' => true];
                            return array_merge(['value' => $model['name']], $opt);
                        },

                    ],
                    //['class' => 'yii\grid\ActionColumn']
                ]
            ]);
            ?>
        </div>
        <?= ''//Html::checkboxList('permissions', $role_permit, $permissions, ['separator' => '<br>']); ?>

        <div class="form-group col-lg-5">
            <h4>Вложенные Роли</h4>
            <?= TreeGrid::widget([
                'dataProvider' => $rolesProvider,
                'keyColumnName' => 'name',
                'parentColumnName' => 'parent',
                'parentRootValue' => $role->name,//'admin', //first parentId value
                'columns' => [
                    [
                        'attribute' => 'name',
                        'header' => 'Роль',
                    ],
                    [
                        'attribute' => 'description',
                        'header' => 'Описание',
                    ],
                    [
                        'attribute' => 'parent',
                        'header' => 'Родитель',
                    ],
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'name' => 'roles',
                        'checkboxOptions' => function($model) use ($myRoles, $role){
                            $opt = $model['parent']==$role->name //is first-level child
                                ? ['checked' => in_array($model['name'], array_keys($myRoles)) ? true : false]
                                : ['hidden' => true];
                            return array_merge(['value' => $model['name']], $opt);
                        },

                    ],
                    //['class' => 'yii\grid\ActionColumn']
                ]
            ]);
            ?>
            <?= ''//Html::checkboxList('permissions', $child_roles, $roles, ['separator' => '<br>']); ?>
        </div>
    </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('db_rbac', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
