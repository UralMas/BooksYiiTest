<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = 'Авторы';
?>

<div class="site-index">
    <div class="jumbotron">
        <h1>Каталог книг</h1>
        <p class="lead">Список авторов</p>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-12">
                <h2>Авторы</h2>
                
                <?php if (Yii::$app->session->hasFlash('success')): ?>
                    <div class="alert alert-success">
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (Yii::$app->session->hasFlash('error')): ?>
                    <div class="alert alert-danger">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php endif; ?>

                <ul class="list-group">
                    <?php foreach ($authors as $author): ?>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-8">
                                    <?= Html::a(
                                        Html::encode($author->full_name), 
                                        ['site/author', 'id' => $author->id]
                                    ) ?>
                                    <span class="badge">Книг: <?= $author->getBooksCount() ?></span>
                                </div>
                                <?php if (!Yii::$app->user->isGuest): ?>
                                    <div class="col-md-4 text-right">
                                        <?= Html::a(
                                            '<span class="fas fa-pencil-alt"></span>',
                                            ['author/update', 'id' => $author->id],
                                            ['class' => 'btn btn-sm btn-primary']
                                        ) ?>
                                        <?= Html::a(
                                            '<span class="fas fa-trash"></span>',
                                            ['author/delete', 'id' => $author->id],
                                            [
                                                'class' => 'btn btn-sm btn-danger',
                                                'data' => [
                                                    'confirm' => 'Вы уверены, что хотите удалить этого автора?',
                                                    'method' => 'post',
                                                ],
                                            ]
                                        ) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>