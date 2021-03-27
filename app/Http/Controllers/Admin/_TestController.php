<?php


namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderStatus;
use MercadoPago\Payment;
use MercadoPago\SDK;
use Google_Client;
use Google_Service_Analytics;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_ReportRequest;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_Dimension;

class _TestController
{
    private $order;
    private $order_status;
    private $google_client;

    public function __construct(Order $order, OrderStatus $order_status, Google_Client $google_client)
    {
        session_start();
        $this->order        = $order;
        $this->order_status = $order_status;
        $this->google_client= $google_client;
    }

    public function testNotification()
    {
        abort(404); // desativado

        $request = '{"action":"payment.updated","api_version":"v1","data":{"id":"7880627975"},"date_created":"2020-08-03T02:05:18Z","id":6363427605,"live_mode":true,"type":"payment","user_id":"158980362","data_id":"7880627975"}';
        $request = json_decode($request);

        $code = $request->data_id;
        $StatusWebHook = $request->action;
        $order = $this->order->getOrderMercadoPago($code);
        if (!$order) {
            // Cria log
            echo "Erro Notificação Mercado Pago - Pedido não encontrado com esse código: " . $code . "<br>";
            return;
        }
        else if ($order) {
            $order_id = (int)$order->id;

            if ($StatusWebHook == "payment.updated") {
                // recupera dados do mercado pago
                SDK::setAccessToken(PROD_TOKEN); // Either Production or SandBox AccessToken
                $payment = new Payment();
                try {
                    $dataPayment = $payment->find_by_id($code);
                } catch(Exception $e) {
                    echo "Consulta Pagamento - Encontrou um problema com o pedido: {$code} <br> Erro: "  . $e->getMessage() . '<br>';
                    return;
                }

                // Cria log
                echo "pegou os dados no mercado pago - Recuperou os dados corretamente<br>";

                $dateUpdate = $dataPayment->date_created;
                $status = $dataPayment->status;
                $codeStatus = (int)$this->deParaStatus($status);

                if ($codeStatus == 0) {
                    echo "status desconhecido - Chegou o status: {$codeStatus} para o pedido: {$order_id}, status desconhecido no sistema<br>";
                    return;
                }

                /** Se estiver cancelado, não receber mais atualização */
                if (
                    $this->order_status->getCountStatusForOrderAndStatusId($order_id, 7) > 0 ||
                    $this->order_status->getCountStatusForOrderAndStatusId($order_id, 99) > 0 ||
                    $this->order_status->getCountStatusForOrderAndStatusId($order_id, 55) > 0
                ) {
                    echo "pedido cancelado, não será mais atualizado - Chegou o status: {$codeStatus} para o pedido: {$order_id}<br>";
                    return;
                }

                /** Não atualizar novamente um status já existente */
                if ($this->order_status->getCountStatusForOrderAndStatusId($order_id, $codeStatus) > 0) {
                    echo "status duplicado - Chegou o status: {$codeStatus} para o pedido: {$order_id}, status já existe no sistema<br>";
                    return;
                }

                /** Cria status */
                $arrUpdate = [
                    'order_id' => $order_id,
                    'code' => $code,
                    'status' => $codeStatus,
                    'reference_order_id' => $order_id,
                    'date' => date('Y-m-d H:i:s', strtotime($dateUpdate))
                ];

                echo "UPDATE STATUS = ".json_encode($arrUpdate). '<br>';

                /** Se for pago, já deixar como aguardando envio */
                if ($codeStatus == 3) {
                    echo "Se for pago, já deixar como aguardando envio...<br>";
                    $arrUpdatePago = [
                        'order_id' => $order_id,
                        'code' => $code,
                        'status' => 50,
                        'reference_order_id' => $order_id,
                        'date' => date('Y-m-d H:i:s', strtotime($dateUpdate))
                    ];
                    echo "UPDATE STATUS = ".json_encode($arrUpdatePago). '<br>';
                }

                /** Volta estoque se pedido for cancelado */
                if ($codeStatus == 7) {
                    echo 'Volta estoque se pedido for cancelad<br>';
                }
            }

            echo '<br>FIM!!!';
        }
    }

