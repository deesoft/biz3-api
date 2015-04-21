<?php

namespace biz\api\hooks\purchase;

use Yii;
use biz\api\models\inventory\GoodsMovement as MGoodsMovement;
use biz\api\models\purchase\Purchase as MPurchase;
use yii\helpers\ArrayHelper;

/**
 * Purchase
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 3.0
 */
class Purchase extends \yii\base\Behavior
{

    public function events()
    {
        return [
            'eMovementApply' => 'movementChangeStatus',
            'eMovementReject' => 'movementChangeStatus',
        ];
    }

    /**
     * Handler for Good Movement created.
     * It used to update stock
     * @param \biz\api\base\Event $event
     */
    public function movementChangeStatus($event)
    {
        /* @var $model MGoodsMovement */
        $model = $event->params[0];
        /*
         * 100 = Purchase
         */
        if (!in_array($model->reff_type, [100])) {
            return;
        }
        $factor = $event->name == 'eMovementApply' ? 1 : -1;

        $purchase = MPurchase::findOne($model->reff_id);
        $purchaseItems = ArrayHelper::index($purchase->items, 'product_id');
        // change total qty for reff document
        /* @var $purcDtl \biz\api\models\purchase\PurchaseDtl */
        foreach ($model->items as $detail) {
            $purcDtl = $purchaseItems[$detail->product_id];
            $purcDtl->total_receive += $factor * $detail->qty;
            $purcDtl->save(false);
        }
    }
}