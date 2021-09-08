<?php

namespace FiftySq\Commerce\Channels\Drivers\Clients\Concerns;

use Illuminate\Http\Client\Response;

/**
 * @method static array get($path, array $arguments = [])
 * @method static array post($path, array $arguments = [])
 * @method static array put($path, array $arguments = [])
 * @method static array delete($path)
 */
trait MakesRequests
{
    /**
     * @param $method
     * @param $path
     * @param  array  $arguments
     * @return mixed
     */
    public function request($method, $path, array $arguments = [])
    {
        $request = $this->client()->{$method}($path, $arguments);

        return $request->json();
    }

    /**
     * @param $name
     * @param $arguments
     * @return Response
     */
    public function __call($name, $arguments): Response
    {
        $method = strtolower($name);

        if (in_array($method, ['get', 'post', 'put', 'delete'])) {
            return call_user_func($method, ...$arguments);
        }

        throw new \InvalidArgumentException("{$name} is an invalid method.");
    }
}
