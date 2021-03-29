<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Admin;
use App\Models\LogAdmin;

class HomeController extends Controller
{
    private $client;
    private $admin;
    private $logAdmin;

    public function __construct(User $client, Admin $admin, LogAdmin $logAdmin)
    {
        $this->client = $client;
        $this->admin = $admin;
        $this->logAdmin = $logAdmin;
    }

    public function dashboard()
    {
        $return = array();
        $lastLoginsClients = array();

        $countClients = $this->client->getCountClients();

        foreach ($this->client->getLastLogins(10) as $client) {
            array_push($lastLoginsClients, array(
                'name'              => $client['name'],
                'email'             => $client['email'],
                'last_login'        => date('d/m/Y H:i', strtotime($client['last_login'])),
                'last_login_order'  => strtotime($client['last_login']),
            ));
        }

        $return['counts'] = array(
            'client' => $countClients,
            'report' => [
                'lastLoginsClients' => $lastLoginsClients
            ]
        );

        return view('admin.home.index', compact('return'));
    }

    public function salesChart()
    {
        setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

        $date = strtotime(date('Y-m-') . '01');

        $arrayTotalsRecebido = array(
            ucfirst( utf8_encode( strftime("%b/%y", strtotime('-5 month', $date)))) => 0,
            ucfirst( utf8_encode( strftime("%b/%y", strtotime('-4 month', $date)))) => 0,
            ucfirst( utf8_encode( strftime("%b/%y", strtotime('-3 month', $date)))) => 0,
            ucfirst( utf8_encode( strftime("%b/%y", strtotime('-2 month', $date)))) => 0,
            ucfirst( utf8_encode( strftime("%b/%y", strtotime('-1 month', $date)))) => 0,
            ucfirst( utf8_encode( strftime("%b/%y", time()))) => 0,
        );

        $arrayMonths = array(
            ucfirst( utf8_encode( strftime("%b/%y", strtotime('-5 month', $date)))),
            ucfirst( utf8_encode( strftime("%b/%y", strtotime('-4 month', $date)))),
            ucfirst( utf8_encode( strftime("%b/%y", strtotime('-3 month', $date)))),
            ucfirst( utf8_encode( strftime("%b/%y", strtotime('-2 month', $date)))),
            ucfirst( utf8_encode( strftime("%b/%y", strtotime('-1 month', $date)))),
            ucfirst( utf8_encode( strftime("%b/%y", time()))),
        );

        $lastDayMonth   = date('m', strtotime('-5 month'));
        $yearMonth      = date('y', strtotime('-5 month'));
        $lastDay        = date("t", mktime(0,0,0,$lastDayMonth,'01',$yearMonth));
        $dateStart      = date('Y-m') . "-{$lastDay}";
        $dateEnd        = date('Y-m', strtotime('-5 month')) . "-01";



        $ordersCanceled = $this->order->getOrdersCanceled();
        $ordersRecebido = $this->order->getOrderRecebido($dateEnd, $dateStart, $ordersCanceled);

        foreach ($ordersRecebido as $order) {
            $keyDate = ucfirst( utf8_encode( strftime("%b/%y", strtotime($order['date_created']))));

//            $arrayTotalsRecebido[$keyDate] += ($order['net_amount'] - $order['value_ship'] - $order['fee_amount']);
            $arrayTotalsRecebido[$keyDate] += $order['net_amount'];

        }

        echo json_encode(['totals' => $arrayTotalsRecebido, 'months' => $arrayMonths]);
    }

