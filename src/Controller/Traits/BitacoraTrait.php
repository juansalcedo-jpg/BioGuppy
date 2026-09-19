<?php

namespace BioGuppy\Controller\Traits;

trait BitacoraTrait
{
    /**
     * Registra una acción (INSERT/UPDATE/DELETE/etc) en la bitácora del sistema.
     * $obj debe ser cualquier instancia de un Model que extienda MasterModel,
     * ya que solo se usa su método insert() para llamar al stored procedure.
     */
    private function registrarBitacora($obj, $accion, $modulo, $idregistro = null, $valoranterior = null, $valornuevo = null){

        $codusuario = $_SESSION['usu_id'] ?? null;

        if(empty($codusuario)){
            return;
        }

        $sql = "CALL sp_registrar_bitacora(:codusuario, :accion, :modulo, :idregistro, :valoranterior, :valornuevo)";

        try{
            $obj->insert($sql, [
                ':codusuario'    => $codusuario,
                ':accion'        => $accion,
                ':modulo'        => $modulo,
                ':idregistro'    => $idregistro,
                ':valoranterior' => $valoranterior,
                ':valornuevo'    => $valornuevo,
            ]);
        }catch(\Throwable $error){
            error_log("No se pudo registrar en bitácora: " . $error->getMessage());
        }

    }
}