<?php
/*
 * @author    Dmitrijs Vasilevskis <dmitrij.vasilevski@gmail.com>
 * @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Local_DeveloperCommands',
    __DIR__
);
