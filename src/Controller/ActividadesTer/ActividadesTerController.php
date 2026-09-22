<?php

namespace BioGuppy\Controller\ActividadesTer;

use BioGuppy\Model\ActividadesTer\ActividadesTerModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

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

    private function obtenerCodTipoActividad($obj,$nombre){
        $sql="SELECT codtipoactividad
        FROM tbltipoactividadterreno
        WHERE UPPER(TRANSLATE(nombreactividad,'ÁÉÍÓÚ','AEIOU'))=UPPER(:nombre)
        LIMIT 1";

        $resultado=$obj->select($sql,[':nombre'=>$nombre])->fetch(PDO::FETCH_ASSOC);

        return $resultado?$resultado['codtipoactividad']:null;
    }

    private function obtenerDepositosActivos($obj){
        $sql="SELECT s.codsitio AS id,s.nombresitio,td.nombretipodeposito AS tipodeposito
        FROM tblsitio s
        INNER JOIN tbltipodeposito td ON td.codtipodeposito=s.codtipodeposito
        WHERE s.estado='A' AND td.estado='A'
        ORDER BY s.nombresitio ASC";

        return $this->consultarSeguro($obj,$sql);
    }

    public function Inspeccion(){

        $obj=new ActividadesTerModel();

        $depositos=$this->obtenerDepositosActivos($obj);

        $fechaMinima=date('Y-m-d',strtotime('-2 days'));
        $fechaMaxima=date('Y-m-d');

        include_once __DIR__.'/../../../view/ActividadesTer/Inspeccion.php';
    }

    public function postCreateInspeccion(){

        $obj=new ActividadesTerModel();

        $depositoId=$_POST['deposito_id']??null;
        $fecha=$_POST['fecha_actividad']??null;
        $hora=$_POST['hora_actividad']??null;
        $ph=$_POST['ph']??null;
        $temperatura=$_POST['temperatura']??null;
        $larvasAedes=$_POST['larvas_aedes']??null;
        $pupas=$_POST['pupas']??null;
        $larvasCulex=$_POST['larvas_culex']??null;
        $observaciones=$_POST['observaciones']??'';

        if(empty($depositoId)){
            $_SESSION['error']="Debe seleccionar un depósito.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if(empty($fecha)){
            $_SESSION['error']="Debe registrar la fecha de la actividad.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        $fechaMinima=date('Y-m-d',strtotime('-2 days'));
        $fechaMaxima=date('Y-m-d');

        if($fecha<$fechaMinima||$fecha>$fechaMaxima){
            $_SESSION['error']="Solo puede registrar actividades de hoy o de los últimos 2 días.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if(empty($hora)){
            $_SESSION['error']="Debe registrar la hora de la actividad.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if(!preg_match('/^(0[8-9]|1[0-7]):[0-5][0-9]$|^18:00$/',$hora)){
            $_SESSION['error']="La hora debe estar entre las 08:00 y las 18:00.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if($ph===null||$ph===''){
            $_SESSION['error']="Debe ingresar el pH del agua.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if(!is_numeric($ph)||$ph<0||$ph>14){
            $_SESSION['error']="El pH debe estar entre 0 y 14.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if($temperatura===null||$temperatura===''){
            $_SESSION['error']="Debe ingresar la temperatura.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if(!is_numeric($temperatura)||$temperatura<0||$temperatura>40){
            $_SESSION['error']="La temperatura debe estar entre 0°C y 40°C.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if($larvasAedes===null||$larvasAedes===''){
            $_SESSION['error']="Debe ingresar la cantidad de larvas Aedes.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if(!preg_match('/^(0|[1-9][0-9]*)$/',$larvasAedes)){
            $_SESSION['error']="Larvas Aedes debe ser un número entero sin ceros a la izquierda.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if($pupas===null||$pupas===''){
            $_SESSION['error']="Debe ingresar la cantidad de pupas.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if(!preg_match('/^(0|[1-9][0-9]*)$/',$pupas)){
            $_SESSION['error']="Pupas debe ser un número entero sin ceros a la izquierda.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if($larvasCulex===null||$larvasCulex===''){
            $_SESSION['error']="Debe ingresar la cantidad de larvas Culex.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if(!preg_match('/^(0|[1-9][0-9]*)$/',$larvasCulex)){
            $_SESSION['error']="Larvas Culex debe ser un número entero sin ceros a la izquierda.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        $codTipo=$this->obtenerCodTipoActividad($obj,'Inspeccion');

        $sql="INSERT INTO public.tblactividadterreno
        (codactividad,codtipoactividad,codsitio,codusuario,fecha,hora,ph,temperatura,larvasaedes,pupas,larvasculex,observaciones,fechacreacion,estado)
        VALUES
        (DEFAULT,:codtipoactividad,:codsitio,:codusuario,:fecha,:hora,:ph,:temperatura,:larvasaedes,:pupas,:larvasculex,:observaciones,DEFAULT,'A')";

        $obj->insert($sql,[
            ':codtipoactividad'=>$codTipo,
            ':codsitio'=>$depositoId,
            ':codusuario'=>$_SESSION['usu_id'],
            ':fecha'=>$fecha,
            ':hora'=>$hora,
            ':ph'=>$ph,
            ':temperatura'=>$temperatura,
            ':larvasaedes'=>$larvasAedes,
            ':pupas'=>$pupas,
            ':larvasculex'=>$larvasCulex,
            ':observaciones'=>$observaciones
        ]);

        $nuevo=$obj->select(
            "SELECT codactividad
            FROM tblactividadterreno
            WHERE codusuario=:codusuario
            ORDER BY codactividad DESC
            LIMIT 1",
            [':codusuario'=>$_SESSION['usu_id']]
        )->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'ActividadesTer',
            $nuevo['codactividad']??null,
            null,
            'Inspección — sitio #'.$depositoId
        );

        $_SESSION['exito']="La actividad de inspección se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();
    }

    public function Siembra(){

        $obj=new ActividadesTerModel();

        $depositos=$this->obtenerDepositosActivos($obj);

        $fechaMinima=date('Y-m-d',strtotime('-2 days'));
        $fechaMaxima=date('Y-m-d');

        include_once __DIR__.'/../../../view/ActividadesTer/Siembra.php';
    }

    public function postCreateSiembra(){

        $obj=new ActividadesTerModel();

        $depositoId=$_POST['deposito_id']??null;
        $fecha=$_POST['fecha_actividad']??null;
        $hora=$_POST['hora_actividad']??null;
        $hembras=$_POST['cantidad_hembras']??null;
        $machos=$_POST['cantidad_machos']??null;
        $tiempoAclimatar=$_POST['tiempo_aclimatar']??null;
        $volumenAgua=$_POST['volumen_agua']??null;
        $recolectarEmpacar=$_POST['recolectar_empacar']??null;
        $observaciones=$_POST['observaciones']??'';

        if(empty($depositoId)){
            $_SESSION['error']="Debe seleccionar un depósito.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if(empty($fecha)){
            $_SESSION['error']="Debe registrar la fecha de la actividad.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        $fechaMinima=date('Y-m-d',strtotime('-2 days'));
        $fechaMaxima=date('Y-m-d');

        if($fecha<$fechaMinima||$fecha>$fechaMaxima){
            $_SESSION['error']="Solo puede registrar actividades de hoy o de los últimos 2 días.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if(empty($hora)){
            $_SESSION['error']="Debe registrar la hora de la actividad.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if(!preg_match('/^(0[8-9]|1[0-7]):[0-5][0-9]$|^18:00$/',$hora)){
            $_SESSION['error']="La hora debe estar entre las 08:00 y las 18:00.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if($hembras===null||$hembras===''){
            $_SESSION['error']="Debe ingresar la cantidad de hembras.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if(!preg_match('/^(0|[1-9][0-9]*)$/',$hembras)){
            $_SESSION['error']="La cantidad de hembras debe ser un número entero sin ceros a la izquierda.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if($machos===null||$machos===''){
            $_SESSION['error']="Debe ingresar la cantidad de machos.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if(!preg_match('/^(0|[1-9][0-9]*)$/',$machos)){
            $_SESSION['error']="La cantidad de machos debe ser un número entero sin ceros a la izquierda.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if(($hembras+$machos)<=0){
            $_SESSION['error']="Debe registrar al menos un guppy (hembra o macho) en la siembra.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if($tiempoAclimatar===null||$tiempoAclimatar===''){
            $_SESSION['error']="Debe ingresar el tiempo de aclimatación.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if(!preg_match('/^(0|[1-9][0-9]*)$/',$tiempoAclimatar)){
            $_SESSION['error']="El tiempo de aclimatación debe ser un número entero sin ceros a la izquierda.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if($volumenAgua===null||$volumenAgua===''){
            $_SESSION['error']="Debe ingresar el volumen de agua.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if(!preg_match('/^(0|[1-9][0-9]*)$/',$volumenAgua)){
            $_SESSION['error']="El volumen de agua debe ser un número entero sin ceros a la izquierda.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if($recolectarEmpacar!=='S'&&$recolectarEmpacar!=='N'){
            $_SESSION['error']="Debe seleccionar si se recolecta y empaca.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        $codTipo=$this->obtenerCodTipoActividad($obj,'Siembra');

        $sql="INSERT INTO public.tblactividadterreno
        (codactividad,codtipoactividad,codsitio,codusuario,fecha,hora,cantidadhembras,cantidadmachos,tiempoaclimatacionmin,volumenagualitros,recolectarempacar,observaciones,fechacreacion,estado)
        VALUES
        (DEFAULT,:codtipoactividad,:codsitio,:codusuario,:fecha,:hora,:hembras,:machos,:tiempoaclimatacionmin,:volumenagualitros,:recolectarempacar,:observaciones,DEFAULT,'A')";

        $obj->insert($sql,[
            ':codtipoactividad'=>$codTipo,
            ':codsitio'=>$depositoId,
            ':codusuario'=>$_SESSION['usu_id'],
            ':fecha'=>$fecha,
            ':hora'=>$hora,
            ':hembras'=>$hembras,
            ':machos'=>$machos,
            ':tiempoaclimatacionmin'=>$tiempoAclimatar,
            ':volumenagualitros'=>$volumenAgua,
            ':recolectarempacar'=>$recolectarEmpacar,
            ':observaciones'=>$observaciones
        ]);

        $nuevo=$obj->select(
            "SELECT codactividad FROM tblactividadterreno
            WHERE codusuario=:codusuario
            ORDER BY codactividad DESC
            LIMIT 1",
            [':codusuario'=>$_SESSION['usu_id']]
        )->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'ActividadesTer',
            $nuevo['codactividad']??null,
            null,
            'Siembra — sitio #'.$depositoId
        );

        $_SESSION['exito']="La actividad de siembra se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();
    }

    public function Seguimiento(){

        $obj=new ActividadesTerModel();

        $depositos=$this->obtenerDepositosActivos($obj);

        $fechaMinima=date('Y-m-d',strtotime('-2 days'));
        $fechaMaxima=date('Y-m-d');

        include_once __DIR__.'/../../../view/ActividadesTer/Seguimiento.php';
    }

    public function postCreateSeguimiento(){

        $obj=new ActividadesTerModel();

        $depositoId=$_POST['deposito_id']??null;
        $fecha=$_POST['fecha_actividad']??null;
        $hora=$_POST['hora_actividad']??null;
        $peces=$_POST['peces']??null;
        $larvas=$_POST['larvas']??null;
        $observaciones=$_POST['observaciones']??'';

        if(empty($depositoId)){
            $_SESSION['error']="Debe seleccionar un depósito.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Seguimiento'));
            exit();
        }

        if(empty($fecha)){
            $_SESSION['error']="Debe registrar la fecha de la actividad.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Seguimiento'));
            exit();
        }

        $fechaMinima=date('Y-m-d',strtotime('-2 days'));
        $fechaMaxima=date('Y-m-d');

        if($fecha<$fechaMinima||$fecha>$fechaMaxima){
            $_SESSION['error']="Solo puede registrar actividades de hoy o de los últimos 2 días.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Seguimiento'));
            exit();
        }

        if(empty($hora)){
            $_SESSION['error']="Debe registrar la hora de la actividad.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Seguimiento'));
            exit();
        }

        if(!preg_match('/^(0[8-9]|1[0-7]):[0-5][0-9]$|^18:00$/',$hora)){
            $_SESSION['error']="La hora debe estar entre las 08:00 y las 18:00.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Seguimiento'));
            exit();
        }

        if($peces!=='S'&&$peces!=='N'){
            $_SESSION['error']="Debe seleccionar si se evidencia presencia de peces.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Seguimiento'));
            exit();
        }

        if($larvas!=='S'&&$larvas!=='N'){
            $_SESSION['error']="Debe seleccionar si se evidencia presencia de larvas.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Seguimiento'));
            exit();
        }

        $codTipo=$this->obtenerCodTipoActividad($obj,'Seguimiento');

        $sql="INSERT INTO public.tblactividadterreno
        (codactividad,codtipoactividad,codsitio,codusuario,fecha,hora,peces,larvas,observaciones,fechacreacion,estado)
        VALUES
        (DEFAULT,:codtipoactividad,:codsitio,:codusuario,:fecha,:hora,:peces,:larvas,:observaciones,DEFAULT,'A')";

        $obj->insert($sql,[
            ':codtipoactividad'=>$codTipo,
            ':codsitio'=>$depositoId,
            ':codusuario'=>$_SESSION['usu_id'],
            ':fecha'=>$fecha,
            ':hora'=>$hora,
            ':peces'=>$peces,
            ':larvas'=>$larvas,
            ':observaciones'=>$observaciones
        ]);

        $nuevo=$obj->select(
            "SELECT codactividad FROM tblactividadterreno
            WHERE codusuario=:codusuario
            ORDER BY codactividad DESC
            LIMIT 1",
            [':codusuario'=>$_SESSION['usu_id']]
        )->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'ActividadesTer',
            $nuevo['codactividad']??null,
            null,
            'Seguimiento — sitio #'.$depositoId
        );

        $_SESSION['exito']="La actividad de seguimiento se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();
    }

    public function Resiembra(){

        $obj=new ActividadesTerModel();

        $depositos=$this->obtenerDepositosActivos($obj);

        $fechaMinima=date('Y-m-d',strtotime('-2 days'));
        $fechaMaxima=date('Y-m-d');

        include_once __DIR__.'/../../../view/ActividadesTer/Resiembra.php';
    }

    public function postCreateResiembra(){

        $obj=new ActividadesTerModel();

        $depositoId=$_POST['deposito_id']??null;
        $fecha=$_POST['fecha_actividad']??null;
        $hora=$_POST['hora_actividad']??null;
        $hembras=$_POST['cantidad_hembras']??null;
        $machos=$_POST['cantidad_machos']??null;
        $tiempoAclimatar=$_POST['tiempo_aclimatar']??null;
        $recolectarEmpacar=$_POST['recolectar_empacar']??null;
        $observaciones=$_POST['observaciones']??'';

        if(empty($depositoId)){
            $_SESSION['error']="Debe seleccionar un depósito.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if(empty($fecha)){
            $_SESSION['error']="Debe registrar la fecha de la actividad.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        $fechaMinima=date('Y-m-d',strtotime('-2 days'));
        $fechaMaxima=date('Y-m-d');

        if($fecha<$fechaMinima||$fecha>$fechaMaxima){
            $_SESSION['error']="Solo puede registrar actividades de hoy o de los últimos 2 días.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if(empty($hora)){
            $_SESSION['error']="Debe registrar la hora de la actividad.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if(!preg_match('/^(0[8-9]|1[0-7]):[0-5][0-9]$|^18:00$/',$hora)){
            $_SESSION['error']="La hora debe estar entre las 08:00 y las 18:00.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if($hembras===null||$hembras===''){
            $_SESSION['error']="Debe ingresar la cantidad de hembras.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if(!preg_match('/^(0|[1-9][0-9]*)$/',$hembras)){
            $_SESSION['error']="La cantidad de hembras debe ser un número entero sin ceros a la izquierda.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if($machos===null||$machos===''){
            $_SESSION['error']="Debe ingresar la cantidad de machos.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if(!preg_match('/^(0|[1-9][0-9]*)$/',$machos)){
            $_SESSION['error']="La cantidad de machos debe ser un número entero sin ceros a la izquierda.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if(($hembras+$machos)<=0){
            $_SESSION['error']="Debe registrar al menos un guppy (hembra o macho) en la resiembra.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if($tiempoAclimatar===null||$tiempoAclimatar===''){
            $_SESSION['error']="Debe ingresar el tiempo de aclimatación.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if(!preg_match('/^(0|[1-9][0-9]*)$/',$tiempoAclimatar)){
            $_SESSION['error']="El tiempo de aclimatación debe ser un número entero sin ceros a la izquierda.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if($recolectarEmpacar!=='S'&&$recolectarEmpacar!=='N'){
            $_SESSION['error']="Debe seleccionar si se recolecta y empaca.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        $codTipo=$this->obtenerCodTipoActividad($obj,'Resiembra');

        $sql="INSERT INTO public.tblactividadterreno
        (codactividad,codtipoactividad,codsitio,codusuario,fecha,hora,cantidadhembras,cantidadmachos,tiempoaclimatacionmin,recolectarempacar,observaciones,fechacreacion,estado)
        VALUES
        (DEFAULT,:codtipoactividad,:codsitio,:codusuario,:fecha,:hora,:hembras,:machos,:tiempoaclimatacionmin,:recolectarempacar,:observaciones,DEFAULT,'A')";

        $obj->insert($sql,[
            ':codtipoactividad'=>$codTipo,
            ':codsitio'=>$depositoId,
            ':codusuario'=>$_SESSION['usu_id'],
            ':fecha'=>$fecha,
            ':hora'=>$hora,
            ':hembras'=>$hembras,
            ':machos'=>$machos,
            ':tiempoaclimatacionmin'=>$tiempoAclimatar,
            ':recolectarempacar'=>$recolectarEmpacar,
            ':observaciones'=>$observaciones
        ]);

        $nuevo=$obj->select(
            "SELECT codactividad FROM tblactividadterreno
            WHERE codusuario=:codusuario
            ORDER BY codactividad DESC
            LIMIT 1",
            [':codusuario'=>$_SESSION['usu_id']]
        )->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'ActividadesTer',
            $nuevo['codactividad']??null,
            null,
            'Resiembra — sitio #'.$depositoId
        );

        $_SESSION['exito']="La actividad de resiembra se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();
    }

    public function listMisActividades(){

        $obj=new ActividadesTerModel();

        $sql="SELECT
        a.codactividad AS id,
        a.fecha,
        t.nombreactividad AS tipo_actividad,
        td.nombretipodeposito AS deposito,
        s.nombresitio AS sitio,
        a.estado
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        INNER JOIN tblsitio s ON s.codsitio=a.codsitio
        INNER JOIN tbltipodeposito td ON td.codtipodeposito=s.codtipodeposito
        WHERE a.codusuario=:codusuario
        ORDER BY a.fecha DESC,a.codactividad DESC";

        $actividades=$this->consultarSeguro($obj,$sql,[
            ':codusuario'=>$_SESSION['usu_id']
        ]);

        $depositos=$this->obtenerDepositosActivos($obj);

        include_once __DIR__.'/../../../view/ActividadesTer/listMisActividades.php';
    }

    public function listActTer(){

        $obj=new ActividadesTerModel();

        $fechaDesde=$_GET['fechaDesde']??'';
        $fechaHasta=$_GET['fechaHasta']??'';
        $codSitio=$_GET['codsitio']??'';
        $codTipoActividad=$_GET['codtipoactividad']??'';

        $errorFechas=null;

        if(!empty($fechaDesde)&&!empty($fechaHasta)&&$fechaDesde>$fechaHasta){
            $errorFechas="La fecha desde no puede ser mayor que la fecha hasta.";
        }

        $condiciones=[];
        $parametros=[];

        if(!empty($fechaDesde)&&!$errorFechas){
            $condiciones[]="a.fecha >= :fechaDesde";
            $parametros[':fechaDesde']=$fechaDesde;
        }

        if(!empty($fechaHasta)&&!$errorFechas){
            $condiciones[]="a.fecha <= :fechaHasta";
            $parametros[':fechaHasta']=$fechaHasta;
        }

        if(!empty($codSitio)){
            $condiciones[]="s.codsitio = :codsitio";
            $parametros[':codsitio']=$codSitio;
        }

        if(!empty($codTipoActividad)){
            $condiciones[]="a.codtipoactividad = :codtipoactividad";
            $parametros[':codtipoactividad']=$codTipoActividad;
        }

        $where=count($condiciones)>0?"WHERE ".implode(" AND ",$condiciones):"";

        $sql="SELECT
        a.codactividad,
        a.fecha,
        t.nombreactividad AS tipo_actividad,
        s.nombresitio AS sitio,
        (u.nombreusuario || ' ' || u.apellidousuario) AS responsable,
        a.observaciones,
        a.estado
        FROM tblactividadterreno a
        JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        JOIN tblsitio s ON s.codsitio=a.codsitio
        JOIN tblusuario u ON u.codusuario=a.codusuario
        $where
        ORDER BY a.fecha DESC,a.codactividad DESC";

        $actividades=$this->consultarSeguro($obj,$sql,$parametros);

        $sitios=$this->consultarSeguro(
            $obj,
            "SELECT codsitio,nombresitio
            FROM tblsitio
            WHERE estado='A'
            ORDER BY nombresitio ASC"
        );

        $tiposActividad=$this->consultarSeguro(
            $obj,
            "SELECT codtipoactividad,nombreactividad
            FROM tbltipoactividadterreno
            WHERE estado='A'
            ORDER BY nombreactividad ASC"
        );

        include_once __DIR__.'/../../../view/ActividadesTer/listActTer.php';
    }

    public function filtro(){

        $obj=new ActividadesTerModel();

        $mes=$_POST['mes']??null;
        $mesInicio=null;
        $mesFin=null;

        if($mes){
            $mesInicio=$mes.'-01';
            $mesFin=date('Y-m-d',strtotime($mesInicio.' +1 month'));
        }

        $coddeposito=$_POST['coddeposito']??null;
        $tipoActividad=$_POST['tipoactividad']??null;

        $sql="SELECT
        a.codactividad AS id,
        a.fecha,
        t.nombreactividad AS tipo_actividad,
        td.nombretipodeposito AS deposito,
        s.nombresitio AS sitio,
        a.estado
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        INNER JOIN tblsitio s ON s.codsitio=a.codsitio
        INNER JOIN tbltipodeposito td ON td.codtipodeposito=s.codtipodeposito
        WHERE a.codusuario=:codusuario
        AND (:mesInicio::date IS NULL OR (a.fecha>=:mesInicio::date AND a.fecha<:mesFin::date))
        AND (:coddeposito::integer IS NULL OR a.codsitio=:coddeposito::integer)
        AND (:tipoactividad::text IS NULL OR t.nombreactividad ILIKE :tipoactividad::text)
        ORDER BY a.fecha DESC,a.codactividad DESC";

        $actividades=$this->consultarSeguro($obj,$sql,[
            ':codusuario'=>$_SESSION['usu_id'],
            ':mesInicio'=>$mesInicio,
            ':mesFin'=>$mesFin,
            ':coddeposito'=>$coddeposito,
            ':tipoactividad'=>$tipoActividad
        ]);

        include_once __DIR__.'/../../../view/ActividadesTer/filtroMisActividades.php';
    }

    public function getUpdate(){

        $obj=new ActividadesTerModel();

        $id=$_GET['id'];

        $sql="SELECT a.*,t.nombreactividad
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        WHERE a.codactividad=:id";

        $actividad=$obj->select($sql,[':id'=>$id])->fetch(PDO::FETCH_ASSOC);

        if(!$actividad||$actividad['codusuario']!=$_SESSION['usu_id']){
            $_SESSION['error']="No tiene permisos para editar este registro.";
            redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
            exit();
        }

        $depositos=$this->obtenerDepositosActivos($obj);

        include_once __DIR__.'/../../../view/ActividadesTer/getUpdateAct.php';
    }

    public function postUpdate(){

        $obj=new ActividadesTerModel();

        $codactividad=$_POST['codactividad']??null;

        $sqlActual="SELECT * FROM tblactividadterreno WHERE codactividad=:id";

        $actual=$obj->select($sqlActual,[
            ':id'=>$codactividad
        ])->fetch(PDO::FETCH_ASSOC);

        if(!$actual||$actual['codusuario']!=$_SESSION['usu_id']){
            $_SESSION['error']="No tiene permisos para editar este registro.";
            redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
            exit();
        }

        $fecha=$_POST['fecha_actividad']??null;
        $hora=$_POST['hora_actividad']??null;
        $observaciones=$_POST['observaciones']??'';

        if(empty($fecha)){
            $_SESSION['error']="La fecha es obligatoria.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        if(empty($hora)){
            $_SESSION['error']="Debe registrar la hora de la actividad.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        if($hora<'08:00'||$hora>'18:00'){
            $_SESSION['error']="La hora debe estar entre las 08:00 y las 18:00.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        if(isset($_POST['ph'])&&$_POST['ph']!==''&&(!is_numeric($_POST['ph'])||$_POST['ph']<0||$_POST['ph']>14)){
            $_SESSION['error']="El pH debe ser un número entre 0 y 14.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        if(isset($_POST['temperatura'])&&$_POST['temperatura']!==''&&(!is_numeric($_POST['temperatura'])||$_POST['temperatura']<0||$_POST['temperatura']>40)){
            $_SESSION['error']="La temperatura debe ser un número entre 0°C y 40°C.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        if(
            (isset($_POST['cantidad_hembras'])&&$_POST['cantidad_hembras']!==''&&(!is_numeric($_POST['cantidad_hembras'])||$_POST['cantidad_hembras']<0))
            ||
            (isset($_POST['cantidad_machos'])&&$_POST['cantidad_machos']!==''&&(!is_numeric($_POST['cantidad_machos'])||$_POST['cantidad_machos']<0))
        ){
            $_SESSION['error']="La cantidad de hembras y machos debe ser un número no negativo.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        $sql="UPDATE tblactividadterreno SET
        fecha=:fecha,
        hora=:hora,
        ph=:ph,
        temperatura=:temperatura,
        larvasaedes=:larvasaedes,
        pupas=:pupas,
        larvasculex=:larvasculex,
        larvas=:larvas,
        peces=:peces,
        cantidadhembras=:cantidadhembras,
        cantidadmachos=:cantidadmachos,
        tiempoaclimatacionmin=:tiempoaclimatacionmin,
        recolectarempacar=:recolectarempacar,
        observaciones=:observaciones
        WHERE codactividad=:codactividad";

        $obj->update($sql,[
            ':fecha'=>$fecha,
            ':hora'=>$hora,
            ':ph'=>(isset($_POST['ph'])&&$_POST['ph']!=='')?$_POST['ph']:$actual['ph'],
            ':temperatura'=>(isset($_POST['temperatura'])&&$_POST['temperatura']!=='')?$_POST['temperatura']:$actual['temperatura'],
            ':larvasaedes'=>(isset($_POST['larvas_aedes'])&&$_POST['larvas_aedes']!=='')?$_POST['larvas_aedes']:$actual['larvasaedes'],
            ':pupas'=>(isset($_POST['pupas'])&&$_POST['pupas']!=='')?$_POST['pupas']:$actual['pupas'],
            ':larvasculex'=>(isset($_POST['larvas_culex'])&&$_POST['larvas_culex']!=='')?$_POST['larvas_culex']:$actual['larvasculex'],
            ':larvas'=>$_POST['larvas']??$actual['larvas'],
            ':peces'=>$_POST['peces']??$actual['peces'],
            ':cantidadhembras'=>(isset($_POST['cantidad_hembras'])&&$_POST['cantidad_hembras']!=='')?$_POST['cantidad_hembras']:$actual['cantidadhembras'],
            ':cantidadmachos'=>(isset($_POST['cantidad_machos'])&&$_POST['cantidad_machos']!=='')?$_POST['cantidad_machos']:$actual['cantidadmachos'],
            ':tiempoaclimatacionmin'=>(isset($_POST['tiempo_aclimatar'])&&$_POST['tiempo_aclimatar']!=='')?$_POST['tiempo_aclimatar']:$actual['tiempoaclimatacionmin'],
            ':recolectarempacar'=>$_POST['recolectar_empacar']??$actual['recolectarempacar'],
            ':observaciones'=>$observaciones,
            ':codactividad'=>$codactividad
        ]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'ActividadesTer',
            $codactividad,
            null,
            'Actividad #'.$codactividad.' editada'
        );

        $_SESSION['exito']="El registro se actualizó correctamente.";

        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();
    }

    public function delete(){

        $obj=new ActividadesTerModel();

        $id=$_GET['id']??null;
        $rol=$_SESSION['nombre_rol']??'';

        $funcionRetorno=($rol==='Coordinador Control Biologico')
            ?'listActTer'
            :'listMisActividades';

        if(empty($id)){
            $_SESSION['error']="Registro no válido.";
            redirect(getUrl('ActividadesTer','ActividadesTer',$funcionRetorno));
            exit();
        }

        $actual=$obj->select(
            "SELECT estado,codusuario
            FROM tblactividadterreno
            WHERE codactividad=:id",
            [':id'=>$id]
        )->fetch(PDO::FETCH_ASSOC);

        $esPropietario=$actual&&$actual['codusuario']==$_SESSION['usu_id'];
        $esCoordinador=($rol==='Coordinador Control Biologico');

        if(!$actual||(!$esPropietario&&!$esCoordinador)){
            $_SESSION['error']="No tiene permisos para modificar este registro.";
            redirect(getUrl('ActividadesTer','ActividadesTer',$funcionRetorno));
            exit();
        }

        $nuevoEstado=($actual['estado']==='A')?'I':'A';

        $obj->update(
            "UPDATE tblactividadterreno
            SET estado=:estado
            WHERE codactividad=:id",
            [
                ':estado'=>$nuevoEstado,
                ':id'=>$id
            ]
        );

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'ActividadesTer',
            $id,
            $actual['estado']==='A'?'Activo':'Inactivo',
            $nuevoEstado==='A'?'Activo':'Inactivo'
        );

        $_SESSION['exito']="El estado del registro se actualizó correctamente.";

        redirect(getUrl('ActividadesTer','ActividadesTer',$funcionRetorno));
        exit();
    }
}