<?php

namespace FiftySq\Commerce\Channels\Clients;

abstract class AbstractClient
{
    /**
     * Make the API client.
     *
     * @return mixed
     */
    abstract public function client();

    /**
     * Provide the base URL.
     *
     * @return mixed
     */
    abstract public function baseUrl();

    /**
     * Provide the headers.
     *
     * @return mixed
     */
    abstract public function headers();
}
