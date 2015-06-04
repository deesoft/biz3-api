<?php

namespace biz\api\controllers;

use Yii;
use biz\api\base\AdvanceController;
use biz\api\models\purchase\Purchase as MPurchase;

/**
 * Description of PurchaseController
 *
 * @property ApiPurchase $api
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 3.0
 */
class PurchaseController extends AdvanceController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'biz\api\models\purchase\Purchase';

    /**
     * @inheritdoc
     */
    public $prefixEventName = 'ePurchase';
    public $extraPatterns = [
        'GET,HEAD {id}{attribute}' => 'viewDetail',
    ];

    /**
     * @var array
     */
    protected $patchingStatus = [
        [MPurchase::STATUS_DRAFT, MPurchase::STATUS_PROCESS, 'process', 'processed'],
        [MPurchase::STATUS_PROCESS, MPurchase::STATUS_DRAFT, 'reject', 'rejected'],
    ];

    /**
     * @param \dee\base\Event $event
     */
    public function ePatch($event)
    {
        /* @var $model MPurchase */
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