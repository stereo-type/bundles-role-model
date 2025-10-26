<?php

/**
 * @package    DtoTOEntityTrait.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Mapper;

use ReflectionClass;

trait DtoToEntityTrait
{
    public function mapDtoToEntity(object $dto, object $entity): void
    {
        $dtoReflection = new ReflectionClass($dto);
        $entityReflection = new ReflectionClass($entity);

        foreach ($dtoReflection->getProperties() as $dtoProperty) {
            $dtoProperty->setAccessible(true);

            // Проверяем, инициализировано ли свойство в DTO
            if (!$dtoProperty->isInitialized($dto)) {
                continue;
            }

            $dtoValue = $dtoProperty->getValue($dto);
            $entityPropertyName = $dtoProperty->getName();

            // Проверяем, существует ли соответствующее свойство в сущности
            if ($entityReflection->hasProperty($entityPropertyName)) {
                $entityProperty = $entityReflection->getProperty($entityPropertyName);
                $entityProperty->setAccessible(true);
                $entityProperty->setValue($entity, $dtoValue);
            } else {
                // Если есть сеттер для свойства
                $setterMethod = 'set' . ucfirst($entityPropertyName);
                if ($entityReflection->hasMethod($setterMethod)) {
                    $entity->{$setterMethod}($dtoValue);
                }
            }
        }
    }
}
