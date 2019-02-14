<?php
namespace Payum\Yii2Extension;

use Payum\Core\Security\AbstractTokenFactory as BaseAbstractTokenFactory;
use yii\helpers\Url;

class AbstractTokenFactory extends BaseAbstractTokenFactorydasdas
{
    /**
     * @param string $path
     * @param array $parameters
     *
     * @return string
     */
    protected function generateUrl($path, array $parameters = array())
    {
        //$ampersand = '&';
        //$schema = '';

        $url = array($path);
        $params = $parameters;

        $fullUrl = array_merge($url, $params);

        die('teste');

        return Url::toRoute($fullUrl);
        //return
        //    Yii::$app->getRequest()->getHostInfo($schema).
        //    Yii::$app->createUrl(trim($path,'/'),$parameters, $ampersand)
        //;
    }
    //testeee
}