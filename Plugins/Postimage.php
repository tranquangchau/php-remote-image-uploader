<?php
/**
 * Plugin for http://postimage.org
 *
 * @release Jun 19, 2014
 * @update Nov 07, 2014
 */
class ChipVN_ImageUploader_Plugins_Postimage extends ChipVN_ImageUploader_Plugins_Abstract
{
    const FREE_UPLOAD_ENPOINT    = 'http://postimage.org/';
    const ACCOUNT_UPLOAD_ENPOINT = 'http://postimg.org/';
    /**
     * Gets upload url endpoint
     *
     * @return string
     */
    private function getUrlEnpoint()
    {
        return $this->getCache()->get('session_login')
            ? self::ACCOUNT_UPLOAD_ENPOINT
            : self::FREE_UPLOAD_ENPOINT;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLogin()
    {
        if (!$this->getCache()->get('session_login')) {
            $this->resetHttpClient();
            $this->client->execute('http://postimage.org/profile.php', 'POST', array(
                'login'    => $this->username,
                'password' => $this->password,
                'submit'   => '',
            ));

            $this->checkHttpClientErrors(__METHOD__);

            if ($this->client->getResponseStatus() == 302
                && $this->client->getResponseArrayCookies('userlogin') != 'deleted'
            ) {
                $this->getCache()->set('session_login', $this->client->getResponseArrayCookies());
            } else {
                $this->getCache()->deleteGroup($this->getIdentifier());
                $this->throwException('%s: Login failed.', __METHOD__);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doUpload()
    {
        $endpoint = $this->getUrlEnpoint();
        $time     = time();

        $this->resetHttpClient();
        $this->client->setSubmitMultipart();
        if ($this->useAccount) {
            $this->client->setCookies($this->getCache()->get('session_login'));
        }
        $this->client->setReferer($endpoint);
        $this->client->setParameters(array(
            'upload'         => '@' . $this->file,
            'session_upload' => $time,
            'um'             => 'computer',
            'forumurl'       => $endpoint,
            'upload_error'   => '',
        ));
        $this->client->setParameters($this->getGeneralParameters());

        $this->client->execute($endpoint);

        $galleryId = (string) $this->client;

        $this->resetHttpClient();
        if ($this->useAccount) {
            $this->client->setCookies($this->getCache()->get('session_login'));
        }
        $this->client->setReferer($endpoint);
        $this->client->setParameters(array(
            'upload[]'       => '',
            'session_upload' => $time,
            'um'             => 'computer',
            'forumurl'       => $endpoint,
            'gallery_id'     => $galleryId,
            'upload_error'   => '',
        ));
        $this->client->setParameters($this->getGeneralParameters());
        $this->client->execute($endpoint, 'POST');

        $this->checkHttpClientErrors(__METHOD__);

        return $this->getImageFromResult($this->client->getResponseHeaders('location'));
    }

    /**
     * {@inheritdoc}
     */
    protected function doTransload()
    {
        $endpoint = $this->getUrlEnpoint();

        $this->resetHttpClient();
        if ($this->useAccount) {
            $this->client->setCookies($this->getCache()->get('session_login'));
        }
        $this->client->setReferer($endpoint);
        $this->client->setParameters(array(
            'forumurl' => $endpoint,
            'ui'       => $ui,
            'um'       => 'web',
            'url_list' => $this->url,
        ));
        $this->client->setParameters($this->getGeneralParameters());
        $this->client->execute($endpoint, 'POST'); // 'index.php?um=web'

        $this->checkHttpClientErrors(__METHOD__);

        return $this->getImageFromResult($this->client->getResponseHeaders('location'));
    }

    /**
     * General parameters for sending.
     *
     * @return array
     */
    protected function getGeneralParameters()
    {
        $time = time();
        $ui   = '24__1440__900__true__?__?__' . date('d/m/Y H:i:s', $time) . '__' . $this->client->getUserAgent() . '__';

        return array(
            'mode'    => 'local',
            'areaid'  => '',
            'hash'    => '',
            'code'    => '',
            'content' => '',
            'tpl'     => '.',
            'ver'     => '',
            'addform' => '',
            'mforum'  => '',
            'submit'  => 'Upload It!',
            'ui'      => $ui,
            'adult'   => 'no',
            'optsize' => '0',
        );
    }

    /**
     * Parse and get image url from result page.
     * Eg: http://postimg.org/image/wvznrbllz/d5a5b291/
     *
     * @param  string $url
     * @return string
     */
    private function getImageFromResult($url)
    {
        $imageId  = $this->getMatch('#^http://post(?:img|image)\.org/\w+/([^/]+)/.*?#', $url);

        if (! $imageId) {
            $this->throwException('%s: Image ID not found.', __METHOD__);
        }

        $this->resetHttpClient();
        $this->client->setFollowRedirect(true, 1);
        $this->client->execute($url);

        $imageUrl = $this->getMatch('#id="code_2".*?>(https?://\w+\.postimg\.org/\w+/\w+\.\w+)#i', $this->client);

        if (!$imageUrl) {
            $this->throwException('%s: Image URL not found.', __METHOD__);
        }

        return $imageUrl;
    }
}
