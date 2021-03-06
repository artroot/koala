<?php

namespace app\modules\admin\controllers;

use app\models\User;
use app\modules\admin\models\Notifyrule;
use app\modules\admin\models\Telegram;
use Yii;
use app\models\Users;
use app\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends DefaultController
{

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();
        //$model = User::findOne(['id' => $id]);
        $old = $model->telegram_key;

        if ($model->load(Yii::$app->request->post())){
            //$model->changePass();
            //$model->setPassword($model->password);
            //$model->generateAuthKey();
            $model->status = User::STATUS_ACTIVE;
            $model->password_hash = Yii::$app->security->generatePasswordHash($model->new_password);
            $model->auth_key = Yii::$app->security->generateRandomString();

            if ($model->save()) {

                foreach ([3, 1, 2, 0] as $chapter) {
                    $rule = new Notifyrule();
                    $rule->setAttributes([
                        'user_id' => $model->id,
                        'chapter' => $chapter,
                        'mail' => 0,
                        'telegram' => 0,
                        'owner' => 0,
                        'performer' => 0,
                        'all' => 0,
                        'create' => 0,
                        'update' => 0,
                        'delete' => 0,
                        'done' => 0
                    ]);
                    $rule->save();
                }

                if ($old != $model->telegram_key and !empty($model->telegram_key)){
                    Telegram::sendMessage(base64_decode($model->telegram_key), 'Your bot has been successfully activated!');
                    Telegram::sendMessage(base64_decode($model->telegram_key),
                        'Show all projects list - /projectList');
                }
                $this->sendToTelegram(sprintf('Was ADDED the new <b>%s</b>: %s <i>(%s %s)</i>',
                    $model->getUsertype()->one()->name,
                    $model->username,
                    $model->first_name,
                    $model->last_name
                ));
                return $this->renderPartial('update', [
                    'model' => $model,
                    'msg' => 'Success!',
                ]);
            }

        }
        print_r($model->errors);
        return $this->renderAjax('create', [
            'model' => $model,
            'action' => '/admin/users/create',
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $old = $model->telegram_key;

        if ($model->load(Yii::$app->request->post())){
            if ($model->password && $model->new_password) $model->password_hash = Yii::$app->security->generatePasswordHash($model->new_password);

            if ($model->save()) {
                if ($old != $model->telegram_key and !empty($model->telegram_key)){
                    Telegram::sendMessage(base64_decode($model->telegram_key), 'Your bot has been successfully activated!');
                    Telegram::sendMessage(base64_decode($model->telegram_key),
                        'Show all projects list - /projectList');
                }
                return $this->renderPartial('update', [
                    'model' => $model,
                    'msg' => 'Success!',
                ]);
            }

        }

        return $this->renderPartial('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->status = $model->status == User::STATUS_DELETED ? User::STATUS_ACTIVE : User::STATUS_DELETED;

        $model->save(false);

        return $this->redirect(['/admin/settings/users']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
