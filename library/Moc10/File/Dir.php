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
 * @package    Moc10_File
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2009-2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.moc10phplibrary.com/LICENSE.TXT     New BSD License
 */

/**
 * Moc10_File_Dir
 *
 * @category   Moc10
 * @package    Moc10_File
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2009-2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.moc10phplibrary.com/LICENSE.TXT     New BSD License
 * @version    2.0.0
 */

class Moc10_File_Dir
{

    /**
     * The directory path
     * @var string
     */
    public $path = null;

    /**
     * The files within the directory
     * @var array
     */
    public $files = array();

    /**
     * Flag to store the full path.
     * @var boolean
     */
    protected $_full = false;

    /**
     * Flag to dig recursively.
     * @var boolean
     */
    protected $_rec = false;

    /**
     * Constructor
     *
     * Instantiate a directory object
     *
     * @param  string $dir
     * @param  boolean $full
     * @param  boolean $rec
     * @throws Exception
     * @return void
     */
    public function __construct($dir, $full = false, $rec = false)
    {

        // Check to see if the directory exists.
        if (!file_exists(dirname($dir))) {
            $lang = new Moc10_Language();
            throw new Exception($lang->__('Error: The directory does not exist.'));
        } else {

            $this->_full = $full;
            $this->_rec = $rec;

            // Set the directory path.
            if ((strpos($dir, '/') !== false) && (DIRECTORY_SEPARATOR != '/')) {
                $this->path = str_replace('/', "\\", $dir);
            } else if ((strpos($dir, "\\") !== false) && (DIRECTORY_SEPARATOR != "\\")) {
                $this->path = str_replace("\\", '/', $dir);
            } else {
                $this->path = $dir;
            }

            // Trim the trailing slash.
            if (strrpos($this->path, DIRECTORY_SEPARATOR) == (strlen($this->path) - 1)) {
                $this->path = substr($this->path, 0, -1);
            }

            // If the recursive flag is passed, traverse recursively.
            if ($this->_rec) {

                $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->path), RecursiveIteratorIterator::SELF_FIRST);
                foreach ($objects as $fileInfo) {
                    if (($fileInfo->getFilename() != '.') && ($fileInfo->getFilename() != '..')) {
                        // If full path flag was passed, store the full path.
                        if ($this->_full) {
                            $this->files[] = ($fileInfo->isDir()) ? ($fileInfo->getPathname() . DIRECTORY_SEPARATOR) : $fileInfo->getPathname();
                        // Else, store only the directory or file name.
                        } else {
                            $this->files[] = ($fileInfo->isDir()) ? ($fileInfo->getFilename() . DIRECTORY_SEPARATOR) : $fileInfo->getFilename();
                        }
                    }
                }

            // Else, only traverse the single directory that was passed.
            } else {

                foreach (new DirectoryIterator($this->path) as $fileInfo) {
                    if(!$fileInfo->isDot()) {
                        // If full path flag was passed, store the full path.
                        if ($this->_full) {
                            $this->files[] = ($fileInfo->isDir()) ? ($this->path . DIRECTORY_SEPARATOR . $fileInfo->getFilename() . DIRECTORY_SEPARATOR) : ($this->path . DIRECTORY_SEPARATOR . $fileInfo->getFilename());
                        // Else, store only the directory or file name.
                        } else {
                            $this->files[] = ($fileInfo->isDir()) ? ($fileInfo->getFilename() . DIRECTORY_SEPARATOR) : $fileInfo->getFilename();
                        }
                    }
                }

            }

        }

    }

    /**
     * Static method to return the system temp directory.
     *
     * @return string
     */
    public static function getSystemTemp()
    {

        $sysTemp = null;

        if (isset($_ENV['TMP']) && !empty($_ENV['TMP'])) {
            $sysTemp = $_ENV['TMP'];
        } else if (isset($_ENV['TEMP']) && !empty($_ENV['TEMP'])) {
            $sysTemp = $_ENV['TEMP'];
        } else if (isset($_ENV['TMPDIR']) && !empty($_ENV['TMPDIR'])) {
            $sysTemp = $_ENV['TMPDIR'];
        } else {
            $sysTemp = sys_get_temp_dir();
        }

        return realpath($sysTemp);

    }


    /**
     * Static method to return the upload temp directory.
     *
     * @return string
     */
    public static function getUploadTemp()
    {

        if (ini_get('upload_tmp_dir') == '') {
            return self::getSystemTemp();
        } else {
            return realpath(ini_get('upload_tmp_dir'));
        }

    }

    /**
     * Get the permissions of the directory.
     *
     * @return int
     */
    public function getMode()
    {

        return substr(sprintf('%o', fileperms($this->path)), -3);

    }

    /**
     * Change the permissions of the directory.
     *
     * @param  int $mode
     * @return void
     */
    public function setMode($mode)
    {

        if (file_exists($this->path)) {
            chmod($this->path, $mode);
            self::__construct($this->path, $this->_full, $this->_rec);
        }

    }

    /**
     * Empty an entire directory.
     *
     * @param  string  $path
     * @param  boolean $del
     * @return void
     */
    public function emptyDir($path, $del = false)
    {

        // Get a directory handle.
        if (!$dh = @opendir($path)) {
            return;
        }

        // Recursively dig throught the directory, deleting files where applicable.
        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..') {
                continue;
            }
            if (!@unlink($path . '/' . $obj)) {
                $this->emptyDir($path . '/' . $obj, true);
            }
        }

        // Close the directory handle.
        closedir($dh);

        // If the delete flag was passed, remove the top level directory.
        if ($del) {
            @rmdir($path);
        }

    }

}
