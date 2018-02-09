<?php
namespace Payum\YiiExtension;

use Yii;
use Payum\Request\BinaryMaskStatusRequest;
use Payum\Request\RedirectUrlInteractiveRequest;
use Payum\Request\SecuredCaptureRequest;

class PaymentController extends \yii\web\Controller
{
    public function actionCapture()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $payment = $this->getPayum()->getRegistry()->getPayment($token->getPaymentName());

        $payment->execute($status = new BinaryMaskStatusRequest($token));
        if (false == $status->isNew()) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            exit;
        }

        if ($interactiveRequest = $payment->execute(new SecuredCaptureRequest($token), true)) {
            if ($interactiveRequest instanceof RedirectUrlInteractiveRequest) {
                $this->redirect($interactiveRequest->getUrl(), true);
            }

            throw new \LogicException('Unsupported interactive request', null, $interactiveRequest);
        }

        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);

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