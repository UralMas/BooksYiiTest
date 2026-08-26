<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = 'Редактировать книгу: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['site/index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['site/author', 'id' => $selectedAuthors[0] ?? 0]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>

<div class="book-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?php if ($model->cover_image): ?>
        <div class="form-group">
            <label>Текущее фото:</label>
            <div>
                <?= Html::img($model->getCoverUrl(), ['alt' => $model->title, 'style' => 'max-height: 150px;']) ?>
            </div>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'imageFile')->fileInput() ?>

    <div class="form-group">
        <label>Выберите авторов</label>
        <?= Html::dropDownList(
                'selectedAuthors[]',
                $selectedAuthors,
                ArrayHelper::map($authors, 'id', 'full_name'),
                [
                        'multiple' => true,
                        'class' => 'form-control',
                        'size' => 10,
                ]
        ) ?>
        <p class="help-block">Удерживайте Ctrl (Cmd на Mac) для выбора нескольких авторов</p>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['site/author', 'id' => $selectedAuthors[0] ?? 0], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>