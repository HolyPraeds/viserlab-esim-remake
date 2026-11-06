<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BlockedEmailDomain implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (self::blocks((string) $value)) {
            $fail('This email address is not allowed.');
        }
    }

    public static function blocks(string $email): bool
    {
        $email = strtolower(trim($email));
        $at = strrpos($email, '@');
        if ($at === false) {
            return false;
        }

        $host = substr($email, $at + 1);

        return $host === 'example.com' || str_ends_with($host, '.example.com');
    }
}
