<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasUlid
{
    public static function bootHasUlid()
    {
        static::creating(function (Model $model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = self::generateUlid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    protected static function generateUlid(): string
    {
        $time = str_pad(base_convert((int)(microtime(true) * 1000), 10, 32), 10, '0', STR_PAD_LEFT);
        $random = '';
        $chars = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
        for ($i = 0; $i < 16; $i++) {
            $random .= $chars[random_int(0, 31)];
        }
        return strtoupper($time . $random);
    }
}
