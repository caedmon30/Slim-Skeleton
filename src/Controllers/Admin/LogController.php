<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\WorkflowLogger;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class LogController
{
    private WorkflowLogger $logger;
    private Twig $view;

    public function __construct(WorkflowLogger $logger, Twig $view)
    {
        $this->logger = $logger;
        $this->view = $view;
    }

    public function index(Request $request, Response $response): Response
    {
        $logs = $this->logger->getLogs();
        return $this->view->render($response, 'admin/logs.html.twig', ['logs' => $logs]);
    }

    public function exportCsv(Request $request, Response $response): Response
    {
        $logs = $this->logger->getLogs();
        $csvContent = "Request ID,User,Previous State,New State,Timestamp\n";

        foreach ($logs as $log) {
            $csvContent .= "{$log['request_id']},{$log['user']},{$log['previous_state']},{$log['new_state']},{$log['timestamp']}\n";
        }

        $response->getBody()->write($csvContent);
        return $response->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="workflow_logs.csv"');
    }

    /**
     * @throws MpdfException
     */
    public function exportPdf(Request $request, Response $response): Response
    {
        $logs = $this->logger->getLogs();
        $html = "<h1>Workflow Logs</h1><table border='1'><tr><th>Request ID</th><th>User</th><th>Previous State</th><th>New State</th><th>Timestamp</th></tr>";

        foreach ($logs as $log) {
            $html .= "<tr><td>{$log['request_id']}</td><td>{$log['user']}</td><td>{$log['previous_state']}</td><td>{$log['new_state']}</td><td>{$log['timestamp']}</td></tr>";
        }

        $html .= "</table>";

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $response->getBody()->write($mpdf->Output('', 'S'));

        return $response->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="workflow_logs.pdf"');
    }
}
