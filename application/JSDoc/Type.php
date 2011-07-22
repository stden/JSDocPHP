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
 * @package    JSDoc_Type
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    LICENSE.TXT     New BSD License
 */

/**
 * JSDocPHP
 *
 * @category   JSDoc
 * @package    JSDoc_Type
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    LICENSE.TXT     New BSD License
 * @version    0.9
 */

class JSDoc_Type
{

    /**
     * Method to get content type from the first line of a doc block content.
     *
     * @param  ArrayObject $docBlock
     * @param  string      $subpackage
     * @return string
     */
    static public function getType($docBlock, $subpackage = null)
    {

        $type = null;

        if (stripos($docBlock->firstLine, 'this.') !== false) {
            if (!is_null($subpackage)) {
                $type = $subpackage . ' Method';
            } else {
                $type = 'Method';
            }
        } else if (stripos($docBlock->firstLine, 'function') !== false) {
            if ((stripos($docBlock->content, 'this.') !== false) && (stripos($docBlock->firstLine, '=') === false)) {
                $prototype = substr($docBlock->firstLine, (stripos($docBlock->firstLine, 'function') + 9));
                $prototype = substr($prototype, 0, strpos($prototype, '('));
                $type = $prototype . ' Prototype';
            } else if (stripos($docBlock->firstLine, 'prototype') !== false) {
                $type = substr($docBlock->firstLine, 0, strpos($docBlock->firstLine, '.')) . ' Prototype Method';
            } else {
                $type = 'Function';
            }
        }

        return $type;

    }

}