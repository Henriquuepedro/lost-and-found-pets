<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Animal;

class AnimalController extends Controller
{
    private $animal;

    public function __construct(Animal $animal)
    {
        $this->animal = $animal;
    }

    public function animals()
    {
        $userId = auth()->guard('client')->user()->id ?? null;
        $dataAnimals = $this->animal->getAnimals($userId);

        return view('user.animal.orders', compact('dataAnimals'));
    }
}
