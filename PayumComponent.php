<?php
namespace Payum\Yii2Extension;

use yii\base\Component;
use Payum\Exception\RuntimeException;
use Payum\Extension\StorageExtension;
use Payum\PaymentInterface;
use Payum\Registry\RegistryInterface;
use Payum\Registry\SimpleRegistry;
use Payum\Request\BinaryMaskStatusRequest;
use Payum\Request\RedirectUrlInteractiveRequest;
use Payum\Request\SecuredCaptureRequest;
use Payum\Security\HttpRequestVerifierInterface;
use Payum\Security\PlainHttpRequestVerifier;
use Payum\Storage\StorageInterface;

use Payum\Core\PayumBuilder;
use Payum\Core\Payum;
use Payum\Core\Model\Payment;




class PayumComponent extends Component
{
    public $paymentClass;
    /**
     * @var array
     */
    public $storages;

    /**
     * @var StorageInterface
     */
    public $tokenStorage;


    public $payments;
    public $shared;

    public function init()
    {
        $this->paymentClass = Payment::class;
        $this->shared = (new PayumBuilder())
        ->addDefaultStorages();

        foreach ($this->payments as $gatewayName => $gatewayArray) {
            $this->shared->addGateway($gatewayName, $gatewayArray);
        }        

        $this->shared = $this->shared->getPayum();
    }

    /**
     * @return StorageInterface
     */
    public function getTokenStorage()
    {
        //die(print_r($this->tokenStorage, true));
        return $this->$tokenStorage::class;
    }

    public function getTokenFactory()
    {
        return $this->shared->getTokenFactory();
    }

    /**
     * @return HttpRequestVerifierInterface
     */
    public function getHttpRequestVerifier()
    {
        return $this->shared->httpRequestVerifier;
    }

    public function getGateway($gatewayName)
    {
        return $this->shared->getGateway($gatewayName);
    }
    
}
