<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Thêm dòng này nếu bạn sử dụng Sanctum
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'department_id', // <-- Thay thế 'department' bằng 'department_id'
        'is_admin',
        'force_password_change',
        'password_changed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'force_password_change' => 'boolean',
            'password_changed_at' => 'datetime',
        ];
    }

    public function isAdmin()
    {
        return $this->is_admin || $this->hasRole('admin'); // Kiểm tra boolean cứng HOẶC vai trò 'admin' bằng Spatie
    }

    public function canViewAllDepartments()
    {
        return $this->isAdmin() || $this->can('view_all_departments');
    }

    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'machine_user');
    }

    // Định nghĩa mối quan hệ nhiều-một với Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function printStations()
    {
        return $this->belongsToMany(PrintStation::class);
    }

    public function scaleStations()
    {
        return $this->belongsToMany(ScaleStation::class);
    }
}
