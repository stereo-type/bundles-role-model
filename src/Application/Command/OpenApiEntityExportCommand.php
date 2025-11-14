<?php

/**
 * @package    OpenApiEntityExportCommand.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Command;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'api:openapi:export-entity',
    description: 'Dump the Open API documentation'
)]
final class OpenApiEntityExportCommand extends Command
{
    private OpenApiFactoryInterface $openApiFactory;
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly ContainerInterface $container
    ) {
        parent::__construct();

        if ($this->container->has('app.public_open_api_factory')) {
            $this->openApiFactory = $this->container->get('app.public_open_api_factory');
        } else {
            throw new \RuntimeException('api_platform.openapi.factory required');
        }
    }

    protected function configure(): void
    {
        $this
            ->addArgument('entity', InputArgument::OPTIONAL, 'Filter OpenAPI documentation by entity')
            ->addOption('yaml', 'y', InputOption::VALUE_NONE, 'Dump the documentation in YAML')
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Write output to file')
            ->addOption('spec-version', null, InputOption::VALUE_OPTIONAL, 'Open API version to use (2 or 3)', '3')
            ->addOption('api-gateway', null, InputOption::VALUE_NONE, 'Enable the Amazon API Gateway compatibility mode');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();
        $io = new SymfonyStyle($input, $output);

        $openApi = $this->openApiFactory->__invoke();
        $data = $this->normalizer->normalize($openApi, 'json', [
            'spec_version' => $input->getOption('spec-version'),
        ]);

        $entityFilter = $input->getArgument('entity');
        if ($entityFilter) {
            $filterNormalized = strtolower(trim($entityFilter));

            $data['paths'] = array_filter(
                $data['paths'] ?? [],
                static function ($operations) use ($filterNormalized) {
                    // Пройдёмся по всем методам внутри пути
                    foreach ($operations as $operation) {
                        if (isset($operation['tags']) && in_array($filterNormalized, array_map('strtolower', $operation['tags']), true)) {
                            return true;
                        }
                    }
                    return false;
                }
            );
            //            unset($data['security'], $data['components'], $data['webhooks'], $data['tags'], $data['servers'], $data['info'], $data['openapi']);
            unset($data['security'], $data['webhooks'], $data['tags'], $data['servers'], $data['info'], $data['openapi']);
        }

        if ($input->getOption('yaml') && !class_exists(Yaml::class)) {
            $output->writeln('The "symfony/yaml" component is not installed.');

            return Command::FAILURE;
        }

        $content = $input->getOption('yaml')
            ? Yaml::dump($data, 10, 2, Yaml::DUMP_OBJECT_AS_MAP | Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_NUMERIC_KEY_AS_STRING)
            : (json_encode($data, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES) ?: '');

        $filename = $input->getOption('output');
        if ($filename && \is_string($filename)) {
            $filesystem->dumpFile($filename, $content);
            $io->success(\sprintf('Data written to %s.', $filename));

            return Command::SUCCESS;
        }

        $output->writeln($content);

        return Command::SUCCESS;
    }

    /**php bin/console api:openapi:export-entity*/
    public static function getDefaultName(): string
    {
        return 'api:openapi:export-entity';
    }
}
