<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image as ImageUpload;

class AnimalImage extends Model
{
    use HasFactory;

    private $countPrimaryImage = 0; // Contador para identificar a imagem primária
    private $request;
    private $dataForm;
    private $animal_id;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'animal_id', 'path', 'primary'
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

    public function insert($request, $animal_id)
    {
        $this->request      = $request;
        $this->dataForm     = $request;
        $this->animal_id   = $animal_id;
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
                    'animal_id' => $animal_id,
                    'path'      => $image_name,
                    'primary'   => $this->dataForm['primaryImage'] == $this->countPrimaryImage ? 1 : 0
                ]);
            }
        }
        if($qntImages === $this->countPrimaryImage) return true;

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

        $uploadPath = "user/img/animals/{$this->animal_id}"; // Faz o upload para o caminho 'admin/dist/images/autos/{ID}/'
        $uploadPathTemp = "user/img/animals/temp";

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

    public function getImagesAnimal($animal_id)
    {
        return $this->where('animal_id', $animal_id)->orderBy('primary')->get();
    }

}
