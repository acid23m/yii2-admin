<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.08.18
 * Time: 18:30
 */

/** @var \yii\web\View $this */
/** @var string $q */
/** @var \S2\Rose\Entity\ResultItem[]|iterable $results */

$this->title = $q;
$this->params['title'] = \Yii::t('dashboard', 'resultat poiska', [
    'query' => $q
]);
?>

<div class="search-results">
    <div class="row">
        <div class="col-xs-12">

            <?php foreach ($results as $result): ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title clearfix">
                            <a href="<?= $result->getUrl() ?>"><?= $result->getTitle() ?></a>
                            <small class="pull-right">
                                <?= $result->getRelevance() ?>
                            </small>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <?= $result->getSnippet() ?>
                    </div>
                    <div class="panel-footer clearfix">
                        <a href="<?= $result->getUrl() ?>"><?= $result->getUrl() ?></a>
                        <span class="pull-right">
                            <?= \Yii::$app->getFormatter()->asDate($result->getDate(), 'short') ?>
                        </span>
                    </div>
                </div>

            <?php endforeach ?>

            <?php if (\count($results) === 0): ?>
                <p><?= \Yii::t('yii', 'No results found.') ?></p>
            <?php endif ?>

        </div>
    </div>
</div>
