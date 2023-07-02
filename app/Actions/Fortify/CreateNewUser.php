<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:30'],
            'last_name' => ['required', 'string', 'max:30'],
            'phone' => ['required','numeric'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'country' => ['required', 'string', 'max:30'], 
            'city' => ['required', 'string', 'max:30'],
            'exp' => ['required', 'string', 'max:30'],
            'birth_date' => ['required', 'date'],
            'current_pos' => ['nullable', 'string'],
            'job_searching' => ['nullable', 'boolean'],
            'profile_photo_path' => ['nullable', 'string'],
            'user_image' => 'image|mimes:jpeg,png,jpg|max:2048',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();


        $imageName = time().'.'.$input['image']->extension();  
        $input['image']->move(public_path('users_images'), $imageName);

        return User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'country' => $input['country'],
            'city' => $input['city'],
            'exp' => $input['exp'],
            'current_pos' => $input['current_pos'],
            'profile_photo_path' => $imageName,
            'birth_date' => $input['birth_date'],
            'password' => Hash::make($input['password']),
        ]);
     }
}
