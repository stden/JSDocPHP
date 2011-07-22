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
 * @package    JSDoc_Properties
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    LICENSE.TXT     New BSD License
 */

/**
 * JSDocPHP
 *
 * @category   JSDoc
 * @package    JSDoc_Properties
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    LICENSE.TXT     New BSD License
 * @version    0.9
 */

class JSDoc_Properties
{

    /**
     * Method to get properties from the doc block content.
     *
     * @param  string $content
     * @return string
     */
    static public function getProperties($content)
    {

        $props = array();
        $matches = array();

        preg_match_all('/this\.+[a-zA-Z0-9-_]+\s*=\s+[a-zA-Z0-9|\'|"|\[\]\.new\sa-zA-Z0-9()]*;$/m', $content, $matches, PREG_OFFSET_CAPTURE);

        if (isset($matches[0][0])) {
            foreach ($matches[0] as $prop) {
                $p = trim(substr($prop[0], 0, strpos($prop[0], '=')));
                if (!in_array($p, $props)) {
                    $props[] = $p;
                }
            }
        }

        return (count($props) > 0) ? $props : null;

    }

}