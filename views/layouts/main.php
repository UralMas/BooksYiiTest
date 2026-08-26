<?php
use yii\helpers\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>
    <?php $this->beginBody() ?>

    <div class="wrap">
        <?php
        NavBar::begin([
                'brandLabel' => 'Каталог книг',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                        'class' => 'navbar navbar-inverse navbar-fixed-top',
                ],
        ]);

        $menuItems = [
                ['label' => 'Топ 10 авторов', 'url' => ['/site/top']],
        ];

        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Авторизоваться', 'url' => ['/site/login']];
        } else {
            $menuItems[] = ['label' => 'Добавить автора', 'url' => ['/author/create']];
            $menuItems[] = ['label' => 'Добавить книгу', 'url' => ['/book/create']];
            $menuItems[] = '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                            'Выйти (' . Yii::$app->user->identity->username . ')',
                            ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>';
        }

        echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
        ]);
        NavBar::end();
        ?>

        <div class="container">
            <?= $content ?>
        </div>
    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>