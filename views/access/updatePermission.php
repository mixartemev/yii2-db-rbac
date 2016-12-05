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

    <h1><?= Html::encode($this->title) ?></h1>

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
        /** @var RbacManager $auth */
        $auth = Yii::$app->authManager;
        $query = $auth->tree();
        echo '<pre>';
        var_dump($query->all());
        echo '</pre>';
        $dataProvider = new ActiveDataProvider([
            'models' => $query->all(),
            'pagination' => false
        ]);
        /*$array = [
            ['child' => 4, 'parent' => 0],
            ['child' => 5, 'parent' => 4],
            ['child' => 6, 'parent' => 5]
        ];
        $dataProvider = new ArrayDataProvider([
            'allModels' => $array,
            'pagination' => false
        ]);

        foreach ($query->all() as $key => $row){
            $array[] = [
                'id' => $key,
                'description' => $row['description'],
                'parent' => $row['parent'],
                'child' => $row['child'],
            ];
        }
        */
        ?>

        <?= TreeGrid::widget([
            'dataProvider' => $dataProvider,
            'keyColumnName' => 'child',
            'parentColumnName' => 'parent',
            'parentRootValue' => 'admin', //first parentId value
            'pluginOptions' => [
                'initialState' => 'collapsed',
            ],
            'columns' => [
                //'name',
                //'id',
                'parent',
                'child',
                'description',
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'permissions',
                    'checkboxOptions' => function($model) use ($role_permit) {
                        return [
                            'value' => $model['child'],
                            'checked' => in_array($model['child'], $role_permit) ? true : false,
                        ];
                    },

                ],
                //['class' => 'yii\grid\ActionColumn']
            ]
        ]);
        ?>

        <div class="form-group">
            <?= Html::label(Yii::t('db_rbac', 'Название роли')); ?>
            <?= Html::textInput('name', $role->name); ?>
        </div>

        <div class="form-group">
            <?= Html::label(Yii::t('db_rbac', 'Текстовое описание')); ?>
            <?= Html::textInput('description', $role->description); ?>
        </div>

        <div class="form-group col-md-6">
            <?= Html::label(Yii::t('db_rbac', 'Роли')); ?>
            <?= Html::checkboxList('permissions', $role_permit, $permissions, ['separator' => '<br>']); ?>
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
