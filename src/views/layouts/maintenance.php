<?php

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var string $content */
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo \Yii::$app->language ?>">
<head>
    <meta charset="<?php echo \Yii::$app->charset ?>">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Security-Policy" content="block-all-mixed-content">
    <meta name="robots" content="noindex,nofollow">
    <title><?php echo Html::encode(Yii::$app->name) ?></title>
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            padding: 0;
            margin: 0;
        }

        body {
            font-family: sans-serif;
            font-size: 16px;
            line-height: 1.75;
            color: #fff;
            background-color: #333;
        }

        section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100vh;
        }

        h1 {
            font-size: 32px;
            font-weight: normal;
            line-height: 1.2;
            padding: 0;
            margin: 0 0 24px 0;
        }

        p {
            padding: 0;
            margin: 0;
        }

        hr {
            width: 10%;
            height: 1px;
            background-color: #555;
            border: none;
            padding: 0;
            margin: 16px 0 32px;
        }

        .creds {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            padding: 0;
            margin: 0;
        }
    </style>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<section>
    <?= $content ?>
</section>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
