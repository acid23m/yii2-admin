<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 06.08.18
 * Time: 0:14
 */

namespace dashboard\models\log;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class LogSearch.
 *
 * @package dashboard\models\log
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class LogSearch extends LogRecord
{
    /**
     * {@inheritdoc}
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['level', 'category', 'log_time', 'prefix', 'message'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     * @param array $params
     * @return ActiveDataProvider
     * @throws InvalidArgumentException
     */
    public function search($params): ActiveDataProvider
    {
        $query = LogRecord::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['id', 'level', 'category', 'log_time'],
                'enableMultiSort' => true,
                'defaultOrder' => ['log_time' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($this->log_time)) {
            if (strpos($this->log_time, ',') !== false) {
                [$log_time_start, $log_time_end] = \explode(',', $this->log_time);
                $log_time_start = (new \DateTime($log_time_start, new \DateTimeZone(\Yii::$app->timeZone)))->format('U');
                $log_time_end = (new \DateTime($log_time_end, new \DateTimeZone(\Yii::$app->timeZone)))->format('U');
                $query->andFilterWhere(['between', '{{log}}.[[log_time]]', $log_time_start, $log_time_end]);
            } else {
                $query->andFilterWhere(['{{log}}.[[log_time]]' => $this->log_time]);
            }
        }

        $query->andFilterWhere([
            '{{log}}.[[id]]' => $this->id,
            '{{log}}.[[level]]' => $this->level
        ]);

        $query->andFilterWhere(['like', '{{log}}.[[category]]', $this->category])
            ->andFilterWhere(['like', '{{log}}.[[prefix]]', $this->prefix])
            ->andFilterWhere(['like', '{{log}}.[[message]]', $this->message]);

        return $dataProvider;
    }

}
