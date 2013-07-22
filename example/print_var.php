<?php

/**
 * Print variable function
 * @param mixed $var
 */
function print_var($var){
    $traces = debug_backtrace();
    $title = '';

    foreach($traces as $trace){
        if($trace['function'] == 'print_var'){
            $title = ($trace['file'] ? $trace['file'] : '');
            $title .= ($trace['file'] && $trace['line'] ? ': ' : '');
            $title .= ($trace['line'] ? $trace['line'] : '');
            break;
        }
    }

    PrintVarService::Init()->PrintVar($var, $title);
}

/**
 * Service print_var
 * Class PrintVarService
 */
class PrintVarService{
    private static $service = null;

    public static function Init(){
        if(!self::$service) self::$service = new PrintVarService();
        return self::$service;
    }

    private $arMethodsExcept = array(
        '__construct',
        '__get',
        '__set',
    );

    private function __construct(){
        $this->PrintStyle();
        $this->PrintScript();
    }

    private function GetStyle(){
        return '
            /* Style reset */
            #print-var-modal * {
                font: normal normal normal 16px Courier;
                font-stretch: normal;
                background: transparent none repeat scroll 0% 0%;
                background-image: none;
                background-size: auto;
                color: black;
                float: none;
                overflow: visible;
                display: inline;
                clip: auto;
                clear: none;
                visibility: visible;
                vertical-align: baseline;
                word-break: normal;
                tab-size: 8;
                text-align-last: start;
                text-transform: none;
                text-shadow: none;
                text-indent: 0;
                text-decoration: none;
                text-align: left;
                text-overflow: clip;
                word-wrap: normal;
                writing-mode: lr-tb;
                direction: ltr;
                letter-spacing: normal;
                white-space: normal;
                unicode-bidi: normal;
                list-style: none outside none;
                border-radius: 0;
                height: auto;
                min-width: 0;
                min-height: 0;
                max-width: none;
                max-height: none;
                width: auto;
                box-sizing: content-box;
                padding: 0;
                bottom: auto;
                left: auto;
                right: auto;
                position: static;
                z-index: auto;
                top: auto;
                margin: 0;
                counter-reset: none;
                counter-increment: none;
                content: none;
                quotes: " ";
                cursor: auto;
                border: medium none transparent;
                outline: invert none medium;
            }

