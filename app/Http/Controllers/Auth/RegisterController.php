<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\IdentityAccess\Application\UseCase\RegisterApplicant;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;

/**
 * Registro público: crea siempre un Aspirante (FUNCTIONAL_SPECIFICATIONS.md §5).
 */
class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(private readonly RegisterApplicant $registerApplicant)
    {
        $this->middleware('guest');
    }

    protected function validator(array $data): ValidatorContract
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'identification_card' => ['required', 'integer', 'min:1'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data): User
    {
        $account = $this->registerApplicant->execute(
            trim($data['name']),
            trim($data['last_name']),
            (int) $data['identification_card'],
            $data['email'],
            $data['password'],
        );

        return User::findOrFail($account->id());
    }
}
