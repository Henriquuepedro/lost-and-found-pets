<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimony;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image as ImageUpload;

class TestimonyController extends Controller
{

    private $testimony;

    public function __construct(Testimony $testimony)
    {
        $this->testimony = $testimony;
    }

    public function list()
    {
        $testimonies = $this->testimony->getTestimony();
        $arrTestimonies = array();

        foreach ($testimonies as $testimony) {
            $status = "";

            if($testimony["approved"] == 0 && strtotime($testimony['updated_at']) == strtotime($testimony['created_at']) && $testimony['user_id'] != 0)
                $status = '<span class="badge badge-warning col-md-12 text-white" style="padding: 5px 0px;">Pendente</span>';
            elseif($testimony["approved"] == 0)
                $status = '<span class="badge badge-danger col-md-12 text-white" style="padding: 5px 0px;">Inativo</span>';
            elseif($testimony["approved"] == 1)
                $status = '<span class="badge badge-success col-md-12" style="padding: 5px 0px;">Ativo</span>';


            array_push($arrTestimonies, [
                "id"            => $testimony["id"],
                "user_id"       => $testimony["user_id"],
                "name"          => $testimony["name"],
                'picture'       => $testimony["picture"],
                'rate'          => $testimony["rate"],
                'status'        => $status,
                'status_order'  => $testimony["approved"],
                'primary'       => $testimony["primary"] == 1 ? '<span class="badge badge-success col-md-12" style="padding: 5px 0px;">Sim</span>' : '<span class="badge badge-danger col-md-12 text-white" style="padding: 5px 0px;">Não</span>',
                'primary_order' => $testimony["primary"],
                'created_at'    => $testimony["created_at"] ? date('d/m/Y H:i', strtotime($testimony["created_at"])) : 'Não Informado',
                'datetime_order'=> $testimony["created_at"] ? strtotime($testimony["created_at"]) : 0,
            ]);
        }

        return view('admin.testimony.index', compact('arrTestimonies'));
    }

    public function new()
    {
        return view('admin.testimony.new');
    }

    public function edit(int $id)
    {
        $testimony = $this->testimony->getTestimony($id);
        if (!$testimony) return redirect()->route('admin.testimonies');

        $dataTestimony = array(
            'id'        => $id,
            'name'      => $testimony['name'],
            'testimony' => $testimony['testimony'],
            'picture'   => $testimony['picture'],
            'rate'      => $testimony['rate'],
            'approved'  => $testimony['approved'],
            'primary'   => $testimony['primary'],
            'user_id'   => $testimony['user_id']
        );

        return view('admin.testimony.edit', compact('dataTestimony'));

    }

    public function update(Request $request)
    {
        $testimony_id   = filter_var($request->testimony_id, FILTER_VALIDATE_INT);
        $name           = filter_var($request->name, FILTER_SANITIZE_STRING);
        $approved       = isset($request->approved) ? 1 : 0;
        $primary        = isset($request->primary) ? 1 : 0;
        $rate           = filter_var($request->rate, FILTER_VALIDATE_INT);
        $testimony_text = filter_var($request->testimony, FILTER_SANITIZE_STRING);

        $testimony = $this->testimony->getTestimony($testimony_id);
        if(!$testimony)
            return redirect()->route('admin.testimonies')
                    ->with('warning', 'Não foi possível encontrar o depoimento!');


        $dataForm = [
            'name'      => $name,
            'testimony' => $testimony_text,
            'rate'      => $rate,
            'approved'  => $approved,
            'primary'   => $primary
        ];
        if($testimony->user_id != 0)
            $dataForm = [
                'approved'  => $approved,
                'primary'   => $primary
            ];

        if($request->picture){
            $picture = $this->upload($request->picture, $testimony_id);
            $dataForm['picture'] = $picture;
        }

        $update = $this->testimony->edit($dataForm, $testimony_id);

        if($update)
            return redirect()->route('admin.testimonies')
                ->with('success', 'Depoimento alterado com sucesso!');

        return redirect()->route('admin.testimonies.edit', ['id' => $testimony_id])
            ->withErrors(['Não foi possível alterar o depoimento, tente novamente'])->withInput();
    }

    public function insert(Request $request)
    {
        $name           = filter_var($request->name, FILTER_SANITIZE_STRING);
        $testimony      = filter_var($request->testimony, FILTER_SANITIZE_STRING);
        $rate           = filter_var($request->rate, FILTER_VALIDATE_INT);

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $dataForm = [
            'name'      => $name,
            'testimony' => $testimony,
            'approved'  => 1,
            'rate'      => $rate
        ];

        $testimony_id   = $this->testimony->insert($dataForm);
        $picture        = $this->upload($request->picture, $testimony_id);
        $update         = $this->testimony->edit(['picture' => $picture], $testimony_id);

        if ($testimony_id && $update) {
            DB::commit();
            return redirect()->route('admin.testimonies')
                ->with('success', 'Depoimento cadastrado com sucesso!');
        }

        DB::rollBack();
        return redirect()->route('admin.testimonies.new')
            ->withErrors(['Não foi possível cadastrar o depoimento, tente novamente']);
    }

    public function remove(Request $request)
    {
        $testimony_id   = $request->testimony_id;
        $testimony      = $this->testimony->getTestimony($testimony_id);

        if(!$testimony) return redirect()->route('admin.testimonies')
            ->with('warning', 'Depoimento não encontrado!');

        $delete = $this->testimony->remove(($testimony_id));

        if($delete)
            return redirect()->route('admin.testimonies')
                ->with('success', 'Depoimento excluído com sucesso!');

        return redirect()->route('admin.testimonies')
            ->with('warning', 'Não foi possível excluir o depoimento, tente novamente');
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
