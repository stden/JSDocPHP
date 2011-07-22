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

class Moc10_Autoloader
{

    /**
     * Method to autoload a class via the file name.
     *
     * @param  string $class
     * @return void
     */
    public static function autoload($class)
    {

        // Set the file name and path.
        $filePath = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

        // Require the file.
        require_once $filePath;

    }

    /**
     * Method to register the autoload class name with the autoload stack.
     *
     * @return void
     */
    public static function registerAutoloader()
    {

        spl_autoload_register('Moc10_Autoloader::autoload');

    }

    /**
     * Method to set the include path of the library.
     *
     * @return void
     */
    public static function setupIncludePath()
    {

    	set_include_path(realpath(dirname(__FILE__) . '/../') . PATH_SEPARATOR . get_include_path());

    }

    /**
     * Method to bootstrap the autoloader.
     *
     * @param  string|array $dirs
     * @return void
     */
    public static function bootstrap($dirs = null)
    {

        if (!is_null($dirs)) {
            if (is_array($dirs)) {
                $realDirs = array();
                foreach ($dirs as $dir) {
                    $realDirs[] = realpath($dir);
                }
                $d = implode(PATH_SEPARATOR, $realDirs) . PATH_SEPARATOR . get_include_path();
            } else {
                $d = realpath($dirs) . PATH_SEPARATOR . get_include_path();
            }
            set_include_path($d);
        }

    	self::setupIncludePath();
    	self::registerAutoloader();

    }

}
