<?php

class Porpagar_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }

    function get_abonos($idcuenta) {
        $q = $this->db->query("SELECT *, DATE_FORMAT(fecha, '%d-%m-%Y') as fecha_es FROM cuentasporpagar_abonos WHERE idcuenta = " . $idcuenta);

        if ($q->num_rows() > 0)
            return $q->result_array();
        return false;
    }
    
    function get_cuenta($idcuenta)
    {
        $q = $this->db->query("SELECT *, DATE_FORMAT(fecha, '%d-%m-%Y') as fecha_es FROM cuentasporpagar WHERE idcuenta = " . $idcuenta);

        if ($q->num_rows() > 0)
            return $q->row_array();
        return false;
    }
    
    function set_abono($data)
    {
        $this->db->set("fecha", "DATE(NOW())", false);
        $this->db->insert("cuentasporpagar_abonos", $data);
    }
    
    function set_cuenta($idcuenta, $data)
    {
        $this->db->set("fechapago", "DATE(NOW())", false);
        $this->db->update("cuentasporpagar", $data, array("idcuenta" => $idcuenta));
    }
    
    function get_total_abonado($idcuenta)
    {
        $q = $this->db->query("SELECT COALESCE(SUM(monto),0) as total FROM cuentasporpagar_abonos WHERE idcuenta = " .  $idcuenta);
        $row = $q->row();
        return $row->total;
    }
}