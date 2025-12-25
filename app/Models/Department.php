<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUniqueCode;

class Department extends Model
{
    use HasUniqueCode;
    
    protected $table = 'phong_ban';
    
    protected $fillable = [
        'ma_phong_ban',
        'ten_phong_ban',
        'ma_mau',
        'trang_thai',
    ];
    
    /**
     * Code generation config
     */
    public function getCodePrefix(): string
    {
        return 'PB'; // Phòng ban
    }
    
    public function getCodeLength(): int
    {
        return 4; // PB0001, PB0002, ...
    }
    
    /**
     * Relationships
     */
    public function users()
    {
        return $this->hasMany(User::class, 'phong_ban_id');
    }
    
    /**
     * Get active employees count
     */
    public function getActiveEmployeesCountAttribute()
    {
        return $this->users()->where('trang_thai', 'hoat_dong')->count();
    }
    
    /**
     * Scope: Only active departments
     */
    public function scopeActive($query)
    {
        return $query->where('trang_thai', 'hoat_dong');
    }
}
