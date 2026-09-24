<?php

namespace BioGuppy\Controller\auditoria;

use BioGuppy\Model\Auditoria\auditoriaModel;
use PDO;

class AuditoriaController{

    // Vista principal: solo filtra por rango de fechas (botón "Filtrar")
    public function listAuditoria(){

        $obj = new auditoriaModel();

        $desde = trim($_GET['desde'] ?? '');
        $hasta = trim($_GET['hasta'] ?? '');

        $resultAuditoria = $this->consultarAuditoria($obj, $desde, $hasta, '');

        include_once __DIR__ . '/../../../view/auditoriaSistema/ListAuditoria.php';
    }

    // Petición AJAX: filtra por usuario mientras se escribe,
    // respetando el rango de fechas que esté aplicado
    public function filtro(){

        $obj = new auditoriaModel();

        $buscar = trim($_GET['buscar'] ?? '');
        $desde  = trim($_GET['desde'] ?? '');
        $hasta  = trim($_GET['hasta'] ?? '');

        $resultAuditoria = $this->consultarAuditoria($obj, $desde, $hasta, $buscar);

        include_once __DIR__ . '/../../../view/auditoriaSistema/filaAuditoria.php';
    }

    private function consultarAuditoria($obj, $desde, $hasta, $usuario){

        $sql = "SELECT
                    b.fecha,
                    CONCAT(u.nombreusuario, ' ', u.apellidousuario) AS usuario,
                    b.accion,
                    b.tablaafectada AS modulo,
                    b.valoranterior,
                    b.valornuevo
                FROM tblbitacora b
                INNER JOIN tblusuario u ON u.codusuario = b.codusuario
                WHERE 1 = 1";

        $params = [];

        if ($desde !== '') {
            $sql .= " AND b.fecha::date >= :desde";
            $params[':desde'] = $desde;
        }

        if ($hasta !== '') {
            $sql .= " AND b.fecha::date <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if ($usuario !== '') {
            // Busca por nombre, apellido o nombre completo (ej: "Juan Salcedo")
            $sql .= " AND CONCAT(u.nombreusuario, ' ', u.apellidousuario) ILIKE :usuario";
            $params[':usuario'] = "%$usuario%";
        }

        $sql .= " ORDER BY b.fecha DESC";

        try {
            return $obj->select($sql, $params);
        } catch (\Throwable $error) {
            error_log("Error en auditoría: " . $error->getMessage());
            return false;
        }
    }

}
