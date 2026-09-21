<?php

namespace BioGuppy\Controller\DashBoard;

require_once __DIR__ . '/../../../vendor/autoload.php';

use BioGuppy\Model\DashBoard\DashBoardModel;
use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\BarPlot;
use Amenadiel\JpGraph\Plot\GroupBarPlot;
use Amenadiel\JpGraph\Graph\PieGraph;
use Amenadiel\JpGraph\Plot\PiePlot;
use PDO;

class DashBoardController
{

    
    public function listDashboard()
    {
        $obj = new DashBoardModel();

        $sqlTanques = "SELECT COUNT(*) AS tanques_activos
                       FROM tblzootanque
                       WHERE estado = 'A'";
        $resultT = $obj->select($sqlTanques);
        $tanques = $resultT->fetch(PDO::FETCH_ASSOC);

        $sqlSitios = "SELECT COUNT(DISTINCT codsitio) AS sitios_visitados
                      FROM tblactividadterreno
                      WHERE estado = 'A'
                      AND DATE_TRUNC('month', fecha) = DATE_TRUNC('month', CURRENT_DATE)";
        $results = $obj->select($sqlSitios);
        $sitios = $results->fetch(PDO::FETCH_ASSOC);

        $sqlActividades = "SELECT COUNT(*) AS actividades_mes
            FROM (
                SELECT codactividad, fecha FROM tblactividadterreno
                WHERE estado = 'A'
                AND DATE_TRUNC('month', fecha) = DATE_TRUNC('month', CURRENT_DATE)
                UNION ALL
                SELECT codactividad, fecha FROM tblactividadzoo
                WHERE estado = 'A'
                AND DATE_TRUNC('month', fecha) = DATE_TRUNC('month', CURRENT_DATE)
            ) AS todas";
        $resultA = $obj->select($sqlActividades);
        $actividades = $resultA->fetch(PDO::FETCH_ASSOC);

        $sqlLarvas = "SELECT COUNT(DISTINCT codsitio) AS focos_larvas
                      FROM tblactividadterreno
                      WHERE estado = 'A'
                      AND (larvasaedes = 'S' OR pupas = 'S' OR larvasculex = 'S')
                      AND DATE_TRUNC('month', fecha) = DATE_TRUNC('month', CURRENT_DATE)";
        $resultL = $obj->select($sqlLarvas);
        $larvas = $resultL->fetch(PDO::FETCH_ASSOC);

        
        include_once __DIR__ . '/../../../view/DashBoard/DashBoard.php';
    }

    
    public function graficaBarras()
    {
        error_reporting(0);
        ini_set('display_errors', 0);

        $obj = new DashBoardModel();

        $sql = "SELECT TO_CHAR(fecha, 'Mon') AS mes,
                       COALESCE(SUM(pecesnacidos), 0) AS nacidos,
                       COALESCE(SUM(pecesmuertos), 0) AS muertos
                FROM tblactividadzoo
                WHERE estado = 'A'
                AND DATE_TRUNC('month', fecha) BETWEEN DATE_TRUNC('month', CURRENT_DATE - INTERVAL '6 months')
                                                   AND DATE_TRUNC('month', CURRENT_DATE)
                GROUP BY mes, DATE_TRUNC('month', fecha)
                ORDER BY DATE_TRUNC('month', fecha)";
        $result = $obj->select($sql)->fetchAll(PDO::FETCH_ASSOC);

        $meses = [];
        $nacidos = [];
        $muertos = [];

        if (!empty($result)) {
            foreach ($result as $row) {
                $meses[]   = trim($row['mes']);
                $nacidos[] = (int)($row['nacidos'] ?? 0);
                $muertos[] = (int)($row['muertos'] ?? 0);
            }
        }

        if (empty($meses) || empty($nacidos) || empty($muertos)) {
            $meses   = ['Sin datos'];
            $nacidos = [0];
            $muertos = [0];
        }

        $graph = new Graph(800, 400);
        $graph->SetScale('textlin');
        $graph->xaxis->SetTickLabels($meses);
        $graph->title->Set('Producción mensual de guppies');

        $barNacidos = new BarPlot($nacidos);
        $barNacidos->SetFillColor('#0d6efd');
        $barNacidos->SetLegend('Nacidos');

        $barMuertos = new BarPlot($muertos);
        $barMuertos->SetFillColor('#dc3545');
        $barMuertos->SetLegend('Muertos');

        $group = new GroupBarPlot([$barNacidos, $barMuertos]);
        $graph->Add($group);

        // Limpieza de cualquier buffer previo antes de enviar la imagen
        if (ob_get_length()) {
            ob_clean();
        }

        $graph->Stroke();
        exit;
    }

    public function graficaPastel()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        try {
            $obj = new DashBoardModel();

            $sql = "SELECT nombreactividad, COUNT(*) AS total
                FROM (
                    SELECT t.nombreactividad 
                    FROM tblactividadterreno a
                    INNER JOIN tbltipoactividadterreno t ON a.codtipoactividad = t.codtipoactividad
                    WHERE a.estado = 'A'
                    UNION ALL
                    SELECT z.nombreactividad 
                    FROM tblactividadzoo a
                    INNER JOIN tbltipoactividadzoo z ON a.codtipoactividad = z.codtipoactividad
                    WHERE a.estado = 'A'
                ) AS union_actividades
                WHERE nombreactividad IS NOT NULL AND TRIM(nombreactividad) != ''
                GROUP BY nombreactividad
                ORDER BY total DESC";

            $result = $obj->select($sql);
            $data = $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];

            $etiquetas = [];
            $valores   = [];

            if (!empty($data)) {
                foreach ($data as $row) {
                    $total = (int)$row['total'];
                    if ($total > 0) {
                        // Nombre de la actividad que aparecerá en la gráfica
                        $etiquetas[] = trim($row['nombreactividad']) . "\n(%.1f%%)";
                        $valores[]   = $total;
                    }
                }
            }

            if (empty($valores)) {
                $etiquetas = ["Sin datos\n(%.1f%%)"];
                $valores   = [1];
            }

            // Aumentamos las dimensiones del lienzo a 360x300 px
            $graph = new PieGraph(360, 300);
            $graph->SetMarginColor('white');
            $graph->SetFrame(false);

            // Crear Plot de Pastel
            $pie = new PiePlot($valores);

            // Asignar el nombre del área/tipo de actividad directamente al gráfico
            $pie->SetLabels($etiquetas);

            // Aumentar el tamaño del círculo pastel
            $pie->SetSize(0.50);
            $pie->SetCenter(0.5, 0.5);

            // Posición de la etiqueta sobre las porciones
            $pie->SetLabelPos(0.55);

            // Paleta de colores
            $colores = ['#3b82f6', '#38bdf8', '#34d399', '#fbbf24', '#a855f7', '#94a3b8'];
            $pie->SetSliceColors($colores);

            $graph->Add($pie);

            if (ob_get_length()) {
                ob_clean();
            }

            header("Content-Type: image/png");
            $graph->Stroke();
            exit;
        } catch (\Throwable $e) {
            if (ob_get_length()) {
                ob_clean();
            }
            header("Content-Type: text/html; charset=utf-8");
            echo "<b>Error:</b> " . $e->getMessage();
            exit;
        }
    }
}