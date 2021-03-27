<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Admin;
use App\Models\Testimony;
use Illuminate\Support\Facades\DB;

class AboutController extends Controller
{
    private $admin;
    private $testimony;

    public function __construct(Admin $admin, Testimony $testimony)
    {
        $this->admin = $admin;
        $this->testimony = $testimony;
    }

    public function about()
    {
        $testimonies = $this->testimony->where(['approved' => 1, 'primary' => 1])->orderBy('id', 'desc')->limit(10)->get();

        return view('user.about.index', compact('testimonies'));
    }
    public function contact()
    {
        $admin = $this->admin->getAdminMain();

        $addressSearch = "{$admin->address},{$admin->number}-{$admin->cep}-{$admin->neighborhood}-{$admin->city}-{$admin->state}";
        $addressView = "{$admin->address}, {$admin->number}";
        $addressView .= $admin->complement != "" ? "<br/>".$admin->complement : "";
        $addressView .= "<br/>{$admin->neighborhood}-{$admin->city}/{$admin->state}<br/>";
        $addressView .= substr($admin->cep, 0, 5) . '-' . substr($admin->cep, 5, 3);

        if(strlen($admin->tel) === 10){
            $telView = '(' . substr($admin->tel, 0, 2) . ') ' . substr($admin->tel, 2, 4)
                . '-' . substr($admin->tel, 6);
        }elseif(strlen($admin->tel) === 11){
            $telView = '(' . substr($admin->tel, 0, 2) . ') ' . substr($admin->tel, 2, 5)
                . '-' . substr($admin->tel, 7);
        }

        $addressEmail = $admin->email_contact;

        return view('user.about.contact', compact('addressSearch', 'addressView', 'telView', 'addressEmail'));
    }

    public function testimonies()
    {
        $mediaRateTetimonies = 0;
        $queryTotalRateTetimonies = $this->testimony->select([DB::raw("SUM(rate) as qty")])->first();
        if($queryTotalRateTetimonies) $mediaRateTetimonies = $queryTotalRateTetimonies->qty;
        $testimonies = $this->testimony->where('approved', 1)->orderBy('id', 'desc')->get();

        return view('user.about.testimonies', compact('testimonies', 'mediaRateTetimonies'));
    }
}
