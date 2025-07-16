<?php

declare(strict_types=1);
/**
 * Copyright (C) Hydrogenpay - All Rights Reserved
 *
 * File: HydrogenpayPublishCommand.php
 * Author: Hydrogenpay
 *   Email: <support@hydrogenpay.com>
 *   Website: https://hydrogenpay.com
 * Date: 07/8/25
 * Time: 7:00 PM
 */

namespace HydrogenAfrica\HydrogenpayCi4\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Autoload;

class HydrogenpayPublishCommand extends BaseCommand
{
    /**
     * The group this command will appear under
     * when listing commands.
     *
     * @var string
     */
    protected $group = 'Hydrogenpay';

    /**
     * The name of the command.
     *
     * @var string
     */
    protected $name = 'hydrogenpay:publish';

    /**
     * Description of how to use this command.
     *
     * @var string
     */
    protected $usage = 'hydrogenpay:publish';

    /**
     * Short description of the command.
     *
     * @var string
     */
    protected $description = 'Publish Hydrogenpay configuration into the current application.';

    /**
     * Command options.
     *
     * @var array
     */
    protected $options = [
        '-f' => 'Force overwrite all existing files in destination',
    ];

    /**
     * Holds the source path of the package.
     *
     * @var string
     */
    protected string $sourcePath;

    /**
     * Run the command.
     *
     * @param array $params
     */
    public function run(array $params): void
    {
        // Determine source path of the package
        $this->determineSourcePath();

        // Publish the Hydrogenpay configuration file
        $this->publishConfig();
    }

    /**
     * Publish the Hydrogenpay config file to the app Config directory.
     */
    protected function publishConfig()
    {
        $path = "{$this->sourcePath}/Config/Hydrogenpay.php";

        // Load and update the namespace and parent class
        $content = file_get_contents($path);
        $content = str_replace('namespace HydrogenAfrica\HydrogenpayCi4\Config', 'namespace Config', $content);
        $content = str_replace("use CodeIgniter\\Config\\BaseConfig;\n", '', $content);
        $content = str_replace('extends BaseConfig', 'extends \\HydrogenAfrica\\HydrogenpayCi4\\Config\\Hydrogenpay', $content);

        // Write updated config to app/Config
        $this->writeFile('Config/Hydrogenpay.php', $content);
    }

    /**
     * Determine the source directory for the files to publish.
     */
    protected function determineSourcePath(): void
    {
        $this->sourcePath = realpath(__DIR__ . '/../');

        if ($this->sourcePath === '/' || empty($this->sourcePath)) {
            CLI::error('Unable to determine the correct source directory. Exiting.');

            exit();
        }
    }

    /**
     * Write a file to the destination directory,
     * handling overwrites and showing CLI messages.
     *
     * @param string $path    Path relative to app namespace
     * @param string $content File content
     */
    protected function writeFile(string $path, string $content): void
    {
        $config  = new Autoload();
        $appPath = $config->psr4[APP_NAMESPACE];

        $filename  = $appPath . $path;
        $directory = dirname($filename);

        // Create the directory if it doesn't exist
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Check for existing file and ask about overwrite if necessary
        if (file_exists($filename)) {
            $overwrite = (bool) CLI::getOption('f');

            if (! $overwrite && CLI::prompt("File '{$path}' already exists in destination. Overwrite?", ['n', 'y']) === 'n') {
                CLI::error("Skipped {$path}. If you wish to overwrite, please use the '-f' option or reply 'y' to the prompt.");

                return;
            }
        }

        // Attempt to write the file and show status
        if (write_file($filename, $content)) {
            CLI::write(CLI::color('Created: ', 'green') . $path);
        } else {
            CLI::error("Error creating {$path}.");
        }
    }
}
