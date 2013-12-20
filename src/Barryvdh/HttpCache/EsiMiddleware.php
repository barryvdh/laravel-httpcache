<?php namespace Barryvdh\HttpCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\HttpCache\Esi;

class EsiMiddleware implements HttpKernelInterface {


    /**
     * Esi Middleware adds a Surrogate-Control HTTP header when the Response needs to be parsed for ESI.
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $app
     * @param Esi $esi
     */
    public function __construct(HttpKernelInterface $app, Esi $esi)
    {
        $this->app = $app;
        $this->esi = $esi;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {

        $response = $this->app->handle($request, $type, $catch);
        if ($type == self::MASTER_REQUEST && !is_null($this->esi)) {
            $this->esi->addSurrogateControl($response);
        }
        return $response;

    }
}