<?php
/**
 * Connect Generator XL extension for Magento
 * (c) 2014 Benoît Xylo
 *
 * PHP version 5
 *
 * NOTICE OF LICENSE
 *
 * This file is part of Connect Generator XL for Magento
 *
 * Connect Generator XL is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License 3.0
 * as published by the Free Software Foundation.
 *
 * Connect Generator XL is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License 3.0 for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Connect Generator XL; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @copyright  2014 Benoît Xylo <devel@xylo.pl>
 * @license    http://www.gnu.org/licenses/agpl-3.0.html  GNU Affero General Public License 3.0
 */

// modman bug workaround
if (array_key_exists('SCRIPT_FILENAME', $_SERVER) && is_link($_SERVER['SCRIPT_FILENAME'])) {
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname($_SERVER['SCRIPT_FILENAME']));
}

require_once 'abstract.php';

/**
 * Shell script for generating Magento Connect packages
 *
 * @package    Xylo_ConnectGenerator
 * @subpackage shell
 * @author     Benoît Xylo <devel@xylo.pl>
 * @version    Release: $Release$
 */
class Xylo_ConnectGenerator_Shell extends Mage_Shell_Abstract {

    /**
     * Run script
     *
     */
    public function run() {
        if ($this->getArg('generate')) {
            $generator = Mage::getModel('xl_connectgenerator/generator');
            if ($config = $this->getArg('json')) {
                $generator->setJsonConfig($config);
            } elseif ($config = $this->getArg('xml')) {
                $generator->setXmlConfig($config);
            } elseif ($config = $this->getArg('yaml')) {
                $generator->setYamlConfig($config);
            } else {
                echo $this->usageHelp();
                return;
            }
            $target = $this->getArg('target') ? $this->getArg('target') : null;
            die($generator->process($target));
        } elseif ($this->getArg('validate')) {
            die('NOT IMPLEMENTED');
        } else {
            echo $this->usageHelp();
            return;
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp() {
        return <<<USAGE
Usage:  php -f connect-generator.php -- [options]
        php -f connect-generator.php -- generate --yaml package.yaml --target /package_folder

  generate              Generates Magento Connect package from provided configuration
  validate              Validates provided configuration
  --json <file path>    Path to configuration file in JSON format
  --yaml <file path>    Path to configuration file in YAML format
  --xml <file path>     Path to configuration file in XML format
  --target <file path>  Folder path where the generated package shall be saved to

USAGE;
    }

}

$shell = new Xylo_ConnectGenerator_Shell();
$shell->run();
