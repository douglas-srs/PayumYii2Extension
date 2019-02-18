<?php
namespace Payum\Yii2Extension;

use Yii;
use Payum\Core\Exception\LogicException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\ReplyInterface;
use Payum\Core\Request\Authorize;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Notify;
use Payum\Core\Request\Refund;
use Payum\Core\Reply\HttpResponse;

class PaymentController extends \yii\web\Controller
{
    public function actionCapture()
    {
        /** @var \Payum\Core\Payum $payum */
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        /** @var \Payum\Core\GatewayInterface $gateway */
        if ($reply = $gateway->execute(new Capture($token), true)) {
            if ($reply instanceof HttpRedirect) {
                return $this->redirect($reply->getUrl());
            }

            throw new \LogicException('Unsupported reply', null, $reply);
        }

        /** @var \Payum\Core\Payum $payum */
        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);

        $this->redirect($token->getAfterUrl());

    }

    public function actionAuthorize()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $gateway = $this->getPayum()->getGateway($token->getGatewayName());
        $gateway->execute($capture = new Authorize($token));
        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);
        $this->redirect($token->getAfterUrl());
    }

    public function actionNotify()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $gateway = $this->getPayum()->getGateway($token->getGatewayName());
        $gateway->execute($capture = new Notify($token));
    }

    public function actionRefund()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);
        $gateway = $this->getPayum()->getGateway($token->getGatewayName());
        $gateway->execute($capture = new Refund($token));
        $this->redirect($token->getAfterUrl());
    }

    /**
     * @return \Payum\YiiExtension\PayumComponent
     */
    protected function getPayum()
    {
        return \Yii::$app->payum;
    }
} 