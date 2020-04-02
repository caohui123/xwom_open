<?php
/**
 * Class name is RegSoftwareSearch * @package backend\modules\common\controllers;
 * @author  Womtech  email:chareler@163.com
 * @DateTime 2020-04-02 15:34 
 */

namespace common\models\reg;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use common\models\reg\RegSoftware;

/**
 * RegSoftwareSearch represents the model behind the search form about `common\models\reg\RegSoftware`.
 */
class RegSoftwareSearch extends RegSoftware
{
    const EMPTY_STRING = "(空字符)";
    const NO_EMPTY = "(非空)";
    const SCENARIO_EDITABLE = 'editable';

    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_EDITABLE => ['title','brief_introduction','description','author'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_id', 'updated_id', 'sortOrder'], 'integer'],
            [['title', 'name', 'title_initial', 'bootstrap', 'service', 'cover', 'brief_introduction', 'description', 'author', 'version', 'is_setting', 'is_rule', 'parent_rule_name', 'route_map', 'default_config', 'console', 'status'], 'safe'],
            [['created_at', 'updated_at'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
        ];
    }
    /**
     * 创建时间  如果不需要或者该数据模型 没有 created_at 字段，您应该删除
     * @return array|false|int
     */
    public function getCreatedAt()
    {
        if(empty($this->created_at)){
            return null;
        }
        $createAt = is_string($this->created_at)?strtotime($this->created_at):$this->created_at;
        if(date('H:i:s', $createAt)=='00:00:00'){
            return [$createAt, $createAt+3600*24];
        }
        return $createAt;
    }
    
    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();
        $this->load($params);
        if ( ! is_null($this->created_at) && strpos($this->created_at, ' - ') !== false ) {
            list($s, $e) = explode(' - ', $this->created_at);
            $query->andFilterWhere(['between', 'created_at', strtotime($s), strtotime($e)]);
        }
        if ( ! is_null($this->updated_at) && strpos($this->updated_at, ' - ') !== false ) {
            list($s, $e) = explode(' - ', $this->updated_at);
            $query->andFilterWhere(['between', 'updated_at', strtotime($s), strtotime($e)]);
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'created_id' => $this->created_id,
            'updated_id' => $this->updated_id,
            'sortOrder' => $this->sortOrder,
        ]);
        $this->filterLike($query, 'title');
        $this->filterLike($query, 'name');
        $this->filterLike($query, 'title_initial');
        $this->filterLike($query, 'bootstrap');
        $this->filterLike($query, 'service');
        $this->filterLike($query, 'cover');
        $this->filterLike($query, 'brief_introduction');
        $this->filterLike($query, 'description');
        $this->filterLike($query, 'author');
        $this->filterLike($query, 'version');
        $this->filterLike($query, 'is_setting');
        $this->filterLike($query, 'is_rule');
        $this->filterLike($query, 'parent_rule_name');
        $this->filterLike($query, 'route_map');
        $this->filterLike($query, 'default_config');
        $this->filterLike($query, 'console');
        $this->filterLike($query, 'status');;
        $dataProvider = new ActiveDataProvider([
            //'pagination' => ['pageSize' => 3,],
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            ]);
        if (!$this->validate()) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    /**
     * @param ActiveQuery $query
     * @param $attribute
     */
    protected function filterLike(&$query, $attribute)
    {
        $this->$attribute = trim($this->$attribute);
        switch($this->$attribute){
            case \Yii::t('yii', '(not set)'):
                $query->andFilterWhere(['IS', $attribute, new Expression('NULL')]);
                break;
            case self::EMPTY_STRING:
                $query->andWhere([$attribute => '']);
                break;
            case self::NO_EMPTY:
                $query->andWhere(['IS NOT', $attribute, new Expression('NULL')])->andWhere(['<>', $attribute, '']);
                break;
            default:
                $query->andFilterWhere(['like', $attribute, $this->$attribute]);
                break;
        }
    }
}
