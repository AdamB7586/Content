<?php

namespace Content\Utilities;

use Sunra\PhpSimple\HtmlDomParser;

class CleanContent {
    
    protected $parser;
    
    protected $markup;
    protected $html;
    protected $css;
    
    public function __construct(){
        $this->parser = new HtmlDomParser();
    }
    
    public function cleanCode($markup){
        $this->html = $this->parser->str_get_html(trim(preg_replace("/\r|\n|\t/", '', $markup)));
        $this->validateImages();
        return $this->html;
    }
    
    private function validateImages(){
        foreach($this->html->find('img') as $images){
            $this->CSSParse($images->style);
            if(!$images->width && $this->css['width']){$images->width = str_replace('px', '', $this->css['width']);}
            if(!$images->height && $this->css['height']){$images->height = str_replace('px', '', $this->css['height']);}
            if($this->css['float']){if($images->class){$images->class.= ' '.$this->css['float'];}else{$images->class = $this->css['float'];}}
            $images->style = null;
        }
    }
    
    private function CSSParse($css){
        preg_match_all('~([a-z-]+)\s*:\s*([^;$]+)~si', $css, $matches, PREG_SET_ORDER);
        foreach($matches as $match){
            $this->css[$match[1]] = isset($match[2]) ? $match[2] : null;
        }
    }
    
}
