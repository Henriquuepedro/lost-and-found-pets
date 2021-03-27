<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image as ImageUpload;
use App\Http\Controllers\Mail\SendMailController;

class ProfileController extends Controller
{
    private $admin;
    private $mail;

    public function __construct(Admin $admin, SendMailController $mail)
    {
        $this->admin = $admin;
        $this->mail = $mail;
    }

    public function index()
    {
        $admin = $this->admin->getAdminMain();

        $arrAdmin = array(
            "name"              => $admin['name'],
            "name_user"         => $admin['name_user'],
            "picture"           => $admin['picture'],
            "cep"               => $admin['cep'],
            "address"           => $admin['address'],
            "number"            => $admin['number'],
            "complement"        => $admin['complement'],
            "neighborhood"      => $admin['neighborhood'],
            "city"              => $admin['city'],
            "state"             => $admin['state'],
            "email"             => $admin['email'],
            "email_contact"     => $admin['email_contact'],
            "email_noreplay"    => $admin['email_noreplay'],
            "password_noreplay" => $admin['password_noreplay'],
            "smtp_noreplay"     => $admin['smtp_noreplay'],
            "port_noreplay"     => $admin['port_noreplay'],
            "secure_noreplay"   => $admin['secure_noreplay'],
            "tel"               => $admin['tel']
        );

        return view('admin.profile.index', compact('arrAdmin'));
    }

    public function update(Request $request)
    {
        $admin_id   = auth()->guard('admin')->user()->id;

        $validator = validator(
            $request->all(),
            [
                'name'              => 'required|min:3',
                'name_user'         => 'required|min:3',
                'email'             => ['required', Rule::unique('admins')->ignore($admin_id)],
                'password'          => 'nullable|confirmed|min:6',
                'tel'               => 'required|min:14'
            ],
            [
                'name.required'     => 'O nome é um campo obrigatório!',
                'name.min'          => 'O nome precisa de no mínimo 3 caracteres!',
                'name_user.required'=> 'O nome do remetente é um campo obrigatório!',
                'name_user.min'     => 'O nome do remetente precisa de no mínimo 3 caracteres!',
                'email.required'    => 'O email é um campo obrigatório!',
                'tel.required'      => 'O telefone é um campo obrigatório!',
                'tel.min'           => 'O telefone precisa ser informado corretamente!',
                'email.unique'      => 'O email já está em uso!',
                'password.confirmed'=> 'As senhas não correspondem!',
                'password.min'      => 'A senha precisa de no mínimo 6 caracteres!'
            ]
        );

        if($validator->fails())
            return redirect()->route('admin.profile')->withErrors($validator)->withInput();

        $arrAdmin = [
            'name'              => filter_var($request->name, FILTER_SANITIZE_STRING),
            'name_user'         => filter_var($request->name_user, FILTER_SANITIZE_STRING),
            'email'             => filter_var($request->email, FILTER_VALIDATE_EMAIL),
            'email_contact'     => filter_var($request->email_contact, FILTER_VALIDATE_EMAIL),
            'email_noreplay'    => filter_var($request->email_noreplay, FILTER_VALIDATE_EMAIL),
            'password_noreplay' => filter_var($request->password_noreplay, FILTER_SANITIZE_STRING),
            'smtp_noreplay'     => filter_var($request->smtp_noreplay, FILTER_SANITIZE_STRING),
            'port_noreplay'     => filter_var($request->port_noreplay, FILTER_SANITIZE_STRING),
            'secure_noreplay'   => filter_var($request->secure_noreplay, FILTER_SANITIZE_STRING),
            'tel'               => filter_var(preg_replace('~[.-]~', '', $request->tel), FILTER_SANITIZE_NUMBER_INT),
            'cep'               => filter_var(preg_replace('~[.-]~', '', $request->cep), FILTER_SANITIZE_NUMBER_INT),
            'address'           => filter_var($request->address, FILTER_SANITIZE_STRING),
            'number'            => filter_var($request->number, FILTER_SANITIZE_STRING),
            'complement'        => $request->complement ? FILTER_VAR($request->complement, FILTER_SANITIZE_STRING) : '',
            'neighborhood'      => filter_var($request->neighborhood, FILTER_SANITIZE_STRING),
            'city'              => filter_var($request->city, FILTER_SANITIZE_STRING),
            'state'             => filter_var($request->state, FILTER_SANITIZE_STRING)
        ];

        // verifica senha atual
        if($request->password) {
            if(!Hash::check($request->password_current, auth()->guard('admin')->user()->password)) {
                return redirect()
                        ->route('admin.profile')
                        ->withErrors(['Senha informada não corresponde com a senha atual!'])
                        ->withInput();
            }
        }

        if($request->password) $arrAdmin['password'] = Hash::make($request->password);
        if($request->picture){
            $picture = $this->uploadProfile($request->picture, $admin_id);
            $arrAdmin['picture'] = $picture;
        }

        $update = $this->admin->edit($arrAdmin, $admin_id);

        if($update)
            return redirect()->route('admin.profile')
                ->with('success', 'Perfil alterado com sucesso!');

        return redirect()->route('admin.profile')
            ->withErrors(['Não foi possível alterar o perfil, tente novamente']);
    }

    public function image_title()
    {
        $admin  = $this->admin->getAdminMain();
        $image  = $admin->image_title == "" ? asset('user/img/title/sem_imagem.png') : asset("user/img/title/{$admin->image_title}");
        $text   = $admin->message_title;


        return view('admin.profile.image_title', compact('image', 'text'));
    }

