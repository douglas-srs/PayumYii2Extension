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
use Payum\Storage\StorageInterface;
use Payum\Core\Storage\FilesystemStorage;
use Payum\Core\Bridge\PlainPhp\Security\HttpRequestVerifier;

use Payum\Core\PayumBuilder;
use Payum\Core\Payum;
use Payum\Core\Model\Payment;

use Payum\Core\Security\GenericTokenFactory;
use Payum\Yii2Extension\TokenFactory;

class PayumComponent extends Component
{
    /**
     * @var PaymentInterface[]
     */
    public $payments;
    /**
     * @var array
     */
    public $storages;
    /**
     * @var StorageInterface
     */
    public $tokenStorage;
    /**
     * @var GenericTokenFactoryInterface
     */
    public $tokenFactory;
    /**
     * @var HttpRequestVerifierInterface
     */
    protected $httpRequestVerifier;
    /**
     * @var RegistryInterface
     */
    public $registry;

    //protected $shared;

    public function init(){
        $tokenPaths = array(
            'capture' => 'payment/capture',
            'notify' => 'payment/notify',
            'authorize' => 'payment/authorize',
            'refund' => 'payment/refund'
        );
        $this->registry = new SimpleRegistry($this->payments, $this->storages, []);
        $this->httpRequestVerifier = new HttpRequestVerifier($this->tokenStorage);
        $this->tokenFactory = new GenericTokenFactory(new TokenFactory($this->tokenStorage, $this->registry), $tokenPaths);

        /*$this->shared = (new PayumBuilder())
        ->addDefaultStorages()
        ->setGenericTokenFactoryPaths($tokenPaths)
        ->setMainRegistry($this->registry)
        ->setHttpRequestVerifier($this->httpRequestVerifier)
        ->setTokenFactory($this->tokenFactory)
        ;       
        
        foreach ($this->payments as $gatewayName => $gatewayConfig) {
            $this->shared->addGateway($gatewayName, $gatewayConfig);
        }

        foreach ($this->storages as $storageName => $storageConfig) {
            $this->shared->addStorage($storageName, $storageConfig);            
        }

        $this->shared->setTokenFactory($this->tokenFactory);
        $this->shared = $this->shared->getPayum();*/    
    }

    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    public function getTokenFactory()
    {
        return $this->tokenFactory;
    }

    public function getHttpRequestVerifier()
    {
        return $this->httpRequestVerifier;
    }

    public function getGateway($name)
    {
        return $this->registry->getGateway($name);
    }

    /*public function getTokenStorage()
    {
        return $this->shared->getTokenStorage();
    }

    public function getTokenFactory()
    {
        return $this->shared->getTokenFactory();
    }

    public function getHttpRequestVerifier()
    {
        return $this->shared->getHttpRequestVerifier();
    }

    public function getGateway($name)
    {
        return $this->shared->getGateway($name);
    }*/
    
}
