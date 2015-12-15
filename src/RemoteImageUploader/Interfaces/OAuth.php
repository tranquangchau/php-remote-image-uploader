<?php

namespace RemoteImageUploader\Interfaces;

interface OAuth
{
    /**
     * Direct user to site for authorization
     * and process get access token if user have authorized.
     *
     * @return void
     */
    public function authorize();

    /**
     * Request token with auth code.
     *
     * @param string $authCode
     * @param string $callbackUrl
     *
     * @return void
     *
     * @throws Exception if failure.
     */
    public function requestToken($authCode, $callbackUrl = '');

    /**
     * Refresh token and save new information.
     *
     * @return void
     *
     * @throws Exception if failure.
     */
    public function refreshToken();

    /**
     * Sets token.
     *
     * @param array $token
     */
    public function setToken(array $token);

    /**
     * Returns token information.
     *
     * @return array
     */
    public function getToken();

    /**
     * Determine if token is expired.
     *
     * @param array $token
     *
     * @return boolean
     */
    public function isExpired(array $token);
}
