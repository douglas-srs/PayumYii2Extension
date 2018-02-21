<?php
namespace Payum\Yii2Extension;

use yii\base\Component;
use Payum\Exception\RuntimeException;
use Payum\Core\Extension\StorageExtension;
use Payum\PaymentInterface;
use Payum\Registry\RegistryInterface;
use Payum\Core\Registry\SimpleRegistry;
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
    public $registry;
    public $httpRequestVerifier;

    public function init()
    {
        $this->paymentClass = Payment::class;
        $this->shared = (new PayumBuilder())
        ->addDefaultStorages();

        $this->registry = new SimpleRegistry($this->payments, $this->storages, []);

        foreach ($this->payments as $gatewayName => $gatewayArray) {
            $this->shared->addGateway($gatewayName, $gatewayArray);
        }        

        $this->shared = $this->shared->getPayum();

        $this->httpRequestVerifier = $this->shared->getHttpRequestVerifier();

        foreach ($this->storages as $storageName => $storage) {
            $this->shared->getGateway($gatewayName)->addExtension(new StorageExtension(
               $storage
            ));
        }

    }

    /**
     * @return StorageInterface
     */
    public function getTokenStorage()
    {
        //die(print_r($this->tokenStorage, true));
        return new \Payum\Yii2Extension\Storage\ActiveRecordStorage('payum_tokens', '\Payum\Yii2Extension\Model\PaymentSecurityToken');
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

    public function getStorage($class)
    {
        return $this->shared->getStorage($class);
    }
    
}
