<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Service\User;

use JsonException;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class GidService
{
    private string $secret;
    private bool $needGid;
    private int $maxLength;


    public function __construct(
        private ParameterBagInterface $parameterBag,
    ) {
        $this->secret = (string)$this->parameterBag->get('slcorp_role_model.secret_key');
        $this->needGid = (bool)$this->parameterBag->get('slcorp_role_model.use_gid');
        $this->maxLength = (int)$this->parameterBag->get('slcorp_role_model.max_length');
    }

    public function needGidGenerate(): bool
    {
        return $this->needGid;
    }

    /**
     * @throws JsonException
     */
    public function generateGid(array $userData): string
    {
        $secretKey = $this->secret;
        $payload = hash('sha256', json_encode($userData, JSON_THROW_ON_ERROR));
        $signature = hash_hmac('sha256', $payload, $secretKey, true);
        $encodedSignature = base64_encode($signature);
        $gid = sprintf('%s.%s', $payload, $encodedSignature);
        if (strlen($gid) > $this->maxLength) {
            throw new RuntimeException('Maximum length exceeded of payload: expected ' . $this->maxLength . ', received: ' . strlen($gid));
        }
        return $gid;
    }

    /**
     * @param string $gid
     * @param array $currentData
     * @return bool
     * @throws JsonException
     */
    public function validateGid(string $gid, array $currentData): bool
    {
        $secretKey = $this->secret;

        if (!strpos($gid, '.')) {
            return false;
        }

        [$payload, $encodedSignature] = explode('.', $gid, 2);

        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secretKey, true));

        if (!hash_equals($expectedSignature, $encodedSignature)) {
            return false;
        }

        $expectedDataHash = hash('sha256', json_encode($currentData, JSON_THROW_ON_ERROR));
        return hash_equals($expectedDataHash, $payload);
    }


}
