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
 * Overriden connect/extension model
 *
 * @package    Xylo_ConnectGenerator
 * @subpackage Model
 * @author     Benoît Xylo <devel@xylo.pl>
 * @version    Release: $Release$
 */
class Xylo_ConnectGenerator_Model_Extension extends Mage_Connect_Model_Extension {

    /**
    * Create package file
    *
    * @return boolean
    */
    public function createPackage($target) {
        if (!$this->getPackageXml()) {
            $this->generatePackageXml();
        }
        $this->getPackage()->save($target);
        return $target . $this->getPackage()->getReleaseFilename();
    }

    public function getTargets() {
        return $this->getPackage()->getTarget()->getTargets();
    }

}
