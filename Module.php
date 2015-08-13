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
 * @property array $mvConfig
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
        $prefix = $this->prefixUrlRule;
        $prefixRoute = $this->uniqueId;
        return[
            [
                'class' => 'dee\rest\UrlRule',
                'prefix' => $prefix,
                'prefixRoute' => $prefixRoute,
                'controller' => [
                    'purchase' => 'purchase',
                    'movement' => 'movement',
                    'sales' => 'sales',
                ]
            ],
//            "{$prefix}/<reff:\w+>/movement" => "{$prefixRoute}/movement/index",
//            "{$prefix}/<reff:\w+>/<reff_id:\d+>/movement" => "{$prefixRoute}/movement/index",
        ];
    }

    private $_mvConfig;
    public function getMvConfig()
    {
        if($this->_mvConfig === null){
            $this->_mvConfig = require (__DIR__.'/config/movement.php');
        }
        return $this->_mvConfig;
    }

    public function setMvConfig($values)
    {
        $this->getMvConfig();
        foreach ($values as $key => $value) {
            if(isset($this->_mvConfig[$key])){
                $this->_mvConfig[$key] = array_merge($this->_mvConfig[$key], $value);
            }  else {
                $this->_mvConfig[$key] = $value;
            }
        }
    }
}