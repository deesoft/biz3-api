<?php

namespace biz\api\controllers\accounting;

/**
 * Description of GLController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>  
 * @since 3.0
 */
class GLController extends \biz\api\base\rest\Controller
{
    /**
     * @inheritdoc
     */
    public $helperClass = 'biz\api\components\accounting\GL';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['delete']);

        return $actions;
    }
}