    public function image_title_insert(Request $request)
    {
        $arrData = array();
        if(isset($request->image_title)) {
            $banner = $this->uploadImageTitle($request->image_title);
            if (!$banner)
                return redirect()->route('admin.image_title')
                    ->with('warning', 'Imagem não pode ser alterada, tente novamente!');
            $arrData['image_title'] = $banner;
        }
        if (isset($request->message_title)) {
            $arrData['message_title'] = $request->message_title;
        }

        if(count($arrData) == 0) {
            return redirect()->route('admin.image_title')
                ->with('warning', 'Não enviado nenhuma informação, tente enviar novamente!');
        }

        $admin_id   = auth()->guard('admin')->user()->id;
        $this->admin->edit($arrData, $admin_id);

            return redirect()->route('admin.image_title')
                ->with('success', 'Imagem alterada com sucesso!');
    }

    public function uploadImageTitle($file)
    {
        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem

        // Verifica extensões
        if($extension != "jpeg" && $extension != "jpg") return false;

        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem
        $nameOriginal = $file->getClientOriginalName(); // Recupera nome da imagem
        $imageName = base64_encode($nameOriginal); // Gera um novo nome para a imagem.
        $imageName = substr($imageName, 0, 15) . rand(0, 100) . ".$extension"; // Pega apenas o 15 primeiros e adiciona a extensão

        $uploadPath = "user/img/title/{$imageName}";
        $realPath   = $file->getRealPath();

        // Exclui os arquivos da pasta
        if(is_dir("user/img/title/")){
            $diretorio = dir("user/img/title/");
            while($arquivo = $diretorio->read())
                if(($arquivo != '.') && ($arquivo != '..'))
                    unlink("user/img/title/" . $arquivo);

            $diretorio->close();
        }

        if(!ImageUpload::make($realPath)->save($uploadPath)) return false;

        return $imageName;

    }

    public function uploadProfile($file, $id)
    {
        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem
        $nameOriginal = $file->getClientOriginalName(); // Recupera nome da imagem
        $imageName = base64_encode($nameOriginal); // Gera um novo nome para a imagem.
        $imageName = substr($imageName, 0, 15) . time() . ".$extension"; // Pega apenas o 15 primeiros e adiciona a extensão

        // Verifica extensões
        if($extension != "png" && $extension != "jpeg" && $extension != "jpg" && $extension != "gif") return false;

        $uploadPath = "user/img/admin/{$imageName}";
        $realPath   = $file->getRealPath();

        ImageUpload::make($realPath)->save($uploadPath);

        return $imageName;

    }

    public function testeConnectionSmtp(Request $request)
    {
        $email      = $request->email;
        $password   = $request->password;
        $smtp       = $request->smtp;
        $port       = $request->port;
        $secure     = $request->secure;

        $dataCheck = array(
            'email'     => $email,
            'password'  => $password,
            'smtp'      => $smtp,
            'port'      => $port,
            'secure'    => $secure,
        );

        return json_encode($this->mail->testeConnectionSmtp($dataCheck));
    }

    public function about()
    {
        $admin  = $this->admin->getAdminMain();
        $image_about        = asset("user/img/about/{$admin->image_about}");
        $title_about        = $admin->title_about;
        $description_about  = $admin->description_about;


        return view('admin.profile.about', compact('image_about', 'title_about', 'description_about'));
    }

    public function about_insert(Request $request)
    {
        if(isset($request->image_about)) {
            $imageAbout = $this->uploadImageAbout($request->image_about);
            if (!$imageAbout)
                return redirect()->route('admin.about')
                    ->with('warning', 'Imagem não pôde ser alterada, tente novamente!');
            $arrData['image_about'] = $imageAbout;
        }
        $arrData['title_about'] = $request->title_about;
        $arrData['description_about'] = $request->description_about;

        if(count($arrData) == 0) {
            return redirect()->route('admin.about')
                ->with('warning', 'Não foi encontrada nenhuma informação, tente enviar novamente!');
        }

        $admin_id   = auth()->guard('admin')->user()->id;
        $this->admin->edit($arrData, $admin_id);

        return redirect()->route('admin.about')
            ->with('success', 'Dados sobre a empresa alterada com sucesso!');
    }

    public function uploadImageAbout($file)
    {
        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem

        // Verifica extensões
        if($extension != "jpeg" && $extension != "jpg" && $extension != "png") return false;

        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem
        $nameOriginal = $file->getClientOriginalName(); // Recupera nome da imagem
        $imageName = base64_encode($nameOriginal); // Gera um novo nome para a imagem.
        $imageName = substr($imageName, 0, 15) . rand(0, 100) . ".$extension"; // Pega apenas o 15 primeiros e adiciona a extensão

        $uploadPath = "user/img/about/{$imageName}";
        $realPath   = $file->getRealPath();

        // Exclui os arquivos da pasta
        if(is_dir("user/img/about/")){
            $diretorio = dir("user/img/about/");
            while($arquivo = $diretorio->read())
                if(($arquivo != '.') && ($arquivo != '..'))
                    unlink("user/img/about/" . $arquivo);

            $diretorio->close();
        }

        if(!ImageUpload::make($realPath)->save($uploadPath)) return false;

        return $imageName;

    }
}
