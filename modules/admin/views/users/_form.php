<?php

    use app\models\Usertype;
use app\modules\admin\models\Notifyrule;
use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">
    <?php $form = ActiveForm::begin(['id' => 'userForm', 'action' => @$action ? [$action] : ['/admin/users/update?id=' . $model->id]]); ?>
    <h4 id="mainSettings">Main</h4>


    <?= $form->field($model, 'usertype_id')->dropDownList(ArrayHelper::map(Usertype::find()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'new_password')->passwordInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'conf_password')->passwordInput(['maxlength' => true]) ?>

    <h4 id="notifySettings">Notifications</h4>

    <div class="thumbnail">

        <?= $form->field($model, 'mail_notify')->checkbox(['value' => 1, 'onchange' => 'if($(this).prop(\'checked\')){$(\'#users-email\').prop(\'disabled\', false);}else{$(\'#users-email\').prop(\'disabled\', true);}']) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'disabled' => ($model->mail_notify ? false : true)]) ?>

    </div>

    <div class="thumbnail">

        <?= $form->field($model, 'telegram_notify')->checkbox(['value' => 1, 'onchange' => 'if($(this).prop(\'checked\')){$(\'#users-telegram_key\').prop(\'disabled\', false);}else{$(\'#users-telegram_key\').prop(\'disabled\', true);}']) ?>

        <?= $form->field($model, 'telegram_key')->textInput(['disabled' => ($model->telegram_notify ? false : true)]) ?>

    </div>

    <?php ActiveForm::end(); ?>

    <h4 id="notifyFilterRulesSettings">Notification filter rules</h4>
    <?= $this->render('@app/modules/admin/views/notifyrule/index', [
        'models' => Notifyrule::findAll(['user_id' => $model->id])
    ]) ?>

    <?php if (@$msg): ?>
        <div class="alert alert-success" role="alert"><?= $msg ?></div>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'form' => 'userForm']) ?>
    </div>

</div>
