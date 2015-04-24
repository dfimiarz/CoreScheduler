<?php

/*
 * The MIT License
 *
 * Copyright 2015 Daniel Fimiarz <dfimiarz@ccny.cuny.edu>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace ccny\scidiv\cores\components;

class ColorSelector {

    private $bg_colors = array(
        "#336600", "#009933", "#00CC00", "#00CC66", "#00CC99", "#009999", "#006699", "#1F3D5C"
    );
    private $text_colors = array(
        "#1d1d1d", "#1d1d1d", "#1d1d1d", "#1d1d1d", "#1d1d1d", "#1d1d1d", "#1d1d1d", "#1d1d1d"
    );
    private $bg_light_colors = array(
        "#ADC299", "#99D6AD", "#99EB99", "#99EBC2", "#99EBD6", "#99D6D6", "#99C2D6", "#A5B1BE"
    );
    private $text_light_colors = array(
        "#777777", "#777777", "#777777", "#777777", "#777777", "#777777", "#777777", "#777777"
    );
    // Hold an instance of the class
    private static $instance;

    //A private constructor prevents objects creatation. Singleton
    private function __construct() {
        
    }

    public static function getColorSelectorObject() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    // Prevent users to clone the instance
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function getFutureColor($number) {
        if (!is_int($number))
            $number = 1;

        $colors = new \StdClass();

        $index = $number % count($this->bg_colors);

        $colors->bg = $this->bg_colors[$index];
        $colors->txt = $this->text_colors[$index];

        return $colors;
    }

    public function getPastColor($number) {
        if (!is_int($number)) {
            $number = 1;
        }

        $colors = new \stdClass();

        $index = $number % count($this->bg_colors);

        $colors->bg = $this->bg_light_colors[$index];
        $colors->txt = $this->text_light_colors[$index];

        return $colors;
    }

}
