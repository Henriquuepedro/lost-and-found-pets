<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image as ImageUpload;

class BannerController extends Controller
{
    private $banner;

    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
    }

    public function list()
    {
        $banners = $this->banner->getBanners();
        $arrBanners = array();

        foreach ( $banners as $banner ) {
            array_push($arrBanners, array(
                'id'    => $banner['id'],
                'order' => $banner['order'],
                'path'  => asset("user/img/banner/{$banner['path']}")
            ));
        }

        return view('admin.banner.index', compact('arrBanners'));
    }

    public function insert(Request $request)
    {
        $banner = $this->upload($request->banner);
        if(!$banner)
            return redirect()->route('admin.banners')
                ->with('warning', 'Banner não pode ser adicionar, tente novamente!');

        $order = $this->banner->getLastNumberOrder() + 1;

        $insert = $this->banner->insert([
            'path'  => $banner,
            'order' => $order
        ]);

        if($insert)
            return redirect()->route('admin.banners')
                    ->with('success', 'Banner adicionado com sucesso!');

        return redirect()->route('admin.banners')
                ->with('warning', 'Banner não pode ser adicionar, tente novamente!');
    }

    public function remove(Request $request)
    {
        $banner_id = (int)$request->banner_id;

        $banner = $this->banner->getBanners($banner_id);

        if(!$banner)
            return redirect()->route('admin.banners')
                ->with('warning', 'Não foi possível encontrar o banner para excluir, tente novamente!');

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $delete = $this->banner->remove($banner_id);
        if(!$delete)
            return redirect()->route('admin.banners')
                ->with('warning', 'Não foi possível excluir o banner, tente novamente!');

        $rearrangeOrder = $this->banner->rearrangeOrder();

        if($rearrangeOrder && $delete && $banner){
            DB::commit();
            return redirect()->route('admin.banners')
                ->with('success', 'Banner excluído com sucesso!');
        }

        DB::rollBack();
        return redirect()->route('admin.banners')
                ->with('warning', 'Não foi possível excluir o banner, tente novamente!');

    }

    public function rearrangeOrder(Request $request)
    {
        $banners = (array)$request->order_banners;
        $order = 0;
        $updated = true;

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        foreach ($banners as $banner) {
            $order++;
            $update = $this->banner->edit(['order' => $order], $banner);
            if(!$update) $updated = false;
        }

        if(!$updated){
            DB::rollBack();
            echo json_encode(false);
            exit();
        }

        DB::commit();
        echo json_encode(true);
        exit();

    }

    public function upload($file)
    {
        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem

        // Verifica extensões
        if($extension != "png" && $extension != "jpeg" && $extension != "jpg" && $extension != "gif") return false;

        $nameOriginal = $file->getClientOriginalName(); // Recupera nome da imagem
        $imageName = base64_encode($nameOriginal); // Gera um novo nome para a imagem.
        $imageName = substr($imageName, 0, 15) . rand(0, 100) . ".$extension"; // Pega apenas o 15 primeiros e adiciona a extensão

        $uploadPath = "user/img/banner/{$imageName}";
        $realPath   = $file->getRealPath();

        if(!ImageUpload::make($realPath)->save($uploadPath)) return false;

        return $imageName;

    }
}
