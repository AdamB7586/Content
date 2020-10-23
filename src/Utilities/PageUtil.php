<?php

namespace Content\Utilities;

class PageUtil
{
    public static function cleanURL($url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}
