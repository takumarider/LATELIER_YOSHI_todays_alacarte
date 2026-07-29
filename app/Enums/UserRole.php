<?php

namespace App\Enums;

enum UserRole: int
{
    case GENERAL = 0;
    case ADMIN = 1;

    public function label(): string
    {
        return match ($this) {
            self::GENERAL => '一般ユーザー',
            self::ADMIN => '管理者',
        };
    }
}
