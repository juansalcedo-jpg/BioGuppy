<?php

namespace BioGuppy\Controller\ReportesTer;

use BioGuppy\Model\ReporteTer\ReporteTer;
use Dompdf\Dompdf;
use Dompdf\Options;
use PDO;

class ReportesTerController
{

    // ---------------------------------------------------------------
    // VISTA PRINCIPAL
    // ---------------------------------------------------------------
    public function listRepoTer()
    {
        include_once __DIR__
            . '/../../../view/ReportesTer/listRepoTer.php';
    }


    // ---------------------------------------------------------------
    // CONSULTA SEGURA (mismo patrón que ActividadesTerController)
    // Evita que un error de PHP/PDO rompa el JSON de salida.
    // ---------------------------------------------------------------
    private function consultarSeguro($obj, $sql, $params = [])
    {
        try {
            return $obj->select($sql, $params);
        } catch (\Throwable $error) {
            error_log("Consulta fallida en ReportesTerController: " . $error->getMessage());
            return false;
        }
    }




    // ---------------------------------------------------------------
    // CONVERTIR UNA IMAGEN DEL PROYECTO A BASE64
    // (para incrustarla directo en el PDF sin depender de rutas
    //  absolutas que Dompdf a veces no puede resolver)
    // ---------------------------------------------------------------
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


    // ---------------------------------------------------------------
    // OBTENER DATOS SEGÚN EL TIPO DE REPORTE
    // (usado tanto por generar() como por exportarPdf())
    // ---------------------------------------------------------------
    private function obtenerDatosReporte($obj, $tipoReporte, $fechaDesde, $fechaHasta)
    {
        if ($tipoReporte === 'sitios') {

            $sql = "SELECT
                        s.fechacreacion::date AS fecha,
                        s.nombresitio AS sitio,
                        s.direccion AS ubicacion,
                        u.nombreusuario || ' ' || u.apellidousuario AS responsable,
                        s.estado
                    FROM tblsitio s
                    LEFT JOIN tblusuario u
                        ON u.codusuario = s.codusuario
                    WHERE s.fechacreacion::date
                        BETWEEN :fechaDesde AND :fechaHasta
                    ORDER BY s.fechacreacion ASC";

            $stmt = $this->consultarSeguro(
                $obj,
                $sql,
                [
                    ':fechaDesde' => $fechaDesde,
                    ':fechaHasta' => $fechaHasta
                ]
            );

            return [
                'titulo'   => 'Reporte de sitios',
                'columnas' => ['Fecha', 'Sitio', 'Ubicación', 'Responsable', 'Estado'],
                'campos'   => ['fecha', 'sitio', 'ubicacion', 'responsable', 'estado'],
                'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
            ];
        }


        if ($tipoReporte === 'actividad') {

            $sql = "SELECT
                        a.fecha,
                        ta.nombreactividad AS tipo,
                        s.nombresitio AS sitio,
                        u.nombreusuario || ' ' || u.apellidousuario AS responsable,
                        a.estado
                    FROM tblactividadterreno a
                    INNER JOIN tbltipoactividadterreno ta
                        ON ta.codtipoactividad = a.codtipoactividad
                    INNER JOIN tblsitio s
                        ON s.codsitio = a.codsitio
                    INNER JOIN tblusuario u
                        ON u.codusuario = a.codusuario
                    WHERE a.fecha
                        BETWEEN :fechaDesde AND :fechaHasta
                    ORDER BY a.fecha ASC, a.codactividad ASC";

            $stmt = $this->consultarSeguro(
                $obj,
                $sql,
                [
                    ':fechaDesde' => $fechaDesde,
                    ':fechaHasta' => $fechaHasta
                ]
            );

            return [
                'titulo'   => 'Por tipo de actividad',
                'columnas' => ['Fecha', 'Tipo', 'Sitio', 'Responsable', 'Estado'],
                'campos'   => ['fecha', 'tipo', 'sitio', 'responsable', 'estado'],
                'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
            ];
        }


        if ($tipoReporte === 'auxiliar') {

            $sql = "SELECT
                        a.fecha,
                        u.nombreusuario || ' ' || u.apellidousuario AS auxiliar,
                        s.nombresitio AS sitio,
                        ta.nombreactividad AS tipo,
                        a.estado
                    FROM tblactividadterreno a
                    INNER JOIN tbltipoactividadterreno ta
                        ON ta.codtipoactividad = a.codtipoactividad
                    INNER JOIN tblsitio s
                        ON s.codsitio = a.codsitio
                    INNER JOIN tblusuario u
                        ON u.codusuario = a.codusuario
                    WHERE a.fecha
                        BETWEEN :fechaDesde AND :fechaHasta
                    ORDER BY u.nombreusuario ASC, a.fecha ASC";

            $stmt = $this->consultarSeguro(
                $obj,
                $sql,
                [
                    ':fechaDesde' => $fechaDesde,
                    ':fechaHasta' => $fechaHasta
                ]
            );

            return [
                'titulo'   => 'Por auxiliar',
                'columnas' => ['Fecha', 'Auxiliar', 'Sitio', 'Tipo', 'Estado'],
                'campos'   => ['fecha', 'auxiliar', 'sitio', 'tipo', 'estado'],
                'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
            ];
        }


        if ($tipoReporte === 'deposito') {

            $sql = "SELECT
                        s.fechacreacion::date AS fecha,
                        s.nombresitio AS sitio,
                        td.nombretipodeposito AS tipo,
                        COUNT(a.codactividad) AS cantidad,
                        s.estado
                    FROM tblsitio s
                    INNER JOIN tbltipodeposito td
                        ON td.codtipodeposito = s.codtipodeposito
                    LEFT JOIN tblactividadterreno a
                        ON a.codsitio = s.codsitio
                        AND a.fecha BETWEEN :fechaDesde AND :fechaHasta
                    WHERE s.fechacreacion::date
                        BETWEEN :fechaDesde2 AND :fechaHasta2
                    GROUP BY s.fechacreacion, s.nombresitio, td.nombretipodeposito, s.estado
                    ORDER BY td.nombretipodeposito ASC";

            $stmt = $this->consultarSeguro(
                $obj,
                $sql,
                [
                    ':fechaDesde'  => $fechaDesde,
                    ':fechaHasta'  => $fechaHasta,
                    ':fechaDesde2' => $fechaDesde,
                    ':fechaHasta2' => $fechaHasta
                ]
            );

            return [
                'titulo'   => 'Por tipo de depósito',
                'columnas' => ['Fecha', 'Sitio', 'Tipo de depósito', 'Cantidad', 'Estado'],
                'campos'   => ['fecha', 'sitio', 'tipo', 'cantidad', 'estado'],
                'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
            ];
        }

        return null;
    }


