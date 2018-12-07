<?php

namespace dashboard\models\user\rest;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class UserSearch.
 *
 * @package dashboard\models\user\rest
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['username', 'email', 'role', 'note', 'ip', 'last_access', 'created_at', 'updated_at'], 'safe'],
            [['tfa', 'status'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
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
    public function search(array $params): ActiveDataProvider
    {
        $query = \Yii::$app->getUser()->can(User::ROLE_SUPER)
            ? User::find()
            : User::find()->limited();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'username',
                    'email',
                    'role',
                    'ip',
                    'tfa',
                    'status',
                    'last_access',
                    'created_at',
                    'updated_at'
                ],
                'enableMultiSort' => true,
                'defaultOrder' => ['username' => SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($this->last_access)) {
            if (strpos($this->last_access, ',') !== false) {
                [$last_access_start, $last_access_end] = \explode(',', $this->last_access);
                $query->andFilterWhere(['between', '{{%user}}.[[last_access]]', $last_access_start, $last_access_end]);
            } else {
                $query->andFilterWhere(['{{%user}}.[[last_access]]' => $this->last_access]);
            }
        }
        if (!empty($this->created_at)) {
            if (strpos($this->created_at, ',') !== false) {
                [$created_at_start, $created_at_end] = \explode(',', $this->created_at);
                $query->andFilterWhere(['between', '{{%user}}.[[created_at]]', $created_at_start, $created_at_end]);
            } else {
                $query->andFilterWhere(['{{%user}}.[[created_at]]' => $this->created_at]);
            }
        }
        if (!empty($this->updated_at)) {
            if (strpos($this->updated_at, ',') !== false) {
                [$updated_at_start, $updated_at_end] = \explode(',', $this->updated_at);
                $query->andFilterWhere(['between', '{{%user}}.[[updated_at]]', $updated_at_start, $updated_at_end]);
            } else {
                $query->andFilterWhere(['{{%user}}.[[updated_at]]' => $this->updated_at]);
            }
        }

        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.role' => $this->role,
            'user.tfa' => $this->tfa,
            'user.status' => $this->status
        ]);

        $query->andFilterWhere(['like', '{{%user}}.[[username]]', $this->username])
            ->andFilterWhere(['like', '{{%user}}.[[email]]', $this->email])
            ->andFilterWhere(['like', '{{%user}}.[[note]]', $this->note])
            ->andFilterWhere(['like', '{{%user}}.[[ip]]', $this->ip]);

        return $dataProvider;
    }

}
