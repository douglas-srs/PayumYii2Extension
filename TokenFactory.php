<?php
namespace Payum\Yii2Extension;

use Payum\Core\Security\AbstractTokenFactory;
use yii\helpers\Url;
use League\Uri\Http as HttpUri;
use yii\db\BaseActiveRecord;

class TokenFactory extends AbstractTokenFactory
{
    /**
     * @param string $path
     * @param array $parameters
     *
     * @return string
     */
    protected function generateUrl($path, array $parameters = array())
    {
        $url = array($path);
        $params = $parameters;

        $fullUrl = array_merge($url, $params);

        return Url::to($fullUrl, true);
    }

    public function createToken($gatewayName, $model, $targetPath, array $targetParameters = [], $afterPath = null, array $afterParameters = [])
    {
        /** @var TokenInterface $token */
        //$token = $this->tokenStorage->create();
        $token = new $this->tokenStorage->_modelClass;
        $token->setHash($token->getHash() ?: Random::generateToken());
        $targetParameters = array_replace(['payum_token' => $token->getHash()], $targetParameters);
        $token->setGatewayName($gatewayName);
        if ($model instanceof BaseActiveRecord) {
            $token->setDetails($model);
        } elseif (null !== $model) {
            $token->setDetails($this->storageRegistry->getStorage($model)->identify($model));
        }
        if (0 === strpos($targetPath, 'http')) {
            $targetUri = HttpUri::createFromString($targetPath);
            $targetUri = $this->addQueryToUri($targetUri, $targetParameters);
            $token->setTargetUrl((string) $targetUri);
        } else {
            $token->setTargetUrl($this->generateUrl($targetPath, $targetParameters));
        }
        if ($afterPath && 0 === strpos($afterPath, 'http')) {
            $afterUri = HttpUri::createFromString($afterPath);
            $afterUri = $this->addQueryToUri($afterUri, $afterParameters);
            $token->setAfterUrl((string) $afterUri);
        } elseif ($afterPath) {
            $token->setAfterUrl($this->generateUrl($afterPath, $afterParameters));
        }
        if ($token->save()){
            //die(print_r($token));
        } else {
            die(print_r($token->getErrors()));
        }

        return $token;
    }


}