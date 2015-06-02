<?php

namespace biz\api\models\master\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use biz\api\models\master\Branch as BranchModel;

/**
 * Branch represents the model behind the search form about `biz\api\models\master\Branch`.
 */
class Branch extends BranchModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'orgn_id', 'created_by', 'updated_by'], 'integer'],
            [['code', 'name', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BranchModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'orgn_id' => $this->orgn_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