    public function salesChartWeek()
    {
        $sunday = date('Y-m-d', strtotime('monday this week'));
        $saturday = date('Y-m-d', strtotime('sunday this week'));

        $arrWeek = array(
            'Monday'    => 0,
            'Tuesday'   => 0,
            'Wednesday' => 0,
            'Thursday'  => 0,
            'Friday'    => 0,
            'Saturday'  => 0,
            'Sunday'    => 0
        );

        $ordersCanceled = $this->order->getOrdersCanceled();
        $ordersRecebido = $this->order->getOrderRecebido($sunday, $saturday, $ordersCanceled);

        foreach ($ordersRecebido as $order) {
            $keyDate = date('l', strtotime($order['date_created']));

//            $arrWeek[$keyDate] += ($order['net_amount'] - $order['value_ship'] - $order['fee_amount']);
            $arrWeek[$keyDate] += $order['net_amount'];
        }

        echo json_encode(['totals' => $arrWeek]);
    }

    /**
     * Consulta ajax
     */
    public function pieChartIten()
    {
        $arrItemsQty = array();
        $arrItemsName= array();

        $ordersCanceled = $this->order->getOrdersCanceled();

        foreach ($this->orderItems->getBestSellingItems($ordersCanceled) as $iten){
            $product = $this->product->getNameProducts($iten['id']);
            array_push($arrItemsQty, (int)$iten['qty']);
            array_push($arrItemsName, $product['name']);
        }

        echo json_encode(['qty' => $arrItemsQty, 'name' => $arrItemsName]);
    }

    /**
     * Consulta ajax
     */
    public function pieChartClient()
    {
        $arrItemsQty = array();
        $arrItemsName= array();

        $ordersCanceled = $this->order->getOrdersCanceled();

        foreach ($this->order->getCustomersWhoBuyMore($ordersCanceled) as $iten){
            $client = $this->client->getNameClient($iten['id']);
            array_push($arrItemsQty, (int)$iten['qty']);
            array_push($arrItemsName, $client->name);
        }
        echo json_encode(['qty' => $arrItemsQty, 'name' => $arrItemsName]);
    }

    public function mapChartState()
    {
        $arrStates = array(
            'AC'=> 0,
            'AL'=> 0,
            'AP'=> 0,
            'AM'=> 0,
            'BA'=> 0,
            'CE'=> 0,
            'DF'=> 0,
            'ES'=> 0,
            'GO'=> 0,
            'MA'=> 0,
            'MT'=> 0,
            'MS'=> 0,
            'MG'=> 0,
            'PA'=> 0,
            'PB'=> 0,
            'PR'=> 0,
            'PE'=> 0,
            'PI'=> 0,
            'RJ'=> 0,
            'RN'=> 0,
            'RS'=> 0,
            'RO'=> 0,
            'RR'=> 0,
            'SC'=> 0,
            'SP'=> 0,
            'SE'=> 0,
            'TO'=> 0
        );

        foreach ($this->orderAddress->getStateOrders() as $saleState)
            $arrStates[$saleState['state']] += $saleState['qty'];

        echo json_encode($arrStates);
    }


    public function salesChartYear()
    {
        $lastYears  = 1;

        $dateStart  = date('Y') . "-12-31 23:59:59";
        $dateEnd    = (date('Y') - $lastYears) . "-01-01 00:00:00";

        $ordersCanceled = $this->order->getOrdersCanceled();
        $ordersRecebido = $this->order->getOrderRecebido($dateEnd, $dateStart, $ordersCanceled);

        $arrayTotalsRecebido = array();
        for ($year = 0; $year <= $lastYears; $year++) {
            $arrayTotalsRecebido[date('Y', strtotime("-{$year} year"))] = array(
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0,
                10 => 0,
                11 => 0,
                12 => 0
            );
        }

        foreach ($ordersRecebido as $order) {

            $year = (int)date('Y', strtotime($order['date_created']));
            $month = (int)date('m', strtotime($order['date_created']));

//            $arrayTotalsRecebido[$year][$month] += ($order['net_amount'] - $order['value_ship'] - $order['fee_amount']);
            $arrayTotalsRecebido[$year][$month] += $order['net_amount'];

        }

        echo json_encode($arrayTotalsRecebido);
    }
}
