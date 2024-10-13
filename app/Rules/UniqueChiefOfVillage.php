<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueChiefOfVillage implements ValidationRule
{
    // /**
    //  * Run the validation rule.
    //  *
    //  * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
    //  */
    // public function validate(string $attribute, mixed $value, Closure $fail): void
    // {
    //     //
    // }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $roleId = Role::where('name', 'cov')->first()->id;
        $existingChief = DB::table('model_has_roles')
            ->where('role_id', $roleId)
            ->whereExists(function ($query) use ($value) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereRaw('users.id = model_has_roles.model_id')
                    ->where('users.village_id', $value);
            })
            ->first();

        if ($existingChief) {
            $fail('There can only be one Chief of Village per village.');
        }
    }

    public function message()
    {
        return 'There can only be one Chief of Village per village.';
    }
}
