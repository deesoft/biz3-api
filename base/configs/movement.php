<?php

use biz\api\models\inventory\GoodsMovement;

/*
 * Avaliable source for good movement
 */

return[
    // Purchase receive
    100 => [
        'type' => GoodsMovement::TYPE_RECEIVE,
        'class' => 'biz\api\models\purchase\Purchase',
        'relation' => 'purchaseDtls',
        'apply_method' => 'applyGR',
    ],
    // Sales release
    200 => [
        'type' => GoodsMovement::TYPE_ISSUE,
        'class' => 'biz\api\models\sales\Sales',
        'relation' => 'salesDtls',
        'apply_method' => 'applyGI',
    ],
    // Transfer release
    300 => [
        'type' => GoodsMovement::TYPE_ISSUE,
        'class' => 'biz\api\models\inventory\Transfer',
        'relation' => 'transferDtls',
        'apply_method' => 'applyGI',
    ],
    // Transfer release
    400 => [
        'type' => GoodsMovement::TYPE_RECEIVE,
        'class' => 'biz\api\models\inventory\Transfer',
        'relation' => 'transferDtls',
        'apply_method' => 'applyGR',
    ],
];
