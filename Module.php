<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace biz\api;

use Yii;

/**
 * Description of Module
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{

    public $prefixUrlRule = 'api';

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $definitions = require(__DIR__ . '/definitions.php');
            foreach ($definitions as $name => $definition) {
                Yii::$container->set($name, $definition);
            }

            $hooks = require(__DIR__ . '/hooks.php');
            Yii::$app->attachBehaviors(array_combine($hooks, $hooks));
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules($this->getUrlRules(), false);

            $jsFile = Yii::getAlias('@biz/client/assets/js/master.app.js', false);
            $app->getAssetManager()->assetMap[$jsFile] = $app->getUrlManager()->createAbsoluteUrl([$this->uniqueId . '/master']);
        }
    }

    protected function getUrlRules()
    {
        return[
            [
                'class' => 'dee\rest\UrlRule',
                'prefix' => $this->prefixUrlRule,
                'prefixRoute' => $this->uniqueId,
                'extraPatterns' => [
                    'GET {id}/items' => 'view-items'
                ],
                'controller' => [
                    'purchase' => 'purchase',
                    'movement' => 'movement',
                    'sales' => 'sales',
                ]
            ],
        ];
    }
}