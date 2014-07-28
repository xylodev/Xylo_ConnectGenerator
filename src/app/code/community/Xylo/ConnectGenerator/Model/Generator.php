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

/**
 * Magento Connect package generator
 *
 * @package    Xylo_ConnectGenerator
 * @subpackage Model
 * @author     Benoît Xylo <devel@xylo.pl>
 * @version    Release: $Release$
 */
class Xylo_ConnectGenerator_Model_Generator {

    /**
     * Config file path
     *
     * @var string
     */
    protected $_config = null;

    /**
     * Config file format
     *
     * @var string
     */
    protected $_configType = null;

    /**
     * Config parser object instance
     *
     * @var Xylo_ConnectGenerator_Model_Parser_Interface
     */
    protected $_configParser = null;

    /**
     * Package content targets array
     *
     * @var array
     */
    protected $_targetMap = null;

    /**
     * Reads config file and returns its content
     *
     * @return string Config file content
     */
    protected function _getConfig() {
        $this->_validateConfig();
        return file_get_contents($this->_config);
    }

    /**
     * Returns instance of the overriden connect/extension model
     *
     * @return Xylo_ConnectGenerator_Model_Extension Extension model object
     */
    protected function _getExtensionModel() {
        return Mage::getSingleton('xl_connectgenerator/extension');
    }

    /**
     * Instantiates and returns config parser object
     *
     * @return Xylo_ConnectGenerator_Model_Parser_Interface Config parser object
     */
    protected function _getParser() {
        if (null === $this->_configParser) {
            $this->_configParser = Xylo_ConnectGenerator_Model_Parser::instantiate($this->_configType);
        }
        return $this->_configParser;
    }

    /**
     * Initializes and returns package content targets array
     *
     * @return array Package content targets array
     */
    protected function _getContentTargetMap() {
        if (null === $this->_targetMap) {
            $targets = new Mage_Connect_Package_Target;
            $this->_targetMap = $targets->getTargets();
        }
        return $this->_targetMap;
    }

    /**
     * Converts provided path to the target name and path
     * relative to the package target root path
     *
     * @param  string $contentPath Path to the file or folder
     * @return Varien_Object
     */
    protected function _convertContentPath($contentPath) {
        $contentPath = './' . ltrim($contentPath, './');
        foreach ($this->_getContentTargetMap() as $target => $uri) {
            if ($target != 'mageweb' && $target != 'mage' && strpos($contentPath, $uri) === 0) {
                return new Varien_Object(array(
                    'target' => $target,
                    'path' => trim(substr($contentPath, strlen($uri)), '/')
                ));
            }
        }
        return new Varien_Object(array(
            'target' => 'mage',
            'path' => ltrim($contentPath, './')
        ));
    }

    /**
     * Exctracts contents from provided configuration and converts
     * it to the format readable by extension model
     *
     * @param  array $config Parsed configuration
     * @return array Contents array
     */
    protected function _exctractContentsConfig($config) {
        $contentsConfig = array(
            'target' => array(),
            'path' => array(),
            'type' => array(),
            'include' => array(),
            'ignore' => array()
        );
        if (array_key_exists('files', $config) && is_array($config['files'])) {
            foreach ($config['files'] as $file) {
                $contentTarget = $this->_convertContentPath($file);
                if ($contentTarget) {
                    $contentsConfig['target'][] = $contentTarget->getTarget();
                    $contentsConfig['path'][] = $contentTarget->getPath();
                    $contentsConfig['type'][] = 'file';
                    $contentsConfig['include'][] = '';
                    $contentsConfig['ignore'][] = '';
                }
            }
        }
        if (array_key_exists('directories', $config) && is_array($config['files'])) {
            foreach ($config['directories'] as $directory) {
                $contentTarget = $this->_convertContentPath($directory);
                if ($contentTarget) {
                    $contentsConfig['target'][] = $contentTarget->getTarget();
                    $contentsConfig['path'][] = $contentTarget->getPath();
                    $contentsConfig['type'][] = 'dir';
                    $contentsConfig['include'][] = '';
                    $contentsConfig['ignore'][] = '';
                }
            }
        }
        return array('contents' => $contentsConfig);
    }

