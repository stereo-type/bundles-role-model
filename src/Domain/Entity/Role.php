<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Domain\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Slcorp\CoreBundle\Domain\Entity\Traits\DeletedEntityTrait;
use Slcorp\CoreBundle\Domain\Entity\Traits\HasTimestamps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Slcorp\RoleModelBundle\Application\DTO\RoleAssignToUserDTO;
use Slcorp\RoleModelBundle\Application\DTO\RoleAssignToUserDTOName;
use Slcorp\RoleModelBundle\Application\DTO\RoleCreateDTO;
use Slcorp\RoleModelBundle\Application\DTO\RoleRemoveFromUserDTO;
use Slcorp\RoleModelBundle\Application\DTO\RoleRemoveFromUserDTOName;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Role\RoleAssignToUserProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Role\RoleAssignToUserProcessorName;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Role\RoleCreateProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Role\RoleRemoveFromUserProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Role\RoleRemoveFromUserProcessorName;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Role\RoleUpdateProcessor;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: Role::PATH),
        new Post(uriTemplate: Role::PATH, input: RoleCreateDTO::class, processor: RoleCreateProcessor::class),
        new Get(uriTemplate: Role::PATH_ID),
        new Patch(uriTemplate: Role::PATH_ID, input: RoleCreateDTO::class, processor: RoleUpdateProcessor::class),
        new Delete(uriTemplate: Role::PATH_ID, security: "is_granted('ROLE_ADMIN')"),
        new Post(uriTemplate: Role::PATH . '/assign-role-to-user', input: RoleAssignToUserDTO::class, processor: RoleAssignToUserProcessor::class),
        new Post(uriTemplate: Role::PATH . '/assign-role-to-user-name', input: RoleAssignToUserDTOName::class, processor: RoleAssignToUserProcessorName::class),
        new Post(uriTemplate: Role::PATH . '/remove-role-from-user', input: RoleRemoveFromUserDTO::class, processor: RoleRemoveFromUserProcessor::class),
        new Post(uriTemplate: Role::PATH . '/remove-role-from-user-name', input: RoleRemoveFromUserDTOName::class, processor: RoleRemoveFromUserProcessorName::class),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'name' => 'exact'])]
#[ORM\Entity]
#[ORM\Table(name: 'role_model_bundle_roles')]
#[ORM\HasLifecycleCallbacks]
class Role
{
    use DeletedEntityTrait;
    use HasTimestamps;

    public const PATH_ID = '/role-model-bundle/roles/{id}';
    public const PATH = '/role-model-bundle/roles';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Operation>
     */
    #[ORM\ManyToMany(targetEntity: Operation::class, inversedBy: 'roles')]
    #[ORM\JoinTable(
        name: 'role_model_bundle_role_operations',
        joinColumns: [new ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'operation_id', referencedColumnName: 'id')]
    )]
    private Collection $operations;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
    }


    public function setId(?int $id): Role
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Role
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Role
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): Role
    {
        foreach ($this->operations as $o) {
            if ($o->getId() === $operation->getId()) {
                return $this;
            }
        }

        $this->operations->add($operation);
        return $this;
    }

    public function removeOperation(Operation $operation): Role
    {
        foreach ($this->operations as $o) {
            if ($o->getId() === $operation->getId()) {
                $this->operations->removeElement($operation);
                return $this;
            }
        }

        return $this;
    }
}
