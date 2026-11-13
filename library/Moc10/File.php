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
 * Moc10_File
 *
 * @category   Moc10
 * @package    Moc10_File
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2009-2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.moc10phplibrary.com/LICENSE.TXT     New BSD License
 * @version    2.0.0
 */

class Moc10_File
{

    /**
     * Full path and name of the file, i.e. '/some/dir/file.ext'
     * @var string
     */
    public $fullpath = null;

    /**
     * Full, absolute directory of file, i.e. '/some/dir/'
     * @var string
     */
    public $dir = null;

    /**
     * Full basename of file, i.e. 'file.ext'
     * @var string
     */
    public $basename = null;

    /**
     * Full filename of file, i.e. 'file'
     * @var string
     */
    public $filename = null;

    /**
     * File extension, i.e. 'ext'
     * @var string
     */
    public $ext = null;

    /**
     * File size in bytes
     * @var int
     */
    public $size = 0;

    /**
     * File mime type
     * @var string
     */
    public $mime = null;

    /**
     * File output data.
     * @var string
     */
    protected $_output = null;

    /**
     * Directory and file permissions, based on chmod, when and if applicable.
     * @var array
     */
    protected $_perm = array();

    /**
     * Array of allowed file types.
     * @var array
     */
    protected $_allowed = array('afm'  => 'application/x-font-afm',
                                'ai'   => 'application/postscript',
                                'aif'  => 'audio/x-aiff',
                                'aiff' => 'audio/x-aiff',
                                'avi'  => 'video/x-msvideo',
                                'bmp'  => 'image/x-ms-bmp',
                                'bz2'  => 'application/bzip2',
                                'css'  => 'text/css',
                                'csv'  => 'text/csv',
                                'doc'  => 'application/msword',
                                'docx' => 'application/msword',
                                'eps'  => 'application/octet-stream',
                                'fla'  => 'application/octet-stream',
                                'flv'  => 'application/octet-stream',
                                'gif'  => 'image/gif',
                                'gz'   => 'application/x-gzip',
                                'html' => 'text/html',
                                'htm'  => 'text/html',
                                'jpe'  => 'image/jpeg',
                                'jpg'  => 'image/jpeg',
                                'jpeg' => 'image/jpeg',
                                'js'   => 'text/plain',
                                'mov'  => 'video/quicktime',
                                'mp2'  => 'audio/mpeg',
                                'mp3'  => 'audio/mpeg',
                                'mp4'  => 'video/mp4',
                                'mpg'  => 'video/mpeg',
                                'mpeg' => 'video/mpeg',
                                'otf'  => 'application/x-font-otf',
                                'pdf'  => 'application/pdf',
                                'pfb'  => 'application/x-font-pfb',
                                'pfm'  => 'application/x-font-pfm',
                                'php'  => 'text/plain',
                                'png'  => 'image/png',
                                'ppt'  => 'application/msword',
                                'pptx' => 'application/msword',
                                'psd'  => 'image/x-photoshop',
                                'sit'  => 'application/x-stuffit',
                                'sitx' => 'application/x-stuffit',
                                'sql'  => 'text/plain',
                                'swf'  => 'application/x-shockwave-flash',
                                'tar'  => 'application/x-tar',
                                'tbz2' => 'application/bzip2',
                                'tgz'  => 'application/x-gzip',
                                'tif'  => 'image/tiff',
                                'tiff' => 'image/tiff',
                                'tsv'  => 'text/tsv',
                                'ttf'  => 'application/x-font-ttf',
                                'txt'  => 'text/plain',
                                'wav'  => 'audio/x-wav',
                                'wma'  => 'audio/x-ms-wma',
                                'wmv'  => 'audio/x-ms-wmv',
                                'xls'  => 'application/msword',
                                'xlsx' => 'application/msword',
                                'xhtml'=> 'application/xhtml+xml',
                                'xml'  => 'application/xml',
                                'zip'  => 'application/x-zip');

    /**
     * Language object
     * @var Moc10_Language
     */
    protected $_lang = null;

