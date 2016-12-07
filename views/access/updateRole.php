<?php
namespace mixartemev\db_rbac\views\access;

use common\components\RbacManager;
use leandrogehlen\treegrid\TreeGrid;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Html;
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
<pre>
        <?php ActiveForm::begin();
        /** @var RbacManager $auth */
        $auth = Yii::$app->authManager;
        $userId = Yii::$app->user->id;


        $roles = \yii\helpers\ArrayHelper::map($auth->getRoles(), 'name', 'description');
        $child_roles = array_keys($auth->getChildRoles($role->name));

        $pers = $auth->getPermissions();

        foreach($auth->getRolesByUser($userId) as $role){
            $myRoles[] = ($auth->getChildRoles($role->name));
            $myPerms[] = ($auth->getPermissionsByRole($role->name));
        }

        $query = $auth->tree();

        $dataProvider = new ActiveDataProvider([
            'models' => $query,
            'pagination' => false
        ]);

        print_r($auth->childrenList());
        $res = [];
        //$auth->childrenRecursive('admin', $auth->getChildrenList(), $res);
        print_r($res);
        ?>
</pre>


        <div class="form-group">
            <?= Html::label(Yii::t('db_rbac', 'Название роли')); ?>
            <?= Html::textInput('name', $role->name); ?>
        </div>

        <div class="form-group">
            <?= Html::label(Yii::t('db_rbac', 'Текстовое описание')); ?>
            <?= Html::textInput('description', $role->description); ?>
        </div>

        <div class="form-group col-md-6">
            <?= Html::label(Yii::t('db_rbac', 'Разрешенные доступы')); ?>
            <?= Html::checkboxList('permissions', $child_roles, $roles, ['separator' => '<br>']); ?>
        </div>

        <div class="form-group col-md-6">
            <?= Html::label(Yii::t('db_rbac', 'Разрешенные доступы')); ?>
            <?= Html::checkboxList('permissions', $role_permit, $permissions, ['separator' => '<br>']); ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('db_rbac', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
