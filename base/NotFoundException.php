<?php

namespace biz\api\base;

/**
 * Description of NotFoundException
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>  
 * @since 3.0
 */
class NotFoundException extends \yii\base\Exception
{

    public function getName()
    {
        return 'Not Found';
    }
}
