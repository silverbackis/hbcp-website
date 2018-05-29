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
        $this->type = strtolower($resource->getPathType());
        if ($this->type === 'dropbox') {
            $this->url = 'https://api.dropboxapi.com/2/sharing/get_shared_link_metadata';
            $this->method = 'POST';
            $this->headers = [
                'Authorization: Bearer ' . $dropboxOauth,
                'Content-Type: application/json'
            ];
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
}
