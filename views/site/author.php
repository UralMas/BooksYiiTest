<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $author->full_name;
?>

<div class="site-author">
    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($author->full_name) ?></h1>
            
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
            
            <h3>Книги автора</h3>
            
            <?php if (empty($books)): ?>
                <div class="alert alert-info">
                    <p>У этого автора пока нет книг.</p>
                </div>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($books as $book): ?>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4><?= Html::encode($book->title) ?></h4>
                                    <p><strong>Год:</strong> <?= Html::encode($book->year) ?></p>
                                    <?php if ($book->description): ?>
                                        <p><strong>Описание:</strong> <?= Html::encode($book->description) ?></p>
                                    <?php endif; ?>
                                    <?php if ($book->isbn): ?>
                                        <p><strong>ISBN:</strong> <?= Html::encode($book->isbn) ?></p>
                                    <?php endif; ?>
                                    <?php if ($book->cover_image): ?>
                                        <p><strong>Обложка:</strong> 
                                            <?= Html::img($book->getCoverUrl(), ['alt' => $book->title, 'style' => 'max-height: 100px;']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <?php if (!Yii::$app->user->isGuest): ?>
                                    <div class="col-md-4 text-right">
                                        <?= Html::a(
                                            '<span class="fas fa-pencil-alt"></span>',
                                            ['book/update', 'id' => $book->id],
                                            ['class' => 'btn btn-sm btn-primary']
                                        ) ?>
                                        <?= Html::a(
                                            '<span class="fas fa-trash"></span>',
                                            ['book/delete', 'id' => $book->id],
                                            [
                                                'class' => 'btn btn-sm btn-danger',
                                                'data' => [
                                                    'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
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
            <?php endif; ?>
            
            <?php if (Yii::$app->user->isGuest): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Подписка на автора</h3>
                    </div>
                    <div class="panel-body">
                        <?php $form = ActiveForm::begin(); ?>
                        
                        <?= $form->field($subscription, 'phone')->textInput(['maxlength' => true, 'placeholder' => '8XXXXXXXXXX']) ?>
                        
                        <div class="form-group">
                            <?= Html::submitButton('Подписаться', ['class' => 'btn btn-success']) ?>
                        </div>
                        
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div>
                <?= Html::a('Назад к списку авторов', ['site/index'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>