<?php
/*
 * @author    Dmitrijs Vasilevskis <dmitrij.vasilevski@gmail.com>
 * @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Local\DeveloperCommands\Console\Command\Create;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Local\DeveloperCommands\Helper\CreateModule;

class Module extends Command 
{
    const COMMAND_NAME = 'create:module';

    const COMMAND_DESCRIPTION = 'Create a new module';

    const ARGUMENT_MODULE_NAME = 'name';

    const ARGUMENT_MODULE_VENDOR = 'vendor';

    /** @var CreateModule */
    private $createModule;

    public function __construct(
        CreateModule $createModule
    ) {
        $this->createModule = $createModule;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
        ->setDescription(self::COMMAND_DESCRIPTION)
        ->setDefinition([
            new InputOption(
                self::ARGUMENT_MODULE_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'Module name'
            ),
            new InputOption(
                self::ARGUMENT_MODULE_VENDOR,
                null,
                InputOption::VALUE_REQUIRED,
                'Module namespace'
            ),
        ]);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $moduleName = $input->getOption(self::ARGUMENT_MODULE_NAME);
        $vendorName = $input->getOption(self::ARGUMENT_MODULE_VENDOR);

        $isModuleCreated = $this->createModule->execute($moduleName, $vendorName);

        $result = $isModuleCreated ? Command::SUCCESS : Command::FAILURE;

        $output->writeln($isModuleCreated ? 'Module created succesfully' : 'Module creation failed');

        return $result;
    }
}
