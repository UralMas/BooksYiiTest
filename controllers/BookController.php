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

        if ($model->load(Yii::$app->request->post())) {
            $selectedAuthors = Yii::$app->request->post('selectedAuthors', []);
            
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->validate()) {
                $model->save();
                
                // Сохраняем связи с авторами
                if (!empty($selectedAuthors)) {
                    foreach ($selectedAuthors as $authorId) {
                        $bookAuthor = new BookAuthor();
                        $bookAuthor->book_id = $model->id;
                        $bookAuthor->author_id = $authorId;
                        $bookAuthor->save();
                    }
                }
                
                // Загружаем картинку
                if ($model->imageFile) {
                    $model->upload();
                    $model->save(false);
                }
                
                Yii::$app->session->setFlash('success', 'Книга успешно добавлена');
                return $this->redirect(['site/author', 'id' => $selectedAuthors[0] ?? 0]);
            }
        }

        $authors = Author::find()->orderBy(['full_name' => SORT_ASC])->all();
        
        return $this->render('create', [
            'model' => $model,
            'authors' => $authors,
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
        $oldImage = $model->cover_image;

        if ($model->load(Yii::$app->request->post())) {
            $selectedAuthors = Yii::$app->request->post('selectedAuthors', []);
            
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->validate()) {
                // Удаляем старые связи
                BookAuthor::deleteAll(['book_id' => $model->id]);
                
                // Добавляем новые связи
                if (!empty($selectedAuthors)) {
                    foreach ($selectedAuthors as $authorId) {
                        $bookAuthor = new BookAuthor();
                        $bookAuthor->book_id = $model->id;
                        $bookAuthor->author_id = $authorId;
                        $bookAuthor->save();
                    }
                }
                
                // Загружаем новую картинку
                if ($model->imageFile) {
                    if ($model->upload()) {
                        $model->save(false);
                    }
                } else {
                    $model->cover_image = $oldImage;
                    $model->save(false);
                }
                
                Yii::$app->session->setFlash('success', 'Книга успешно обновлена');
                return $this->redirect(['site/author', 'id' => $selectedAuthors[0] ?? 0]);
            }
        }

        $authors = Author::find()->orderBy(['full_name' => SORT_ASC])->all();
        
        return $this->render('update', [
            'model' => $model,
            'authors' => $authors,
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
