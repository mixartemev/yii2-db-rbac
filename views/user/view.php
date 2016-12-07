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
?>
<h3><?=Yii::t('db_rbac', 'Управление ролями пользователя');?> <?= $user->getUserName(); ?></h3>
<?php ActiveForm::begin(['action' => ["update", 'id' => $user->getId()]]); ?>
<h4>Роли</h4>
<?php
/** @var RbacManager $auth */
$auth = Yii::$app->authManager;

$res =[];
$auth->childrenRecursive('admin', $res);
$arr = [];
foreach($roles as $name => $description){
    $parent = array_key_exists($name, $res) ? $res[$name] : null;
    $arr []= ['name' => $name, 'description' => $description, 'parent' => $parent];
}
$myRoles = [];
foreach($user_permit as $directRole){
    $myRoles = array_merge($myRoles, array_keys($auth->getChildRoles($directRole)));
}
$myRoles = array_unique($myRoles);

echo TreeGrid::widget([
    'dataProvider' => new ArrayDataProvider(['allModels' => $arr]),
    'keyColumnName' => 'name',
    'parentColumnName' => 'parent',
    'parentRootValue' => null, //first parentId value
    'pluginOptions' => [
        //'initialState' => 'collapsed',
    ],
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
//var_dump($user_permit);
//var_dump($myRoles);
ActiveForm::end(); ?>
