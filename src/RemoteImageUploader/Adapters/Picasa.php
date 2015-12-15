<?php

namespace RemoteImageUploader\Adapters;

use RemoteImageUploader\Interfaces\OAuth;
use RemoteImageUploader\Factory;

class Picasa extends Factory implements OAuth
{
    /**
     * {@inheritdoc}
     */
    public function authorize()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function requestToken($authCode, $callbackUrl = '')
    {
    }

    /**
     * {@inheritdoc}
     */
    public function refreshToken(array $token)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(array $token)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired(array $token)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function doUpload($file)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function doTransload($url)
    {
    }
}
