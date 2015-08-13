<?php

namespace biz\api\controllers;

use Yii;
use biz\api\base\AdvanceController;
use biz\api\models\inventory\GoodsMovement as MMovement;

/**
 * Description of PurchaseController
 *
 * @property ApiPurchase $api
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 3.0
 */
class MovementController extends AdvanceController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'biz\api\models\inventory\GoodsMovement';
    public $modelSearchClass = 'biz\api\models\inventory\searchs\GoodsMovement';

    /**
     * @inheritdoc
     */
    public $prefixEventName = 'eMovement';

    /**
     * @var array
     */
    protected $patchingStatus = [
        [MMovement::STATUS_DRAFT, MMovement::STATUS_APPLIED, 'apply', 'applied'],
        [MMovement::STATUS_APPLIED, MMovement::STATUS_DRAFT, 'reject', 'rejected'],
    ];

    /**
     * @param \dee\base\Event $event
     */
    public function ePatch($event)
    {
        /* @var $model MMovement */
        list($model, $dirty, $olds) = $event->params;
        // status changed
        if (isset($dirty['status'])) {
            foreach ($this->patchingStatus as $change) {
                if ($olds['status'] == $change[0] && $dirty['status'] == $change[1]) {
                    $this->fire($change[2], [$model]);
                }
            }
        }
    }

    /**
     * @param \dee\base\Event $event
     */
    public function ePatched($event)
    {
        /* @var $model MMovement */
        list($model, $dirty, $olds) = $event->params;
        // status changed
        if (isset($dirty['status'])) {
            foreach ($this->patchingStatus as $change) {
                if ($olds['status'] == $change[0] && $dirty['status'] == $change[1]) {
                    $this->fire($change[3], [$model]);
                }
            }
        }
    }
}