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
 * Moc10_File_Upload
 *
 * @category   Moc10
 * @package    Moc10_File
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2009-2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.moc10phplibrary.com/LICENSE.TXT     New BSD License
 * @version    2.0.0
 */

class Moc10_File_Upload extends Moc10_File
{

    /**
     * Set the maximum allowed file size. Set to null to allow any size.
     * @var int
     */
    protected $_max = 5000000;

    /**
     * Constructor
     *
     * Instantiate a file object based on a file that has been uploaded.
     *
     * @param  string $upload
     * @param  string $file
     * @param  int    $size
     * @param  array  $types
     * @throws Exception
     * @return void
     */
    public function __construct($upload, $file, $size = null, $types = null)
    {

        $lang = new Moc10_Language();

        // Check to see if the upload directory exists.
        if (!file_exists(dirname($file))) {
            throw new Exception($lang->__('Error: The upload directory does not exist.'));
        }

        // Check to see if the permissions are set correctly.
        if (($this->_checkPermissions(dirname($file))) != 777) {
            throw new Exception($lang->__('Error: Permission denied.'));
        }

        // Move the uploaded file, creating a file object with it.
        if (move_uploaded_file($upload, $file)) {
            chmod($file, 0777);
            parent::__construct($file, true, $types);

            // Check the file size requirement.
            if ((!is_null($size)) && ($this->size > $size)) {
                $this->delete();
                throw new Exception($lang->__('Error: The file uploaded is too big.'));
            } else if ((is_null($size)) && (!is_null($this->_max)) && ($this->size > $this->_max)) {
                $this->delete();
                throw new Exception($lang->__('Error: The file uploaded is too big.'));
            }
        } else {
            throw new Exception($lang->__('Error: There was an error in uploading the file.'));
        }

    }

}
