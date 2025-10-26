<?php

/**
 * @package    ResponseTrait.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Presentation\Controller;

use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

trait ResponseTrait
{
    public function getResponse(callable $function): JsonResponse
    {
        try {
            return $this->json($function(), Response::HTTP_OK);
        } catch (BundleException $e) {
            return $this->json(
                [
                    'error' => $e->getMessage(),
                    'data' => $e->getExceptionData()->toArray()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $e) {
            return $this->json(
                [
                    'error' => $e->getMessage(),
                    'data' =>
                        [
                            'type' => get_class($e),
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ]
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
