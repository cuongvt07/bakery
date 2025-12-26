<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUniqueCode
{
    /**
     * Boot the trait
     */
    protected static function bootHasUniqueCode()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getCodeColumn()})) {
                $model->{$model->getCodeColumn()} = $model->generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique code for the model
     */
    public function generateUniqueCode(): string
    {
        $prefix = $this->getCodePrefix();
        $length = $this->getCodeLength();
        $column = $this->getCodeColumn();
        
        do {
            $number = $this->getNextNumber();
            $code = $prefix . str_pad($number, $length, '0', STR_PAD_LEFT);
        } while (static::where($column, $code)->exists());
        
        return $code;
    }

    /**
     * Get the next sequential number
     */
    protected function getNextNumber(): int
    {
        $column = $this->getCodeColumn();
        $prefix = $this->getCodePrefix();
        $prefixLength = strlen($prefix);
        
        $latest = static::where($column, 'like', $prefix . '%')
            ->orderByRaw("CAST(SUBSTRING({$column}, " . ($prefixLength + 1) . ") AS UNSIGNED) DESC")
            ->first();
        
        if (!$latest) {
            return 1;
        }
        
        $latestNumber = (int) substr($latest->{$column}, $prefixLength);
        return $latestNumber + 1;
    }

    /**
     * Get the code prefix (override in model)
     * Default: First 2 letters of table name uppercase
     */
    public function getCodePrefix(): string
    {
        // Override this in your model
        return strtoupper(substr($this->getTable(), 0, 2));
    }

    /**
     * Get the code length (override in model)
     * Default: 4 digits
     */
    public function getCodeLength(): int
    {
        return 4; // Override in model if needed
    }

    /**
     * Get the code column name (override in model)
     */
    public function getCodeColumn(): string
    {
        // Default code column names
        return match(true) {
            property_exists($this, 'codeColumn') => $this->codeColumn,
            $this->getTable() === 'nguoi_dung' => 'ma_nhan_vien',
            $this->getTable() === 'nha_cung_cap' => 'ma_ncc',
            $this->getTable() === 'san_pham' => 'ma_san_pham',
            $this->getTable() === 'diem_ban' => 'ma_diem_ban',
            $this->getTable() === 'phong_ban' => 'ma_phong_ban',
            $this->getTable() === 'cong_thuc_san_xuat' => 'ma_cong_thuc',
            default => 'code',
        };
    }
}
