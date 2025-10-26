<?php

/**
 * @package    RequestHandlerInterface.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Presentation\RequestHandler;

use Symfony\Component\HttpFoundation\Request;

interface RequestHandlerInterface
{
    public function handle(Request $request): object;
}
