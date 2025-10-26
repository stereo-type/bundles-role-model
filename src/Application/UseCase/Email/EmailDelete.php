<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\Email;

use Slcorp\RoleModelBundle\Application\Service\Email\EmailService;

readonly class EmailDelete
{
    public function __construct(
        private EmailService $service,
    ) {
    }

    public function execute(int $id, bool $flush = true): bool
    {
        return $this->service->delete($id, $flush);
    }

}
