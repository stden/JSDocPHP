<?php
/**
 * JSDocPHP
 *
 * This is the controller file. You can parse one JS file or multiple
 * files within a folder. Simply issue the following command:
 *
 * 'php jsdoc.php myjavascript.js'
 *
 * OR
 *
 * 'php jsdoc.php myjavascriptfolder'
 *
 * It will create the API docs in a folder called 'docs'.
 * Optionally, you can pass an output folder to it as well:
 *
 * 'php jsdoc.php myjavascriptfolder mydocs'
 *
 * @category   JSDocPHP
 * @package    JSDoc
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    LICENSE.TXT     New BSD License
 * @version    0.9
 */

require_once 'bootstrap.php';

if (isset($argv[1]) && (($argv[1] == '-h') || ($argv[1] == '--help') || ($argv[1] == '/?'))) {
    $readme = new Moc10_File('README', false, array());
    echo $readme->read();
    exit(0);
}

$jsdocs = array();

// Make sure a file or directory is passed.
if (!isset($argv[1])) {

    exit(PHP_EOL . 'You must pass either a Javascript file or a directory with JavaScript files in it. Run \'php jsdoc.php -h\' for help.' . PHP_EOL);

// If argument is a directory.
} else if (is_dir($argv[1])) {

    // If the first argument is a directory, loop through to get the files in it.
    try {

        $dir = new Moc10_File_Dir($argv[1], true, true);
        foreach ($dir->files as $file) {
            if (is_file($file)) {
                $jsdocs[] = new JSDoc($file);
            }
        }

    // Display any exceptions.
    } catch (Exception $e) {
        exit(PHP_EOL . $e->getMessage() . PHP_EOL);
    }

// If argument is just a file.
} else if (file_exists($argv[1])) {

    try {
        $jsdocs[] = new JSDoc($argv[1]);
    } catch (Exception $e) {
        exit(PHP_EOL . $e->getMessage() . PHP_EOL);
    }

}

// Parse through each JavaScript file and create the output.
if (count($jsdocs) > 0) {

    // Create output directory.
    $output = (isset($argv[2])) ? $argv[2] : 'docs';
    if (!file_exists($output)) {
        mkdir($output);
        chmod($output, 0777);
    }

    // Loop through the JS files, parsing them and rendering the docs.
    foreach ($jsdocs as $jsdoc) {
        echo PHP_EOL . 'Parsing ' . $jsdoc->basename . '...' . PHP_EOL;
        $jsdoc->parse()->render(dirname(__FILE__) . DIRECTORY_SEPARATOR . $output);
    }

    echo PHP_EOL . 'Done!' . PHP_EOL;
// Else, if no JS files were found.
} else {
    exit(PHP_EOL . 'No JavaScript files were found.' . PHP_EOL);
}

?>