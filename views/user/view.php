<?php
namespace mixartemev\db_rbac\views\user;

use mixartemev\db_rbac\interfaces\UserRbacInterface;
use Yii;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var UserRbacInterface $user */
/* @var array $user_permit array of permitted roles */
/* @var array $roles array of all role */
?>
<h3><?=Yii::t('db_rbac', 'Управление ролями пользователя');?> <?= $user->getUserName(); ?></h3>
<?php ActiveForm::begin(['action' => ["update", 'id' => $user->getId()]]); ?>

<?= Html::checkboxList('roles', $user_permit, $roles, ['separator' => '<br>']); ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('db_rbac', 'Сохранить'), ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

