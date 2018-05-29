<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Model\BrowserParams;
use Buzz\Browser;
use Buzz\Exception\RequestException;
use Buzz\Message\Response;
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
        $browser = new Browser();
        $browserParams = new BrowserParams($resource, $this->dropboxOauth);
        try {
            $browserResponse = $browser->call(...$browserParams->getBrowserCallArray());
        } catch (RequestException $e) {
            $this->context->buildViolation($constraint->unresolved_url_message)
                ->setParameter('{{ url }}', $resource->getPath())
                ->atPath('path')
                ->addViolation();
            return;
        }
        $this->processResponse($resource, $constraint, $browserResponse, $browserParams);
    }

    private function processResponse(ResourceEntity $resource, Constraint $constraint, Response $response, BrowserParams $browserParams): void
    {
        $statusCode = $response->getStatusCode();
        switch ($browserParams->getType()) {
            case 'dropbox':
                $this->processDropboxResponse($resource, $constraint, $response);
                break;
            case 'osf':
                $this->processOsfResponse($resource, $constraint, $response);
                break;
            default:
                $this->genericStatusCodeCheck($resource, $constraint, $response, $statusCode);
                break;
        }
    }

    private function genericStatusCodeCheck(ResourceEntity $resource, Constraint $constraint, Response $response, int $statusCode): bool
    {
        if ($statusCode !== 200) {
            $this->context->buildViolation($constraint->invalid_url_message)
                ->setParameter('{{ url }}', $resource->getPath())
                ->setParameter('{{ status_code }}', $response->getStatusCode())
                ->atPath('path')
                ->addViolation();
            return false;
        }
        return true;
    }

    private function processDropboxResponse(ResourceEntity $resource, Constraint $constraint, Response $response): void
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode === 409) {
            $errors = json_decode($response->getContent(), true);
            $this->context->buildViolation('Dropbox returned an error: ' . $errors['error_summary'])
                ->atPath('path')
                ->addViolation();
            return;
        }
        if ($this->genericStatusCodeCheck($resource, $constraint, $response, $statusCode)) {
            $dropboxData = json_decode($response->getContent(), true);
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

    private function processOsfResponse(ResourceEntity $resource, Constraint $constraint, Response $response): void
    {
        $statusCode = $response->getStatusCode();
        if ($this->genericStatusCodeCheck($resource, $constraint, $response, $statusCode)) {
            $osfData = json_decode($response->getContent(), true);
            $attr = $osfData['data']['attributes'];
            $resource->setDropboxSize($attr['size']);
            $resource->setDropboxUploaded(new \DateTime($attr['date_modified']));
        }
    }
}
