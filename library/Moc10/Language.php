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
 * @package    Moc10_Language
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2009-2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.moc10phplibrary.com/LICENSE.TXT     New BSD License
 */

/**
 * Moc10_Language
 *
 * @category   Moc10
 * @package    Moc10_Language
 * @author     Nick Sagona, III <nick@moc10media.com>
 * @copyright  Copyright (c) 2009-2011 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.moc10phplibrary.com/LICENSE.TXT     New BSD License
 * @version    2.0.0
 */

class Moc10_Language
{

    /**
     * Default system language
     * @var string
     */
    protected $_language = null;

    /**
     * Language content
     * @var array
     */
    protected $_content = array('source' => array(), 'output' => array());

    /**
     * Constructor
     *
     * Instantiate the language object.
     *
     * @param  string $lng
     * @return void
     */
    public function __construct($lng = null)
    {

        $this->_language = (!is_null($lng)) ? $lng : 'en';
        $this->_loadCurrentLanguage();

    }

    /**
     * Get current language setting.
     *
     * @return string
     */
    public function getLanguage() {

        return $this->_language;

    }

    /**
     * Load language content from an XML file.
     *
     * @param  string $langFile
     * @throws Exception
     * @return void
     */
    public function loadFile($langFile)
    {

        if (file_exists($langFile)) {
            if (($xml =@ new SimpleXMLElement($langFile, LIBXML_NOWARNING, true)) !== false) {
                foreach ($xml->text as $text) {
                    if (isset($text->source) && isset($text->output)) {
                        $this->_content['source'][] = (string)$text->source;
                        $this->_content['output'][] = (string)$text->output;
                    }
                }
            } else {
                throw new Exception('Error: There was an error processing that XML file.');
            }
        } else {
            throw new Exception('Error: The language file ' . $langFile . ' does not exist.');
        }

    }

    /**
     * Return the translated string
     *
     * @param  string $str
     * @param  string|array $params
     * @return $str
     */
    public function __($str, $params = null)
    {

        return $this->_translate($str, $params);

    }

    /**
     * Echo the translated string.
     *
     * @param  string $str
     * @param  string|array $params
     * @return void
     */
    public function _e($str, $params = null)
    {

        echo $this->_translate($str, $params);

    }

    /**
     * Get languages from the XML files.
     *
     * @param  string $dir
     * @return array
     */
    public function getLanguages($dir = null)
    {

        $langsAry = array();
        $langDirectory = (!is_null($dir)) ? $dir : dirname(__FILE__) . '/Language/Data';

        if (file_exists($langDirectory)) {
            $langDir = new Moc10_File_Dir($langDirectory);
            foreach ($langDir->files as $file) {
                if ($file != '__.xml') {
                    if (($xml =@ new SimpleXMLElement($langDirectory . '/' . $file, LIBXML_NOWARNING, true)) !== false) {
                        if ((string)$xml->attributes()->name == (string)$xml->attributes()->native) {
                            $langsAry[str_replace('.xml', '', $file)] = $xml->attributes()->native;
                        } else {
                            $langsAry[str_replace('.xml', '', $file)] = $xml->attributes()->native . ' (' . $xml->attributes()->name . ")";
                        }
                    }
                }
            }
        }

        return $langsAry;

    }

