<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.08.18
 * Time: 17:53
 */

namespace dashboard\controllers\web;

use dashboard\models\index\SearchIndex;
use yii\helpers\Html;

/**
 * Class SearchController.
 *
 * @package dashboard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class SearchController extends BaseController
{
    /**
     * Find content by query.
     * @param string $q Search query
     * @return string
     * @throws \S2\Rose\Exception\ImmutableException
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionResult($q): string
    {
        if (!empty($q)) {
            $q = Html::encode($q);

            /** @var SearchIndex $search_index */
            $search_index = \Yii::createObject(SearchIndex::class);
            $results = $search_index->find($q);
        } else {
            $results = [];
        }

        return $this->render('result', compact('q', 'results'));
    }

}
