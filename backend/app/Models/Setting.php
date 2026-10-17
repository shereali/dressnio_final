<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    /** Keys that hold credentials. They are stored encrypted and never sent back in full. */
    public const SECRET_KEYS = ['sms_api_key', 'sslcommerz_store_password', 'steadfast_api_key', 'steadfast_secret_key'];

    public static function isSecret(string $key): bool
    {
        return in_array($key, self::SECRET_KEYS, true);
    }

    /** Reads one setting, decrypting credentials. */
    public static function read(string $key, mixed $default = null): mixed
    {
        $value = static::where('key', $key)->value('value');

        if ($value === null || $value === '') {
            return $default;
        }

        if (static::isSecret($key)) {
            try {
                return Crypt::decryptString($value);
            } catch (DecryptException $e) {
                // A value saved before encryption was introduced; it is re-encrypted on the next save
                return $value;
            }
        }

        return $value;
    }

    /** Writes one setting, encrypting credentials. */
    public static function write(string $key, mixed $value): void
    {
        $value = is_array($value) ? json_encode($value) : (string) $value;

        if (static::isSecret($key) && $value !== '') {
            $value = Crypt::encryptString($value);
        }

        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
