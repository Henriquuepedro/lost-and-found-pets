<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\User;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\OrderStatus;
use App\Models\Image;
use function GuzzleHttp\Psr7\str;

class AccountController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        return view('user.account.index');
    }

    public function orders()
    {
        return view('user.animal.orders');
    }

    public function edit()
    {
        $dataClient = auth()->guard('client')->user();
        $arrDataClient = array(
            'name' => $dataClient->name,
            'email' => $dataClient->email
        );

        return view('user.account.edit', compact('arrDataClient'));
    }

    public function editPost(Request $request)
    {
        $userId   = auth()->guard('client')->user()->id;

        $validator = validator(
            $request->all(),
            [
                'name'      => 'required|min:3',
                'email'     => ['required', Rule::unique('users')->ignore($userId)],
                'password'  => 'nullable|confirmed|min:6'
            ],
            [
                'name.required'     => 'O nome é um campo obrigatório!',
                'name.min'          => 'O nome precisa de no mínimo 3 caracteres!',
                'email.required'    => 'O email é um campo obrigatório!',
                'email.unique'      => 'O email já está em uso!',
                'password.confirmed'=> 'As senhas não correspondem!',
                'password.min'      => 'A senha precisa de no mínimo 6 caracteres!'
            ]
        );

        if($validator->fails())
            return redirect()->route('user.account.edit')->withErrors($validator)->withInput();


        // verifica senha atual
        if($request->password) {
            if(!Hash::check($request->password_current, auth()->guard('client')->user()->password)) {
                return redirect()
                    ->route('user.account.edit')
                    ->withErrors(['Senha informada não corresponde com a senha atual!'])
                    ->withInput();
            }
        }

        $dataUserUpdate = [
            'name'  => $request->name,
            'email' => $request->email
        ];

        if($request->password)
            $dataUserUpdate['password'] = Hash::make($request->password);

        $update = $this ->user
                        ->where('id', $userId)
                        ->update($dataUserUpdate);

        if($update)
            return redirect()->route('user.account.edit')
                ->with('success', 'Cadastro alterado com sucesso!');


        return redirect()->route('user.account.edit')
            ->withErrors(['Não foi possível alterar o usuário']);
    }
}
