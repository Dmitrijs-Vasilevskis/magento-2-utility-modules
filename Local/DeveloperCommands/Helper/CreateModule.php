<?php 
/*
 * @author    Dmitrijs Vasilevskis <dmitrij.vasilevski@gmail.com>
 * @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Local\DeveloperCommands\Helper;

use Magento\Framework\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;

class CreateModule
{
    /** @var DirectoryList */
    private $directoryList;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    protected $rootPath;

    /** @var string */
    protected $templatePath;

    protected const MODULE_PATH = '/app/code/';

    protected const TEMPLATE_PATH = '/app/code/Local/DeveloperCommands/templates/';

    protected const TEMPLATE_MODULE_XML = 'module.xml.stub';

    protected const MODULE_XML = 'etc/module.xml';

    protected const TEMPLATE_REGISTRATION = 'registration.php.stub';

    protected const REGISTRATION = 'registration.php';

    /**
     *
     * @param DirectoryList $directoryList
     * @param LoggerInterface $logger
     */
    public function __construct(
        DirectoryList $directoryList,
        LoggerInterface $logger)
    {
        $this->directoryList = $directoryList;
        $this->logger = $logger;
        $this->rootPath = $this->directoryList->getRoot();
        $this->templatePath = $this->rootPath . self::TEMPLATE_PATH;
    }

    /**
     * Execute module creation.
     *
     * @param string $moduleName
     * @param string $vendorName
     * 
     * @return bool
     */
    public function execute($moduleName, $vendorName): bool
    {
        if(!$this->getIsVendorExist($this->getVendorPath($vendorName))) {
            $this->logger->error('Vendor directory does not exist.');

            return false;
        }

        if($this->getIsModuleExist($moduleName, $vendorName)) {
            $this->logger->error("Module with the name $moduleName already exist.");

            return false;
        }

        $registration = $this->createFileFromTemplate(
            self::TEMPLATE_REGISTRATION,
            implode(DIRECTORY_SEPARATOR, [$this->getVendorPath($vendorName), $moduleName, self::REGISTRATION]),
            [
                '{{MODULE_NAME}}' => implode('_', [$vendorName, $moduleName]),
            ]
        );
        
        $moduleXml = $this->createFileFromTemplate(
            self::TEMPLATE_MODULE_XML,
            implode(DIRECTORY_SEPARATOR, [$this->getVendorPath($vendorName), $moduleName, self::MODULE_XML]),
            [
                '{{MODULE_NAME}}' => implode('_', [$vendorName, $moduleName]),
            ]
        );

        return $registration && $moduleXml;
    }

    /**
     * Return the path to the vendor directory.
     *
     * @param string $vendorName 
     * @return string
     */
    private function getVendorPath(string $vendorName): string
    {
        return $this->rootPath . self::MODULE_PATH . $vendorName;
    }

    /**
     * Check if a vendor directory exists.
     *
     * @param string $vendorName
     * @return bool
     */
    public function getIsVendorExist(string $vendorPath): bool
    {
        return is_dir($vendorPath);
    }

    /**
     * Check if a module directory exists for a given vendor and module name.
     *
     * @param string $folderName 
     * @param string $vendorName 
     * @return bool 
     */

    public function getIsModuleExist(string $folderName, string $vendorName): bool
    {
        return is_dir($this->directoryList->getRoot() . self::MODULE_PATH . $vendorName . DIRECTORY_SEPARATOR . $folderName);
    }

    /**
     * Create a file from a template.
     *
     * @param string $templateName 
     * @param string $destinationPath
     * @param array $args
     * 
     * @throws \Exception
     * @return bool|int
     */
    public function createFileFromTemplate(string $templateName, string $destinationPath, array $args): bool|int
    {
        $templateFile = implode(DIRECTORY_SEPARATOR, [$this->templatePath, $templateName]);

        if (!file_exists($templateFile)) {
            throw new \Exception("Template file $templateFile not found.");
        }

        $templateContent = file_get_contents($templateFile);

        $fileContent = str_replace(
            array_keys($args),
            array_values($args),
            $templateContent
        );

        $destinationDir = dirname($destinationPath);
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }
        
        return file_put_contents($destinationPath, $fileContent);
    }
}
