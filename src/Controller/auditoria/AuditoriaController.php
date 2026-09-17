<?php

namespace BioGuppy\Controller\auditoria;

use BioGuppy\Model\Auditoria\auditoriaModel;
use PDO;

class AuditoriaController{

    public function listAuditoria(){

        $obj = new auditoriaModel();

        $desde = $_GET['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? '';
        $usuario = $_GET['usuario'] ?? '';

        $sql = "SELECT
                    b.fecha,
                    CONCAT(u.nombreusuario, ' ', u.apellidousuario) AS usuario,
                    b.accion,
                    b.tablaafectada AS modulo,
                    COALESCE(b.valornuevo, b.valoranterior, '') AS detalle
                FROM tblbitacora b
                INNER JOIN tblusuario u ON u.codusuario = b.codusuario
                WHERE 1 = 1";

        $params = [];

        if (!empty($desde)) {
            $sql .= " AND b.fecha::date >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND b.fecha::date <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if (!empty($usuario)) {
            $sql .= " AND (u.nombreusuario ILIKE :usuario OR u.apellidousuario ILIKE :usuario)";
            $params[':usuario'] = "%$usuario%";
        }

        $sql .= " ORDER BY b.fecha DESC";

        try {
            $resultAuditoria = $obj->select($sql, $params);
        } catch (\Throwable $error) {
            die("ERROR SQL: " . $error->getMessage());
        }

        include_once __DIR__ . '/../../../view/auditoriaSistema/listAuditoria.php';

    }

}