<?php

namespace biz\api\base;

/**
 * Description of StatusPatchTrait
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
trait StatusPatchTrait
{
    /**
     * @var array
     */
    public $patchingStatus = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return[
            'patch' => 'ePatch',
        ];
    }

    /**
     * @param \dee\base\Event $event
     */
    public function ePatch($event)
    {
        /* @var $model \yii\db\ActiveRecord */
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