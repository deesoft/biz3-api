<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace biz\api\base;

use Yii;
use yii\helpers\Inflector;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * Description of Controller
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Controller extends \dee\rest\Controller
{
    /**
     * The insert operation.
     */
    const OP_INSERT = 'insert';

    /**
     * The update operation.
     */
    const OP_UPDATE = 'update';

    /**
     * The delete operation.
     */
    const OP_PATCH = 'patch';

    /**
     * The delete operation.
     */
    const OP_DELETE = 'delete';

    /**
     * @var string prefix event name
     */
    public $prefixEventName;

    /**
     * @var boolean
     */
    private $_attached = false;

    /**
     * 
     * @return array
     */
    public function events()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function ensureBehaviors()
    {
        parent::ensureBehaviors();
        if (!$this->_attached) {
            $this->_attached = true;
            foreach ($this->events() as $event => $handler) {
                $this->on($event, is_string($handler) ? [$this, $handler] : $handler, null, false);
            }
        }
    }

    /**
     * Lists all models.
     * @return mixed
     */
    public function query()
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        $query = $modelClass::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->fire('query', [$dataProvider]);
        return $dataProvider;
    }

    /**
     * Displays a single model.
     * @param integer $id
     * @return mixed
     */
    public function view($id)
    {
        $model = $this->findModel($id);
        $this->fire('view', [$model]);
        return $model;
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function create()
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        $model = new $modelClass;
        $this->beginTransaction(static::OP_INSERT);
        try {
            $this->fire('beforeCreate', [$model]);
            $model->load(Yii::$app->request->post(), '');
            $this->fire('create', [$model]);
            if ($model->save()) {
                $this->fire('created', [$model]);
                $this->commit();
                $model->refresh();
            } else {
                $this->fire('rollbackCreate', [$model]);
                $this->rollBack();
            }
        } catch (\Exception $e) {
            $this->fire('errorCreate', [$model, $e]);
            $this->rollBack();
            throw $e;
        }

        return $model;
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function update($id)
    {
        $model = $this->findModel($id);

        $this->beginTransaction(static::OP_UPDATE);
        try {
            $this->fire('beforeUpdate', [$model]);
            $model->load(Yii::$app->request->post(), '');
            $this->fire('update', [$model]);
            if ($model->save()) {
                $this->fire('updated', [$model]);
                $this->commit();
                $model->refresh();
            } else {
                $this->fire('rollbackUpdate', [$model]);
                $this->rollBack();
            }
        } catch (\Exception $e) {
            $this->fire('errorUpdate', [$model, $e]);
            $this->rollBack();
            throw $e;
        }

        return $model;
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function patch($id)
    {
        $model = $this->findModel($id);

        $this->beginTransaction(static::OP_PATCH);
        try {
            $this->fire('beforePatch', [$model]);
            $patchs = Yii::$app->request->post();
            foreach ($patchs as $patch) {
                $this->doPatch($model, $patch);
            }
            $this->fire('patch', [$model]);
            if ($model->save()) {
                $this->fire('patched', [$model]);
                $this->commit();
                $model->refresh();
            } else {
                $this->fire('rollbackPatch', [$model]);
                $this->rollBack();
            }
        } catch (\Exception $e) {
            $this->fire('errorPatch', [$model, $e]);
            $this->rollBack();
            throw $e;
        }

        return $model;
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function delete($id)
    {
        $model = $this->findModel($id);
        $this->beginTransaction(static::OP_DELETE);
        try {
            $this->fire('beforeDelete', [$model]);
            if ($model->delete()) {
                $this->fire('deleted', [$model]);
                $this->commit();
            } else {
                $this->fire('rollbackDelete', [$model]);
                $this->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            $this->fire('errorDelete', [$model, $e]);
            $this->rollBack();
            throw $e;
        }

        return true;
    }

    /**
     * Trigger event
     * @param string $name
     * @param array $params
     */
    protected function fire($name, $params = [])
    {
        if ($this->prefixEventName === null) {
            $this->prefixEventName = 'e' . Inflector::id2camel($this->id);
        }
        $event = new Event($params);
        $this->trigger($name, $event);
        Yii::$app->trigger($this->prefixEventName . ucfirst($name), $event);
    }
    /**
     * List of transaction object
     * @var array
     */
    private $_transaction = [];

    /**
     * Check using transaction
     * @param int $operation
     * @return boolean
     */
    protected function opTransaction($operation)
    {
        return true;
    }

    /**
     * Begins a transaction.
     * @param int $operation
     */
    protected function beginTransaction($operation)
    {
        if ($this->opTransaction($operation)) {
            /* @var $modelClass ActiveRecord */
            $modelClass = $this->modelClass;
            $transaction = $modelClass::getDb()->beginTransaction();
        } else {
            $transaction = null;
        }
        $this->_transaction[] = $transaction;
    }

    /**
     * Commits a transaction.
     * @throws Exception if the transaction is not active
     */
    protected function commit()
    {
        $transaction = array_pop($this->_transaction);
        if ($transaction && $transaction instanceof \yii\db\Transaction) {
            $transaction->commit();
        }
    }

    /**
     * Rolls back a transaction.
     * @throws Exception if the transaction is not active
     */
    protected function rollback()
    {
        $transaction = array_pop($this->_transaction);
        if ($transaction && $transaction instanceof \yii\db\Transaction) {
            $transaction->rollBack();
        }
    }
}