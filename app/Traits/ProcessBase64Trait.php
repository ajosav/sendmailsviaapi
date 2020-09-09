<?php

namespace App\Traits;



Trait ProcessBase64Trait {
    
    public function extractFile($source) {
        $result = '';
        
        if(strpos($source, ',')) {
            @list($removed, $file) = explode(',', $source);
        } else {
            return base64_decode($source);
        }

        $decoded_file = base64_decode(($file));

        return $decoded_file;

    }
}