<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AnimalImage;
use App\Models\City;
use Illuminate\Http\Request;
use App\Models\Animal;
use App\Models\Neighborhood;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AnimalController extends Controller
{
    private $animal;
    private $neighborhood;
    private $city;
    private $animalImage;

    public function __construct(Animal $animal, Neighborhood $neighborhood, City $city, AnimalImage $animalImage)
    {
        $this->animal = $animal;
        $this->neighborhood = $neighborhood;
        $this->city = $city;
        $this->animalImage = $animalImage;
    }

    public function animals()
    {
        $userId = auth()->guard('client')->user()->id ?? null;
        $dataAnimals = $this->animal->getAnimals($userId);

        return view('user.animal.list', compact('dataAnimals'));
    }

    public function create()
    {
        $cities = $this->city->getAllCitiesActive();
        return view('user.animal.new', compact('cities'));
    }

    public function insert(Request $request)
    {
        $userId = auth()->guard('client')->user()->id;

        DB::beginTransaction();// Iniciando transação manual para evitar insert não desejáveis

        // Insere dados do animal
        $insertAnimal  = $this->animal->insert([
            'user_created'       => $userId,
            'name'               => $request->name ? filter_var($request->name, FILTER_SANITIZE_STRING) : NULL,
            'species'            => $request->species ? filter_var($request->species, FILTER_SANITIZE_STRING) : NULL,
            'sex'                => $request->sex ? filter_var($request->sex, FILTER_SANITIZE_STRING) : NULL,
            'age'                => $request->age ? FILTER_VAR($request->age, FILTER_SANITIZE_STRING) : NULL,
            'size'               => $request->size ? filter_var($request->size, FILTER_SANITIZE_STRING) : NULL,
            'color'              => $request->color ? filter_var($request->color, FILTER_SANITIZE_STRING) : NULL,
            'race'               => $request->race ? filter_var($request->race, FILTER_SANITIZE_STRING) : NULL,
            'place'              => $request->place ? filter_var($request->place, FILTER_SANITIZE_STRING) : NULL,
            'city'               => $request->city ? (int)$request->city : NULL,
            'neigh'              => $request->neigh ? (int)$request->neigh : NULL,
            'disappearance_date' => $request->disappearance_date ? \DateTime::createFromFormat('d/m/Y H:i', trim($request->disappearance_date))->format('Y-m-d H:i:s') : NULL,
            'phone_contact'      => $request->phone_contact ? filter_var(preg_replace('~[.-]~', '', $request->phone_contact), FILTER_SANITIZE_NUMBER_INT) : NULL,
            'email_contact'      => $request->email_contact ? (filter_var($request->email_contact, FILTER_VALIDATE_EMAIL) ? $request->email_contact : NULL) : NULL,
            'observation'        => filter_var($request->observation)
        ]);
        $codAnimal   = $insertAnimal->id; // Recupera código inserido no banco
        $insertImage = $this->animalImage->insert($request, $codAnimal); // Insere imagens do animal

        if($insertImage && $insertAnimal) {
            DB::commit();
            return redirect()->route('user.account.animals')
                ->with('success', 'Animal anunciado com sucesso!');
        }
        else{
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['Não foi possível criar o anúncio, tente novamente!']);
        }

    }

    public function getNeighsCity(Request $request)
    {
        $city = $request->city;

        if (!$city)
            return response()->json([], Response::HTTP_OK);


        return response()->json($this->neighborhood->getNeighByCity($city), Response::HTTP_OK);

    }

    public function list()
    {
        $city   = 0;
        $neigh  = 0;
        $date   = 0;

        if (isset($_GET['data']) && !empty($_GET['data']) && strtotime($_GET['data'])) {
            $date = $_GET['data'];
        }
        if (isset($_GET['cidade']) && !empty($_GET['cidade'])) {
            $city = filter_var($_GET['cidade'], FILTER_SANITIZE_STRING);
            if (empty($city)) $city = 0;
        }
        if (isset($_GET['bairro']) && !empty($_GET['bairro'])) {
            $neigh = filter_var($_GET['bairro'], FILTER_SANITIZE_STRING);
            if (empty($neigh)) $neigh = 0;
        }

        $filter = [
            'city'  => $city,
            'neigh' => $neigh,
            'date'  => $date
        ];

        $dataAnimals = $this->animal->getAllAnimals($city, $neigh, $date);
        $cities = $this->city->getAllCitiesActive();

        return view('user.animal.search', compact('dataAnimals', 'cities', 'filter'));
    }

    public function searchFind($id)
    {
        $animal = $this->animal->getAnimal($id);
        $imagesAnimal = $this->animalImage->getImagesAnimal($id);

        $animal['city_name'] = $this->city->getCity($animal['city']);
        $animal['neigh_name'] = $this->neighborhood->getNeigh($animal['neigh']);

        $blockChat = Auth::guard('client')->user() ? ($animal['user_created'] == Auth::guard('client')->user()->id ? true : false) : true;

        return view('user.animal.searchFind', compact('animal', 'imagesAnimal', 'blockChat'));
    }
}
