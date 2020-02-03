<?php
/**
 * MIT License
 *
 * Copyright (c) 2020 Nearby Creative https://nearbycreative.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Light\Console;

/**
 * Class Color
 *
 * @package Light\Console
 */
class Color
{
    /**
     * @var array
     */
    public static $background_colors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47'
    ];

    /**
     * @var array
     */
    public static $foreground_colors = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' =>  '1;34',
        'green' =>  '0;32',
        'light_green' =>  '1;32',
        'cyan' =>  '0;36',
        'light_cyan' =>  '1;36',
        'red' =>  '0;31',
        'light_red' =>  '1;31',
        'purple' =>  '0;35',
        'light_purple' =>  '1;35',
        'brown' =>  '0;33',
        'yellow' =>  '1;33',
        'light_gray' =>  '0;37',
        'white' =>  '1;37'
    ];

    /**
     * Get a color string
     *
     * <code>
     * Light\Console\Color::getColoredString($string, "yellow", null);
     * </code>
     *
     * @param $string
     * @param null $foreground_color
     * @param null $background_color
     * @return string
     */
    public static function getColoredString($string, $foreground_color = null, $background_color = null)
    {
        $colored_string = "";

        if ($foreground_color && isset(self::$foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . self::$foreground_colors[$foreground_color] . "m";
        }

        if ($background_color && isset(self::$background_colors[$background_color])) {
            $colored_string .= "\033[" . self::$background_colors[$background_color] . "m";
        }

        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }
}