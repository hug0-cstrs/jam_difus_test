<?php

class PhpInputStreamMock {
    public static $content;
    private $position = 0;
    
    public function stream_open($path, $mode, $options, &$opened_path) {
        return true;
    }
    
    public function stream_read($count) {
        $ret = substr(self::$content, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }
    
    public function stream_eof() {
        return $this->position >= strlen(self::$content);
    }
    
    public function stream_stat() {
        return [];
    }
    
    public function stream_seek($offset, $whence) {
        $this->position = $offset;
        return true;
    }
} 