    // ---------------------------------------------------------------
    // VALIDAR PARÁMETROS DEL REPORTE (usado por generar() y exportarPdf())
    // ---------------------------------------------------------------
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


    // ---------------------------------------------------------------
    // GENERAR REPORTE (JSON para la tabla en pantalla)
    // ---------------------------------------------------------------
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


    // ---------------------------------------------------------------
    // EXPORTAR REPORTE A PDF
    // ---------------------------------------------------------------
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


        // -----------------------------------------------------------
        // LOGOS (BioGuppy y Secretaría de Salud)
        // -----------------------------------------------------------
        $logoSistema = $this->imagenBase64(
            __DIR__ . '/../../../img/logo.jpeg'
        );

        $logoSecretaria = $this->imagenBase64(
            __DIR__ . '/../../../img/Logo_SecretariaSalud.png'
        );


        // -----------------------------------------------------------
        // ARMAR TABLA HTML PARA EL PDF
        // -----------------------------------------------------------
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



        // -----------------------------------------------------------
        // GENERAR EL PDF
        // -----------------------------------------------------------
        $opciones = new Options();
        $opciones->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($opciones);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();

        $nombreArchivo = 'reporte_terreno_' . $tipoReporte . '_' . date('Ymd_His') . '.pdf';

        $dompdf->stream($nombreArchivo, ['Attachment' => true]);
        exit();
    }
}