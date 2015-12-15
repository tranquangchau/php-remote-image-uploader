<?php
/**
 * You can use this adapter and upload without API,
 * but Imgur limits 25 images for guest upload in a period.
 * So you should use API to avoid this issue by register an API here
 * {@link http://api.imgur.com/oauth2/addclient}.
 */
namespace RemoteImageUploader\Adapters;

use RemoteImageUploader\Factory;
use RemoteImageUploader\Interfaces\OAuth;
use Exception;

class Imgur extends Factory implements OAuth
{
    const SITE_URL = 'http://imgur.com/';
    const AUTHORIZE_ENDPOINT = 'https://api.imgur.com/oauth2/authorize';
    const TOKEN_ENDPOINT = 'https://api.imgur.com/oauth2/token';
    const UPLOAD_ENPOINT = 'https://api.imgur.com/3/image';

    const START_SESSION_ENDPOINT = 'http://imgur.com/upload/start_session';
    const GUEST_UPLOAD_ENDPOINT = 'http://imgur.com/upload';
    const KEY_EXPIRES_AT = 'EXPIRES_AT';

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), array(
            'api_key'    => null, // client id
            'api_secret' => null, // client secret

            // if you have `refresh_token` you can set it here
            // to pass authorize action.
            'refresh_token' => null,

            // If you don't want to authorize by yourself, you can set
            // this option to `true`, it will requires `username` and `password`.
            // But sometimes Imgur requires captcha for authorize so this option
            // will be failed. And you need to set it to `false` and do it by
            // yourself.
            'auto_authorize' => false,
            'username'       => null,
            'password'       => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function authorize()
    {
        if ($this->getRefreshToken()) {
            return;
        }

        $params = array(
            'client_id'     => $this['api_key'],
            'response_type' => 'code',
            'state'         => 'RIU'
        );
        $url = sprintf('%s?%s', self::AUTHORIZE_ENDPOINT, http_build_query($params));

        if (isset($_GET['code'])) {
            $this->requestToken($_GET['code']);
        } elseif ($this['auto_authorize']) {
            $this->autoAuthorize($url);
        } else {
            $this->redirectTo($url);
        }
    }

    private function autoAuthorize($url)
    {
        // imgur allows request token at first request.
        // so we should try response_type to token to reduce number of requests.
        $url = str_replace('response_type=code', 'response_type=token', $url);

        $request = $this->createRequest($url)->send();

        preg_match('#(?:name|id)=[\'"]allow[\'"].*?value="([^"]+)"#', $request, $match) && $allowValue = $match[1];

        if (empty($allowValue)) {
            throw new Exception('Auto authorize: Not found ALLOW_VALUE');
        }
        $target = $request->getOptions('url');
        $cookies = $request->getResponseArrayCookies();

        $request = $this->createRequest($target, 'POST')
            ->withHeader('Referer', $target)
            ->withCookie($cookies)
            ->withFormParam(array(
                'username'                 => $this['username'],
                'password'                 => $this['password'],
                'allow'                    => $allowValue,
                '_jafo[activeExperiments]' => '[{"expID":"exp3025","variation":"control"}]',
                '_jafo[experimentData]'    => '{}',
            ))
            ->send();

        $params = substr(strstr($request->getResponseHeaderLine('location'), '#', false), 1);
        if ($request->getResponseStatus() == 403 || !$params) {
            throw new Exception('Auto authorize failed');
        }
        parse_str($params, $token);

        $this->setToken($token);
    }

    /**
     * {@inheritdoc}
     */
    public function requestToken($authCode, $callbackUrl = '')
    {
        $request = $this->createRequest(self::TOKEN_ENDPOINT, 'POST')
            ->withFormParam(array(
                'code'          => $authCode,
                'client_id'     => $this['api_key'],
                'client_secret' => $this['api_secret'],
                'grant_type'    => 'authorization_code'
            ))
            ->send();

        if ($request->getResponseStatus() != 200) {
            throw new Exception('Request token failed');
        }

        $token = json_decode($request, true);

        $this->setToken($token);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshToken()
    {
        $request = $this->createRequest(self::TOKEN_ENDPOINT, 'POST')
            ->withFormParam(array(
                'refresh_token' => $this->getRefreshToken(),
                'client_id'     => $this['api_key'],
                'client_secret' => $this['api_secret'],
                'grant_type'    => 'refresh_token'
            ))
            ->send();

        if ($request->getResponseStatus() != 200) {
            throw new Exception('Refresh token failed');
        }

        $token = json_decode($request, true);

        $this->setToken($token);
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(array $token)
    {
        $token = array_merge($this->getData('token', array()), $token);
        $token[self::KEY_EXPIRES_AT] = time() + $token['expires_in'];

        $this->setData('token', $token, $token['expires_in']);
        $this->setData('refresh_token', $token['refresh_token'], 86400 * 365 * 10);
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->getData('token', array());
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired(array $token)
    {
        return empty($token[self::KEY_EXPIRES_AT]) || $token[self::KEY_EXPIRES_AT] < time();
    }

    /**
     * {@inheritdoc}
     */
    protected function doUpload($file)
    {
        return $this->doGuestUpload($file);
    }

    /**
     * {@inheritdoc}
     */
    protected function doTransload($url)
    {
        return $this->doGuestTransload($url);
    }

    private function getRefreshToken()
    {
        return $this['refresh_token'] ? $this['refresh_token'] : $this->getData('refresh_token');
    }

    private function getGuestSession()
    {
        if (!$session = $this->getData('guest_session')) {
            $request = $this->createRequest(self::START_SESSION_ENDPOINT)
                ->withHeader('X-Requested-With', 'XMLHttpRequest')
                ->withHeader('Referer', self::SITE_URL)
                ->send();

            $result = json_decode($request, true);

            if (empty($result['sid'])) {
                throw new Exception('Start session failed');
            }

            $session = $result['sid'];
            $this->setData('guest_session', $session, 900);
        }

        return $session;
    }

    protected function doGuestUpload($file)
    {
        $request = $this->createRequest(self::GUEST_UPLOAD_ENDPOINT, 'POST')
            ->withHeader('Referer', self::SITE_URL)
            ->withFormParam($this->getGuestUploadGeneralParams())
            ->withFormFile('Filedata', $file)
            ->send();

        return $this->handleGuestUploadResult($file, $request, 'Guest upload failed');
    }

    protected function doGuestTransload($url)
    {
        $request = $this->createRequest(self::GUEST_UPLOAD_ENDPOINT, 'POST')
            ->withHeader('Referer', self::SITE_URL)
            ->withFormParam($this->getGuestUploadGeneralParams())
            ->withFormParam('url', $url)
            ->send();

        return $this->handleGuestUploadResult($url, $request, 'Guest transload failed');
    }

    private function getGuestUploadGeneralParams()
    {
        return array(
            'current_upload' => 1,
            'total_uploads'  => 1,
            'terms'          => 1,
            'gallery_type'   => '',
            'location'       => 'outside',
            'gallery_submit' => 0,
            'create_album'   => 0,
            'album_title'    => 'Optional Album Title',
            'sid'            => $this->getGuestSession()
        );
    }

    private function handleGuestUploadResult($file, $request, $errorMessage)
    {
        $result = json_decode($request, true);

        if (isset($result['data']['hash'])) {
            return sprintf('http://i.imgur.com/%s.%s', $result['data']['hash'], $this->getExtension($file));
        }

        if (isset($result['data']['error']['message'])) {
            $this->checkReachedLimit();

            $errorMessage = sprintf('%s %s', $result['data']['error']['message'], $result['data']['error']['type']);
        }

        throw new Exception($errorMessage);
    }

    private function checkReachedLimit()
    {
        $request = $this->createRequest('http://imgur.com/upload/checkcaptcha?total_uploads=1&create_album=0')
            ->send();
        $result = json_decode($request, true);
        if (!empty($result['data']['overLimits'])) {
            throw new Exception(sprintf('Guest upload over limits, please use api. %s', $request));
        }
    }

    private function getExtension($fileName)
    {
        // bmp will be converted to jpg
        $extension = 'jpg';
        preg_match('#\.(gif|jpg|jpeg|png)$#i', $fileName, $match) && $extension = $match[1];

        return strtolower($extension);
    }
}
