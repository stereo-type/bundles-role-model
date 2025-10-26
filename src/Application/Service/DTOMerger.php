<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Service;

use ReflectionClass;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**Не сработает если у сущности нет сетеров и геттеров и для полей описанных snake_case*/
readonly class DTOMerger
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function mergeDtoIntoEntity(object $entity, object $dto): void
    {

        $dtoReflection = new ReflectionClass($dto);
        $entityReflection = new ReflectionClass($entity);

        foreach ($dtoReflection->getProperties() as $dtoProperty) {
            $dtoProperty->setAccessible(true);
            if ($dtoProperty->isInitialized($dto)) {
                $value = $dtoProperty->getValue($dto);

                if ($value !== null) {
                    $propertyName = $dtoProperty->getName();


                    if (!$this->propertyAccessor->isReadable($dto, $propertyName)) {
                        continue;
                    }

                    try {
                        $setterMethod = 'set' . ucfirst($propertyName);

                        // Проверяем наличие метода-сеттера в сущности
                        if ($entityReflection->hasMethod($setterMethod)) {
                            $method = $entityReflection->getMethod($setterMethod);

                            if ($method->isPublic()) {
                                $method->invoke($entity, $value);
                            }
                        } else {
                            // Попытка записи напрямую, если сеттера нет
                            //                            if ($this->propertyAccessor->isWritable($entity, $propertyName)) {
                            //                                $this->propertyAccessor->setValue($entity, $propertyName, $value);
                            //                            }
                        }
                    } catch (NoSuchPropertyException) {
                        // Пропускаем, если свойство отсутствует в сущности
                        continue;
                    }
                }
            }
        }
    }
}
