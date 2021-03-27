<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;

class PromotionsController extends Controller
{
    private $promotion;

    public function __construct(Promotion $promotion)
    {
        $this->promotion = $promotion;
    }

    public function list()
    {
        $promotions = $this->promotion->getPromotions();
        $arrPromotions = array();

        foreach ($promotions as $promotion) {

            $type = "";

            switch ($promotion['type']) {
                case 1:
                    $type = 'Frete Grátis - Frete menor que X reais';
                    break;
                case 2:
                    $type = 'Frete Grátis - Pedido maior que X reais (PAC)';
                    break;
                default:
                    $type = 'Tipo não encontrado';
            }

            array_push($arrPromotions, [
                "id"            => $promotion["id"],
                "type"          => $type,
                'value'         => number_format($promotion["value"], 2, ',', '.'),
                'value_order'   => $promotion["value"],
                'active'        => $promotion["active"] == 1 ? '<span class="badge badge-success col-md-12" style="padding: 5px 0px;">Ativo</span>' : '<span class="badge badge-danger col-md-12 text-white" style="padding: 5px 0px;">Inativo</span>',
                'created_at'    => $promotion["created_at"] ? date('d/m/Y H:i:s', strtotime($promotion["created_at"])) : 'Não Informado',
                'datetime_order'=> $promotion["created_at"] ? strtotime($promotion["created_at"]) : 0
            ]);
        }

        return view('admin.promotion.index', compact('arrPromotions'));
    }

    public function new()
    {
        return view('admin.promotion.new');
    }

    public function insert(Request $request)
    {
        $type   = $request->type;
        $value  = $request->value;
        $active = isset($request->active) ? 1 : 0;

        if(!$this->promotion->getPromotionForTypeActive($type) && $active == 1) {
            return redirect()->route('admin.promotion.new')
                ->withErrors(['Já existe uma promoção com esse tipo ativa, altere a promoção desse tipo ou inative-a!'])
                ->withInput();
        }

        $dataInsert = array(
            'type'      => $type,
            'value'     => $this->formataValor($value, 'en'),
            'active'    => $active
        );

        $insert = $this->promotion->insert($dataInsert);

        if($insert)
            return redirect()->route('admin.promotions')
                ->with('success', 'Promoção cadastrado com sucesso!');

        return redirect()->route('admin.promotion.new')
            ->with('warning', 'Não foi possível cadastrar a promoção, tente novamente!');
    }


    public function edit(int $id)
    {
        $promotion = $this->promotion->getPromotions($id);
        if(!$promotion) return redirect()->route('admin.promotions');

        return view('admin.promotion.edit', compact('promotion'));
    }

    public function update(Request $request)
    {
        $id     = filter_var($request->promotion_id, FILTER_VALIDATE_INT);
        $type   = $request->type;
        $value  = $request->value;
        $active = isset($request->active) ? 1 : 0;

        // verifica se essa promoção foi ativada e se existe outra como ativa
        if(!$this->promotion->getPromotionForTypeActive($type, $id) && $active == 1) {
            return redirect()->back()
                ->withErrors(['Já existe uma promoção com esse tipo ativa, altere a promoção desse tipo ou inative-a!'])
                ->withInput();
        }

        $dataUpdate = array(
            'type'      => $type,
            'value'     => $this->formataValor($value, 'en'),
            'active'    => $active
        );

        $update = $this->promotion->edit($dataUpdate, $id);

        if($update)
            return redirect()->route('admin.promotions')
                ->with('success', 'Promoção alterada com sucesso!');

        return redirect()->back()
            ->withInput()
            ->with('warning', 'Não foi possível alterar a promoção, tente novamente!');
    }

    public function remove(Request $request)
    {
        $promotion_id = (int)$request->promotion_id;

        if($promotion_id == 0)
            return redirect()->route('admin.promotions')
                ->with('warning', 'Não foi possível excluir a promoção, ocorreu um problema para recuperar informações da promoção!');

        $delete = $this->promotion->remove($promotion_id);

        if($delete)
            return redirect()->route('admin.promotions')
                ->with('success', 'Promoção excluída com sucesso!');


        return redirect()->route('admin.promotions')
            ->with('warning', 'Não foi possível excluir a promoção');

    }
}