    /**
     * Process and translate an new XML language document from the
     * template file via Google Translate using a provided API key.
     *
     * @param  string $lang
     * @param  string $api
     * @param  string|boolean $dest
     * @param  string $src
     * @param  string $tmpl
     * @return void
     */
    public function generateLanguageFile($lang, $api, $dest = false, $src = 'en', $tmpl = null)
    {

        $langCodes = array('af'    => 'Afrikaans',
                           'sq'    => 'Albanian',
                           'ar'    => 'Arabic',
                           'be'    => 'Belarusian',
                           'bg'    => 'Bulgarian',
                           'ca'    => 'Catalan',
                           'zh'    => 'Chinese',
                           'hr'    => 'Croatian',
                           'cs'    => 'Czech',
                           'da'    => 'Danish',
                           'nl'    => 'Dutch',
                           'et'    => 'Estonian',
                           'tl'    => 'Filipino',
                           'fi'    => 'Finnish',
                           'fr'    => 'French',
                           'gl'    => 'Galician',
                           'de'    => 'German',
                           'el'    => 'Greek',
                           'en'    => 'English',
                           'ht'    => 'Haitian Creole',
                           'iw'    => 'Hebrew',
                           'hi'    => 'Hindi',
                           'hu'    => 'Hungarian',
                           'is'    => 'Icelandic',
                           'id'    => 'Indonesian',
                           'ga'    => 'Irish',
                           'it'    => 'Italian',
                           'ja'    => 'Japanese',
                           'lv'    => 'Latvian',
                           'lt'    => 'Lithuanian',
                           'mk'    => 'Macedonian',
                           'ms'    => 'Malay',
                           'mt'    => 'Maltese',
                           'no'    => 'Norwegian',
                           'fa'    => 'Persian',
                           'pl'    => 'Polish',
                           'pt'    => 'Portuguese',
                           'ro'    => 'Romanian',
                           'ru'    => 'Russian',
                           'sr'    => 'Serbian',
                           'sk'    => 'Slovak',
                           'sl'    => 'Slovenian',
                           'es'    => 'Spanish',
                           'sw'    => 'Swahili',
                           'sv'    => 'Swedish',
                           'th'    => 'Thai',
                           'tr'    => 'Turkish',
                           'uk'    => 'Ukrainian',
                           'vi'    => 'Vietnamese',
                           'cy'    => 'Welsh',
                           'yi'    => 'Yiddish');

        // Check for valid source and output language codes.
        if (!array_key_exists($lang, $langCodes)) {
            throw new Exception($this->__('Error: The output language selection is not valid.'));
        } else if (!array_key_exists($src, $langCodes)) {
            throw new Exception($this->__('Error: The source language selection is not valid.'));
        } else if ($lang == $src) {
            throw new Exception($this->__('Error: Both the output and source language selections are the same. Please choose again.'));
        } else {

            // Get language.
            $url = 'https://www.googleapis.com/language/translate/v2?key=' . $api . '&q=' . $langCodes[$lang] . '&source=' . $src . '&target=' . $lang;

            $options = array(CURLOPT_URL => $url,
                             CURLOPT_HEADER => FALSE,
                             CURLOPT_RETURNTRANSFER => TRUE,
                             CURLOPT_SSL_VERIFYPEER => FALSE);

            $curl = new Moc10_Curl($options);
            $trans = $curl->execute();
            unset($curl);

            if (strpos($trans, 'Bad Request') !== false) {
                throw new Exception($this->__('Error: There was an error processing that Google Translate URL. Please check your API key.'));
            } else {

                $result = json_decode($trans);
                $langTrans = $result->data->translations[0]->translatedText;

                $url = 'https://www.googleapis.com/language/translate/v2?key=' . $api . '&q=' . $langCodes[$lang] . '&source=' . $lang . '&target=' . $src;

                $options = array(CURLOPT_URL => $url,
                                 CURLOPT_HEADER => FALSE,
                                 CURLOPT_RETURNTRANSFER => TRUE,
                                 CURLOPT_SSL_VERIFYPEER => FALSE);

                $curl = new Moc10_Curl($options);
                $trans = $curl->execute();
                unset($curl);

                $result = json_decode($trans);
                $langName = $result->data->translations[0]->translatedText;

                // Get template text.
                $langText = array();
                $langTmplFile = (!is_null($tmpl)) ? $tmpl : dirname(__FILE__) . '/Language/Data/__.xml';
                if (file_exists($langTmplFile)) {
                    if (($xml =@ new SimpleXMLElement($langTmplFile, LIBXML_NOWARNING, true)) !== false) {
                        foreach ($xml->text as $text) {
                            $langText[] = (string)$text->source;
                        }
                    }
                }

                // Construct URL queries.
                $qValues = array();
                if (isset($langText[0])) {
                    $q = '';
                    foreach ($langText as $text) {
                        if (strlen($q) >= 1500) {
                            $qValues[] = $q;
                            $q = '';
                        }
                        $q .= '&q=' . urlencode($text);
                    }
                    if ($q != '') {
                        $qValues[] = $q;
                    }

                    // Process each URL query, returning Google Translate's JSON data.
                    if (isset($qValues[0])) {
                        $transText = array();
                        foreach ($qValues as $qString) {
                            $url = 'https://www.googleapis.com/language/translate/v2?key=' . $api . $qString . '&source=' . $src . '&target=' . $lang;

                            $options = array(CURLOPT_URL => $url,
                                             CURLOPT_HEADER => FALSE,
                                             CURLOPT_RETURNTRANSFER => TRUE,
                                             CURLOPT_SSL_VERIFYPEER => FALSE);

                            $curl = new Moc10_Curl($options);
                            $trans = $curl->execute();
                            unset($curl);

                            $result = json_decode($trans);
                            foreach ($result->data->translations as $text) {
                                $transText[] = (string)$text->translatedText;
                            }
                        }
                    }
                }

                // Construct the new XML language file.
                $langDoc = new Moc10_Dom('XML', 'utf-8');
                $langDoc->setDTD("<!DOCTYPE language [\n    <!ELEMENT language ANY>\n    <!ELEMENT text (source,output)>\n    <!ELEMENT source ANY>\n    <!ELEMENT output ANY>\n    <!ATTLIST language\n        src       CDATA    #REQUIRED\n        output    CDATA    #REQUIRED\n        name      CDATA    #REQUIRED\n        native    CDATA    #REQUIRED\n    >\n]>");
                $langNode = new Moc10_Dom_Child('language');
                $langNode->setAttributes('src', $src);
                $langNode->setAttributes('output', $lang);
                $langNode->setAttributes('name', $langName);
                $langNode->setAttributes('native', $langTrans);

                $textNodes = array();

                // Add the text nodes.
                for ($i = 0; $i < count($langText); $i++) {
                    $text = new Moc10_Dom_Child('text', null, null, false, '    ');
                    $source = new Moc10_Dom_Child('source', str_replace(' & ', ' &amp; ', $langText[$i]), null, false, '        ');
                    $output = new Moc10_Dom_Child('output', str_replace(' & ', ' &amp; ', $transText[$i]), null, false, '        ');
                    $text->addChildren(array($source, $output));
                    $textNodes[] = $text;

                }

                $langNode->addChild($textNodes);
                $langDoc->addChild($langNode);

                // Output to browser.
                if (!$dest) {
                    $langDoc->render(true);
                // Else, output to file.
                } else {
                    $langXMLFile = new Moc10_File($dest . '/' . $lang . '.xml');
                    $langXMLFile->write($langDoc->render(true));
                }

            }

        }

    }

    /**
     * Translate and return the string.
     *
     * @param  string $str
     * @param  string|array $params
     * @return $str
     */
    protected function _translate($str, $params = null)
    {

        $key = array_search($str, $this->_content['source']);
        $trans = ($key !== false) ? $this->_content['output'][$key] : $str;

        if (!is_null($params)) {
            if (is_array($params)) {
                foreach ($params as $key => $value) {
                    $trans = str_replace('%' . ($key + 1), $value, $trans);
                }
            } else {
                $trans = str_replace('%1', $params, $trans);
            }
        }

        return $trans;

    }

    /**
     * Get language content from the XML file.
     *
     * @return void
     */
    protected function _loadCurrentLanguage()
    {

        $this->loadFile(dirname(__FILE__) . '/Language/Data/' . $this->_language . '.xml');

    }

}
