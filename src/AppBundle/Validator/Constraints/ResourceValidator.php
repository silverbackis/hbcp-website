<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Model\BrowserParams;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use AppBundle\Entity\Resource as ResourceEntity;

class ResourceValidator extends ConstraintValidator
{
    private $dropboxOauth;

    public function __construct(string $dropboxOauth)
    {
        $this->dropboxOauth = $dropboxOauth;
    }

    /**
     * @param \AppBundle\Entity\Resource $resource
     * @param Constraint $constraint
     */
    public function validate($resource, Constraint $constraint): void
    {
        $browserParams = new BrowserParams($resource, $this->dropboxOauth);
        $client = new Client();

        try {
            $browserResponse = $client->request($browserParams->getMethod(), $browserParams->getUrl(), [
                'headers' => $browserParams->getHeaders(),
                'body' => $browserParams->getContent()
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
//            dump($response, $response->getBody()->getContents());
            $this->processResponse($resource, $constraint, $response, $browserParams);
//            $this->context->buildViolation($constraint->unresolved_url_message)
//                ->setParameter('{{ url }}', $browserParams->getUrl())
//                ->atPath('path')
//                ->addViolation();
            return;
        }
        $this->processResponse($resource, $constraint, $browserResponse, $browserParams);
    }

    private function processResponse(ResourceEntity $resource, Constraint $constraint, ResponseInterface $response, BrowserParams $browserParams): void
    {
//        $statusCode = $response->getStatusCode();
        switch ($browserParams->getType()) {
            case 'dropbox':
                $this->processDropboxResponse($resource, $constraint, $response, $browserParams->getUrl());
                break;
            case 'osf':
                $this->processOsfResponse($resource, $constraint, $response, $browserParams->getUrl());
                break;
//            default:
//                $this->genericStatusCodeCheck($resource, $constraint, $response, $statusCode, $browserParams->getUrl());
//                break;
        }
    }

    private function genericStatusCodeCheck(ResourceEntity $resource, Constraint $constraint, ResponseInterface $response, int $statusCode, string $requestUrl): bool
    {
        if ($statusCode !== 200) {
            $this->context->buildViolation($constraint->invalid_url_message)
                ->setParameter('{{ url }}', $requestUrl)
                ->setParameter('{{ status_code }}', $response->getStatusCode())
                ->atPath('path')
                ->addViolation();
            return false;
        }
        return true;
    }

    private function processDropboxResponse(ResourceEntity $resource, Constraint $constraint, ResponseInterface $response, string $requestUrl): void
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode === 409) {
            $errors = json_decode($response->getBody(), true);
            $this->context->buildViolation('Dropbox returned an error: ' . $errors['error_summary'])
                ->atPath('path')
                ->addViolation();
            return;
        }
        if ($this->genericStatusCodeCheck($resource, $constraint, $response, $statusCode, $requestUrl)) {
            $dropboxData = json_decode($response->getBody(), true);
            if (!isset($dropboxData['size'])) {
                $this->context->buildViolation($constraint->invalid_url_message)
                    ->setParameter('{{ url }}', $resource->getPath())
                    ->setParameter('{{ status_code }}', $response->getStatusCode())
                    ->atPath('path')
                    ->addViolation();
                return;
            }
            $resource->setDropboxSize($dropboxData['size']);
            $resource->setDropboxUploaded(new \DateTime($dropboxData['client_modified']));
        }
    }

    private function processOsfResponse(ResourceEntity $resource, Constraint $constraint, ResponseInterface $response, string $requestUrl): void
    {
        $statusCode = $response->getStatusCode();
        if ($this->genericStatusCodeCheck($resource, $constraint, $response, $statusCode, $requestUrl)) {
            $osfData = json_decode($response->getBody(), true);
            $attr = $osfData['data']['attributes'];
            $resource->setDropboxSize($attr['size']);
            $resource->setDropboxUploaded(new \DateTime($attr['date_modified']));
        }
    }
}
