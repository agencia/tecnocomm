<?php

class Cotizacion_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }

    function get_cotizaciones() {
        $q = $this->db->query("SELECT * FROM subcotizacion order by identificador2");
        if ($q->num_rows() > 0)
            return $q->result_array();
        return false;
    }
    
    function set_up_consecutivo(){
        if($this->get_consecutivo() > 0)
        {
            $this->db->set("numero", "numero+1", FALSE);
            $this->db->where("anio", "YEAR(NOW())", false);
            $this->db->update("cot_consecutivo");
        } else{
            $this->db->set("anio", "YEAR(NOW())", FALSE);
            $this->db->set("numero", "1", FALSE);
            $this->db->insert("cot_consecutivo");
        }
        return $this->get_consecutivo();
    }
    
    function get_consecutivo(){
        $q = $this->db->query("SELECT numero FROM cot_consecutivo ORDER BY numero LIMIT 1");
        if ($q->num_rows() > 0){
            $r = $q->row();
            return $r->numero;
        }
        return 0;
    }

    function set_cotizacion_by_ip($idip, $consecutivo){
        //var_dump($idip);
        $this->set_up_consecutivo();
        $this->load->model("cliente_model");
        $idcliente = $this->cliente_model->get_ip_by_id($idip);
        $this->db->insert("cotizacion", array("idcliente" => $idcliente["idcliente"], "consecutivo" => $consecutivo, "idip" => $idip));
        $q = $this->db->get_where("cotizacion", array("consecutivo" => $consecutivo, "idip" => $idip));
        if ($q->num_rows() > 0)
            return $q->row_array();
        return false;
    }
    
    function set_subcotizacion($data){
        
            session_start();
        $cotizacion = $this->set_cotizacion_by_ip($data["idip"], $data["textfield"]);
        $this->load->model("cliente_model");
        $cliente = $this->cliente_model->get_cliente_by_idip($data["idip"]);
        //var_dump($data["idip"]);
        $sa = "";
        if(strlen($cotizacion["consecutivo"]) == 1)
            $sa = "00";
        elseif(strlen($cotizacion["consecutivo"]) == 2)
            $sa = "0";
        $identificador = "C-" . $sa . $cotizacion["consecutivo"] . "-" . date("y") . $cliente['abreviacion'];
        if(($data['suministro']==1) || ($data['suministro']==3)){
                $det="SERVICIO DE INSTALACION ";
                $monto=1;
        }
        else{
                $det="0";
                $monto=1;
        }
        $insertar = $data;
        $insertar["idcotizacion"] = $cotizacion["idcotizacion"];
        $insertar["identificador2"] = $identificador;
        $insertar["contacto"] = 1;
        $insertar["descrimano"] = $det;
        $insertar["monto"] = $monto;
        $insertar["marca"] = "TECNOCOMM";
        $insertar["codigo"] = "TECNOCOMM";
        $insertar["unidad"] = "SERV";
        $insertar["cantidad"] = "1.00";
        $insertar["usercreo"] = $_SESSION['MM_Userid'];
        $insertar["descuentoreal"] = $insertar["descuento"];
        $insertar["formapago"] = $insertar["forma"];
        $insertar["identificador"] = $insertar["textfield"];
        $insertar["tipoentrega"] = $insertar["entrega"];
        $insertar["utilidad_global"] = $insertar["utilidad"];
        $insertar["estado"] = 1;
        unset($insertar["suministro"]);
        unset($insertar["textfield"]);
        unset($insertar["utilidad"]);
        unset($insertar["cotext"]);
        unset($insertar["idip"]);
        unset($insertar["forma"]);
        unset($insertar["entrega"]);
        unset($insertar["button"]);
        unset($insertar["radiobutton"]);
        $this->db->set("fecha", "NOW()", false);
        $this->db->insert("subcotizacion", $insertar);
        $this->db->order_by("idsubcotizacion", "desc");
        $q = $this->db->get("subcotizacion");
        if ($q->num_rows() > 0)
            return $q->row_array();
        return false;
    }
    
    function get_subcotizacion_by_id($idsubcotizacion){
        //var_dump($idsubcotizacion);
        $q = $this->db->get_where("subcotizacion", array("idsubcotizacion"=>$idsubcotizacion));
        if ($q->num_rows() > 0)
            return $q->row_array();
        return false;
    }
    
    function get_partidas_by_idsub($idsubcotizacion){
        $q = $this->db->get_where("subcotizacionarticulo", array("idsubcotizacion"=>$idsubcotizacion));
        if ($q->num_rows() > 0)
            return $q->result_array();
        return false;
    }
    
    function get_partidas_total_by_idsub($idsubcotizacion){
        $q = $this->db->get_where("subcotizacionarticulo", array("idsubcotizacion"=>$idsubcotizacion));
        return $q->num_rows();
    }
    
    function duplicar($data){
        
        $partidas_originales = $this->get_partidas_by_idsub($data["idorigen"]);
        $insertar = array();
//        var_dump($partidas_originales);
        for($i=0;$i<count($partidas_originales); $i++){
            $partidas_originales[$i]["idsubcotizacion"] = $data["iddestino"];
            unset($partidas_originales[$i]["idsubcotizacionarticulo"]);
        }
        
        $this->db->insert_batch("subcotizacionarticulo", $partidas_originales);
    }
    
    function get_idip_by_idcotizacion($idcotizacion){
        $this->db->select("idip");
        $q = $this->db->get_where("cotizacion", array("idcotizacion" => $idcotizacion));
        if ($q->num_rows() > 0){
            $r = $q->row_array();
            return $r["idip"];
        }
        return false;
    }
}