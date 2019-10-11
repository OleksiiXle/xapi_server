<?php

namespace common\components\conservation;

use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;


class ActiveDataProviderConserve extends ActiveDataProvider
{
    public $conserveName;
    public $conserves = [];
    public $pageSize = 10;
    public $startPage =1;
    public $baseModel; //-- объект основной модели данных
    public $filterModelClass; //-- класс модели фильтра
    public $filterModel; //-- модель фильтра
    public $hasFilter=false;
    public $searchId = 0; //-- если не 0 - первый раз переход на страницу где этот ид
    public $exportQuery = [ //-- для вывода в файл, передается во вьюху фильтра
        'filterModelClass' => '',
        'filter' => [],
        'sort' => [],
    ];

    public function __construct(array $config = [])
    {

        $session = \Yii::$app->session;
        if ($session->get('searchIid')){
            $this->searchId = $session->get('searchIid');
            $session->remove('searchIid');
        }

        parent::__construct($config);
        $this->getConserves();
        $this->pagination = [
            'class'            => 'common\components\conservation\PaginationConserve',
            'conserveName' => $this->conserveName,
            'pageSize' => $this->pageSize,
            'startPage' => $this->conserves['startPage'],
            'totalCount' => 0,
            'searchId' => $this->searchId,
        ];
        //-- фильтр
        if (isset($this->filterModelClass)){
            $this->exportQuery['filterModelClass'] = $this->filterModelClass;
            $params = [];
            if (!$this->filterModel){
                $this->filterModel = new $this->filterModelClass;
            }

            if (\Yii::$app->request->isPost){ //-- пришел новый фильтр
                $params = \Yii::$app->request->post();
                $this->filterModel->load($params);
                $cJSON = \Yii::$app->conservation->setConserveGridDB($this->conserveName, 'filter', json_encode($this->filterModel->getAttributes()));
                $cJSON = \Yii::$app->conservation->setConserveGridDB($this->conserveName, $this->pagination->pageParam, 1);
                $this->pagination->startPage = 0;
            } elseif (isset($this->conserves['filter'])){ //-- фильтр не пришел, но может быть что нибудь есть в консерве с прошлого раза
                $params = (array) $this->conserves['filter'];
                $this->filterModel->setAttributes($params);
            }
          //  \Yii::trace(\yii\helpers\VarDumper::dumpAsString($this->filterModel), 'dbg');
            $this->query = $this->filterModel->getQuery();

            $this->exportQuery['filter']= $this->filterModel->getAttributes();
         //   \Yii::trace(\yii\helpers\VarDumper::dumpAsString($this->query), 'dbg');

        } else {
            $this->query = $this->baseModel;

        }


    }

    public function getConserves(){
        $buf = \Yii::$app->conservation->getConserveGridDB($this->conserveName);
        $this->conserves['startPage'] = (isset($buf['data']['page'])) ? $buf['data']['page'] : $this->startPage;
        $this->conserves['sort'] = (isset($buf['data']['sort'])) ? $buf['data']['sort'] : null;
        $this->conserves['filter'] = (isset($buf['data']['filter'])) ? json_decode($buf['data']['filter']) : null;

    }

    /**
     * {@inheritdoc}
     */
    protected function prepareModels()
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }
        $query = clone $this->query;

        if (($sort = $this->getSort()) !== false) {
            $orders = $sort->getOrders();
            if (!empty($orders)){
                $cJSON = \Yii::$app->conservation->setConserveGridDB($this->conserveName, 'sort', $orders);
                $this->conserves['sort'] = $orders;
            } elseif (!empty($this->conserves['sort'])) {
                $orders = $this->conserves['sort'];
            }
            $query->addOrderBy($orders);
        }

        $this->exportQuery['sort'] = $orders;

        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            if ($pagination->totalCount === 0) {
                return [];
            }

            if ($this->searchId > 0){
                $idField = (!empty($query->join) || !empty($query->joinWith)) ? $query->modelClass::tableName() . '.id' : 'id';
                $qtmp = clone $query;
                $qtmp->select = [$idField];
                $retTmp = $qtmp->createCommand()->queryAll();
                $fieldPosition = array_search(['id' => $this->searchId],$retTmp);
                if (isset($fieldPosition)){
                    if ($fieldPosition < $this->pageSize){
                        $offset = 0;
                    } elseif ($fieldPosition == $this->pageSize){
                        $offset = 1;
                    } else {
                        $buf = intdiv($fieldPosition, $this->pageSize);
                        $offset = $buf + 1;
                    }
                    $this->pagination->searchPage = $offset;
                }
            }
            $offs = $pagination->getOffset();
            $query->limit($pagination->getLimit())->offset($offs);
        }

        return $query->all($this->db);
    }
}