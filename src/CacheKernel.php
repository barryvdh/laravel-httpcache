<?php 

namespace Barryvdh\HttpCache;

use Illuminate\Contracts\Http\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CacheKernel implements HttpKernelInterface
{
    /** @var  Kernel */
    protected $kernel;

    protected function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        return $this->kernel->handle($request);
    }

    /**
     * Wrap a Laravel Kernel in a Symfony HttpKernel
     *
     * @param Kernel $kernel
     * @param null $storagePath
     * @param SurrogateInterface|null $surrogate
     * @param array $options
     * @return Kernel|HttpCache
     */
    public static function wrap(Kernel $kernel, $storagePath = null, SurrogateInterface $surrogate = null, $options = [] )
    {
        $storagePath = $storagePath ?: storage_path('httpcache');
        $store = new Store($storagePath);

        $wrapper = new static($kernel);
        $kernel = new HttpCache($wrapper, $store, $surrogate, $options);

        return $kernel;
    }
}
