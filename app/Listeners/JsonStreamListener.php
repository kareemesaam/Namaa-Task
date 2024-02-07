<?php

namespace App\Listeners;

use JsonStreamingParser\Listener\IdleListener;

class JsonStreamListener extends IdleListener {
    protected $objectCallback;
    protected $currentObject;

    public function __construct(callable $objectCallback) {
        $this->objectCallback = $objectCallback;
    }

    public function startObject() : void {
        $this->currentObject = [];
    }

    public function endObject() : void {
        call_user_func($this->objectCallback, $this->currentObject);
    }

    public function key(string $key) : void {
        $this->currentKey = $key;
    }

    public function value($value) : void {
        $this->currentObject[$this->currentKey] = $value;
    }
}
