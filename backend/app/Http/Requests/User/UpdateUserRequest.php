<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        $userId = $user instanceof \App\Models\User ? $user->id : $user;
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 
                Rule::unique('users', 'email')->ignore($userId) // Menggunakan class Rule agar lebih clean
            ],
            'password' => ['nullable', 'string', 
                Password::min(8)->letters()->numbers()
            ],
            'role' => ['required', 
                Rule::in(['admin', 'petugas', 'peminjam']) //input hanya boleh dari opsi ini
            ],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}