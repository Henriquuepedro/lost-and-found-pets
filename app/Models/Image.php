<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image as ImageUpload;

class Image extends Model
{
    private $countPrimaryImage = 0; // Contador para identificar a imagem primária
    private $request;
    private $dataForm;
    private $product_id;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'path', 'primary'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function edit($request, $dataForm)
    {
        $this->request      = $request;
        $this->dataForm     = $dataForm;
        $this->product_id   = $dataForm['product_id'];
        $qntImages          = isset($request['old_images']) ? count($request->old_images) : 0;
        $variableImage      = 'old_images';
        $uploadPath         = "user/img/products/{$this->product_id}";
        $uploadPathTemp     = "user/img/products/temp";

        if($qntImages === 0) {
            $qntImages = isset($request['images']) ? count($request->images) : 0;
            $variableImage = 'images';
        }


        $this->emptyPath($uploadPathTemp);

        $imagesOld = [];
        // Percorre as imagens
        if($qntImages !== 0){
            foreach($dataForm[$variableImage] as $key => $imageOld){
                $expImage = explode('_', $imageOld);

                if(is_object($imageOld)) $expImage[0] = $key;

                if($expImage[0] === "old"){

                    // Consulta nome da imagem
                    $imgDb = $this->where([
                        ['product_id', $this->product_id],
                        ['id', $expImage[1]]
                    ])->get()[0];

                    array_push($imagesOld, [$imgDb->path, true]);

                    // Move arquivos para pasta de arquivos temporários
                    copy("$uploadPath/{$imgDb->path}", "$uploadPathTemp/{$imgDb->path}");
                    copy("$uploadPath/thumbnail_{$imgDb->path}", "$uploadPathTemp/thumbnail_{$imgDb->path}");
                }
                else array_push($imagesOld, [$request->file('images')[$expImage[0]], false]);

            }
        }

//        $this->emptyPath($uploadPath);
        // Deleta todas as imagens para realizar a inserção novamente
        $this->where('product_id', $this->product_id)->delete();

        $verificaPrimaryImage = true;
        foreach($imagesOld as $image){
            $primaryImage = 0;

            $this->countPrimaryImage++; // Contador para identicar a imagem primária

            $image_name = $this->upload($image[0], $image[1]);
            if(!$image_name) break;

            $primaryImageExp = explode('_', $this->dataForm['primaryImage']);

            if($verificaPrimaryImage) {
                if ($primaryImageExp[0] === 'old')
                    $primaryImage = substr($primaryImageExp[1], 0, -1);
                else {
                    $newImagesUpload = isset($request->images) ? count($request->file('images')) : 0;
                    $primaryImage = $primaryImageExp[0] + ($qntImages - $newImagesUpload);
                }
            }
            if($primaryImage == $this->countPrimaryImage) $verificaPrimaryImage = false;

            // Insere dados da imagem o banco
            $this->create([
                'product_id'    => $this->product_id,
                'path'          => $image_name,
                'primary'       => $primaryImage == $this->countPrimaryImage ? 1 : 0
            ]);
        }


        if($qntImages === $this->countPrimaryImage){
            if($verificaPrimaryImage) $this->where([['product_id', $this->product_id],['id', 1]])->update(['primary' => 1]);
            return true;
        }

        return false;
    }


    public function upload($file, $imageOld)
    {
        $imageName = "";

        if(!$imageOld) {
            $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem
            $nameOriginal = $file->getClientOriginalName(); // Recupera nome da imagem
            $imageName = base64_encode($nameOriginal); // Gera um novo nome para a imagem.
            $imageName = substr($imageName, 0, 15) . rand(0, 100) . ".$extension"; // Pega apenas o 15 primeiros e adiciona a extensão
        }
        if($imageOld) $imageName = $file;

        $uploadPath = "user/img/products/{$this->product_id}"; // Faz o upload para o caminho 'admin/dist/images/autos/{ID}/'
        $uploadPathTemp = "user/img/products/temp";

        if(!$imageOld) {
            if ($file->move($uploadPath, $imageName)) { // Verifica se a imagem foi movida com sucesso
                $this->resizeImage($uploadPath, $imageName);
                return $imageName;
            }
        }
        if($imageOld) {
            copy("$uploadPathTemp/{$imageName}", "$uploadPath/{$imageName}");
            copy("$uploadPathTemp/thumbnail_{$imageName}", "$uploadPath/thumbnail_{$imageName}");
            return $imageName;
        }

        return false;
    }

    public function resizeImage($uploadPath, $imageName)
    {
        ImageUpload::make("{$uploadPath}/{$imageName}")
            ->resize(250, 250)
            ->save("{$uploadPath}/thumbnail_{$imageName}");
    }

    public function emptyPath($uploadPath)
    {
        // Exclui os arquivos da pasta
        if(is_dir($uploadPath)){
            $diretorio = dir($uploadPath);
            while($arquivo = $diretorio->read())
                if(($arquivo != '.') && ($arquivo != '..'))
                    unlink($uploadPath . "/" . $arquivo);

            $diretorio->close();
        }
    }

    public function insert($request, $dataForm, $product_id)
    {
        $this->request      = $request;
        $this->dataForm     = $dataForm;
        $this->product_id   = $product_id;
        $qntImages          = isset($request['images']) ? count($request->images) : 0;
        $this->dataForm['primaryImage'] = $this->dataForm['primaryImage'] ?? 1;

        // Percorre todas as imagens enviadas para fazer o upload delas e insere seus dados no banco
        if($qntImages !== 0) {
            foreach ($request->file('images') as $file) {
                $this->countPrimaryImage++; // Contador para identicar a imagem primária

                $image_name = $this->upload($file, false);

                if (!$image_name) break;

                // Insere dados da imagem o banco
                $this->create([
                    'product_id'=> $product_id,
                    'path'      => $image_name,
                    'primary'   => $this->dataForm['primaryImage'] == $this->countPrimaryImage ? 1 : 0
                ]);
            }
        }
        if($qntImages === $this->countPrimaryImage) return true;

        return false;
    }

    public function getImagePrimaryProduct($product_id)
    {
        return $this->where('product_id', $product_id)->first();
    }
}