            /* Styles */
            #print-var-modal {
                border: 1px solid rgb(221, 221, 221);
                border-radius: 10px;
                overflow-x: visible;
                position: fixed;
                z-index: 999999999;
                box-shadow: 0 0 7px rgb(230, 230, 230);
                padding-bottom: 10px;
                max-width: 900px;
                background-color: white;
            }

            #print-var-modal div,
            #print-var-modal ul,
            #print-var-modal p {
                display: block;
            }

            #print-var-modal .head {
                border-radius: 10px 10px 0 0;
                background-color: rgb(240, 240, 240);
                padding: 10px;
                font-family: Arial;
                position: relative;
                cursor: default;
                -moz-user-select: none;
                -khtml-user-select: none;
                -webkit-user-select: none;
                -o-user-select: none;
                user-select: none;
            }

            #print-var-modal .head span{
                font-size: 1.1em;
                font-weight: bolder;
                color: #2d2d2d;
                text-shadow: 1px 1px 2px rgb(180, 180, 180);
            }

            #print-var-modal .head p{
                font-size: 0.9em;
                margin: 5px 0 3px;
            }

            #print-var-modal .button {
                text-align: center;
                font-family: Arial;
                font-size: 1em;
                line-height: 0.9em;
                float: right;
                width: 0.9em;
                border-radius: 10px;
                margin-left: 5px;
                cursor: pointer;
            }

            #print-var-modal .button:hover{
                background-color: rgb(160, 160, 160);
            }

            #print-var-modal .modal-close, #print-var-modal .min, #print-var-modal .max{
                font-size: 1.1em;
                width: 1.1em;
                line-height: 1.1em;
                position: absolute;
                top: 8px;
                right: 8px;
                background-color: rgb(170, 170, 170);
                color: #fff;
            }

            #print-var-modal .min, #print-var-modal .max{
                right: 35px;
            }

            #print-var-modal .body {
                overflow: visible;
                overflow-y: auto;
                padding: 10px 10px 0 10px;
                max-height: 400px;
                height: auto;
            }

            #print_var_container {
                font-family: Courier;
                overflow: auto;
                background-color: white;
                padding-left: 25px;
                width: auto;
                min-height: 0px;
                max-height: none;
                height: auto;
            }

            #print_var_container ul {
                overflow: visible;
                list-style-type: none;
                margin: 0 0 8px  -13px;
                padding: 0 0 0 45px;
                border-left: rgb(226, 226, 226) 1px dotted;
            }

            #print-var-modal li {
                display: list-item;
            }

            #print_var_container .button {
                background-color: rgb(200, 200, 200);
                color: #fff;
                float: left;
                margin: 2px 0 0 -20px;
                font-family: Times Mew Roman, serif;
                text-align: center;
            }

            #print_var_container .button:hover {
                background-color: rgb(190,190, 190);
            }

            #print_var_container i {
                display: block;
                margin: 4px;
                width: 0px;
                height: 0px;
                border-style: solid;
            }

            #print_var_container .name {
                color: #66170D;
                padding-right: 5px;
            }

            #print_var_container .separator {
                color: #000000;
                padding-right: 5px;
            }

            #print_var_container .count {
                color: #000000;
            }

            #print_var_container .null {
                color: #0A1F80;
            }

            #print_var_container .bool {
                color: #0A1F80;
            }

            #print_var_container .int {
                color: #1F45F7;
            }

            #print_var_container .float {
                color: #1F45F7;
            }

            #print_var_container .string {
                color: #3C811B;
            }

            #print_var_container .array {
                color: #0A1F80;
            }

            #print_var_container .object {
                color: #000000;
            }

            #print_var_container .method {
                color: #000000;
            }
        ';
    }

    private function GetScript(){
        return '
            (function(undefined){
                var jQuerySource = "http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js";

                var jQueryCode = function($){
                    $(function(){
                        var container = $(".print-var-modal");

                        var position = function(container){
                            container.each(function(){
                                var c = $(this);
                                var cw = c.width();
                                var ch = c.height();

                                var w = $(window);
                                var ww = w.width();
                                var wh = w.height();

                                c.css({
                                   top: ((wh - ch)/2) + "px",
                                   left: ((ww - cw)/2) + "px"
                                });
                            });
                        };
                        var padding = function(container){
                            container.find(".print_var_container").each(function(){
                                var body = $(this);
                                if(!body.find(".button").length){
                                    body.css("padding-left", 0);
                                }
                            });
                        };

                        container.appendTo("body").fadeIn();
                        position(container);
                        padding(container);

                        var head =  container.find(".head");
                        var current = null;
                        var delta = null;

                        head.mousedown(function(e){
                            var _this = $(this);
                            _this.css("cursor", "move");
                            current = _this.closest(".print-var-modal");
                            delta = current.offset();
                            delta.top -= e.pageY;
                            delta.left -= e.pageX;
                        });
                        head.bind("mouseup mouseleave", function(){
                            var _this = $(this);
                            _this.css("cursor", "default");
                            current = null;
                        });
                        $("body").mousemove(function(e){
                            if(current){
                                current.css({
                                    top:  (e.pageY + delta.top)+"px",
                                    left: (e.pageX + delta.left)+"px",
                                });

                                current.find(".head").css("cursor", "move");
                            }
                        });

                        container.find(".button").click(function(e){
                            e.preventDefault();
                            var _this = $(this);

                            if(_this.hasClass("close")){
                                _this.text("+")
                                     .removeClass("close")
                                     .addClass("open")
                                     .parent()
                                     .find("ul")
                                     .slideUp(200)
                                     .find(".button")
                                     .text("+")
                                     .removeClass("close")
                                     .addClass("open");
                                return false;
                            }

                            if(_this.hasClass("open")){
                                _this.text("-")
                                     .removeClass("open")
                                     .addClass("close")
                                     .parent()
                                     .find("> span > ul")
                                     .slideDown(200);
                                return false;
                            }

                            if(_this.hasClass("min")){
                                _this.text("+")
                                     .removeClass("min")
                                     .addClass("max");

                                container.find(".head")
                                         .css("border-radius", "10px");

                                container.css("padding-bottom", "0")
                                         .find(".body")
                                         .slideUp();
                                return false;
                            }

                            if(_this.hasClass("max")){
                                _this.text("-")
                                     .removeClass("max")
                                     .addClass("min");

                                container.find(".head")
                                         .css("border-radius", "10px 10px 0 0");

                                container.css("padding-bottom", "10px")
                                         .find(".body")
                                         .slideDown();
                                return false;
                            }

                            if(_this.hasClass("modal-close")){
                                container.fadeOut();
                                return false;
                            }
                        });
                    });
                };

                if(window.jQuery == undefined){
                    var script = document.createElement("script");
                    script.src = jQuerySource;
                    script.type = "text/javascript";

                    var head = document.getElementsByTagName("head")[0];
                    head.appendChild(script);

                    var timer = setInterval(function(){
                        if(window.jQuery != undefined){
                            clearInterval(timer);
                            jQueryCode(window.jQuery);
                        }
                    }, 100);
                } else {
                    jQueryCode(window.jQuery);
                }
            })();
        ';
    }

    private function PrintStyle(){
        $style = $this->GetStyle();
        $style = preg_replace('/[\s]{2,}/', ' ', $style);
        $style = preg_replace('/\/\*[^\*\/]*\*\//', '', $style);
        $style = trim($style);
        print '<style type="text/css">' . $style . '</style>';
    }

    private function PrintScript(){
        $script = $this->GetScript();
        $script = preg_replace('/[\s]{2,}/', ' ', $script);
        $script = preg_replace('/\/\*[^\*\/]*\*\//', '', $script);
        $script = trim($script);
        print '<script type="text/javascript">' . $script . '</script>';
    }

    private function PrintButton(){
        print '<div class="button close">-</div>';
    }

    private function PrintType($type){
        print '<span class="type ' . $type . '">(';
        print $type;
        print ')</span>';
    }

    private function PrintName($name, $prefix=null){
        if(is_null($name)) return;

        print '<span class="name">';

        if($prefix) print $prefix;
        if(!$prefix && is_string($name)) print "'";

        if($name === 0) print '0';
        else if($name === false) print 'false';
        else if($name === '') print "''";
        else print $name;

        if(!$prefix && is_string($name)) print "'";

        print '</span>';
    }

    private function PrintSeparator($separator='='){
        if(empty($separator)) return;

        print '<span class="separator">';
        print $separator;
        print '</span>';
    }

    private function PrintValue($var, $name=null, $separator='=', $namePrefix=null){
        if(is_null($var)){
            $this->PrintName($name, $namePrefix);
            $this->PrintSeparator($separator);
            $this->PrintNull($var);
            return;
        }

        if(is_bool($var)){
            $this->PrintName($name, $namePrefix);
            $this->PrintSeparator($separator);
            $this->PrintType('bool');
            $this->PrintBool($var);
            return;
        }

        if(is_integer($var)){
            $this->PrintName($name, $namePrefix);
            $this->PrintSeparator($separator);
            $this->PrintType('int');
            $this->PrintInteger($var);
            return;
        }

        if(is_float($var)){
            $this->PrintName($name, $namePrefix);
            $this->PrintSeparator($separator);
            $this->PrintType('float');
            $this->PrintFloat($var);
            return;
        }

        if(is_string($var)){
            $this->PrintName($name, $namePrefix);
            $this->PrintSeparator($separator);
            $this->PrintType('string');
            $this->PrintString($var);
            return;
        }

        if(is_array($var)){
            $this->PrintButton();
            $this->PrintName($name, $namePrefix);
            $this->PrintSeparator($separator);
            $this->PrintType('array');
            $this->PrintArray($var);
            return;
        }

        if(is_object($var)){
            $this->PrintButton();
            $this->PrintObject($var, $name, $separator, $namePrefix);
            return;
        }
    }

    private function PrintNull($var){

        print '<span class="value null">null</span>';
    }

    private function PrintBool($var){
        print '<span class="value bool">';
        print $var ? 'true' : 'false';
        print '</span>';
    }

    private function PrintInteger($var){
        print '<span class="value int">';
        print $var ? $var : 0;
        print '</span>';
    }

    private function PrintFloat($var){
        print '<span class="value float">';
        print $var ? $var : 0;
        print '</span>';
    }

    private function PrintString($var){
        print '<span class="value string">\'';
        print $var ? $var : '';
        print '\'</span>';
    }

    private function PrintArray($var){
        print '<span class="count array">[' . count($var) . ']</span>';
        print '<span class="value">';
        print '<ul>';

        foreach($var as $key=>$value){
            print '<li>';
            $this->PrintValue($value, $key, '=>');
            print '</li>';
        }

        print '</ul></span>';
    }

    private function PrintObject($var, $name=null, $separator='=', $namePrefix=null){
        $this->PrintName($name, $namePrefix);
        $this->PrintSeparator($separator);

        $className = get_class($var);

        $reflect = new ReflectionClass($var);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        $methods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);

        print '<span class="type object">' . $className . '</span>';

        $countProps = 0;
        foreach($props as $prop){
            if($prop->isStatic()) continue;
            $countProps++;
        }

        print '<span class="count object">{' . $countProps . '}</span>';
        print '<span class="value">';
        print '<ul>';

        foreach($props as $prop){
            if($prop->isStatic()) continue;

            print '<li>';
            $this->PrintValue($prop->getValue($var), $prop->getName(), '=');
            print '</li>';
        }

        foreach($methods as $method){
            if($method->isStatic()) continue;

            $name = $method->getName();

            if(in_array($name, $this->arMethodsExcept)) continue;

            print '<li>';
            $this->PrintButton();
            print '<span class="name">' . $name . '</span>';

            $params = $method->getParameters();

            print '<span class="count method">(' . count($params) . ')</span>';
            print '<span class="value">';
            print '<ul>';

            foreach($params as $param){
                $name = $param->getName();

                print '<li>';
                if($param->isDefaultValueAvailable())
                {
                    $default = $param->getDefaultValue();
                    $this->PrintValue($default, $name, '=', '$');
                } else {
                    $this->PrintName($name, '$');
                }
                print '</li>';
            }

            print '</ul></span>';
        }

        print '</ul></span>';
    }

    public function PrintVar($var, $title=null){
        if(defined('DISABLE_PRINT_VAR')) return;

        print '<div id="print-var-modal" class="print-var-modal" style="display: none;">';
            print '<div class="head">';
                print '<span>PrintVar</span>';
                print '<p>'.$title.'</p>';
                print '<a class="button modal-close">x</a>';
                print '<a class="button min">-</a>';
            print '</div>';
            print '<div class="body">';
                print '<div id="print_var_container" class="print_var_container">';
                $this->PrintValue($var, null, null, null);
                print '</div>';
            print '</div>';
        print '</div>';
    }
}