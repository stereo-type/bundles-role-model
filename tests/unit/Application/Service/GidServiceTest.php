<?php

/**
 * @package    GidServiceTest.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\Service;

use Slcorp\RoleModelBundle\Application\Service\User\GidService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GidServiceTest extends WebTestCase
{
    private GidService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $container = static::createClient()->getContainer();
        $this->service = $container->get(GidService::class);

    }

    public function testValidateGid(): void
    {
        $userData = [
            'last_name' => 'Иващенко',
            'first_name' => 'Иван',
            'middle_name' => 'Иванович',
            'email' => 'ivanov@example.com',
        ];

        $gid = $this->service->generateGid($userData);
        echo "Generated GID: $gid\n";


        $currentData = [
            'last_name' => 'Иващенко',
            'first_name' => 'Иван',
            'middle_name' => 'Иванович',
            'email' => 'ivanov@example.com',
        ];

        $isValid = $this->service->validateGid($gid, $currentData);

        self::assertTrue($isValid);
    }
}
