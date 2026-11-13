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
 * @package    JSDoc_Block
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    LICENSE.TXT     New BSD License
 */

/**
 * JSDocPHP
 *
 * @category   JSDoc
 * @package    JSDoc_Block
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    LICENSE.TXT     New BSD License
 * @version    0.9
 */

class JSDoc_Block
{

    /**
     * Method to parse a doc block.
     *
     * @param  string $str
     * @param  int    $start
     * @param  int    $length
     * @return ArrayObject
     */
    static public function parse($str, $start, $length)
    {

        $docBlock = substr($str, $start, $length);
        $docBlockArray = array();

        $header = substr($docBlock, 0, strpos($docBlock, '* @'));
        $header = trim(str_replace('   ', ' ', str_replace('  ', ' ', str_replace("\n", "", str_replace('*', '', $header)))));
        if ($header != '') {
            if (substr($header, 0, 1) == '/') {
                $header = trim(substr($header, 1));
            }
            $docBlockArray['header'] = $header;
        }

        if (strpos($docBlock, '@author') !== false) {
            $author = substr($docBlock, (strpos($docBlock, '@author') + 7));
            $author = substr($author, 0, strpos($author, "\n"));
            $docBlockArray['author'] = trim($author);
        }
        if (strpos($docBlock, '@copyright') !== false) {
            $copyright = substr($docBlock, (strpos($docBlock, '@copyright') + 10));
            $copyright = substr($copyright, 0, strpos($copyright, "\n"));
            $docBlockArray['copyright'] = trim($copyright);
        }
        if (strpos($docBlock, '@license') !== false) {
            $license = substr($docBlock, (strpos($docBlock, '@license') + 8));
            $license = substr($license, 0, strpos($license, "\n"));
            $docBlockArray['license'] = trim($license);
        }
        if (strpos($docBlock, '@category') !== false) {
            $category = substr($docBlock, (strpos($docBlock, '@category') + 9));
            $category = substr($category, 0, strpos($category, "\n"));
            $docBlockArray['category'] = trim($category);
        }
        if (strpos($docBlock, '@package') !== false) {
            $package = substr($docBlock, (strpos($docBlock, '@package') + 8));
            $package = substr($package, 0, strpos($package, "\n"));
            $docBlockArray['package'] = trim($package);
        }
        if (strpos($docBlock, '@subpackage') !== false) {
            $subpackage = substr($docBlock, (strpos($docBlock, '@subpackage') + 11));
            $subpackage = substr($subpackage, 0, strpos($subpackage, "\n"));
            $docBlockArray['subpackage'] = trim($subpackage);
        }
        if (strpos($docBlock, '@desc') !== false) {
            $desc = substr($docBlock, (strpos($docBlock, '@desc') + 5));
            if (strpos($desc, "@") !== false) {
                $desc = substr($desc, 0, strpos($desc, "@"));
            } else if (strpos($desc, "*/") !== false) {
                $desc = substr($desc, 0, strpos($desc, "*/"));
            }
            $docBlockArray['desc'] = trim(preg_replace('/\s\s*/', ' ', str_replace("\n", "", str_replace('*', '', $desc))));
        }

        if (strpos($docBlock, '@param') !== false) {
            $matches = array();
            preg_match_all('/@param\s*/', $docBlock, $matches, PREG_OFFSET_CAPTURE);
            if (isset($matches[0][0])) {
                $params = array();
                foreach ($matches[0] as $param) {
                    $par = substr($docBlock, ($param[1] + strlen($param[0])));
                    $par = trim(substr($par, 0, strpos($par, "\n")));
                    $parAry = explode(' ', $par);
                    $params[trim($parAry[count($parAry) - 1])] = trim($parAry[0]);
                }
                $docBlockArray['params'] = $params;
            }
        }

        if (strpos($docBlock, '@return') !== false) {
            $return = substr($docBlock, (strpos($docBlock, '@return') + 7));
            $return = substr($return, 0, strpos($return, "\n"));
            $docBlockArray['return'] = trim($return);
        }

        $docBlockArray['start']    = $start;
        $docBlockArray['end']      = $start + $length;
        $docBlockArray['length']   = $length;
        $docBlockArray['docBlock'] = $docBlock;

        return new ArrayObject($docBlockArray, ArrayObject::ARRAY_AS_PROPS);

    }

}