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
use Slcorp\CoreBundle\Domain\Entity\Traits\DeletedEntityTrait;
use Slcorp\CoreBundle\Domain\Entity\Traits\HasTimestamps;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Slcorp\RoleModelBundle\Application\DTO\OperationAddToRoleDTO;
use Slcorp\RoleModelBundle\Application\DTO\OperationCreateDTO;
use Slcorp\RoleModelBundle\Application\DTO\OperationRemoveFromRoleDTO;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Operation\OperationAddToRoleProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Operation\OperationCreateProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Operation\OperationRemoveFromRoleProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\Operation\OperationUpdateProcessor;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: Operation::PATH),
        new Post(uriTemplate: Operation::PATH, input: OperationCreateDTO::class, processor: OperationCreateProcessor::class),
        new Get(uriTemplate: Operation::PATH_ID),
        new Patch(uriTemplate: Operation::PATH_ID, input: OperationCreateDTO::class, processor: OperationUpdateProcessor::class),
        new Delete(uriTemplate: Operation::PATH_ID, security: "is_granted('ROLE_ADMIN')"),
        new Post(uriTemplate: Role::PATH . '/add-operation-to-role', input: OperationAddToRoleDTO::class, processor: OperationAddToRoleProcessor::class),
        new Post(uriTemplate: Role::PATH . '/remove-operation-from-role', input: OperationRemoveFromRoleDTO::class, processor: OperationRemoveFromRoleProcessor::class),
    ],
    normalizationContext: ['groups' => ['operation:read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['code' => 'exact', 'id' => 'exact', 'name' => 'partial'])]
#[ORM\Entity]
#[ORM\Table(name: 'role_model_bundle_operations')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\Tree(type: "nested")]
class Operation
{
    use DeletedEntityTrait;
    use HasTimestamps;

    public const PATH_ID = '/role-model-bundle/operations/{id}';
    public const PATH = '/role-model-bundle/operations';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['operation:read'])]
    private ?int $id = null;

    #[Groups(['operation:read'])]
    #[ORM\Column(length: 255, unique: true)]
    private string $code;

    #[Groups(['operation:read'])]
    #[ORM\Column(length: 255)]
    private string $name;

    #[Groups(['operation:read'])]
    #[ORM\Column(length: 255)]
    private string $comment;

    #[Gedmo\TreeLeft]
    #[ORM\Column]
    private int $lft;

    #[Gedmo\TreeLevel]
    #[ORM\Column]
    private int $lvl;

    #[Gedmo\TreeRight]
    #[ORM\Column]
    private int $rgt;

    #[Groups(['operation:read'])]
    #[Gedmo\TreeRoot]
    #[ORM\ManyToOne(targetEntity: Operation::class)]
    #[ORM\JoinColumn(name: 'root', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Operation $root = null;

    #[Groups(['operation:read'])]
    #[Gedmo\TreeParent]
    #[ORM\ManyToOne(targetEntity: Operation::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent', nullable: true)]
    private ?Operation $parent = null;

    /** @var Collection<int, Operation> */
    #[ORM\OneToMany(targetEntity: Operation::class, mappedBy: 'parent')]
    #[ORM\OrderBy(['lft' => 'ASC'])]
    private Collection $children;

    /** @var Collection<int, Role> */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'operations')]
    private Collection $roles;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    /** @var ?Collection<int, Role> */
    private ?Collection $oldRoles = null;


    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->oldRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Operation
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Operation
    {
        $this->name = $name;
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): Operation
    {
        $this->comment = $comment;
        return $this;
    }

    public function getLft(): int
    {
        return $this->lft;
    }

    public function setLft(int $lft): Operation
    {
        $this->lft = $lft;
        return $this;
    }

    public function getLvl(): int
    {
        return $this->lvl;
    }

    public function setLvl(int $lvl): Operation
    {
        $this->lvl = $lvl;
        return $this;
    }

    public function getRgt(): int
    {
        return $this->rgt;
    }

    public function setRgt(int $rgt): Operation
    {
        $this->rgt = $rgt;
        return $this;
    }

    public function getRoot(): ?Operation
    {
        return $this->root;
    }

    public function setRoot(?Operation $root): Operation
    {
        $this->root = $root;
        return $this;
    }

    public function getParent(): ?Operation
    {
        return $this->parent;
    }

    public function setParent(?Operation $parent): Operation
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection<int,Operation>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param Collection<int,Operation> $children
     * @return $this
     */
    public function setChildren(Collection $children): Operation
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return Collection<int,Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param Collection<int,Role> $roles
     * @return $this
     */
    public function setRoles(Collection $roles): Operation
    {
        $this->roles = $roles;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Operation
    {
        $this->description = $description;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function syncRoles(): void
    {
        $roleIds = array_map(static fn ($role) => $role->getId(), $this->roles->toArray());
        foreach ($this->roles as $role) {
            $role->addOperation($this);
        }
        if ($this->oldRoles !== null) {
            foreach ($this->oldRoles as $oldRole) {
                if (!in_array($oldRole->getId(), $roleIds, true)) {
                    $oldRole->removeOperation($this);
                }
            }
        }
    }

    #[ORM\PreFlush]
    public function preFlush(): void
    {
        $ids1 = array_map(static fn ($item) => $item->getId(), $this->roles->toArray());
        $ids2 = array_map(static fn ($item) => $item->getId(), $this->oldRoles->toArray());

        if ($ids1 !== $ids2) {
            /**Если старые роли не равны новым, значит было изменение - иммитрируем обновление чтоб сработал PreUpdate*/
            $this->time_modified = new DateTime();
        }
    }

    #[ORM\PostLoad]
    public function postLoad(): void
    {
        /**Будет null потому что консутркутор не вызывается*/
        if ($this->oldRoles === null) {
            $this->oldRoles = clone $this->roles;
        }
    }
}
