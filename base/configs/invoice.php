<?php

use biz\api\models\accounting\Invoice;

/*
 * Avaliable source for good movement
 */

return[
    // Purchase receive
    100 => [
        'type' => Invoice::TYPE_INCOMING,
        'class' => 'biz\api\models\purchase\Purchase',
        'relation' => 'purchaseDtls',
        'apply_method' => 'applyGR',
        'uom_field' => 'uom_id',
    ],
];
