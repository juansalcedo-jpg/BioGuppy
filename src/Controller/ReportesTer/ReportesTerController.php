<?php

namespace BioGuppy\Controller\ReportesTer;

use BioGuppy\Model\ReporteTer\ReporteTer;
use BioGuppy\Controller\ReportesTer\Strategies\SitiosStrategy;
use BioGuppy\Controller\ReportesTer\Strategies\ActividadStrategy;
use BioGuppy\Controller\ReportesTer\Strategies\AuxiliarStrategy;
use BioGuppy\Controller\ReportesTer\Strategies\DepositoStrategy;
use Dompdf\Dompdf;
use Dompdf\Options;
use PDO;

class ReportesTerController
{

    public function listRepoTer()
    {
        include_once __DIR__
            . '/../../../view/ReportesTer/listRepoTer.php';
    }

    private function consultarSeguro($obj, $sql, $params = [])
    {
        try {
            return $obj->select($sql, $params);
        } catch (\Throwable $error) {
            error_log("Consulta fallida en ReportesTerController: " . $error->getMessage());
            return false;
        }
    }


    private function imagenBase64($rutaAbsoluta)
    {
        if (!file_exists($rutaAbsoluta)) {
            return '';
        }

        $extension = strtolower(pathinfo($rutaAbsoluta, PATHINFO_EXTENSION));
        $mime = ($extension === 'jpg') ? 'jpeg' : $extension;

        $datos = file_get_contents($rutaAbsoluta);

        return 'data:image/' . $mime . ';base64,' . base64_encode($datos);
    }

    private function obtenerDatosReporte($obj, $tipoReporte, $fechaDesde, $fechaHasta)
    {
        $strategies = [
            'sitios'    => SitiosStrategy::class,
            'actividad' => ActividadStrategy::class,
            'auxiliar'  => AuxiliarStrategy::class,
            'deposito'  => DepositoStrategy::class
        ];

        $strategyClass = $strategies[$tipoReporte] ?? null;

        if ($strategyClass === null) {
            return null;
        }

        $strategy = new $strategyClass(
            \Closure::fromCallable([$this, 'consultarSeguro'])
        );

        return $strategy->generar(
            $obj,
            $fechaDesde,
            $fechaHasta
        );
    }

    private function validarParametros($tipoReporte, $fechaDesde, $fechaHasta, &$mensajeError)
    {
        if (empty($tipoReporte) || empty($fechaDesde) || empty($fechaHasta)) {
            $mensajeError = 'Debe seleccionar el tipo de reporte y las fechas.';
            return false;
        }

        $regexFecha = '/^\d{4}-\d{2}-\d{2}$/';

        if (!preg_match($regexFecha, $fechaDesde) || !preg_match($regexFecha, $fechaHasta)) {
            $mensajeError = 'Las fechas ingresadas no son válidas.';
            return false;
        }

        $regexTipo = '/^(sitios|actividad|auxiliar|deposito)$/';

        if (!preg_match($regexTipo, $tipoReporte)) {
            $mensajeError = 'El tipo de reporte no es válido.';
            return false;
        }

        if ($fechaDesde > $fechaHasta) {
            $mensajeError = 'La fecha desde no puede ser mayor que la fecha hasta.';
            return false;
        }

        return true;
    }


    // GENERAR REPORTE (JSON para la tabla en pantalla)
    public function generar()
    {
        $obj = new ReporteTer();

        $tipoReporte = trim($_POST['tipoReporte'] ?? '');
        $fechaDesde  = trim($_POST['fechaDesde'] ?? '');
        $fechaHasta  = trim($_POST['fechaHasta'] ?? '');

        $mensajeError = '';

        if (!$this->validarParametros($tipoReporte, $fechaDesde, $fechaHasta, $mensajeError)) {

            echo json_encode(
                ['ok' => false, 'mensaje' => $mensajeError],
                JSON_UNESCAPED_UNICODE
            );

            exit();
        }

        try {

            $reporte = $this->obtenerDatosReporte($obj, $tipoReporte, $fechaDesde, $fechaHasta);

            echo json_encode(
                [
                    'ok'     => true,
                    'tipo'   => $tipoReporte,
                    'titulo' => $reporte['titulo'],
                    'datos'  => $reporte['datos']
                ],
                JSON_UNESCAPED_UNICODE
            );

        } catch (\Throwable $error) {

            error_log("Error generando reporte de terreno: " . $error->getMessage());

            echo json_encode(
                [
                    'ok' => false,
                    'mensaje' => 'Ocurrió un error al generar el reporte. Intenta de nuevo.'
                ],
                JSON_UNESCAPED_UNICODE
            );
        }

        exit();
    }

