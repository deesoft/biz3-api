<?php

namespace biz\api\controllers\inventory;

use Yii;
use biz\api\base\Controller;
use biz\api\models\inventory\GoodsMovement as MGoodsMovement;

/**
 * Description of MovementController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>  
 * @since 3.0
 */
class MovementController extends Controller
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'biz\api\models\inventory\GoodsMovement';

    /**
     * @inheritdoc
     */
    public $prefixEventName = 'eMovement';

    /**
     * @var array 
     */
    protected $patchingStatus = [
        [MGoodsMovement::STATUS_DRAFT, MGoodsMovement::STATUS_APPLIED, 'apply'],
        [MGoodsMovement::STATUS_APPLIED, MGoodsMovement::STATUS_DRAFT, 'reject'],
    ];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return[
            'patch' => 'ePatch'
        ];
    }

    /**
     *
     * @param \biz\api\base\Event $event
     */
    public function ePatch($event)
    {
        /* @var $model MGoodsMovement */
        $model = $event->params[0];
        $dirty = $model->getDirtyAttributes();
        $olds = $model->getOldAttributes();
        // status changed
        if (isset($dirty['status'])) {
            foreach ($this->patchingStatus as $change) {
                if ($olds['status'] == $change[0] && $dirty['status'] == $change[1]) {
                    $this->fire($change[2], [$model]);
                }
            }
        }
    }
}