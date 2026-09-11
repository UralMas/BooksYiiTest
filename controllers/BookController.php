<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use app\models\Book;
use app\models\Author;
use app\models\BookAuthor;

class BookController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new Book();
        $selectedAuthors = [];

        if ($model->load(Yii::$app->request->post()) && $model->saveWithImageAndAuthors($selectedAuthors)) {
            Yii::$app->session->setFlash('success', 'Книга успешно добавлена');
            return $this->redirect(['site/author', 'id' => $selectedAuthors[0] ?? 0]);
        }
        
        return $this->render('create', [
            'model' => $model,
            'authors' => Author::getListSorted(),
            'selectedAuthors' => $selectedAuthors,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = Book::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Книга не найдена');
        }

        $selectedAuthors = array_column($model->authors, 'id');

        if ($model->load(Yii::$app->request->post()) && $model->saveWithImageAndAuthors($selectedAuthors, true)) {
            Yii::$app->session->setFlash('success', 'Книга успешно обновлена');
            return $this->redirect(['site/author', 'id' => $selectedAuthors[0] ?? 0]);
        }
        
        return $this->render('update', [
            'model' => $model,
            'authors' => Author::getListSorted(),
            'selectedAuthors' => $selectedAuthors,
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = Book::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Книга не найдена');
        }

        $authorId = $model->authors[0]->id ?? 0;
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Книга успешно удалена');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить книгу');
        }

        return $this->redirect(['site/author', 'id' => $authorId]);
    }
}
