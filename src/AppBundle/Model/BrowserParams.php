<?php

namespace AppBundle\Model;

use AppBundle\Entity\Resource;

class BrowserParams
{
    /** @var string */
    private $url;
    /** @var string */
    private $method = 'GET';
    /** @var array */
    private $headers = [];
    /** @var string */
    private $content = '';
    /** @var string */
    private $type;

    public function __construct(Resource $resource, string $dropboxOauth = '')
    {
        $this->headers = [
            'Accept' => 'text/html,application/xhtml+xml',
            'accept-language' => 'en-GB,en;',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36',
            'upgrade-insecure-requests' => '1',
            'dnt' => '1',
            'cache-control' => 'max-age=0',
            'accept-encoding' => 'gzip, deflate, br'
        ];
        $this->type = strtolower($resource->getPathType());
        if ($this->type === 'dropbox') {
            $this->url = 'https://api.dropboxapi.com/2/sharing/get_shared_link_metadata';
            $this->method = 'POST';
            $this->headers['Authorization'] = 'Bearer ' . $dropboxOauth;
            $this->headers['Content-Type'] = 'application/json';
            $this->content = json_encode(['url' => $resource->getPath()]);
        } elseif ($this->type === 'osf') {
            $exploded = explode('/', $resource->getPath());
            $fileId = $exploded[\count($exploded)-2];
            $this->url = sprintf('https://api.osf.io/v2/files/%s/', $fileId);
        } else {
            $this->url = $resource->getPath();
        }
    }

    public function getBrowserCallArray(): array
    {
        return [
            $this->url,
            $this->method,
            $this->headers,
            $this->content
        ];
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array|string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return false|string
     */
    public function getContent()
    {
        return $this->content;
    }
}
