<?php
/**
 * Copyright © Sillove commerce All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Sillove\PageSpeed\Observer\Frontend\Controller;

use \Magento\Framework\UrlInterface;
use \Magento\Store\Model\StoreManagerInterface;

if (!defined('PAGESPEED_PATH')) {
    define('PAGESPEED_PATH', __dir__);
}
class FrontSendResponseBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var array
     */
    public $addSettings = [];

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    public $request;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    public $httpHeader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    public $directory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    public $urlInterface;

    /**
     * @var \Sillove\PageSpeed\Helper\Data
     */
    public $helper;

    /**
     * @var string
     */
    public $mediaPath;

    /**
     * @var string
     */
    public $documentRoot;

    /**
     * @var string
     */
    public $mediaUrl;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    public $driver;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    public $encrypter;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\HTTP\Header $httpHeader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Sillove\PageSpeed\Helper\Data $helper
     * @param \Magento\Framework\Filesystem\Driver\File $driver
     * @param \Magento\Framework\Encryption\EncryptorInterface $encrypter
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\HTTP\Header $httpHeader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\UrlInterface $urlInterface,
        \Sillove\PageSpeed\Helper\Data $helper,
        \Magento\Framework\Filesystem\Driver\File $driver,
        \Magento\Framework\Encryption\EncryptorInterface $encrypter
    ) {

        $this->helper = $helper;
        $this->httpHeader = $httpHeader;
        $this->storeManager = $storeManager;
        $this->directory = $dir;
        $this->urlInterface = $urlInterface;
        $this->request = $request;
        $this->driver = $driver;
        $this->encrypter = $encrypter;
        $this->mediaPath = $dir->getPath('media');
        $this->documentRoot  =  $dir->getPath('pub');
        $this->mediaUrl = rtrim($storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA), '/');

        if (strpos($this->mediaUrl, '/pub/') !== false) { // NOSONAR
            $this->addSettings['documentRoot'] = str_replace('/pub/media', '', $this->mediaPath);
        } else {
            $this->addSettings['documentRoot']  =  $this->directory->getPath('pub');
        }

        $homeUrl1 = explode('/', $this->mediaUrl);
        if (strpos($this->mediaUrl, '/pub/') !== false) {
            array_pop($homeUrl1);
        }
        array_pop($homeUrl1);
        $uri = \Laminas\Uri\UriFactory::factory($this->mediaUrl);
        $query = $uri->parse($this->mediaUrl);
        $mediaUrlArr = [
            'scheme' => $query->getScheme(),
            'host' => $query->getHost(),
            'path' => $query->getPath(),
        ];
        $this->addSettings['homeUrl'] = implode('/', $homeUrl1);/* edit */
        $homeUrlStrPos = strpos($this->addSettings['homeUrl'], 'https:');
        $this->addSettings['secure'] = $homeUrlStrPos !== false ? 'https://' : 'http://';

        $this->addSettings['fullUrl'] = $urlInterface->getCurrentUrl();
        $full_url_array = explode('?', $this->addSettings['fullUrl']);
        $this->addSettings['fullUrlWithoutParam'] = $full_url_array[0];
        $this->addSettings['rootCachePath'] = $this->mediaPath.'/cache/'.$mediaUrlArr['host'];
        $this->addSettings['cacheUrl'] = str_replace(
            $this->addSettings['documentRoot'],
            $this->addSettings['homeUrl'],
            $this->addSettings['rootCachePath']
        );
        $this->addSettings['js_ext'] = '.js';
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $settings = $this->helper->getSettings();

        /* check module settings is enabled*/
        if (! ($settings['optimization_on'] && $settings['js'])) {
            return;
        }

        $response = $observer->getEvent()?->getData('response');
        $html = $response?->getBody();

        if (!$response || $html === '') {
            return;
        }
        /**
         * Return if no body tag found. for ajax request. exclude page from js optimization
         */
        if (strpos($html, '<body') === false ||
            $this->checkIfPageExcluded($settings['exclude_page_from_load_combined_js'])
        ) {
            return;
        }

        $html = str_replace([
        '<script type="text/javascript"',
        '<script  type="text/javascript"',
        "<script type='text/javascript'"
        ], ['<script ','<script ','<script '], $html); // NOSONAR
        $all_links = $this->getAllLinks($html, ['script']);
        $html = $this->minify($html, $all_links['script']);
        $html = $this->getStrReplaceBulk($html);
        $contentFile = $this->driver->fileGetContents(PAGESPEED_PATH.'/assets/js/script-load.js');
        $html = $this->insertContentHead($html, '<script>'.$contentFile.'</script>', 3); // NOSONAR
        $response->setBody($html);
    }

    /**
     * Check if page excluded
     *
     * @param mixed $exclude_setting
     * @return bool
     */
    private function checkIfPageExcluded($exclude_setting):bool
    {
        $e_p_from_optimization = !empty($exclude_setting) ? explode("\r\n", $exclude_setting) : [];
        if (!empty($e_p_from_optimization)) {
            $testing = $this->request->getParam('testing');
            foreach ($e_p_from_optimization as $e_page) {
                if (empty($e_page)) {
                    continue;
                }
                if (empty($testing) && $this->addSettings['homeUrl'] == $e_page) {
                    return true;
                } elseif ($this->addSettings['homeUrl'] != $e_page) {
                    if (strpos($this->addSettings['fullUrl'], $e_page)!==false) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Replaces multiple strings in the given HTML content using arrays of search strings and replacement strings.
     *
     * @param string $html The HTML content in which to perform the replacements.
     * @return string The HTML content after performing the bulk string replacements.
     */
    private function getStrReplaceBulk($html):string
    {
        global $str_replace_str_array, $str_replace_rep_array; // @codingStandardsIgnoreLine
        return str_replace($str_replace_str_array, $str_replace_rep_array, $html);
    }

    /**
     * Minifies the given HTML content, including inline JavaScript.
     *
     * @param string $html The HTML content to minify.
     * @param array $script_links An array of JavaScript file links to include in the minified HTML.
     * @return string The minified HTML content.
     */
    private function minify($html, $script_links)
    {
        if (!empty($script_links)) {
            $final_merge_js = [];
            $final_merge_has_js = [];

            // @codingStandardsIgnoreLine
            for ($si=0; $si < count($script_links); $si++) {
                $script = $script_links[$si];
                $script_obj = $this->parseLink('script', $script_links[$si]);
                $script_text = '';
                if (!array_key_exists('src', $script_obj)) {
                    $script_text = $this->parseScript($script);

                }
                // this will  continue if type is like something text/x-magento-init
                if (!empty($script_obj['type']) &&
                    strtolower($script_obj['type']) != 'application/javascript' &&
                    strtolower($script_obj['type']) != 'text/javascript' &&
                    strtolower($script_obj['type']) != 'text/jsx;harmony=true'
                ) {
                    continue;
                }
                /*script tag with src attribute*/
                if (!empty($script_obj['src'])) {

                    /**
                     * it will covert url to Array (
                     * [path] => /static/version1708066082/frontend/Norwalk/default_child/en_US/requirejs/require.js
                     * [query] => a=1
                     * )
                     */
                    $pattern = '/(.*)\/\/'.str_replace(
                        '/',
                        '\/',
                        str_replace(
                            $this->addSettings['secure'],
                            '',
                            rtrim($this->addSettings['homeUrl'], '/')
                        )
                    ).'(.*)/';
                    $src = preg_replace($pattern, '$2', $script_obj['src']);
                    $uri = \Laminas\Uri\UriFactory::factory($src);
                    $query = $uri->parse($src);
                    $url_array = [
                        'path' => $query->getPath(),
                    ];
                    if (strpos($url_array['path'], '/version') !== false) {
                        $url_array['path'] = preg_replace('/version(\d)*\//', '', $url_array['path']);
                    }
                    $url_array['path'] = str_replace('/pub/', '/', $url_array['path']);
                    if (!$this->isExternalUrl($script_obj['src']) && $this->endsWith($url_array['path'], '.js')) {
                        // /var/www/html/norwalk-cloud/pub : $this->addSettings['documentRoot']);
                        // $url_array['path'] :  /static/frontend/Norwalk/default_child/en_US/requirejs/require.js
                        if ($this->driver->isExists($this->addSettings['documentRoot'].$url_array['path'])) {
                            $url_array['path'] = $this->createFileCacheJs($url_array['path']);
                        } else {
                            $url_array['path'] = $this->getCreateFileCacheJsUrl($script_obj['src']);
                        }
                        $script_obj['src'] = $this->addSettings['homeUrl'].$url_array['path'];
                    }

                    $val = $script_obj['src'];
                    
                    if (!empty($val) && !$this->isExternalUrl($val) && strpos($script, '.js') !== false) {
                        $final_merge_js[] = $url_array['path'];
                        $final_merge_has_js[] = $script;
                        if (!empty($final_merge_js)) {
                            $cache_js_url = $this->createJsCombinedCacheFile($final_merge_js);
                            $this->replaceJsFilesWithCombinedFiles($final_merge_has_js, $cache_js_url);
                            $final_merge_js = [];
                            $final_merge_has_js = [];
                        }
                    } elseif ($this->isExternalUrl($val)) {
                        $script_obj['type'] = 'lazyload_int';
                        $script_obj['data-src'] = $script_obj['src'];
                        unset($script_obj['src']);
                        $this->strReplaceSet($script, $this->implodeLinkArray('script', $script_obj));
                    }
                } else {
                    /*script tag without src attribute*/
                    $script_modified = '<script type="lazyload_int" ';

                    foreach ($script_obj as $key => $value) {
                        if ($key != 'type') {
                            $script_modified .= $key.'="'.$value.'"';
                        }
                    }
                    $script_modified = $script_modified.'>'.$script_text.'</script>';
                    $this->strReplaceSet($script, $script_modified);

                }
                if ($si == count($script_links)-1 && !empty($final_merge_has_js)) {
                    if (!empty($final_merge_js)) { // NOSONAR
                        $cache_js_url = $this->createJsCombinedCacheFile($final_merge_js);
                        $this->replaceJsFilesWithCombinedFiles($final_merge_has_js, $cache_js_url);
                        $final_merge_js = [];
                    }
                }
            }
        }
        return $html;
    }

    /**
     * Inserts content into the <head> section of the provided HTML content at the specified position.
     *
     * @param string $html The HTML content in which to insert the content into the <head> section.
     * @param string $content The content to insert into the <head> section.
     * @param int $pos The position at which to insert the content (0 for the beginning, 1 for the end).
     * @return string The HTML content with the inserted content in the <head> section.
     */

    private function insertContentHead($html, $content, $pos)
    {
        global $insert_content_head; // @codingStandardsIgnoreLine
        $insert_content_head[] = [$content,$pos];

        $html = preg_replace('/<head([^<]*)>/', '<head$1>'.$content, $html, 1, $count);
        if (empty($count)) {
              $html = preg_replace('/<html([^<]*)>/', '<html$1>'.$content, $html, 1, $count);
        }

        return $html;
    }

    /**
     * Creates a cached JavaScript file based on the provided URL.
     *
     * @param string $path The URL of the JavaScript file.
     * @return string The relative path to the cached JavaScript file.
     */
    private function getCreateFileCacheJsUrl($path)
    {
        $hashName = $this->encrypter->hash($path);
        $cache_file_path = $this->getCachePath('js').'/'.$hashName.'.js'; // @codingStandardsIgnoreLine
        if (!$this->driver->isExists($cache_file_path)) {
            $html = $this->driver->fileGetContents($path);
            $html = $this->getModifyFileCacheJs($html, $path);
            $this->createFile($cache_file_path, $html);
        }
        return str_replace($this->addSettings['documentRoot'], '', $cache_file_path);
    }

    /**
     * Modifies the content of a JavaScript cache file based on the provided HTML content and file path.
     *
     * @param string $html The HTML content to be used for modification.
     * @param string $path The path of the JavaScript cache file being modified.
     * @return string The modified HTML content.
     */
    private function getModifyFileCacheJs($html, $path)
    {
        $src_array = explode('/', $path);
        $count = count($src_array);
        unset($src_array[$count-1]);
        if ((strpos($html, 'holdready:') !== false ||
            strpos($html, 'S.holdReady') !== false) &&
            empty($this->addSettings['holdready'])
        ) {
            $html .= ';if(typeof($) == "undefined"){$ = jQuery;}else{var $ = jQuery;}';
            $this->addSettings['holdready'] = 1;
        }
        return $html;
    }

    /**
     * Creates an HTML tag with attributes from the given array.
     *
     * @param string $tag The HTML tag name.
     * @param array $array An associative array containing tag attributes (key-value pairs).
     * @return string The generated HTML tag with attributes.
     */
    private function implodeLinkArray($tag, $array)
    {
        $link = '<'.$tag.' ';
        foreach ($array as $key => $arr) {
            $link .= $key.'="'.$arr.'" ';
        }
        if ($tag == 'script') {
            $link .= '></script>';
        } else {
            $link .= '/>';
        }
        return $link;
    }

    /**
     * Adds a pair of search and replacement strings to be used in bulk string replacement.
     *
     * @param string $str The search string to be replaced.
     * @param string $rep The replacement string.
     * @return void
     */
    private function strReplaceSet($str, $rep)
    {
        global $str_replace_str_array, $str_replace_rep_array; // @codingStandardsIgnoreLine
        $str_replace_str_array[] = $str;
        $str_replace_rep_array[] = $rep;
    }

    /**
     * Replaces JavaScript file references with a combined JavaScript file URL in HTML content.
     *
     * @param array $final_merge_has_js An array of JavaScript file references to be replaced.
     * @param string $cache_js_url The URL of the combined JavaScript file.
     * @return void
     */
    private function replaceJsFilesWithCombinedFiles($final_merge_has_js, $cache_js_url)
    {
        if (!empty($final_merge_has_js)) {
            // @codingStandardsIgnoreLine
            for ($ii = 0; $ii < count($final_merge_has_js); $ii++) {
                if ($ii == count($final_merge_has_js) -1) {
                    $this->strReplaceSet(
                        $final_merge_has_js[$ii],
                        '<script type="lazyload_int" data-src="'.$cache_js_url.'"></script>'
                    );
                } else {
                    $this->strReplaceSet($final_merge_has_js[$ii], '');
                }
            }
        }
    }

    /**
     * Creates a cache file containing combined JavaScript files specified in the provided array.
     *
     * @param array|string $final_merge_js An array of JavaScript file paths or a single JavaScript file path.
     * @return string|void The URL of the created cache file, or void if no cache file is created.
     */
    private function createJsCombinedCacheFile($final_merge_js)
    {
        $file_name = is_array($final_merge_js) ? implode('-', $final_merge_js) : '';
        if (!empty($file_name)) {
            $hashName = $this->encrypter->hash($file_name);
            $js_file_name = $hashName.$this->addSettings['js_ext']; // @codingStandardsIgnoreLine
            if (!$this->driver->isFile($this->getCachePath('all-js').'/'.$js_file_name)) {
                $all_js = '';
                foreach ($final_merge_js as $script_path) {
                    $all_js .= $this->driver->fileGetContents($this->addSettings['documentRoot'].$script_path).";\n";
                }
                $this->createFile($this->getCachePath('all-js').'/'.$js_file_name, $all_js);
            }

            return $this->addSettings['cacheUrl'].'/all-js/'.$js_file_name;
        }
    }

    /**
     * Retrieves the cache path based on the provided subpath, if any.
     *
     * @param string $path Optional subpath within the cache directory.
     * @return string The full cache path including the subpath, if provided.
     */
    private function getCachePath($path = '')
    {
        $cache_path = $this->addSettings['rootCachePath'].(!empty($path) ? '/'.$path : '');
        $this->getCheckIfFolderExists($cache_path);
        return $cache_path;
    }

    /**
     * Get check if folder exists
     *
     * @param string $path
     */
    private function getCheckIfFolderExists($path)
    {
        if ($this->driver->isDirectory($path)) {
            return $path;
        }
        try {
            $this->driver->createDirectory($path, 0777);
        } catch (\Exception $e) {
            return 'Message: '.$path .$e->getMessage();
        }
        return $path;
    }

    /**
     * Creates a new file with the specified path and writes the provided text to it.
     *
     * @param string $path The path of the file to create.
     * @param string $text The text content to write to the file. Default is an empty comment.
     * @return bool True if the file creation and writing were successful, false otherwise.
     */
    private function createFile($path, $text = '//')
    {
        $file = $this->driver->fileOpen($path, 'w');
        $this->driver->fileWrite($file, $text);
        $this->driver->fileClose($file);
        return true;
    }

    /**
     * Creates a cached JavaScript file based on the provided path.
     *
     * @param string $path The path to the JavaScript file.
     * @return string The relative path to the cached JavaScript file.
     */
    private function createFileCacheJs($path)
    {
        $hashName = $this->encrypter->hash($path);
        $cache_file_path = $this->GetCachePath('js').'/'.$hashName.'.js'; // @codingStandardsIgnoreLine
        if (!$this->driver->isExists($cache_file_path)) {
            $html = $this->driver->fileGetContents($this->addSettings['documentRoot'].$path);
            $src_array = explode('/', $path);
            $count = count($src_array);
            unset($src_array[$count-1]);
            if (strpos($path, 'require.js') !== false || strpos($path, 'require.min.js') !== false) {
                $html = str_replace(['fn,4','fn, 4'], ['fn,20','fn, 20'], $html);
            }
            if (strpos($html, 'jQuery requires a window with a document') !== false &&
                empty($this->addSettings['holdready'])) {
                $html .= ';if(typeof($) == "undefined"){$ = jQuery;}else{var $ = jQuery;} jQuery.holdReady( true );';
                $this->addSettings['holdready'] = 1;
            }

            if (strpos(trim($html), '"use strict";') === 0) {
                $html = preg_replace('/"use strict";/', '', $html, 1);
            }
            
            if (strpos($path, 'custom_js_after_load.js') === false && !empty($this->addSettings['holdready'])) {
                $html = ';jQuery.holdReady( false );'.$html;
            }

            $html = str_replace('sourceMappingURL=', 'sourceMappingURL='.implode('/', $src_array), $html.";\n");
            $this->createFile($cache_file_path, $html);
        }
        return str_replace($this->addSettings['documentRoot'], '', $cache_file_path);
    }

    /**
     * Checks if the given string ends with the specified test string.
     *
     * @param string $string The string to check.
     * @param string $test The test string to compare.
     * @return bool True if the string ends with the test string, false otherwise.
     */
    private function endsWith($string, $test): bool
    {
        $str_arr = explode('?', $string);
        $ext = '.'.pathinfo($str_arr[0], PATHINFO_EXTENSION); // @codingStandardsIgnoreLine
        if ($ext == $test) {
            return true;
        }
        return false;
    }

    /**
     * Parses the content enclosed within a <script> tag from the given HTML content or URL.
     *
     * @param string $link The HTML content or URL from which to parse the script content.
     * @return string The content enclosed within the <script> tag, or an empty string if not found.
     */
    private function parseScript($link)
    {
        $data_exists = strpos($link, '>');
        $link_arr = '';
        if (!empty($data_exists)) {
            $end_tag_pointer = strpos($link, '</script>', $data_exists);
            $link_arr = substr($link, $data_exists+1, $end_tag_pointer-$data_exists-1);
        }
        return $link_arr;
    }

    /**
     * Parses the specified HTML tag from the given link and returns an array of its attributes.
     *
     * @param string $tag The HTML tag to parse.
     * @param string $link The HTML content or URL from which to parse the tag.
     * @return array An associative array containing the attributes of the specified HTML tag.
     */
    private function parseLink($tag, $link)
    {
        $xmlDoc = new \DOMDocument();
        // @codingStandardsIgnoreLine
        if (@$xmlDoc->loadHTML($link) === false) {
            return [];
        }
        $tag_html = $xmlDoc->getElementsByTagName($tag);
        $link_arr = [];
        if (!empty($tag_html[0])) {
            foreach ($tag_html[0]->attributes as $attr) {
                $link_arr[$attr->nodeName] = $attr->nodeValue;
            }
        }
        return $link_arr;
    }

    /**
     * Checks if the given URL is an external URL.
     *
     * @param string $url The URL to check.
     * @return bool True if the URL is external, false otherwise.
     */
    private function isExternalUrl($url): bool
    {
        $uri = \Laminas\Uri\UriFactory::factory($url);
        $query = $uri->parse($url);
        $components = [
            'scheme' => $query->getScheme(),
            'host' => $query->getHost(),
            'path' => $query->getPath(),
        ];
        // @codingStandardsIgnoreLine
        return !empty($components['host']) && strcasecmp($components['host'], $_SERVER['HTTP_HOST']);
    }

    /**
     * Extracts data between specified start and end tags from the given string.
     *
     * @param string $data The string from which to extract data.
     * @param string $start_tag The starting HTML tag.
     * @param string $end_tag The ending HTML tag.
     * @return array An array containing data between each occurrence of the start and end tags.
     */
    private function getTagsData($data, $start_tag, $end_tag)
    {
        $data_exists = 0;
        $i=0;
        $end_tag_char_len = strlen($end_tag);
        $script_array = [];
        while ($data_exists != -1 && $i<500) {
            $data_exists = strpos($data, $start_tag, $data_exists);
            if (!empty($data_exists)) {
                $end_tag_pointer = strpos($data, $end_tag, $data_exists);
                $script_array[] = substr($data, $data_exists, $end_tag_pointer-$data_exists+$end_tag_char_len);
                $data_exists = $end_tag_pointer;
            } else {
                $data_exists = -1;
            }
            $i++;
        }
        return $script_array;
    }

    /**
     * Retrieves all script from the provided data.
     *
     * This function parses the provided data to extract script and returns them.
     *
     * @param mixed $data The data from which script are to be extracted.
     * @param array $resources An optional array to store additional resources.
     * @return array An array containing the extracted script.
     */
    private function getAllLinks($data, $resources = [])
    {
        $resource_arr = [];
        $comment_tag = $this->getTagsData($data, '<!--', '-->');
        $data = str_replace($comment_tag, '', $data);
        if (in_array('script', $resources)) {
            $resource_arr['script'] = $this->getTagsData($data, '<script', '</script>');
        } else {
            $resource_arr['script'] = [];
        }

        return $resource_arr;
    }
}