    /**
     * Constructor
     *
     * Instantiate the file object, either from a file on disk or as a new file.
     *
     * @param  string  $fl
     * @param  boolean $up
     * @param  array   $typ
     * @return void
     */
    public function __construct($fl, $up = false, $typ = null)
    {

        $this->_lang = new Moc10_Language();

        if (!is_null($typ)) {
            $this->setAllowedTypes($typ);
        }

        $this->_setFile($fl, $up);

    }

    /**
     * Test if a certain file type is allowed.
     *
     * @param  string $type
     * @return boolean
     */
    public function isAllowed($type)
    {

        return (array_key_exists(strtolower($type), $this->_allowed)) ? true : false;

    }

    /**
     * Get the current allowed files types.
     *
     * @return array
     */
    public function getAllowedTypes()
    {

        return $this->_allowed;

    }

    /**
     * Set the allowed files types, overriding any previously allowed types.
     *
     * @param  array $types
     * @return void
     */
    public function setAllowedTypes($types = null)
    {

        $this->_allowed = array();
        $ary = (is_null($types) || !is_array($types)) ? array() : $types;
        $this->addAllowedTypes($ary);

    }

    /**
     * Set the allowed files types.
     *
     * @param  array $types
     * @throws Exception
     * @return void
     */
    public function addAllowedTypes($types)
    {

        // Check to see if the parameter is an array.
        if (!is_array($types)) {
            throw new Exception($this->_lang->__('Error: The parameter passed is not an array.'));
        // Else, append the additional types to the $_allowed array.
        } else {
            foreach ($types as $key => $value) {
                $this->_allowed[$key] = $value;
            }
        }

    }

    /**
     * Get the permissions of the file.
     *
     * @param  boolean $dir
     * @return int|boolean
     */
    public function getMode($dir = false)
    {

        return ($dir) ? $this->_perm['dir'] : $this->_perm['file'];

    }

    /**
     * Change the permissions of the file.
     *
     * @param  string|oct $mode
     * @param  boolean    $dir
     * @return void
     */
    public function setMode($mode, $dir = false)
    {

        if ($dir) {
            if (file_exists($this->dir)) {
                chmod($this->dir, $mode);
                $this->_setFile($this->fullpath);
            }
        } else {
            if (file_exists($this->fullpath)) {
                chmod($this->fullpath, $mode);
                $this->_setFile($this->fullpath);
            }
        }

    }

    /**
     * Read data from a file.
     *
     * @param  int|string $off
     * @param  int|string $len
     * @return string
     */
    public function read($off = null, $len = null)
    {

        $data = null;

        // Read from the output buffer
        if (!is_null($this->_output)) {
            if (!is_null($off)) {
                $data = (!is_null($len)) ? substr($this->_output, $off, $len) : substr($this->_output, $off);
            } else {
                $data = $this->_output;
            }
        // Else, if the file exists, then read the data from the actual file
        } else if (file_exists($this->fullpath)) {
            if (!is_null($off)) {
                $data = (!is_null($len)) ? file_get_contents($this->fullpath, null, null, $off, $len) : $this->_output = file_get_contents($this->fullpath, null, null, $off);
            } else {
                $data = file_get_contents($this->fullpath);
            }
        }

        return $data;

    }

    /**
     * Write data to a file.
     *
     * @param  string  $data
     * @param  boolean $append
     * @return Moc10_File
     */
    public function write($data, $append = false)
    {

        // If the file is to be appended.
        if ($append) {
            $this->_output .= $data;
        //Else, overwrite the file contents.
        } else {
            $this->_output = $data;
        }

        return $this;

    }

    /**
     * Copy the file object directly to another file on disk.
     *
     * @param  string $new
     * @throws Exception
     * @return Moc10_File
     */
    public function copy($new)
    {

        // Check to see if the new file already exists, and if the permissions are set correctly.
        if (file_exists($new)) {
            throw new Exception($this->_lang->__('Error: The file already exists.'));
        } else if (($this->_checkPermissions(dirname($new))) != 777) {
            throw new Exception($this->_lang->__('Error: Permission denied.'));
        } else {
            if (file_exists($this->fullpath)) {
                copy($this->fullpath, $new);
            } else {
                file_put_contents($new, $this->_output);
            }
            chmod($new, 0777);
            $this->_setFile($new);
        }

        return $this;

    }

