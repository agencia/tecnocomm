<?php

class Cliente_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }

    function get_ip_by_id($idip) {
//        var_dump($idip);
        $this->db->where("idip", $idip);;
        $q = $this->db->get("ip");
        if ($q->num_rows() > 0)
            return $q->row_array();
        return false;
    }
    
    function get_cliente_by_idip($idip){
        $ip  = $this->get_ip_by_id($idip);
        $this->db->where("idcliente", $ip["idcliente"]);
        $q = $this->db->get("cliente");
        if ($q->num_rows() > 0)
            return $q->row_array();
        return false;
    }
    
}