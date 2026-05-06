<?php

namespace App\Services\Rule;

use Illuminate\Contracts\Validation\Rule;

class NoUrlLinksRule implements Rule
{

    public function passes($attribute, $value)
    {
        // Implement the logic to check for URL patterns in the input content
        return !preg_match('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.\-]*(\?\S+)?)?)?)@', $value);
    }

    public function message()
    {
        // return ':attribute 不得包含URL链接.';
        return '内容不得包含链接.';
    }

    public static function check($value)
    {
        return !preg_match('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.\-]*(\?\S+)?)?)?)@', $value);
    }
}
