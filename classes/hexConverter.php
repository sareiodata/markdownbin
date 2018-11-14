<?php
/**
* Markdown Bin (https://markdownbin.com)
*
* @license   https://github.com/slimphp/Slim/blob/3.x/LICENSE.md (MIT License)
*/
namespace MarkdownBin;

/**
 * hexConverter
 *
 * This is a small utility app that converts a string to hex and back,
 * configure, and run a Slim Framework application.
 * \MarkdownBin\hexConverter
 *
 */

class hexConverter{
    /**
     * Converts string to hex
     *
     * @param string|string
     * @return string
     */
    static function strToHex($string)
    {
        $hex='';
        for ($i=0; $i < strlen($string); $i++){
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    /**
     * Converts hex to string
     *
     * @param hex|string
     * @return string
     */
    static function hexToStr($hex)
    {
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }
}