<?php
/**
 * Moc10 Library
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.TXT.
 * It is also available through the world-wide-web at this URL:
 * http://www.moc10phplibrary.com/LICENSE.TXT
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@moc10media.com so we can send you a copy immediately.
 *
 * @category   Moc10
 * @package    Moc10_Autoloader
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2009-2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.moc10phplibrary.com/LICENSE.TXT     New BSD License
 */

/**
 * Moc10_Autoloader
 *
 * @category   Moc10
 * @package    Moc10_Autoloader
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2009-2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.moc10phplibrary.com/LICENSE.TXT     New BSD License
 * @version    2.0.0
 */

// Require the library's autoloader.
require_once dirname(__FILE__) . '/library/Moc10/Autoloader.php';

// Call the autoloader's bootstrap function.
Moc10_Autoloader::bootstrap(dirname(__FILE__) . '/application/');

?>