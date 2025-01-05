<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait TwoFactorAuthenticatable
{
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }
        
        $this->two_factor_recovery_codes = encrypt(json_encode($codes));
        $this->save();
        
        return $codes;
    }

    public function validateRecoveryCode(string $code): bool
    {
        $codes = json_decode(decrypt($this->two_factor_recovery_codes), true);
        
        $position = array_search($code, $codes);
        
        if ($position !== false) {
            unset($codes[$position]);
            $this->two_factor_recovery_codes = encrypt(json_encode(array_values($codes)));
            $this->save();
            return true;
        }
        
        return false;
    }

    public function addTrustedDevice(string $deviceId): void
    {
        $devices = $this->two_factor_trusted_devices 
            ? json_decode(decrypt($this->two_factor_trusted_devices), true) 
            : [];
            
        $devices[] = [
            'id' => $deviceId,
            'added_at' => now()->timestamp,
            'valid_until' => now()->addDays(30)->timestamp
        ];
        
        $this->two_factor_trusted_devices = encrypt(json_encode($devices));
        $this->save();
    }

    public function isTrustedDevice(string $deviceId): bool
    {
        if (!$this->two_factor_trusted_devices) {
            return false;
        }

        $devices = json_decode(decrypt($this->two_factor_trusted_devices), true);
        
        foreach ($devices as $key => $device) {
            if ($device['id'] === $deviceId) {
                if (now()->timestamp > $device['valid_until']) {
                    unset($devices[$key]);
                    $this->two_factor_trusted_devices = encrypt(json_encode(array_values($devices)));
                    $this->save();
                    return false;
                }
                return true;
            }
        }
        
        return false;
    }
} 