    /**
     * Move the file object directly to another location on disk.
     *
     * @param  string $new
     * @throws Exception
     * @return Moc10_File
     */
    public function move($new)
    {

        // Check to see if the new file already exists, and if the permissions are set correctly.
        if (file_exists($new)) {
            throw new Exception($this->_lang->__('Error: The file already exists.'));
        } else if (($this->_checkPermissions(dirname($new)) != 777) || ($this->_perm['dir'] != 777)) {
            throw new Exception('Error: Permission denied.');
        } else {
            if (file_exists($this->fullpath)) {
                rename($this->fullpath, $new);
            } else {
                file_put_contents($new, $this->_output);
            }
            chmod($new, 0777);
            $this->_setFile($new);
        }

        return $this;

    }

    /**
     * Output the file object directly.
     *
     * @param  boolean $download
     * @return void
     */
    public function output($download = false)
    {

        // Send the file's mime type.
        header('Content-type: ' . $this->mime);

        // Determine if the force download argument has been passed.
        $attach = ($download) ? 'attachment; ' : null;

        // Send the file information.
        header('Content-disposition: ' . $attach . 'filename=' . $this->basename);

        // Send cache control headers for IE SSL issue.
        if ($_SERVER['SERVER_PORT'] == 443) {
            header('Expires: 0');
            header('Cache-Control: private, must-revalidate');
            header('Pragma: cache');
        }

        // Output the file contents.
        echo $this->read();

    }


    /**
     * Save the file object to disk.
     *
     * @param  string $to
     * @param  boolean $append
     * @return void
     */
    public function save($to = null, $append = false)
    {

        $file = (is_null($to)) ? $this->fullpath : $to;

        if ($append) {
            file_put_contents($file, $this->read(), FILE_APPEND);
        } else {
            file_put_contents($file, $this->read());
        }

        return $this;

    }

    /**
     * Export array data to CSV format.
     *
     * @param  array        $ary
     * @param  string|array $omit
     * @param  string       $delim
     * @param  string       $esc
     * @param  string       $dt
     * @return void
     */
    public function export($ary, $omit = null, $delim = ',', $esc = '"', $dt = null)
    {

        $output = '';
        $headerAry = array();

        if (is_null($omit)) {
            $omit = array();
        } else if (!is_array($omit)) {
            $omit = array($omit);
        }

        // Initialize and clean the header fields.
        foreach ($ary[0] as $key => $value) {

            if (!in_array($key, $omit)) {

                $k = new Moc10_String((string)$key);

                if ($k->pos($esc) !== false) {
                    $k->replace($esc, $esc . $esc);
                }
                if ($k->pos($delim) !== false) {
                    $k = new Moc10_String($esc . $k . $esc);
                }

                $headerAry[] = (string)$k;

            }

        }

        // Set header output.
        $output .= implode($delim, $headerAry) . "\n";

        // Initialize and clean the field values.
        foreach ($ary as $value) {

            $rowAry = array();

            foreach ($value as $key => $val) {

                if (!in_array($key, $omit)) {
                    if (!is_null($dt)) {
                        if ((strtotime($val) !== false) || (stripos($key, 'date') !== false)) {
                            $v = (date($dt, strtotime($val)) != '12/31/1969') ? new Moc10_String(date($dt, strtotime((string)$val))) : new Moc10_String('');
                        } else {
                            $v = new Moc10_String((string)$val);
                        }
                    } else {
                        $v = new Moc10_String((string)$val);
                    }
                    if ($v->pos($esc) !== false) {
                        $v->replace($esc, $esc . $esc);
                    }
                    if ($v->pos($delim) !== false) {
                        $v = new Moc10_String($esc . (string)$v . $esc);
                    }

                    $rowAry[] = $v;

                }

            }

            // Set field output.
            $output .= implode($delim, $rowAry) . "\n";

        }

        $this->write($output);

        return $this;

    }

