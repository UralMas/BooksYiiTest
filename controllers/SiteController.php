<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Author;
use app\models\Book;
use app\models\Subscription;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'authors' => Author::getAllSorted(),
        ]);
    }

    public function actionTop()
    {
        $years = Author::getAvailableYears();
        $year = Yii::$app->request->get('year', date('Y'));
        $topAuthors = Author::getTopAuthors($year);
        
        return $this->render('top', [
            'years' => $years,
            'year' => $year,
            'topAuthors' => $topAuthors,
        ]);
    }

    public function actionAuthor(int $id)
    {
        $author = Author::findOne($id);
        if (!$author) {
            throw new NotFoundHttpException('Автор не найден');
        }

        if (Yii::$app->request->isPost && !Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('error', 'Только неавторизованные пользователи могут подписываться');
        }

        $subscription = new Subscription();
        if ($subscription->load(Yii::$app->request->post()) && $subscription->saveForAuthor($author)) {
            Yii::$app->session->setFlash('success', "Вы успешно подписаны на автора {$author->full_name}");
            return $this->refresh();
        }

        return $this->render('author', [
            'author' => $author,
            'books' => $author->getBooksSorted(),
            'subscription' => $subscription,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
