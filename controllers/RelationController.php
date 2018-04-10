<?php

namespace app\controllers;

use app\models\Issue;
use Yii;
use app\models\Relation;
use app\models\RelationSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RelationController implements the CRUD actions for Relation model.
 */
class RelationController extends DefaultController
{

    /**
     * Lists all Relation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RelationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
/**
     * Lists all Relation models.
     * @return mixed
     */
    public function actionIndexfrom()
    {
        $searchModel = new RelationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index_from', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Relation model.
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
     * Creates a new Relation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Relation();

        if ($model->load(Yii::$app->request->post())){
            $relations = null;
            $from_issueModel = Issue::findOne(['id' => $model->from_issue]);
            foreach ($model->to_issues as $to_issue){
                $relation = new Relation();
                $relation->load(Yii::$app->request->post());
                $relation->to_issue = $to_issue;
                $relation->save();
                $to_issueModel = Issue::findOne(['id' => $relation->to_issue]);
                $relations .= "\r\n" . ' <b>' . $to_issueModel->index() . '</b> <b>' . $to_issueModel->name . '</b>';
            }
            $this->sendToTelegram(sprintf('User <b>%s</b> ADDED the new relations in issue: <b>%s</b> ASSOCIATED WITH: %s ' . "\r\n" . 'With comment: <i>%s</i>',
                Yii::$app->user->identity->username,
                @$from_issueModel->index() . ' ' . @$from_issueModel->name,
                @$relations,
                @$model->comment
            ));

            return $this->redirect(Url::previous());
        }

        return $this->redirect(Url::previous());
        /*if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);*/
    }

    /**
     * Updates an existing Relation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Relation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Url::previous());
    }

    /**
     * Finds the Relation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Relation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Relation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}