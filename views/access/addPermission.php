<?php
namespace mixartemev\db_rbac\views\access;

use Yii;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $form ActiveForm */
$this->title = Yii::t('db_rbac', 'Новое разрешение');
$this->params['breadcrumbs'][] = ['label' => Yii::t('db_rbac', 'Разрешения'), 'url' => ['permission']];
$this->params['breadcrumbs'][] = Yii::t('db_rbac', 'Новое разрешение');
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

        <?php ActiveForm::begin(); ?>

        <div class="form-group">
            <?= Html::label(Yii::t('db_rbac', 'Текстовое описание')); ?>
            <?= Html::textInput('description'); ?>
        </div>

        <div class="form-group">
            <?= Html::label(Yii::t('db_rbac', 'Название')); ?>
            <?= Html::textInput('name'); ?>
            <?=Yii::t('db_rbac', '* Формат module/controller/action<br>site/article - доступ к странице site/article<br>site - доступ к любым action контроллера site');?>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('db_rbac', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
