<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Pimpinan = 'pimpinan';
    case Sekretariat = 'sekretariat';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Pimpinan => 'Pimpinan',
            self::Sekretariat => 'Sekretariat',
        };
    }
}
