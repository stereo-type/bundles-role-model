<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Exception;

enum ExceptionType: string
{
    case ConstraintViolationList = 'ConstraintViolationList';
    case NotFound = 'NotFound';
    case AlreadyExist = 'AlreadyExist';
    case UnExpected = 'UnExpected';
    case IncorrectData = 'IncorrectData';
}
