<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Porpagar extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
    var $url = "http://localhost/tecnocomm/";
    var $tipopago = array("Cheque", "Transferencia", "Efectivo", "Otro");
    
	public function index()
	{
		$this->load->view('welcome_message');
	}
        
        public function pagar($idcuenta)
        {
            $data["idcuenta"] = $idcuenta;
            $data["tipopago"] = $this->tipopago;
            $this->load->model("porpagar_model");
            $cuenta = $this->porpagar_model->get_cuenta($idcuenta);
            $abonos = $this->porpagar_model->get_abonos($idcuenta);
            $data["porpagar"] = $cuenta["monto"];
            $data["pagado"] = $this->porpagar_model->get_total_abonado($idcuenta);
            $data["falta"] = $data["porpagar"] - $data["pagado"];
            $this->load->view("cuentas/porpagar/pagar", $data);
        }
        
        public function detalle($idfactura)
        {
            $data["titulo"] = "Cuentas Por Pagar";
            $data["url"] = $this->url;
            $data2["idfactura"] = $idfactura;
            $this->load->model("porpagar_model");
            $data2["abonos"] = $this->porpagar_model->get_abonos($idfactura);
            //var_dump($data2["abonos"] );
            $cuenta = $this->porpagar_model->get_cuenta($idfactura);
            //var_dump($cuenta);
            $data2["porpagar"] = $cuenta["monto"];
            $data2["tipopago"] = $this->tipopago;
            $this->load->view("html/head", $data);
            $data2["pagado"] = $this->porpagar_model->get_total_abonado($idfactura);
            $data2["falta"] = $data2["porpagar"] - $data2["pagado"];
            $this->load->view("cuentas/porpagar/detalle", $data2);
        }
        
        function registrarpago()
        {
            $idcuenta = $this->input->post("idcuenta");
            $this->load->model("porpagar_model");
            $this->porpagar_model->set_abono($this->input->post());
            $cuenta = $this->porpagar_model->get_cuenta($idcuenta);
            $abonos = $this->porpagar_model->get_abonos($idcuenta);
            $data["saldo"] = $cuenta["monto"] - $this->porpagar_model->get_total_abonado($idcuenta);;
            if($data["saldo"] == 0)
            {
                $data["estado"] = 1;
                $data["tipopago"] = $this->input->post("tipopago");
                $data["referencia"] = $this->input->post("referencia");
                $this->porpagar_model->set_cuenta($idcuenta,$data);
            }
        }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */