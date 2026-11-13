<?php
/**
 * JSDocPHP
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.TXT.
 *
 * @category   JSDoc
 * @package    JSDoc
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    LICENSE.TXT     New BSD License
 */

/**
 * JSDocPHP
 *
 * @category   JSDoc
 * @package    JSDoc
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    LICENSE.TXT     New BSD License
 * @version    0.9
 */

class JSDoc extends Moc10_File
{

    /**
     * Global variables
     * @var array
     */
    protected $_globals = array();

    /**
     * Prototype extensions
     * @var array
     */
    protected $_prototypes = array();

    /**
     * Doc blocks
     * @var array
     */
    protected $_docBlocks = array();

    /**
     * Array of allowed file types.
     * @var array
     */
    protected $_allowed = array('css'  => 'text/css',
                                'csv'  => 'text/csv',
                                'html' => 'text/html',
                                'htm'  => 'text/html',
                                'js'   => 'text/plain',
                                'php'  => 'text/plain',
                                'tsv'  => 'text/tsv',
                                'txt'  => 'text/plain',
                                'xhtml'=> 'application/xhtml+xml',
                                'xml'  => 'application/xml');

    /**
     * Constructor
     *
     * Instantiate the JSDoc object.
     *
     * @param  string  $fle
     * @return void
     */
    public function __construct($fle)
    {
        parent::__construct($fle);
    }

    /**
     * Method to parse the JavaScript in the JSDoc object.
     *
     * @return JSDoc Object
     */
    public function parse()
    {

        $this->_output = $this->read();

        $this->_parseGlobals();
        $this->_parsePrototypes();

        // Get top-level docblocks.
        $startPattern = '/^\/\*\*/m';
        $endPattern = '/^\s\*\//m';
        $matches = array();

        preg_match_all($startPattern, $this->_output, $matches, PREG_OFFSET_CAPTURE);

        if (isset($matches[0])) {
            foreach ($matches[0] as $match) {
                $mat = array();
                preg_match($endPattern, $this->_output, $mat, PREG_OFFSET_CAPTURE, $match[1]);
                $start = $match[1];
                $length = (isset($mat[0])) ? (($mat[0][1] - $start) + 3) : 0;
                $this->_docBlocks[] = JSDoc_Block::parse($this->_output, $start, $length);
            }
        }

        // Get the content under the docblocks and then the subsequent sub-level docblocks.
        if (isset($this->_docBlocks[0])) {

            for ($i = 0; $i < count($this->_docBlocks); $i++) {

                $start = $this->_docBlocks[$i]->end;

                if ($i < (count($this->_docBlocks) - 1)) {
                    $end = $this->_docBlocks[$i+1]->start;
                    $length = $end - $start;
                    $content = substr($this->_output, $start, $length);
                } else {
                    $content = substr($this->_output, $start);
                    $length = strlen($content);
                    $end = $start + $length;
                }

                // Set content properties for top-level docblocks.
                $content = trim($content);
                $firstLine = substr($content, 0, strpos($content, "\n"));
                if (substr($firstLine, 0, 5) == 'if (!') {
                    $firstLine = substr($content, (strpos($content, '{') + 1));
                    $firstLine = trim(substr($firstLine, 0, (strpos($firstLine, '{') + 1)));
                }
                if (substr($firstLine, -1) == '{') {
                    $firstLine .= ' }';
                }

                $this->_docBlocks[$i]->content = $content;
                $this->_docBlocks[$i]->firstLine = $firstLine;
                $this->_docBlocks[$i]->contentStart = $start;
                $this->_docBlocks[$i]->contentEnd = $end;
                $this->_docBlocks[$i]->contentLength = $length;
                $this->_docBlocks[$i]->contentType = JSDoc_Type::getType($this->_docBlocks[$i]);
                $this->_docBlocks[$i]->properties = JSDoc_Properties::getProperties($this->_docBlocks[$i]->content);

                // Get sub-level docblocks.
                $startPattern = '/\/\*\*/m';
                $endPattern = '/\s\*\//m';
                $matches = array();

                preg_match_all($startPattern, $content, $matches, PREG_OFFSET_CAPTURE);

                if (isset($matches[0])) {

                    $this->_docBlocks[$i]->docBlocks = array();

                    foreach ($matches[0] as $match) {
                        $mat = array();
                        preg_match($endPattern, $content, $mat, PREG_OFFSET_CAPTURE, $match[1]);
                        $start = $match[1];
                        $length = (isset($mat[0])) ? (($mat[0][1] - $start) + 3) : 0;
                        $this->_docBlocks[$i]->docBlocks[] = JSDoc_Block::parse($content, $start, $length);
                    }

                    // Get the content under the sub-level docblocks.
                    if (isset($this->_docBlocks[$i]->docBlocks[0])) {

                        for ($j = 0; $j < count($this->_docBlocks[$i]->docBlocks); $j++) {

                            $start = $this->_docBlocks[$i]->docBlocks[$j]->end;

                            if ($j < (count($this->_docBlocks[$i]->docBlocks) - 1)) {
                                $end = $this->_docBlocks[$i]->docBlocks[$j+1]->start;
                                $length = $end - $start;
                                $cont = substr($content, $start, $length);
                            } else {
                                $cont = substr($content, $start);
                                $length = strlen($cont);
                                $end = $start + $length;
                            }

                            // Set content properties for sub-level docblocks.
                            $cont = trim($cont);
                            $firstLine = substr($cont, 0, strpos($cont, "\n"));
                            if (substr($firstLine, 0, 5) == 'if (!') {
                                $firstLine = substr($cont, (strpos($cont, '{') + 1));
                                $firstLine = trim(substr($firstLine, 0, (strpos($firstLine, '{') + 1)));
                            }
                            if (substr($firstLine, -1) == '{') {
                                $firstLine .= ' }';
                            }
                            $this->_docBlocks[$i]->docBlocks[$j]->content = $cont;
                            $this->_docBlocks[$i]->docBlocks[$j]->firstLine = $firstLine;
                            $this->_docBlocks[$i]->docBlocks[$j]->contentStart = $start;
                            $this->_docBlocks[$i]->docBlocks[$j]->contentEnd = $end;
                            $this->_docBlocks[$i]->docBlocks[$j]->contentLength = $length;
                            $this->_docBlocks[$i]->docBlocks[$j]->contentType = JSDoc_Type::getType($this->_docBlocks[$i]->docBlocks[$j], $this->_docBlocks[$i]->subpackage);

                        }

                    }

                }

            }

        }

        return $this;

    }

