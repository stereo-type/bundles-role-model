<?php

/**
 * @package    FullNameSerchFilter.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Infrastructure\Api\Filter\User;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class FullNameSearchFilter extends AbstractFilter
{
    /**
     * Применение фильтрации по полному имени (firstname, lastname, patronymic)
     */
    private function applyFilter(QueryBuilder $queryBuilder, string $fullName): void
    {
        $alias = $queryBuilder->getRootAliases()[0];
        $fullName = trim($fullName);
        if ($fullName) {
            $nameParts = explode(' ', $fullName);

            $queryBuilder
                ->andWhere("$alias.lastname LIKE :lastname")
                ->setParameter('lastname', '%' . $nameParts[0] . '%');

            if (isset($nameParts[1])) {
                $queryBuilder
                    ->andWhere("$alias.firstname LIKE :firstname")
                    ->setParameter('firstname', '%' . $nameParts[1] . '%');
            }

            if (isset($nameParts[2])) {
                $queryBuilder
                    ->andWhere("$alias.patronymic LIKE :patronymic")
                    ->setParameter('patronymic', '%' . $nameParts[2] . '%');
            }
        }
    }


    public function getDescription(string $resourceClass): array
    {
        return [
            'fullName' => [
                'property' => 'fullName',
                'type' => 'string',
                'required' => false,
                'description' => 'Поиск пользователя по полному имени (ФИО)',
            ],
        ];
    }

    /**
     * @param string $property
     * @param mixed $value
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param Operation|null $operation
     * @param array $context
     * @return void
     */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ($property === 'fullName' && !empty($value)) {
            $this->applyFilter($queryBuilder, (string)$value);
        }
    }
}
