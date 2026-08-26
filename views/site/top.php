<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Топ 10 авторов';
?>

<div class="site-top">
    <div class="jumbotron">
        <h1>Топ 10 авторов</h1>
        <p class="lead">Авторы, выпустившие больше всего книг за выбранный год</p>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-12">
                <?php $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => ['site/top'],
                ]); ?>
                
                <div class="form-group">
                    <label>Выберите год:</label>
                    <?= Html::dropDownList(
                        'year',
                        $year,
                        array_combine($years, $years),
                        ['class' => 'form-control', 'onchange' => 'this.form.submit()']
                    ) ?>
                </div>
                
                <?php ActiveForm::end(); ?>
                
                <?php if (!empty($topAuthors)): ?>
                    <h3>Топ 10 авторов за <?= Html::encode($year) ?> год</h3>
                    <ul class="list-group">
                        <?php foreach ($topAuthors as $index => $author): ?>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="badge">#<?= $index + 1 ?></span>
                                        <?= Html::a(
                                            Html::encode($author['full_name']),
                                            ['site/author', 'id' => $author['id']]
                                        ) ?>
                                        <span class="badge">Книг: <?= $author['books_count'] ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-info">
                        <p>Нет книг за выбранный год.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>