    /**
     * Method to render the JSDoc.
     *
     * @param  string $dir
     * @return void
     */
    public function render($dir)
    {

        $nav = array();
        $content = '<h1>' . $this->basename . '</h1>' . PHP_EOL;

        // Format the first top-level block.
        if (count($this->_docBlocks) > 0) {
            $content .= '<table class="jsDocTable">' . PHP_EOL;
            foreach ($this->_docBlocks[0] as $key => $value) {
                if (
                    ($key != 'content') &&
                    ($key != 'docBlock') &&
                    ($key != 'firstLine') &&
                    ($key != 'docBlocks') &&
                    ($key != 'contentType') &&
                    (stripos($key, 'start') === false) &&
                    (stripos($key, 'end') === false) &&
                    (stripos($key, 'length') === false)
                    ) {
                    if ($key == 'header') {
                        $content .= '    <tr><td class="header" style="background-color: #fff;" colspan="2">' . Moc10_String::factory($value)->links(true) . '</td></tr>' . PHP_EOL;
                    } else {
                        if (($key == 'properties') && is_array($value)) {
                            $content .= '    <tr><td style="width: 15%; font-weight: bold;">' . $key . '</td><td>' . implode('<br />', $value)  . '</td></tr>' . PHP_EOL;
                        } else {
                            if ($key != 'properties') {
                                $val = (is_string($value)) ? (string)Moc10_String::factory($value)->links(true) : $value;
                                $content .= '    <tr><td style="width: 15%; font-weight: bold;">' . $key . '</td><td>' . $val . '</td></tr>' . PHP_EOL;
                            }
                        }
                    }
                }
            }
            $content .= '</table>' . PHP_EOL;
        }

        // Format the global variables, if any.
        if (count($this->_globals) > 0) {
            $content .= '<h2>Globals</h2>' . PHP_EOL;
            $content .= '<ul>' . PHP_EOL;
            foreach ($this->_globals as $global) {
                $content .= '    <li><strong>' . $global . '</strong></li>' . PHP_EOL;
            }
            $content .= '</ul>' . PHP_EOL;
        }

        // Format the prototype extensions, if any.
        if (count($this->_prototypes) > 0) {
            $content .= '<h2>Prototype Extensions</h2>' . PHP_EOL;
            foreach ($this->_prototypes as $key => $proto) {
                $content .= '<h3>' . $key . '</h3>' . PHP_EOL;
                $content .= '<ul>' . PHP_EOL;
                foreach ($proto as $method) {
                    $content .= '    <li>' . $key . '.' . $method . '</li>' . PHP_EOL;
                }
                $content .= '</ul>' . PHP_EOL;
            }
        }

        // Write and save the first top level doc file.
        $header = new Moc10_File(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../templates/header.html'));
        $footer = new Moc10_File(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../templates/footer.html'));

        $nav[$this->basename . '.html'] = $this->basename;

        $jsdoc = new Moc10_File($dir . DIRECTORY_SEPARATOR . $this->basename . '.html');
        $jsdoc->write($header->read() . $content . $footer->read());
        $jsdoc->save();
        echo 'Writing to ' . $this->basename . '.html...' . PHP_EOL;

        // Loop through the rest of the docblocks.
        if (isset($this->_docBlocks[1])) {

            $subpackages = array();

            for ($i = 1; $i < count($this->_docBlocks); $i++) {

                if (isset($this->_docBlocks[$i]->subpackage) && ($this->_docBlocks[$i]->subpackage != '') && (!in_array($this->_docBlocks[$i]->subpackage, $subpackages))) {
                    $content = '<h2>' . $this->_docBlocks[$i]->subpackage . '</h2>' . PHP_EOL;
                    $subpackages[] = $this->_docBlocks[$i]->subpackage;
                }

                $content .= '<table class="jsDocTable">' . PHP_EOL;

                // Loop through the values of the docblock, rendering them.
                foreach ($this->_docBlocks[$i] as $key => $value) {
                    if (
                        ($key != 'content') &&
                        ($key != 'docBlock') &&
                        ($key != 'docBlocks') &&
                        (stripos($key, 'start') === false) &&
                        (stripos($key, 'end') === false) &&
                        (stripos($key, 'length') === false)
                        ) {
                        if (($key == 'header') && ($value != '')) {
                            $content .= '    <tr><td class="header" style="background-color: #fff;" colspan="2">' . $value . '</td></tr>' . PHP_EOL;
                        } else if (($key == 'contentType') && ($value != '')) {
                            $content .= '    <tr><td style="width: 15%; font-weight: bold;">Type</td><td>' . $value . '</td></tr>' . PHP_EOL;
                        } else {
                            if ($key != 'contentType') {
                                if (($key == 'properties') && is_array($value)) {
                                    $content .= '    <tr><td style="width: 15%; font-weight: bold;">' . ucfirst($key) . '</td><td>' . implode('<br />', $value)  . '</td></tr>' . PHP_EOL;
                                } else {
                                    if (($key != 'properties') && ($value != '')) {
                                        if ($key == 'params') {
                                            $val = null;
                                            foreach ($value as $k => $v) {
                                                $val .= $k . ' (' . $v . ')<br />';
                                            }
                                        } else {
                                            $val = $value;
                                        }
                                        $content .= '    <tr><td style="width: 15%; font-weight: bold;">' . ucfirst(str_replace('firstLine', 'structure', $key)) . '</td><td>' . $val . '</td></tr>' . PHP_EOL;
                                    }
                                }
                            }
                        }
                    }
                }
                // Loop through any sub-level docblocks.
                if (isset($this->_docBlocks[$i]->docBlocks[0])) {
                    $content .= '</table>' . PHP_EOL;
                    $content .= '<h3>Methods</h3>' . PHP_EOL;
                    $content .= '<table class="jsDocTable">' . PHP_EOL;
                    foreach ($this->_docBlocks[$i]->docBlocks as $db) {
                        $content .= '    <tr><td colspan="2" style="background-color: #fff; padding: 15px 0 5px 0;"><strong class="blue">' . $db->firstLine . '</strong></td></tr>' . PHP_EOL;
                        $content .= '    <tr><td colspan="2">' . $db->header . '</td></tr>' . PHP_EOL;
                        foreach ($db as $k => $v) {
                            if (
                                ($k != 'content') &&
                                ($k != 'docBlock') &&
                                ($k != 'docBlocks') &&
                                ($k != 'header') &&
                                (stripos($k, 'start') === false) &&
                                (stripos($k, 'end') === false) &&
                                (stripos($k, 'length') === false)
                                ) {
                                if (($k == 'contentType') && ($v != '')) {
                                    $content .= '    <tr><td style="width: 15%; font-weight: bold;">Type</td><td>' . $v . '</td></tr>' . PHP_EOL;
                                } else {
                                    if ($k != 'contentType') {
                                        if ($k == 'params') {
                                            $vl = null;
                                            foreach ($v as $j => $w) {
                                                $vl .= $j . ' (' . $w . ')<br />';
                                            }
                                        } else {
                                            $vl = $v;
                                        }
                                        $content .= '    <tr><td style="width: 33%; font-weight: bold;">' . ucfirst(str_replace('firstLine', 'structure', $k)) . '</td><td>' . $vl . '</td></tr>' . PHP_EOL;
                                    }
                                }
                            }
                        }
                    }
                }
                $content .= '</table>' . PHP_EOL;

                // Write and save the new doc file.
                if (isset($this->_docBlocks[$i]->subpackage) && ($this->_docBlocks[$i]->subpackage != '') && (!in_array($this->_docBlocks[$i]->subpackage, $subpackages))) {
                    // Actually, don't do anything.
                } else {
                    $header = new Moc10_File(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../templates/header.html'));
                    $footer = new Moc10_File(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../templates/footer.html'));

                    $html = strtolower(str_replace('&', 'and', str_replace(' ', '-', $this->_docBlocks[$i]->subpackage))) . '.html';
                    $nav[$html] = $this->_docBlocks[$i]->subpackage;

                    $jsdoc = new Moc10_File($dir . DIRECTORY_SEPARATOR . $html);
                    $jsdoc->write($header->read() . $content . $footer->read());
                    $jsdoc->save();
                    echo 'Writing to ' . $html . '...' . PHP_EOL;
                }
            }
        }

        // Build the nav tree.
        if (count($nav) > 0) {
            $content = '<ul id="nav">' . PHP_EOL;
            $i = 0;
            foreach ($nav as $key => $value) {
                if ($i == 0) {
                    $content .= '    <li style="font-size: 1.1em;"><a target="content" href="' . $key . '">' . $value . '</a></li>' . PHP_EOL;
                } else {
                    $content .= '    <li style="padding-left: 10px; font-size: 0.85em;">&gt; <a target="content" href="' . $key . '">' . $value . '</a></li>' . PHP_EOL;
                }
                $i++;
            }
            $content .= '</ul><hr><!-- End //-->' . PHP_EOL;

            // If no nav file has been created yet, create one, and copy the index file over as well.
            if (!file_exists($dir . DIRECTORY_SEPARATOR . 'nav.html') && !file_exists($dir . DIRECTORY_SEPARATOR . 'index.html')) {

                $header = new Moc10_File(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../templates/nav-header.html'));
                $footer = new Moc10_File(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../templates/footer.html'));

                $navFile = new Moc10_File($dir . DIRECTORY_SEPARATOR . 'nav.html');
                $navFile->write($header->read() . $content . $footer->read());
                $navFile->save();
                echo 'Writing to nav.html...' . PHP_EOL;

                $top_header = new Moc10_File(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../templates/top-header.html'));
                $top_header->copy($dir . DIRECTORY_SEPARATOR . 'top-header.html');

                $keys = array_keys($nav);

                $index = new Moc10_File(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../templates/index.html'));
                $newIndex = new Moc10_File($dir . DIRECTORY_SEPARATOR . 'index.html');
                $newIndex->write(str_replace('[{first_doc}]', $keys[0], $index->read()));
                $newIndex->save();

            // Else, append the new nav to the existing nav file.
            } else {

                $navFile = new Moc10_File($dir . DIRECTORY_SEPARATOR . 'nav.html');
                $curNav = str_replace('</ul><hr><!-- End //-->', '</ul><hr>' . PHP_EOL . $content . '<hr><!-- End //-->', $navFile->read());

                if (strpos($curNav, '<hr><!-- End //-->' . PHP_EOL . '<hr><!-- End //-->') != false) {
                    $curNav = str_replace('<hr><!-- End //-->' . PHP_EOL . '<hr><!-- End //-->', '<hr><!-- End //-->' . PHP_EOL, $curNav);
                }

                $navFile->write($curNav);
                $navFile->save();

            }

        }

    }

    /**
     * Method to parse the global variables
     *
     * @return void
     */
    protected function _parseGlobals()
    {

        $matches = array();

        preg_match_all('/^var(.*);$/m', $this->_output, $matches, PREG_OFFSET_CAPTURE);

        if (isset($matches[0])) {
            foreach ($matches[0] as $match) {
                $gbl = str_replace(';', '', str_replace('var', '', $match[0]));
                $this->_globals[] = trim($gbl);
            }
        }

    }

    /**
     * Method to parse the prototype extensions
     *
     * @return void
     */
    protected function _parsePrototypes()
    {

        $matches = array();

        preg_match_all('/[a-zA-Z0-9_-]*\.prototype\.[a-zA-Z0-9_-]*/m', $this->_output, $matches, PREG_OFFSET_CAPTURE);

        if (isset($matches[0])) {
            foreach ($matches[0] as $match) {
                $proto = explode('.', $match[0]);
                $params = substr($this->_output, $match[1], strpos($this->_output, '('));
                $params = substr($params, 0, strpos($params, ')'));
                $params = substr($params, (strpos($params, '(') + 1));
                if (!isset($this->_prototypes[$proto[0]])) {
                    $this->_prototypes[$proto[0]] = array();
                    $this->_prototypes[$proto[0]][] = $proto[2] . '(' . $params . ')';
                } else {
                    $this->_prototypes[$proto[0]][] = $proto[2] . '(' . $params . ')';
                }
            }
        }

        foreach ($this->_prototypes as $key => $value) {
            $ary = $value;
            sort($ary);
            $this->_prototypes[$key] = $ary;
        }

    }

}