<?php

/**
 * @package    CommandTrait.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Command;

use RuntimeException;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

trait CommandTrait
{
    private float $_begin = 0;
    private float $_end = 0;
    private float $_interrupt = 0;

    private ConsoleStyle $io;
    private InputInterface $input;

    protected function now(): string
    {
        return date('d.m.Y H:i:s');
    }

    protected function name(): string
    {
        return static::class;
    }

    protected function log(string $message, bool $time = true): void
    {
        if ($time) {
            $message = '[' . $this->now() . '] ' . $message;
        }
        $this->io->text($message);
    }

    protected function success(string $message): void
    {
        $this->io->block($message, 'OK', 'fg=black;bg=green', ' ', true);
    }

    protected function error(string $message): void
    {
        $this->io->error($message);
    }

    protected function info(string $message): void
    {
        $this->io->info($message);
    }

    protected function section(string $message): void
    {
        $this->io->section($message);
    }

    protected function warning(string $message): void
    {
        $this->io->warning($message);
    }

    protected function note(string $message): void
    {
        $this->io->note($message);
    }

    protected function caution(string $message): void
    {
        $this->io->caution($message);
    }

    protected function start_log(): void
    {
        $this->_begin = microtime(true);
        $this->section('[' . $this->now() . '] Start processing execute: ' . $this->name());
    }

    protected function error_log(Throwable $error): void
    {
        $this->_interrupt = microtime(true);
        $this->error('[' . $this->now() . '] Error after : ' . ($this->_interrupt - $this->_begin) . ' sec. Message: ' . $error->getMessage());

        $arguments = $this->input->getArguments();
        if (isset($arguments['debug'])) {
            $message = PHP_EOL . 'File: ' . $error->getFile();
            $message .= PHP_EOL . 'Line: ' . $error->getLine();
            $message .= PHP_EOL . 'Trace: ' . $error->getTraceAsString();
            $this->error($message);
        }
    }

    protected function end_log(): void
    {
        $this->_end = microtime(true);
        $this->success('[' . $this->now() . '] Finished processing. Time spend ' . ($this->_end - $this->_begin) . ' sec.');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->io = new ConsoleStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->start_log();
        try {
            $result = $this->runCommand();
            if ($result === Command::SUCCESS) {
                $this->end_log();
            } elseif ($result === Command::INVALID) {
                $this->error_log(new RuntimeException('Некорректные данные'));
            } elseif ($result === Command::FAILURE) {
                $this->error_log(new RuntimeException('Ошибка выполнения'));
            }
            return $result;
        } catch (Throwable $e) {
            $this->error_log($e);
            return Command::FAILURE;
        }
    }

}
