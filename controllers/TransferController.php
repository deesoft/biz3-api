<?php

namespace biz\api\controllers;

use Yii;
use biz\api\base\AdvanceController;
use biz\api\models\inventory\Transfer as MTransfer;

/**
 * Description of PurchaseController
 *
 * @property ApiPurchase $api
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 3.0
 */
class TransferController extends AdvanceController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'biz\api\models\inventory\Transfer';
    public $modelSearchClass = 'biz\api\models\inventory\searchs\Transfer';

    /**
     * @inheritdoc
     */
    public $prefixEventName = 'eTransfer';

    /**
     * @var array
     */
    protected $patchingStatus = [
        [MTransfer::STATUS_DRAFT, MTransfer::STATUS_PROCESS, 'process', 'processed'],
        [MTransfer::STATUS_PROCESS, MTransfer::STATUS_DRAFT, 'reject', 'rejected'],
    ];

    /**
     * @param \dee\base\Event $event
     */
    public function ePatch($event)
    {
        /* @var $model MTransfer */
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
        /* @var $model MPurchase */
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