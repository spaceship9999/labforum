<?php

namespace App\Traits;

trait ResponseTraits {
    public function noAccessRightResponse() {
        return response(array(
            'error' => 'no_access_rights',
        ), 401);
    }

    public function pageNotFound() {
        return response(array(
            'error' => 'page_not_found',
        ), 404);
    }
}
