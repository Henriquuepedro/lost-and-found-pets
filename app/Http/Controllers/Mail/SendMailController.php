<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Admin;
use App\Models\OrderAddress;
use App\Models\OrderItems;
use App\Models\Image;
use App\Models\Order;

class SendMailController extends Controller
{
    private $mail;
    private $admin;
    private $orderAddress;
    private $orderItems;
    private $image;
    private $order;
    private $logo;
    private $company_name;
    private $emailNoReplay;
    private $passwordNoReplay;
    private $smtpNoReplay;
    private $portNoReplay;
    private $secureNoReplay;

    public function __construct(Admin $admin, OrderAddress $ordeAddress, OrderItems $orderItems, Image $image, Order $order)
    {
        $this->mail         = new PHPMailer(true);
        $this->admin        = $admin;
        $this->orderItems   = $orderItems;
        $this->orderAddress = $ordeAddress;
        $this->image        = $image;
        $this->order        = $order;

        $dataAdmin          = $admin->getAdminMain();
        $this->logo         = asset("user/img/admin/{$dataAdmin->picture}");
        $this->company_name = $dataAdmin->name;
        $this->emailNoReplay = $dataAdmin->email_noreplay;
        $this->passwordNoReplay = $dataAdmin->password_noreplay;
        $this->smtpNoReplay = $dataAdmin->smtp_noreplay;
        $this->portNoReplay = $dataAdmin->port_noreplay;
        $this->secureNoReplay = $dataAdmin->secure_noreplay;
    }

    /**
     * ENVIAR EMAIL DE BOAS VINDAS
     *
     * @return bool|bool[]|string
     */
    public function newUser()
    {
        $viewLogoHeader = false;
        $user = auth()->guard('client')->user();
        $logo = $this->logo;
        $company = $this->company_name;

        if(!$user) return [false];

        $options            = new \stdClass();
        $options->body      = view('mail.newuser', compact('logo', 'company', 'user', 'viewLogoHeader'));
        $options->subject   = "Cadastro {$this->company_name}";
        $options->email     = $user->email;
        $options->name      = $user->name;

        return $this->send($options);
    }

    /**
     * ENVIAR CONTATO DO SITE POR EMAIL
     *
     * @param Request $request
     * @return array|bool|string
     */
    public function contact(Request $request)
    {
        $viewLogoHeader = true;
        $admin = $this->admin->getAdminMain();
        $contact = $request;
        $logo = $this->logo;
        $company = $this->company_name;

        if(!$admin) return [false, "Não encontrado um e-mail padrão apra envio"];

        $options            = new \stdClass();
        $options->body      = view('mail.contact', compact('logo', 'company', 'contact', 'viewLogoHeader'));
        $options->subject   = "Contato {$this->company_name}";
        $options->email     = $admin->email_contact;
        $options->name      = $admin->name;

        return $this->send($options);
    }

    /**
     * ENVIAR PEDIDO POR EMAIL
     *
     * @param int $order
     */
    public function newOrder(int $order)
    {
        $viewLogoHeader = true;
        $arrItems   = array();
        $logo = $this->logo;
        $company = $this->company_name;

        $user = auth()->guard('client')->user();

        $userId     = $user->id;
        $userName   = $user->name;
        $userEmail  = $user->email;
        $urlOrder   = route('user.account.order', ['id' => $order]);

        $items   = $this->orderItems->getItemsOfOrder($order);
        $address = $this->orderAddress->getAddressOfOrder($order);

        $arrAddress = [
            'address'       => $address->address,
            'cep'           => $this->formataCep($address->cep),
            'number'        => $address->number,
            'complement'    => $address->complement,
            'reference'     => $address->reference,
            'neighborhood'  => $address->neighborhood,
            'city'          => $address->city,
            'state'         => $address->state,
        ];
        foreach ($items as $iten) {
            array_push($arrItems, [
                'description'   => $iten['description'],
                'product_id'    => $iten['product_id'],
                'quantity'      => (int)$iten['quantity'],
                'amount'        => number_format($iten['amount'], 2, ',', '.'),
                'total_iten'    => number_format($iten['total_iten'], 2, ',', '.'),
                'image'         => asset("user/img/products/{$iten['product_id']}/thumbnail_" . $this->image->getImagePrimaryProduct($iten['product_id'])->path)

            ]);
        }

        $options            = new \stdClass();
        $options->body      = view('mail.neworder', compact('logo', 'company', 'userName', 'arrAddress', 'arrItems', 'order', 'urlOrder', 'viewLogoHeader'));
        $options->subject   = "Pedido Realizado";
        $options->email     = $userEmail;
        $options->name      = $userName;

        return $this->send($options);
    }

