<?php

namespace AppBundle\Validator\Constraints;

use Buzz\Browser;
use Buzz\Exception\RequestException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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
    public function validate($resource, Constraint $constraint)
    {
        $browser = new Browser();
        $headers = [];
        $isDropbox = strtolower($resource->getPathType()) == 'dropbox';
        if ($isDropbox) {
            $url = 'https://api.dropboxapi.com/2/sharing/get_shared_link_metadata';
            $method = 'POST';
            $headers = [
                'Authorization: Bearer ' . $this->dropboxOauth,
                'Content-Type: application/json'
            ];
            $content = json_encode(['url' => $resource->getPath()]);
        } else {
            $url = $resource->getPath();
            $method = 'GET';
            $content = '';
        }
        try{
            $browserResponse = $browser->call($url, $method, $headers, $content);
            if ($browserResponse->getStatusCode() === 409) {
                $errors = json_decode($browserResponse->getContent(), true);
                $this->context->buildViolation('Dropbox returned an error: ' . $errors['error_summary'])
                    ->atPath('path')
                    ->addViolation();
            } elseif ($browserResponse->getStatusCode() !== 200) {
                $this->context->buildViolation($constraint->invalid_url_message)
                    ->setParameter('{{ url }}', $resource->getPath())
                    ->setParameter('{{ status_code }}', $browserResponse->getStatusCode())
                    ->atPath('path')
                    ->addViolation();
            }
            if ($isDropbox) {
                /*
                array:10 [▼
                  ".tag" => "file"
                  "url" => "https://www.dropbox.com/s/i84hiek4qz9b9ef/abc_cover_v2_1%20SBP_ISBN.pdf?dl=0"
                  "id" => "id:ooub6PjRAIMAAAAAAA7W_Q"
                  "name" => "abc_cover_v2_1 SBP_ISBN.pdf"
                  "path_lower" => "/_resources/silverback publishing/_sharedinspectioncopies/abc of behaviour change theories/abc_cover_v2_1 sbp_isbn.pdf"
                  "link_permissions" => array:3 [▼
                    "resolved_visibility" => array:1 [▼
                      ".tag" => "public"
                    ]
                    "requested_visibility" => array:1 [▼
                      ".tag" => "public"
                    ]
                    "can_revoke" => true
                  ]
                  "client_modified" => "2017-04-19T11:04:04Z"
                  "server_modified" => "2017-08-16T15:28:12Z"
                  "rev" => "1c018f31a70d48"
                  "size" => 11920185
                ]
                 */
                $dropboxData = json_decode($browserResponse->getContent(), true);
                $resource->setDropboxSize($dropboxData['size']);
                $resource->setDropboxUploaded(new \DateTime($dropboxData['client_modified']));
            }
        } catch (RequestException $e)
        {
            $this->context->buildViolation($constraint->unresolved_url_message)
                ->setParameter('{{ url }}', $resource->getPath())
                ->atPath('path')
                ->addViolation();
        }
    }
}
