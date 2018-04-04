<?php

    use app\models\Issuepriority;
    use app\models\Issuestatus;
    use app\models\Issuetype;
    use app\models\Project;
    use app\models\Sprint;
use app\models\State;
use app\models\Users;
    use app\models\Version;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\jui\DatePicker;
    use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Issue */
/* @var $form yii\widgets\ActiveForm */


$issueName = Project::findOne(['id' => $model->project_id])->name . '-' . $model->id;

?>
<br>
<div class="issue-form">

    <?php $form = ActiveForm::begin(['id' => 'issueForm', 'action' => $action]); ?>

    <div class="row">
        <div class="col-md-8">
            <a class="btn btn-default btn-xs" onclick="$('#issue_descr').toggle('fast'); $('#issue-name-s').toggle('fast');">
                <span class="glyphicon glyphicon-edit"></span>
            </a>
            <h4>
                <?= Issuestatus::findOne([
                    'id' => $model->issuestatus_id
                ])->state_id == State::DONE ? sprintf('<s>%s</s>', $issueName) : $issueName  ?>
                <span> <?= $model->name ?></span>
            </h4>

            <span id="issue-name-s" style="display: none;">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            </span>

            <p id="issue_descr"><?= nl2br($model->description) ?></p>
        </div>
        <div class="col-md-4 img-thumbnail">
            <table>
                <?php
                    $template = [
                        'template' => "<tr><td>{label}</td><td>{input}\n{hint}\n{error}</td></tr>"
                    ];
                    ?>
            <?= $form->field($model, 'project_id', $template)->dropDownList(ArrayHelper::map(Project::find()->all(), 'id', 'name'), ['class' => 'btn btn-link']) ?>
            <?= $form->field($model, 'issuepriority_id', $template)->dropDownList(ArrayHelper::map(Issuepriority::find()->all(), 'id', 'name'), ['class' => 'btn btn-link']) ?>
            <?= $form->field($model, 'issuetype_id', $template)->dropDownList(ArrayHelper::map(Issuetype::find()->all(), 'id', 'name'), ['class' => 'btn btn-link']) ?>
            <?= $form->field($model, 'issuestatus_id', $template)->dropDownList(ArrayHelper::map(Issuestatus::find()->all(), 'id', 'name'), ['class' => 'btn btn-link']) ?>
            <?= $form->field($model, 'performer_id', $template)->dropDownList(ArrayHelper::map(Users::find()->all(), 'id', 'username'),
                ['prompt' => 'Not set', 'class' => 'btn btn-link']) ?>
            <?= $form->field($model, 'detected_version_id', $template)->dropDownList(ArrayHelper::map(
                Version::find()->where(['project_id' => $model->project_id])->all(), 'id', 'name'),
                ['prompt' => 'Not set', 'class' => 'btn btn-link']) ?>
            <?= $form->field($model, 'resolved_version_id', $template)->dropDownList(ArrayHelper::map(
                    Version::find()->where(['project_id' => $model->project_id])->all(), 'id', 'name'),
                    ['prompt' => 'Not set', 'class' => 'btn btn-link']) ?>
            <?= $form->field($model, 'sprint_id', $template)->dropDownList(ArrayHelper::map(
                Sprint::find()->all(), 'id', 'name'),
                ['prompt' => 'Not set', 'class' => 'btn btn-link']) ?>

            <?= $form->field($model, 'deadline', $template)->input('datetime-local') ?>
            </table>
        </div>
    </div>



    <?= $form->field($model, 'create_date')->textInput() ?>

    <?= $form->field($model, 'finish_date')->textInput() ?>






    <?= $form->field($model, 'version_id')->textInput() ?>








    <?= $form->field($model, 'parentissue_id')->textInput() ?>

    <?= $form->field($model, 'relatedissue_id')->textInput() ?>



    <?php ActiveForm::end(); ?>



</div>
