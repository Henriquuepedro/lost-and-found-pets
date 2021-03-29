<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class ClientController extends Controller
{
    private $client;

    public function __construct(User $client)
    {
        $this->client   = $client;
    }

    public function list()
    {
        $clients = $this->client->getAllClients();
        $arrClients = array();

        foreach ($clients as $client) {
            array_push($arrClients, [
                "id"            => $client["id"],
                "name"          => $client["name"],
                'email'         => $client["email"],
                'tel'           => $this->formatPhone($client["tel"]),
                'created_at'    => $client["created_at"] ? date('d/m/Y H:i', strtotime($client["created_at"])) : 'Não Informado',
                'datetime_order'=> $client["created_at"] ? strtotime($client["created_at"]) : 0,
            ]);
        }

        return view('admin.client.index', compact('arrClients'));
    }

    public function view(int $id)
    {
        $client = $this->client->getClient($id);
        if(!$client) return redirect()->route('admin.clients');

        $dataClient = array(
            'id'            => $id,
            'name'          => $client['name'],
            'email'         => $client['email'],
            'created_at'    => $client["created_at"] ? date('d/m/Y H:i', strtotime($client["created_at"])) : 'Não Informado',
            'updated_at'    => $client["updated_at"] ? date('d/m/Y H:i', strtotime($client["updated_at"])) : 'Não Informado',
        );

        return view('admin.client.view', compact('dataClient'));
    }
}