    /**
     * Import CSV data to an array.
     *
     * @param  string $delim
     * @param  string $esc
     * @return void
     */
    public function import($delim = ',', $esc = '"')
    {

        // Read the file data, seperating by new lines.
        $lines = explode("\n", $this->read());

        $lines_of_data = array();
        $new_lines_of_data = array();

        // Loop through the line data, parsing any quoted or escaped data.
        foreach ($lines as $data) {

            if ($data != '') {
                if (strpos($data, $esc) !== false) {
                    $matches = array();
                    preg_match_all('/"([^"]*)"/', $data, $matches);
                    if (isset($matches[0])) {
                        foreach ($matches[0] as $value) {
                            $escaped_data = str_replace('"', '', $value);
                            $escaped_data = str_replace($delim, '[{c}]', $escaped_data);
                            $data = str_replace($value, $escaped_data, $data);
                        }
                    }

                }

                // Finalize the data and store in the array.
                $data = str_replace($delim, '[{d}]', $data);
                $data = str_replace('[{c}]', $delim, $data);
                $lines_of_data[] = explode('[{d}]', $data);

            }

        }

        // Create a corresponding associative array by converting the array keys to the header names.
        for ($i = 1; $i < count($lines_of_data); $i++) {

            $new_lines_of_data[$i-1] = array();

            foreach ($lines_of_data[$i] as $key => $value) {
                $newKey = trim($lines_of_data[0][$key]);
                $new_lines_of_data[$i-1][$newKey] = trim($value);
            }

        }

        // Return the newly formed array data.
        return $new_lines_of_data;

    }

    /**
     * Delete the file object directly from disk.
     *
     * @throws Exception
     * @return void
     */
    public function delete()
    {

        // Check to make sure the file exists and the permissions are set correctly before attempting to delete it from disk.
        if (file_exists($this->fullpath)) {

            if (!is_null($this->_perm['file']) && ($this->_perm['file'] != 777)) {
                throw new Exception($this->_lang->__('Error: Permission denied.'));
            } else {

                unlink($this->fullpath);

                // Reset file object properties.
                $props = get_class_vars(get_class($this));

                foreach (array_keys($props) as $key) {
                    $this->{$key} = null;
                }

            }

        }

    }

    /**
     * Set the file and its properties.
     *
     * @param  string  $file
     * @param  boolean $upload
     * @throws Exception
     * @return void
     */
    protected function _setFile($file, $upload = false)
    {

        // Set file object properties.
        $file_parts = pathinfo($file);

        $this->fullpath = $file;
        $this->dir = $file_parts['dirname'] . '/';
        $this->basename = $file_parts['basename'];
        $this->filename = $file_parts['filename'];
        $this->ext = (isset($file_parts['extension'])) ? $file_parts['extension'] : null;
        $this->_perm['dir'] = $this->_checkPermissions($this->dir);

        // Check if the file exists, and set the size and permissions accordingly.
        if (file_exists($file)) {
            // Check if the server is a Linux/Unix server or a Windows server.
            $this->_perm['file'] = $this->_checkPermissions($this->fullpath);
            $this->size = filesize($file);
        } else {
            // Check if the server is a Linux/Unix server or a Windows server.
            $this->_perm['file'] = 777;
            $this->size = 0;
        }

        // Check to see if the file is an accepted file format.
        if (!is_null($this->_allowed) && !is_null($this->ext) && (count($this->_allowed) > 0) && (!array_key_exists(strtolower($this->ext), $this->_allowed))) {
            if ($upload) {
                $this->delete();
            }
            throw new Exception($this->_lang->__('Error: The file is not an accepted file format.'));
        } else {
            // Set the mime type of the file.
            $this->mime = (!is_null($this->ext) && !is_null($this->_allowed)) ? $this->_allowed[strtolower($this->ext)] : null;
        }

    }

    /**
     * Check file or directory permissions.
     *
     * @param  string $file
     * @throws Exception
     * @return string
     */
    protected function _checkPermissions($file)
    {

        $perm = '';

        if (DIRECTORY_SEPARATOR == '/') {
            $perm = substr(sprintf('%o', fileperms($file)), -3);
        } else {
            if (!is_writable($file)) {
               throw new Exception($this->_lang->__('Error: The file or directory (%1) is not writable.', $file));
            } else {
               $perm = 777;
            }
        }

        return $perm;

    }

}
