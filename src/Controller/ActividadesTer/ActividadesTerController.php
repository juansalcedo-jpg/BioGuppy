<?php

namespace BioGuppy\Controller\ActividadesTer;

use BioGuppy\Model\ActividadesTer\ActividadesTerModel;
use BioGuppy\Controller\Traits\BitacoraTrait;
use PDO;

class ActividadesTerController{
    use BitacoraTrait;

    private function consultarSeguro($obj,$sql,$params=[]){
        try{
            return $obj->select($sql,$params);
        }catch(\Throwable $error){
            error_log("Consulta fallida en ActividadesTerController: ".$error->getMessage());
            return false;
        }
    }

    private function error($mensaje,$funcion,$params=[]){
        $_SESSION['error']=$mensaje;
        redirect(getUrl('ActividadesTer','ActividadesTer',$funcion,$params));
        exit();
    }

    private function obtenerDepositosActivos($obj){
        return $this->consultarSeguro($obj,"SELECT s.codsitio AS id,s.nombresitio,td.nombretipodeposito AS tipodeposito
        FROM tblsitio s
        INNER JOIN tbltipodeposito td ON td.codtipodeposito=s.codtipodeposito
        WHERE s.estado='A' AND td.estado='A'
        ORDER BY s.nombresitio ASC");
    }

    private function obtenerCodTipoActividad($obj,$nombre){
        $resultado=$obj->select("SELECT codtipoactividad
        FROM tbltipoactividadterreno
        WHERE TRANSLATE(UPPER(nombreactividad),'ÁÉÍÓÚ','AEIOU')=TRANSLATE(UPPER(:nombre),'ÁÉÍÓÚ','AEIOU')
        LIMIT 1",[':nombre'=>$nombre])->fetch(PDO::FETCH_ASSOC);
        return $resultado?$resultado['codtipoactividad']:null;
    }

    private function detectarTipo($nombre){
        $tipo=strtoupper($nombre);
        if(strpos($tipo,'RESIEMBRA')!==false) return 'Resiembra';
        if(strpos($tipo,'INSPEC')!==false) return 'Inspeccion';
        if(strpos($tipo,'SEGUIMIENTO')!==false) return 'Seguimiento';
        if(strpos($tipo,'SIEMBRA')!==false) return 'Siembra';
        return null;
    }

    private function validarFechaHora($fecha,$hora,$funcion,$params=[],$ultimosDosDias=true){
        if(empty($fecha)) $this->error("Debe registrar la fecha de la actividad.",$funcion,$params);

        if($ultimosDosDias){
            $min=date('Y-m-d',strtotime('-2 days'));
            $max=date('Y-m-d');
            if($fecha<$min||$fecha>$max) $this->error("Solo puede registrar actividades de hoy o de los últimos 2 días.",$funcion,$params);
        }elseif($fecha>date('Y-m-d')){
            $this->error("La fecha no puede ser mayor a la fecha actual.",$funcion,$params);
        }

        if(empty($hora)) $this->error("Debe registrar la hora de la actividad.",$funcion,$params);
        if(!preg_match('/^(0[8-9]|1[0-7]):[0-5][0-9]$|^18:00$/',$hora)){
            $this->error("La hora debe estar entre las 08:00 y las 18:00.",$funcion,$params);
        }
    }

    private function validarEntero($valor,$nombre,$funcion,$params=[]){
        if($valor===null||$valor==='') $this->error("Debe ingresar ".$nombre.".",$funcion,$params);
        if(is_numeric($valor)&&$valor<0) $this->error(ucfirst($nombre)." no puede ser un número negativo.",$funcion,$params);
        if(!preg_match('/^(0|[1-9][0-9]*)$/',(string)$valor)){
            $this->error(ucfirst($nombre)." debe ser un número entero sin ceros a la izquierda.",$funcion,$params);
        }
    }

    private function datosActividad($tipo,$fuente,$funcion,$params=[],$actual=[]){
        $datos=[
            'ph'=>$actual['ph']??null,
            'temperatura'=>$actual['temperatura']??null,
            'larvasaedes'=>$actual['larvasaedes']??0,
            'pupas'=>$actual['pupas']??0,
            'larvasculex'=>$actual['larvasculex']??0,
            'peces'=>$actual['peces']??null,
            'larvas'=>$actual['larvas']??null,
            'cantidadhembras'=>$actual['cantidadhembras']??null,
            'cantidadmachos'=>$actual['cantidadmachos']??null,
            'tiempoaclimatacionmin'=>$actual['tiempoaclimatacionmin']??null,
            'volumenagualitros'=>$actual['volumenagualitros']??null,
            'recolectarempacar'=>$actual['recolectarempacar']??null
        ];

        if($tipo==='Inspeccion'){
            $ph=$fuente['ph']??null;
            $temperatura=$fuente['temperatura']??null;
            $aedes=$fuente['larvas_aedes']??null;
            $pupas=$fuente['pupas']??null;
            $culex=$fuente['larvas_culex']??null;

            if($ph===null||$ph==='') $this->error("Debe ingresar el pH del agua.",$funcion,$params);
            if(!is_numeric($ph)||$ph<0||$ph>14) $this->error("El pH debe estar entre 0 y 14.",$funcion,$params);
            if($temperatura===null||$temperatura==='') $this->error("Debe ingresar la temperatura.",$funcion,$params);
            if(!is_numeric($temperatura)||$temperatura<0||$temperatura>40) $this->error("La temperatura debe estar entre 0°C y 40°C.",$funcion,$params);

            $this->validarEntero($aedes,'la cantidad de larvas Aedes',$funcion,$params);
            $this->validarEntero($pupas,'la cantidad de pupas',$funcion,$params);
            $this->validarEntero($culex,'la cantidad de larvas Culex',$funcion,$params);

            $datos['ph']=$ph;
            $datos['temperatura']=$temperatura;
            $datos['larvasaedes']=$aedes;
            $datos['pupas']=$pupas;
            $datos['larvasculex']=$culex;
        }

        if($tipo==='Siembra'||$tipo==='Resiembra'){
            $hembras=$fuente['cantidad_hembras']??null;
            $machos=$fuente['cantidad_machos']??null;
            $tiempo=$fuente['tiempo_aclimatar']??null;
            $recolectar=$fuente['recolectar_empacar']??null;

            $this->validarEntero($hembras,'la cantidad de hembras',$funcion,$params);
            $this->validarEntero($machos,'la cantidad de machos',$funcion,$params);
            $this->validarEntero($tiempo,'el tiempo de aclimatación',$funcion,$params);

            if(((int)$hembras+(int)$machos)<=0) $this->error("Debe registrar al menos un guppy, hembra o macho.",$funcion,$params);
            if($recolectar!=='S'&&$recolectar!=='N') $this->error("Debe seleccionar si se recolecta y empaca.",$funcion,$params);

            $datos['cantidadhembras']=$hembras;
            $datos['cantidadmachos']=$machos;
            $datos['tiempoaclimatacionmin']=$tiempo;
            $datos['recolectarempacar']=$recolectar;

            if($tipo==='Siembra'){
                $volumen=$fuente['volumen_agua']??null;
                $this->validarEntero($volumen,'el volumen de agua',$funcion,$params);
                $datos['volumenagualitros']=$volumen;
            }
        }

        if($tipo==='Seguimiento'){
            $peces=$fuente['peces']??null;
            $larvas=$fuente['larvas']??null;
            if($peces!=='S'&&$peces!=='N') $this->error("Debe seleccionar si se evidencia presencia de peces.",$funcion,$params);
            if($larvas!=='S'&&$larvas!=='N') $this->error("Debe seleccionar si se evidencia presencia de larvas.",$funcion,$params);
            $datos['peces']=$peces;
            $datos['larvas']=$larvas;
        }

        return $datos;
    }

    private function sqlMisActividades($extra=''){
        return "SELECT a.codactividad AS id,a.fecha,t.nombreactividad AS tipo_actividad,
        td.nombretipodeposito AS deposito,s.nombresitio AS sitio,a.estado
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        INNER JOIN tblsitio s ON s.codsitio=a.codsitio
        INNER JOIN tbltipodeposito td ON td.codtipodeposito=s.codtipodeposito
        WHERE a.codusuario=:codusuario
        $extra
        ORDER BY a.fecha DESC,a.codactividad DESC";
    }

    private function mostrarActividad($tipo){
        $obj=new ActividadesTerModel();
        $depositos=$this->obtenerDepositosActivos($obj);
        $modo='crear';
        $tipoActividad=$tipo;
        include_once __DIR__.'/../../../view/ActividadesTer/getUpdateAct.php';
    }

    public function Inspeccion(){ $this->mostrarActividad('Inspeccion'); }
    public function Siembra(){ $this->mostrarActividad('Siembra'); }
    public function Seguimiento(){ $this->mostrarActividad('Seguimiento'); }
    public function Resiembra(){ $this->mostrarActividad('Resiembra'); }

    public function postCreate(){
        $obj=new ActividadesTerModel();
        $tipo=$_POST['tipo_actividad']??null;

        if(!in_array($tipo,['Inspeccion','Siembra','Seguimiento','Resiembra'],true)){
            $this->error("Tipo de actividad no válido.",'Inspeccion');
        }

        $deposito=$_POST['deposito_id']??null;
        $fecha=$_POST['fecha_actividad']??null;
        $hora=$_POST['hora_actividad']??null;
        $observaciones=$_POST['observaciones']??'';

        if(empty($deposito)) $this->error("Debe seleccionar un depósito.",$tipo);
        $this->validarFechaHora($fecha,$hora,$tipo);

        $codTipo=$this->obtenerCodTipoActividad($obj,$tipo);
        if(!$codTipo) $this->error("No se encontró el tipo de actividad seleccionado.",$tipo);

        $datos=$this->datosActividad($tipo,$_POST,$tipo);

        $sql="INSERT INTO tblactividadterreno
        (codtipoactividad,codsitio,codusuario,fecha,hora,ph,temperatura,larvasaedes,pupas,larvasculex,peces,larvas,
        cantidadhembras,cantidadmachos,tiempoaclimatacionmin,volumenagualitros,recolectarempacar,observaciones,estado)
        VALUES
        (:codtipoactividad,:codsitio,:codusuario,:fecha,:hora,:ph,:temperatura,:larvasaedes,:pupas,:larvasculex,:peces,:larvas,
        :cantidadhembras,:cantidadmachos,:tiempoaclimatacionmin,:volumenagualitros,:recolectarempacar,:observaciones,'A')";

        try{
            $obj->insert($sql,[
                ':codtipoactividad'=>$codTipo,
                ':codsitio'=>$deposito,
                ':codusuario'=>$_SESSION['usu_id'],
                ':fecha'=>$fecha,
                ':hora'=>$hora,
                ':ph'=>$datos['ph'],
                ':temperatura'=>$datos['temperatura'],
                ':larvasaedes'=>$datos['larvasaedes'],
                ':pupas'=>$datos['pupas'],
                ':larvasculex'=>$datos['larvasculex'],
                ':peces'=>$datos['peces'],
                ':larvas'=>$datos['larvas'],
                ':cantidadhembras'=>$datos['cantidadhembras'],
                ':cantidadmachos'=>$datos['cantidadmachos'],
                ':tiempoaclimatacionmin'=>$datos['tiempoaclimatacionmin'],
                ':volumenagualitros'=>$datos['volumenagualitros'],
                ':recolectarempacar'=>$datos['recolectarempacar'],
                ':observaciones'=>$observaciones
            ]);
        }catch(\Throwable $error){
            error_log("Error al registrar actividad de terreno: ".$error->getMessage());
            $this->error("No fue posible guardar la actividad. Verifique los datos e intente nuevamente.",$tipo);
        }

        $nuevo=$obj->select("SELECT codactividad FROM tblactividadterreno
        WHERE codusuario=:codusuario ORDER BY codactividad DESC LIMIT 1",[
            ':codusuario'=>$_SESSION['usu_id']
        ])->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora($obj,'INSERT','ActividadesTer',$nuevo['codactividad']??null,null,$tipo.' — sitio #'.$deposito);
        $_SESSION['exito']="La actividad se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();
    }

    public function listMisActividades(){
        $obj=new ActividadesTerModel();
        $actividades=$this->consultarSeguro($obj,$this->sqlMisActividades(),[':codusuario'=>$_SESSION['usu_id']]);
        $depositos=$this->obtenerDepositosActivos($obj);
        include_once __DIR__.'/../../../view/ActividadesTer/listMisActividades.php';
    }

    public function filtro(){
        $obj=new ActividadesTerModel();
        $mes=!empty($_POST['mes'])?$_POST['mes']:null;
        $mesInicio=$mes?$mes.'-01':null;
        $mesFin=$mes?date('Y-m-d',strtotime($mesInicio.' +1 month')):null;
        $coddeposito=!empty($_POST['coddeposito'])?$_POST['coddeposito']:null;
        $tipo=!empty($_POST['tipoactividad'])?$_POST['tipoactividad']:null;

        $extra="AND (:mesInicio::date IS NULL OR (a.fecha>=:mesInicio::date AND a.fecha<:mesFin::date))
        AND (:coddeposito::integer IS NULL OR a.codsitio=:coddeposito::integer)
        AND (:tipoactividad::text IS NULL OR t.nombreactividad ILIKE :tipoactividad::text)";

        $actividades=$this->consultarSeguro($obj,$this->sqlMisActividades($extra),[
            ':codusuario'=>$_SESSION['usu_id'],
            ':mesInicio'=>$mesInicio,
            ':mesFin'=>$mesFin,
            ':coddeposito'=>$coddeposito,
            ':tipoactividad'=>$tipo
        ]);

        include_once __DIR__.'/../../../view/ActividadesTer/filtroMisActividades.php';
    }

    public function listActTer(){
        $obj=new ActividadesTerModel();
        $desde=$_GET['fechaDesde']??'';
        $hasta=$_GET['fechaHasta']??'';
        $sitio=$_GET['codsitio']??'';
        $tipo=$_GET['codtipoactividad']??'';
        $errorFechas=null;

        if($desde&&$hasta&&$desde>$hasta) $errorFechas="La fecha desde no puede ser mayor que la fecha hasta.";

        $cond=[];
        $params=[];
        if($desde&&!$errorFechas){ $cond[]="a.fecha>=:desde"; $params[':desde']=$desde; }
        if($hasta&&!$errorFechas){ $cond[]="a.fecha<=:hasta"; $params[':hasta']=$hasta; }
        if($sitio){ $cond[]="a.codsitio=:sitio"; $params[':sitio']=$sitio; }
        if($tipo){ $cond[]="a.codtipoactividad=:tipo"; $params[':tipo']=$tipo; }

        $where=$cond?"WHERE ".implode(" AND ",$cond):"";

        $actividades=$this->consultarSeguro($obj,"SELECT a.codactividad,a.fecha,t.nombreactividad AS tipo_actividad,
        s.nombresitio AS sitio,(u.nombreusuario || ' ' || u.apellidousuario) AS responsable,a.observaciones,a.estado
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        INNER JOIN tblsitio s ON s.codsitio=a.codsitio
        INNER JOIN tblusuario u ON u.codusuario=a.codusuario
        $where ORDER BY a.fecha DESC,a.codactividad DESC",$params);

        $sitios=$this->consultarSeguro($obj,"SELECT codsitio,nombresitio FROM tblsitio WHERE estado='A' ORDER BY nombresitio ASC");
        $tiposActividad=$this->consultarSeguro($obj,"SELECT codtipoactividad,nombreactividad FROM tbltipoactividadterreno WHERE estado='A' ORDER BY nombreactividad ASC");

        include_once __DIR__.'/../../../view/ActividadesTer/listActTer.php';
    }

    public function getUpdate(){
        $obj=new ActividadesTerModel();
        $id=$_GET['id']??null;
        if(empty($id)) $this->error("Registro no válido.",'listMisActividades');

        $actividad=$obj->select("SELECT a.*,t.nombreactividad
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        WHERE a.codactividad=:id",[':id'=>$id])->fetch(PDO::FETCH_ASSOC);

        if(!$actividad||$actividad['codusuario']!=$_SESSION['usu_id']){
            $this->error("No tiene permisos para editar este registro.",'listMisActividades');
        }

        $modo='editar';
        $tipoActividad=$this->detectarTipo($actividad['nombreactividad']);
        include_once __DIR__.'/../../../view/ActividadesTer/getUpdateAct.php';
    }

    public function postUpdate(){
        $obj=new ActividadesTerModel();
        $id=$_POST['codactividad']??null;
        if(empty($id)) $this->error("Registro no válido.",'listMisActividades');

        $actual=$obj->select("SELECT a.*,t.nombreactividad
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        WHERE a.codactividad=:id",[':id'=>$id])->fetch(PDO::FETCH_ASSOC);

        if(!$actual||$actual['codusuario']!=$_SESSION['usu_id']){
            $this->error("No tiene permisos para editar este registro.",'listMisActividades');
        }

        $tipo=$this->detectarTipo($actual['nombreactividad']);
        if(!$tipo) $this->error("Tipo de actividad no válido.",'listMisActividades');

        $retorno=['id'=>$id];
        $fecha=$_POST['fecha_actividad']??null;
        $hora=$_POST['hora_actividad']??null;
        $observaciones=$_POST['observaciones']??'';

        $this->validarFechaHora($fecha,$hora,'getUpdate',$retorno,false);
        $datos=$this->datosActividad($tipo,$_POST,'getUpdate',$retorno,$actual);

        $sql="UPDATE tblactividadterreno SET
        fecha=:fecha,hora=:hora,ph=:ph,temperatura=:temperatura,
        larvasaedes=:larvasaedes,pupas=:pupas,larvasculex=:larvasculex,
        peces=:peces,larvas=:larvas,cantidadhembras=:cantidadhembras,
        cantidadmachos=:cantidadmachos,tiempoaclimatacionmin=:tiempoaclimatacionmin,
        volumenagualitros=:volumenagualitros,recolectarempacar=:recolectarempacar,
        observaciones=:observaciones
        WHERE codactividad=:id";

        try{
            $obj->update($sql,[
                ':fecha'=>$fecha,
                ':hora'=>$hora,
                ':ph'=>$datos['ph'],
                ':temperatura'=>$datos['temperatura'],
                ':larvasaedes'=>$datos['larvasaedes'],
                ':pupas'=>$datos['pupas'],
                ':larvasculex'=>$datos['larvasculex'],
                ':peces'=>$datos['peces'],
                ':larvas'=>$datos['larvas'],
                ':cantidadhembras'=>$datos['cantidadhembras'],
                ':cantidadmachos'=>$datos['cantidadmachos'],
                ':tiempoaclimatacionmin'=>$datos['tiempoaclimatacionmin'],
                ':volumenagualitros'=>$datos['volumenagualitros'],
                ':recolectarempacar'=>$datos['recolectarempacar'],
                ':observaciones'=>$observaciones,
                ':id'=>$id
            ]);
        }catch(\Throwable $error){
            error_log("Error al actualizar actividad de terreno: ".$error->getMessage());
            $this->error("No fue posible actualizar la actividad. Verifique los datos e intente nuevamente.",'getUpdate',$retorno);
        }

        $this->registrarBitacora($obj,'UPDATE','ActividadesTer',$id,null,'Actividad #'.$id.' editada');
        $_SESSION['exito']="El registro se actualizó correctamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();
    }

    public function delete(){
        $obj=new ActividadesTerModel();
        $id=$_GET['id']??null;
        $rol=$_SESSION['nombre_rol']??'';
        $retorno=$rol==='Coordinador Control Biologico'?'listActTer':'listMisActividades';

        if(empty($id)) $this->error("Registro no válido.",$retorno);

        $actual=$obj->select("SELECT estado,codusuario FROM tblactividadterreno WHERE codactividad=:id",[
            ':id'=>$id
        ])->fetch(PDO::FETCH_ASSOC);

        $propietario=$actual&&$actual['codusuario']==$_SESSION['usu_id'];
        $coordinador=$rol==='Coordinador Control Biologico';

        if(!$actual||(!$propietario&&!$coordinador)){
            $this->error("No tiene permisos para modificar este registro.",$retorno);
        }

        $nuevoEstado=$actual['estado']==='A'?'I':'A';
        $obj->update("UPDATE tblactividadterreno SET estado=:estado WHERE codactividad=:id",[
            ':estado'=>$nuevoEstado,
            ':id'=>$id
        ]);

        $this->registrarBitacora($obj,'UPDATE','ActividadesTer',$id,$actual['estado'],$nuevoEstado);
        $_SESSION['exito']="El estado del registro se actualizó correctamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer',$retorno));
        exit();
    }
}
?>
