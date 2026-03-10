<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Department; // <-- Import Department model
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role; // <-- Import Role model
class UserForm extends Component
{
    public $user;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;
    public $department_id; // <-- Thay đổi thành department_id
    public $selectedRoles = []; // <-- Mảng để lưu các vai trò được chọn
    public $departments; // <-- Biến để lưu danh sách phòng ban
    public $allRoles; // <-- Biến để lưu tất cả các vai trò
    public function mount($userId = null)
    {
        $this->departments = Department::orderBy('name')->get();
        $this->allRoles = Role::orderBy('name')->get(); // <-- Lấy tất cả vai trò

        if ($userId) {
            $this->user = User::findOrFail($userId);
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->role = $this->user->role; // Giữ lại nếu bạn vẫn dùng cột 'role'
            $this->department_id = $this->user->department_id;
            $this->selectedRoles = $this->user->roles->pluck('name')->toArray(); // <-- Lấy các vai trò hiện có
        } else {
            $this->user = new User();
            $this->role = 'user'; // Giá trị mặc định cho cột 'role'
            $this->department_id = null;
            $this->selectedRoles = ['user']; // Mặc định gán vai trò 'user' khi tạo mới
        }
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user->id),
            ],
            'password' => [
                Rule::requiredIf(!$this->user->exists),
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => ['required', 'string', Rule::in(['admin', 'user'])], // Giữ lại nếu bạn vẫn dùng cột 'role'
            'department_id' => ['nullable', 'exists:departments,id'],
            'selectedRoles' => ['nullable', 'array'],
            'selectedRoles.*' => ['exists:roles,name'], // <-- Mỗi vai trò phải tồn tại
        ];
    }

    public function saveUser()
    {
        $this->validate();

        // Không cho phép người dùng tự gỡ vai trò admin của chính mình
        if ($this->user->id === Auth::id() && !in_array('admin', $this->selectedRoles) && $this->user->hasRole('admin')) {
            session()->flash('error', 'Bạn không thể tự gỡ vai trò admin của chính mình.');
            return;
        }

        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->role = $this->role; // Giữ lại nếu bạn vẫn dùng cột 'role'
        $this->user->department_id = $this->department_id;

        if ($this->password) {
            $this->user->password = Hash::make($this->password);
        }

        $this->user->save();

        // Đồng bộ vai trò cho người dùng
        $this->user->syncRoles($this->selectedRoles);

        $this->password = '';
        $this->password_confirmation = '';

        session()->flash('success', 'Người dùng đã được ' . ($this->user->wasRecentlyCreated ? 'tạo' : 'cập nhật') . ' thành công!');

        return redirect()->route('users.index');
    }

    public function render()
    {
        $title = $this->user->exists ? 'Chỉnh sửa Người dùng' : 'Tạo Người dùng Mới';
        return view('livewire.admin.user-form', compact('title'));
    }
}
