<?php

if (! function_exists('esc')) {
    function esc($value) {
        return e((string) $value);
    }
}

if (! function_exists('lines')) {
    function lines($value) {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $value))));
    }
}
