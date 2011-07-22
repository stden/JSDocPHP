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
 * @package    Moc10_Dom
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2009-2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.moc10phplibrary.com/LICENSE.TXT     New BSD License
 */

/**
 * Moc10_Dom
 *
 * @category   Moc10
 * @package    Moc10_Dom
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2009-2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.moc10phplibrary.com/LICENSE.TXT     New BSD License
 * @version    2.0.0
 */

class Moc10_Dom extends Moc10_Dom_Abstract
{

    /**
     * Document type
     * @var string
     */
    protected $_type = null;

    /**
     * Document content type
     * @var string
     */
    protected $_contentType = 'text/html';

    /**
     * Document charset
     * @var string
     */
    protected $_charset = 'utf-8';

    /**
     * Document doctypes
     * @var array
     */
    protected $_doctypes = array('HTML_TRANS' => "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n",
                                 'HTML_STRICT' => "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\n",
                                 'HTML_FRAMES' => "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\" \"http://www.w3.org/TR/html4/frameset.dtd\">\n",
                                 'XHTML_TRANS' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n",
                                 'XHTML_STRICT' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n",
                                 'XHTML_FRAMES' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n",
                                 'XHTML11' => "<?xml version=\"1.0\" encoding=\"[{charset}]\"?>\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n",
                                 'XML' => "<?xml version=\"1.0\" encoding=\"[{charset}]\"?>\n",
                                 'RSS' => "<?xml version=\"1.0\" encoding=\"[{charset}]\"?>\n",
                                 'ATOM' => "<?xml version=\"1.0\" encoding=\"[{charset}]\"?>\n");

    /**
     * Constructor
     *
     * Instantiate the document object
     *
     * @param  string $type
     * @param  string $charset
     * @param  array|Moc10_Dom_Child $childNode
     * @param  string $indent
     * @throws Exception
     * @return void
     */
    public function __construct($type = null, $charset = 'utf-8', $childNode = null, $indent = null)
    {

        $this->_lang = new Moc10_Language();

        // Check the document type, else set the properties.
        if ((!is_null($type)) && (!array_key_exists($type, $this->_doctypes))) {
            throw new Exception($this->_lang->__('Error: That doctype is not allowed.'));
        } else {
            $this->_type = $type;
            if ($type == 'ATOM') {
                $this->_contentType = 'application/atom+xml';
            } else if ($type == 'RSS') {
                $this->_contentType = 'application/rss+xml';
            } else if ($type == 'XML') {
                $this->_contentType = 'application/xml';
            } else {
                $this->_contentType = 'text/html';
            }
            $this->_charset = $charset;
            $this->_indent = $indent;
            if (!is_null($childNode)) {
                $this->addChild($childNode);
            }
        }

    }

    /**
     * Method to return the document type.
     *
     * @return void
     */
    public function getType()
    {

        return $this->_type;

    }

    /**
     * Method to return the document charset.
     *
     * @return void
     */
    public function getCharset()
    {

        return $this->_charset;

    }

    /**
     * Method to set the document type.
     *
     * @param  string $type
     * @return void
     */
    public function setType($type)
    {

        $this->_type = $type;

    }

    /**
     * Method to set the document type declaration in the doctype header.
     *
     * @param  string $dtd
     * @return void
     */
    public function setDTD($dtd)
    {

        if (($this->_type == 'XML') || ($this->_type == 'RSS') || ($this->_type == 'ATOM')) {
            $this->_doctypes[$this->_type] .= $dtd . "\n";
        }

    }

    /**
     * Method to set the document charset.
     *
     * @param  string $chr
     * @return void
     */
    public function setCharset($chr)
    {

        $this->_charset = $chr;

    }

    /**
     * Method to render the document and its child elements.
     *
     * @param  boolean $ret
     * @return void
     */
    public function render($ret = false)
    {

        // If the return flag is passed, return output.
        if ($ret) {
            $this->_output = '';
            if (!is_null($this->_type)) {
                $this->_output .= str_replace('[{charset}]', $this->_charset, $this->_doctypes[$this->_type]);
            }
            foreach ($this->_childNodes as $child) {
                $this->_output .= $child->render(true, 0, $this->_indent);
            }
            return $this->_output;
        // Else, print output.
        } else {

            if (!is_null($this->_type)) {
                if (!headers_sent()) {
                    header('Content-type: ' . $this->_contentType);
                }
                print(str_replace('[{charset}]', $this->_charset, $this->_doctypes[$this->_type]));
            }

            foreach ($this->_childNodes as $child) {
                $child->render(false, 0, $this->_indent);
            }

        }

    }

}
