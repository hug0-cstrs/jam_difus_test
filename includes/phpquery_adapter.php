<?php
require_once __DIR__ . '/../vendor/autoload.php';

class PhpQueryAdapter {
    private static $instance = null;
    private $document;

    private function __construct() {
        $this->document = phpQuery::newDocument('');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function wrapContent($content) {
        return phpQuery::newDocument($content);
    }

    public function find($selector) {
        return $this->document->find($selector);
    }

    public function html($content = null) {
        if ($content === null) {
            return $this->document->html();
        }
        $this->document->html($content);
        return $this;
    }

    public function append($content) {
        $this->document->append($content);
        return $this;
    }

    public function attr($name, $value = null) {
        if ($value === null) {
            return $this->document->attr($name);
        }
        $this->document->attr($name, $value);
        return $this;
    }

    public function addClass($class) {
        $this->document->addClass($class);
        return $this;
    }

    public function removeClass($class) {
        $this->document->removeClass($class);
        return $this;
    }

    public function toggleClass($class) {
        $this->document->toggleClass($class);
        return $this;
    }

    public function val($value = null) {
        if ($value === null) {
            return $this->document->val();
        }
        $this->document->val($value);
        return $this;
    }

    public function text($content = null) {
        if ($content === null) {
            return $this->document->text();
        }
        $this->document->text($content);
        return $this;
    }

    public function data($key, $value = null) {
        if ($value === null) {
            return $this->document->data($key);
        }
        $this->document->data($key, $value);
        return $this;
    }
} 