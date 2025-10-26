<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\Email;

use Slcorp\CoreBundle\Application\Service\Validator\ValidatorDTOInterface;
use Slcorp\RoleModelBundle\Application\DTO\EmailCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Service\Email\EmailService;
use Slcorp\RoleModelBundle\Domain\Entity\Email;

readonly class EmailCreate
{
    public function __construct(
        private EmailService $service,
        private ValidatorDTOInterface $validationService,
    ) {
    }

    public function execute(EmailCreateDTO $dto, bool $flush = true): Email
    {
        $errors = $this->validationService->validateDTO($dto);
        if ($errors->count() > 0) {
            throw BundleException::validationErrors($errors);
        }
        return $this->service->create($dto, $flush);
    }
}