    // Generar reporte PDF
    public function exportarPdf()
    {
        $obj = new ReporteTer();

        $tipoReporte = trim($_GET['tipoReporte'] ?? '');
        $fechaDesde  = trim($_GET['fechaDesde'] ?? '');
        $fechaHasta  = trim($_GET['fechaHasta'] ?? '');

        $mensajeError = '';

        if (!$this->validarParametros($tipoReporte, $fechaDesde, $fechaHasta, $mensajeError)) {
            $_SESSION['error'] = $mensajeError;
            redirect(getUrl('ReportesTer', 'ReportesTer', 'listRepoTer'));
            exit();
        }

        try {

            $reporte = $this->obtenerDatosReporte($obj, $tipoReporte, $fechaDesde, $fechaHasta);

        } catch (\Throwable $error) {

            error_log("Error exportando PDF de terreno: " . $error->getMessage());
            $_SESSION['error'] = 'Ocurrió un error al generar el PDF.';
            redirect(getUrl('ReportesTer', 'ReportesTer', 'listRepoTer'));
            exit();
        }

        //Logos
        $logoSistema = $this->imagenBase64(
            __DIR__ . '/../../../img/logo.jpeg'
        );

        $logoSecretaria = $this->imagenBase64(
            __DIR__ . '/../../../img/Logo_SecretariaSalud.png'
        );

        //Armar tabla
        $html = '<html><head><meta charset="utf-8">
            <style>
                @page {
                    margin: 210px 40px 90px 40px;
                }

                body {
                    font-family: Helvetica, Arial, sans-serif;
                    font-size: 12px;
                    color: #222;
                }

                header {
                    position: fixed;
                    top: -190px;
                    left: 0px;
                    right: 0px;
                    height: 170px;
                }

                header img.logo-bioguppy {
                    height: 140px;
                }

                header img.logo-secretaria {
                    height: 190px;
                }

                header td {
                    width: 33%;
                    vertical-align: middle;
                }

                h2 {
                    margin-bottom: 2px;
                    text-align: center;
                }

                p.rango {
                    color: #555;
                    margin-top: 0;
                    margin-bottom: 16px;
                    text-align: center;
                }

                table.datos {
                    width: 100%;
                    border-collapse: collapse;
                }

                table.datos th {
                    background: #212529;
                    color: #fff;
                    padding: 6px;
                    text-align: left;
                }

                table.datos td {
                    padding: 6px;
                    border-bottom: 1px solid #ddd;
                }

                table.datos tr:nth-child(even) {
                    background: #f5f5f5;
                }

                footer {
                    position: fixed;
                    bottom: -70px;
                    left: 0px;
                    right: 0px;
                    height: 60px;
                    font-size: 11px;
                    color: #666;
                    text-align: center;
                    border-top: 1px solid #ddd;
                    padding-top: 8px;
                    line-height: 1.6;
                }
            </style>
            </head><body>

            <header>
                <table style="width:100%; border:none;">
                    <tr>
                        <td style="text-align:left;">'
                            . ($logoSistema ? '<img class="logo-bioguppy" src="' . $logoSistema . '">' : '')
                            . '</td>
                        <td></td>
                        <td style="text-align:right;">'
                            . ($logoSecretaria ? '<img class="logo-secretaria" src="' . $logoSecretaria . '">' : '')
                            . '</td>
                    </tr>
                </table>
            </header>

            <footer>
                Documento generado automáticamente por el Sistema BioGuppy — Monitoreo y Control Biológico del Dengue.<br>
                Uso institucional. Consolidación de información de campo y zoocriadero. Todos los derechos reservados &copy; ' . date('Y') . '.<br>
                Contacto: bioguppy@gmail.com
            </footer>
        ';

        $html .= '<h2>Reportes Terreno — ' . htmlspecialchars($reporte['titulo']) . '</h2>';
        $html .= '<p class="rango">Del ' . htmlspecialchars($fechaDesde) . ' al ' . htmlspecialchars($fechaHasta) . '</p>';

        $html .= '<table class="datos"><thead><tr>';
        foreach ($reporte['columnas'] as $columna) {
            $html .= '<th>' . htmlspecialchars($columna) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        if (empty($reporte['datos'])) {

            $colspan = count($reporte['columnas']);
            $html .= '<tr><td colspan="' . $colspan . '" style="text-align:center; padding:20px;">
                No se encontraron registros para la consulta seleccionada.
            </td></tr>';

        } else {

            foreach ($reporte['datos'] as $fila) {
                $html .= '<tr>';
                foreach ($reporte['campos'] as $campo) {

                    $valor = $fila[$campo] ?? '';

                    if ($campo === 'estado') {
                        $valor = ($valor === 'A') ? 'Activo' : 'Inactivo';
                    }

                    $html .= '<td>' . htmlspecialchars((string) $valor) . '</td>';
                }
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table></body></html>';

        //Generar Pdf
        $opciones = new Options();
        $opciones->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($opciones);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();

        $nombreArchivo = 'reporte_terreno_' . $tipoReporte . '_' . date('Ymd') . '.pdf';

        $dompdf->stream($nombreArchivo, ['Attachment' => true]);
        exit();
    }
}