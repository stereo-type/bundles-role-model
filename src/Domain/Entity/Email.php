<?php

namespace Slcorp\RoleModelBundle\Domain\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Slcorp\CoreBundle\Domain\Entity\Traits\HasTimestamps;
use Doctrine\ORM\Mapping as ORM;
use Slcorp\RoleModelBundle\Application\DTO\EmailCreateDTO;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Email\EmailCreateProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Email\EmailUpdateProcessor;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: Email::PATH),
        new Post(uriTemplate: Email::PATH, input: EmailCreateDTO::class, processor: EmailCreateProcessor::class),
        new Get(uriTemplate: Email::PATH_ID),
        new Patch(uriTemplate: Email::PATH_ID, input: EmailCreateDTO::class, processor: EmailUpdateProcessor::class),
        new Delete(uriTemplate: Email::PATH_ID, security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['email:read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['email' => 'partial','id' => 'exact'])]
#[ORM\Entity]
#[ORM\Table(name: 'role_model_bundle_user_emails')]
#[ORM\HasLifecycleCallbacks]
class Email
{
    use HasTimestamps;

    public const PATH_ID = '/role-model-bundle/emails/{id}';
    public const PATH = '/role-model-bundle/emails';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['email:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['email:read'])]
    private string $email;

    #[ORM\ManyToOne(inversedBy: 'emails')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['email:read'])]
    private User $user;

    public function setId(?int $id): Email
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): Email
    {
        $this->email = $email;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Email
    {
        $this->user = $user;

        return $this;
    }
}