    /**
     * ENVIAR PEDIDO POR EMAIL
     *
     * @param int $order
     */
    public function updateOrder(int $order, int $status)
    {
        $viewLogoHeader = true;
        $arrItems   = array();
        $logo = $this->logo;
        $company = $this->company_name;

        $user = $this->order->getOrder($order);

        $userName   = $user->name_sender;
        $userEmail  = $user->email_sender;
        $urlOrder   = route('user.account.order', ['id' => $order]);

        $items   = $this->orderItems->getItemsOfOrder($order);
        $address = $this->orderAddress->getAddressOfOrder($order);

        $arrAddress = [
            'address'       => $address->address,
            'cep'           => $this->formataCep($address->cep),
            'number'        => $address->number,
            'complement'    => $address->complement,
            'reference'     => $address->reference,
            'neighborhood'  => $address->neighborhood,
            'city'          => $address->city,
            'state'         => $address->state,
        ];

        foreach ($items as $iten) {
            array_push($arrItems, [
                'description'   => $iten['description'],
                'product_id'    => $iten['product_id'],
                'quantity'      => (int)$iten['quantity'],
                'amount'        => number_format($iten['amount'], 2, ',', '.'),
                'total_iten'    => number_format($iten['total_iten'], 2, ',', '.'),
                'image'         => asset("user/img/products/{$iten['product_id']}/thumbnail_" . $this->image->getImagePrimaryProduct($iten['product_id'])->path)
            ]);
        }

        $statusOrder = $this->statusOrder[$status];

        $options            = new \stdClass();
        $options->body      = view('mail.updateorder', compact('logo', 'company', 'userName', 'arrAddress', 'arrItems', 'order', 'urlOrder', 'statusOrder', 'viewLogoHeader'));
        $options->subject   = "Pedido Atualizado";
        $options->email     = $userEmail;
        $options->name      = $userName;

        return $this->send($options);
    }

    /**
     * ENVIAR NOTIFICAÇÃO DE BOLETO EMAIL
     *
     * @param int $order
     */
    public function billetOverduePayment(int $order)
    {
        $viewLogoHeader = true;
        $arrItems   = array();
        $logo = $this->logo;
        $company = $this->company_name;

        $user = $this->order->getOrder($order);

        $userName   = $user->name_sender;
        $userEmail  = $user->email_sender;
        $urlOrder   = route('user.account.order', ['id' => $order]);
        $urlBillet  = $user->link_billet;

        $items   = $this->orderItems->getItemsOfOrder($order);

        foreach ($items as $iten) {
            array_push($arrItems, [
                'description'   => $iten['description'],
                'product_id'    => $iten['product_id'],
                'quantity'      => (int)$iten['quantity'],
                'amount'        => number_format($iten['amount'], 2, ',', '.'),
                'total_iten'    => number_format($iten['total_iten'], 2, ',', '.'),
                'image'         => asset("user/img/products/{$iten['product_id']}/thumbnail_" . $this->image->getImagePrimaryProduct($iten['product_id'])->path)
            ]);
        }

        $options            = new \stdClass();
        $options->body      = view('mail.billetOverduePayment', compact('logo', 'company', 'userName', 'arrItems', 'order', 'urlOrder', 'viewLogoHeader', 'urlBillet'));
        $options->subject   = "Pagamento Pendente";
        $options->email     = $userEmail;
        $options->name      = $userName;

        return $this->send($options);
    }

    public function forgotPassword($email, $name, $hash)
    {
        $viewLogoHeader = true;
        $urlHash = route('user.resetPassword', ['hash' => $hash]);
        $logo = $this->logo;
        $company = $this->company_name;

        $options            = new \stdClass();
        $options->body      = view('mail.forgot_password', compact('logo', 'company', 'email', 'name', 'urlHash', 'viewLogoHeader'));
        $options->subject   = "Recuperar Senha {$this->company_name}";
        $options->email     = $email;
        $options->name      = $name;

        return $this->send($options);
    }

    public function testeConnectionSmtp($data)
    {
        $mail = new PHPMailer(true);
        $mail->SMTPAuth = true;
        $mail->Username = $data['email'];
        $mail->Password = $data['password'];
        $mail->Host = $data['smtp'];
        $mail->Port = $data['port'];
        $mail->SMTPSecure = $data['secure'];

        $validCredentials = false;

        try {
            $validCredentials = $mail->SmtpConnect();
        }
        catch(Exception $error) { /* Error handling ... */ }

        return $validCredentials;

    }

    private function send($options)
    {
        try {

            // ----------------- AJUSTES PEGO DO ARRAY -----------------
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';
            $this->mail->Username = $this->emailNoReplay; // EMAIL PARA ENVIO
            $this->mail->Password = $this->passwordNoReplay;
            $this->mail->AddAddress($options->email, $options->name); // EMAIL DESTINATÁRIO
            $this->mail->FromName = "Loja {$this->company_name}"; // NOME CONTATO
            $this->mail->Subject = $options->subject; //ASSUNTO EMAIL

            $this->mail->Body = $options->body;

            $this->mail->Host = $this->smtpNoReplay;
            $this->mail->Port = (int)$this->portNoReplay;
            $this->mail->IsSMTP(); // use SMTP
            $this->mail->SMTPAuth = true; // SMTP autenticação
            $this->mail->SMTPSecure = $this->secureNoReplay;
            $this->mail->From = $this->mail->Username;
            return $this->mail->Send() ? [true] : [false, "Não foi possível enviar a mensagem, tente novamente mais tarde"];
        } catch (Exception $e) {
            return [false, "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}"];
        }
    }

}
