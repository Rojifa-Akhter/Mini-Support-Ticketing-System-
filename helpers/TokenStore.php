<?php
// helpers/TokenStore.php

class TokenStore {
    private $file = __DIR__ . '/tokens.json';
    private $tokens = [];

    public function __construct() {
        if (file_exists($this->file)) {
            $content = file_get_contents($this->file);
            $this->tokens = json_decode($content, true) ?: [];
        }
    }

    public function save() {
        file_put_contents($this->file, json_encode($this->tokens));
    }

    public function addToken($token, $userId) {
        $this->tokens[$token] = $userId;
        $this->save();
    }

    public function removeToken($token) {
        unset($this->tokens[$token]);
        $this->save();
    }

    public function getUserId($token) {
        return $this->tokens[$token] ?? null;
    }
}