    /**
     * Exctracts dependencies from provided configuration
     * and converts it to the format readable by extension model
     *
     * @param  array $config Parsed configuration
     * @return array Dependencies array
     */
    protected function _exctractDependenciesConfig($config) {
        $dependenciesConfig = array();
        if (array_key_exists('depends', $config) && is_array($config['depends'])) {
            if (array_key_exists('php', $config['depends']) && is_array($config['depends']['php'])) {
                if (array_key_exists('min', $config['depends']['php'])) {
                    $dependenciesConfig['depends_php_min'] = $config['depends']['php']['min'];
                }
                if (array_key_exists('max', $config['depends']['php'])) {
                    $dependenciesConfig['depends_php_max'] = $config['depends']['php']['max'];
                }
            }
        }
        return $dependenciesConfig;
    }

    /**
     * Checks if config file format is set and
     * if the config file exists and is readable
     *
     * @throws Xylo_ConnectGenerator_Exception If $config parsing fails
     * @return bool
     */
    protected function _validateConfig() {
        if (null === $this->_config) {
            throw new Xylo_ConnectGenerator_Exception('Configuration path not set');
        }
        if (null === $this->_configType) {
            throw new Xylo_ConnectGenerator_Exception('Configuration type not set');
        }
        if (!is_readable($this->_config)) {
            throw new Xylo_ConnectGenerator_Exception('Configuration file does not exist or cannot be read');
        }
        return true;
    }

    /**
     * Unset redundant params, not used by the extension model,
     * from the config array
     *
     * @param  array $config Parsed config array
     * @return array Config array with redundant params unset
     */
    protected function _unsetRedundantConfigParams($config) {
        $cleanConfig = $config;
        if (array_key_exists('files', $cleanConfig)) {
            unset($cleanConfig['files']);
        }
        if (array_key_exists('directories', $cleanConfig)) {
            unset($cleanConfig['directories']);
        }
        if (array_key_exists('depends', $cleanConfig)) {
            unset($cleanConfig['depends']);
        }
        return $cleanConfig;
    }

    /**
     * Sets config file path and format
     *
     * @param string $config Config file path
     * @param string $configType Config file format
     * @return Xylo_ConnectGenerator_Model_Generator
     */
    public function setConfig($config, $configType) {
        $this->_config = $config;
        $this->_configType = $configType;
        return $this;
    }

    /**
     * Sets config file path in JSON format
     *
     * @param string $config Config file path
     * @return Xylo_ConnectGenerator_Model_Generator
     */
    public function setJsonConfig($config) {
        return $this->setConfig($config, 'json');
    }

    /**
     * Sets config file path in XML format
     *
     * @param string $config Config file path
     * @return Xylo_ConnectGenerator_Model_Generator
     */
    public function setXmlConfig($config) {
        return $this->setConfig($config, 'xml');
    }

    /**
     * Sets config file path in YAML format
     *
     * @param string $config Config file path
     * @return Xylo_ConnectGenerator_Model_Generator
     */
    public function setYamlConfig($config) {
        return $this->setConfig($config, 'yaml');
    }

    /**
     * Generates Magento Connect package
     *
     * @param string|null $target Target directory for the package
     * @return string Absolute path to the generated package
     */
    public function process($target = null) {
        $config = $this->_getParser()->parse($this->_getConfig());
        $contentsConfig = $this->_exctractContentsConfig($config);
        $dependenciesConfig = $this->_exctractDependenciesConfig($config);
        $config = $this->_unsetRedundantConfigParams(array_merge($config, $contentsConfig, $dependenciesConfig));
        $targetDir = $target ? realpath($target) . DS : Mage::helper('connect')->getLocalPackagesPath();
        return $this->_getExtensionModel()->setData($config)->createPackage($targetDir);
    }

    /**
     * Parses provided config file
     *
     * @return array Parsed config array
     */
    public function parseConfig() {
        $config = $this->_getParser()->parse($this->_getConfig());
        $contentsConfig = $this->_exctractContentsConfig($config);
        $dependenciesConfig = $this->_exctractDependenciesConfig($config);
        $config = $this->_unsetRedundantConfigParams(array_merge($config, $contentsConfig, $dependenciesConfig));
        return $config;
    }

}
