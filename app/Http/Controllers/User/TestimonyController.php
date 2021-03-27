<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Testimony;
use Intervention\Image\Facades\Image as ImageUpload;

class TestimonyController extends Controller
{
    private $testimony;

    public function __construct(Testimony $testimony)
    {
        $this->testimony = $testimony;
    }

    public function newForUserTestimony(Request $request)
    {

        $user = auth()->guard('client')->user();

        $user_id    = $user->id;
        $name       = $user->name;
        $testimony  = filter_var($request->testimony, FILTER_SANITIZE_STRING);
        $rate       = filter_var($request->rate, FILTER_VALIDATE_INT);

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $dataForm = [
            'name'      => $name,
            'testimony' => $testimony,
            'rate'      => $rate,
            'user_id'   => $user_id
        ];

        $testimony_id   = $this->testimony->insert($dataForm);
        $picture        = $this->upload($request->picture, $testimony_id);
        if (!$picture) {
            DB::rollBack();
            return redirect()->route('user.account')
                ->withErrors(['Não foi possível identificar a imagem enviada, tente novamente']);
        }
        $update         = $this->testimony->edit(['picture' => $picture], $testimony_id);

        if ($testimony_id && $update && $picture) {
            DB::commit();
            return redirect()->route('user.account')
                ->with('success', 'Depoimento enviado com sucesso!');
        }

        DB::rollBack();
        return redirect()->route('user.account')
            ->withErrors(['Não foi possível enviar o depoimento, tente novamente']);
    }

    public function upload($file, $id)
    {
        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem

        // Verifica extensões
        if($extension != "png" && $extension != "jpeg" && $extension != "jpg" && $extension != "gif") return false;

        $imageName  = "{$id}.{$extension}"; // Pega apenas o 15 primeiros e adiciona a extensão
        $uploadPath = "user/img/testimony/{$imageName}";
        $realPath   = $file->getRealPath();

        ImageUpload::make($realPath)->resize(70,70)->save($uploadPath);

        return $imageName;

    }
}