    private function deParaStatus($status)
    {
        switch ($status) {
            case 'pending':
                return 1;
                break;
            case 'in_process':
                return 2;
                break;
            case 'approved':
            case 'authorized':
                return 3;
                break;
            case 'in_mediation':
                return 5;
                break;
            case 'rejected':
            case 'cancelled':
                return 7;
                break;
            case 'refunded':
            case 'charged_back':
                return 6;
            default:
                return 0;
                break;
        }
    }

    public function gogleAnalytics()
    {
        try {
            $this->google_client->setAuthConfig(storage_path('app/public/google/client_secret_296954161978-kk40mmeh3ohts52rmh4bis70qrekoidm.apps.googleusercontent.com.json'));
            $this->google_client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);
        } catch (\Exception $e) {
            dd($e);
        }
        // Handle authorization flow from the server.
        if (! isset($_GET['code'])) {
            $auth_url = $this->google_client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            $this->google_client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $this->google_client->getAccessToken();
            $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/admin/test/google-analytics';
            if (!$_SESSION['access_token'])
                header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }

        // If the user has already authorized this app then get an access token
        // else redirect to ask the user to authorize access to Google Analytics.
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            // Set the access token on the client.
                $access_token = $_SESSION['access_token']['access_token'];
                $this->google_client->setAccessToken($access_token);
//
//                // Create an authorized analytics service object.
                $analytics = new Google_Service_AnalyticsReporting($this->google_client);
//
//                // Call the Analytics Reporting API V4.
                $response = $this->getReport($analytics);
//            dd($response);
//
//                // Print the response.
                $this->printResults($response);
        } else {
//            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/test/google-analytics';
//            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
    }


    /**
     * Queries the Analytics Reporting API V4.
     *
     * @param service An authorized Analytics Reporting API V4 service object.
     * @return The Analytics Reporting API V4 response.
     */
    private function getReport($analytics) {

        // Replace with your view ID, for example XXXX.
        $VIEW_ID = "228768495";

        // Create the DateRange object.
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate("7daysAgo");
        $dateRange->setEndDate("today");

        // Create the Metrics object.
//        $sessions = new Google_Service_AnalyticsReporting_Metric();
//        $sessions->setExpression("ga:searchResultViews");
//        $sessions->setAlias("searchResultViews");

        // Create the Dimension object.
        $sessions = new Google_Service_AnalyticsReporting_Dimension();
        $sessions->setName("ga:referralPath");

        // Create the ReportRequest object.
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($VIEW_ID);
        $request->setDateRanges($dateRange);
        $request->setDimensions(array($sessions));
//        $request->setMetrics(array($sessions));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $request) );
        return $analytics->reports->batchGet( $body );
    }

    /**
     * Parses and prints the Analytics Reporting API V4 response.
     *
     * @param An Analytics Reporting API V4 response.
     */
    private function printResults($reports) {
        for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
            $report = $reports[ $reportIndex ];
            $header = $report->getColumnHeader();
            $dimensionHeaders = $header->getDimensions();
            $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
            $rows = $report->getData()->getRows();

            if (!$dimensionHeaders) $dimensionHeaders = array();

//            dd($header, $dimensionHeaders, $metricHeaders, $rows);

            for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                $row = $rows[ $rowIndex ];
                $dimensions = $row->getDimensions();
                $metrics = $row->getMetrics();
                for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
                    print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
                }

                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    for ($k = 0; $k < count($values); $k++) {
                        $entry = $metricHeaders[$k];
                        print($entry->getName() . ": " . $values[$k] . "\n");
                    }
                }
            }
        }
    }
}
