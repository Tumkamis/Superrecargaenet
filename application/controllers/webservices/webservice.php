<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of webservice
 *
 * @author marioeduardo
 */
class webservice  extends CI_Controller{
    //put your code here
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }
    
    public function index() {
        //$data = json_decode($_POST['d'], true);
        date_default_timezone_set('America/Mexico_City');
        $_POST = json_decode(file_get_contents('php://input'), true);
        $arr_msjc = array();
        $arr_msjc['numero'] = $_POST['numero'];
        $arr_msjc['puntoventa'] = $_POST['puntoventa'];
        $arr_msjc['operador'] = $_POST['operador'];
        $arr_msjc['FECHA'] = date('Y-m-d H:i:s');
        $datos = array();
        if($arr_msjc['numero']==NULL || $arr_msjc['puntoventa']==NULL || $arr_msjc['operador']==NULL){
            $datos['resultado'] = "Faltan datos";
        }
        else{
            $folio=post_nws($arr_msjc);
            if ($arr_msjc['numero'] == '4444444444' || $arr_msjc['numero'] == '5555555555') {
                $datos['resultado'] = 1;
                $datos['folio'] = $folio;
            } else {
                $datos['resultado'] = 0;
                $datos['folio'] = $folio;
            }
        }
        
        header("HTTP/1.1 200 OK");
        header("Content-Type: application/json");
        echo json_encode($datos);
    }
    
    public function SMS_API() {
        $_POST = json_decode(file_get_contents('php://input'), true);
        $arr_sms = array();
        $arr_sms['id'] = $_POST['id'];
        $arr_sms['subAccount'] = $_POST['subAccount'];
        $arr_sms['campaignAlias'] = $_POST['campaignAlias'];
        $arr_sms['carrierId'] = $_POST['carrierId'];
        $arr_sms['carrierName'] = $_POST['carrierName'];
        $arr_sms['source'] = $_POST['source'];
        $arr_sms['shortCode'] = $_POST['shortCode'];
        $arr_sms['messageText'] = $_POST['messageText'];
        $arr_sms['receivedAt'] = $_POST['receivedAt'];
        $arr_sms['receivedDate'] = $_POST['campaignAlias'];
        $arr_sms['mtid'] = $_POST['mt']['id'];
        $arr_sms['mtcorrelationId'] = $_POST['mt']['correlationId'];
        $arr_sms['mtusername'] = $_POST['mt']['username'];
        $arr_sms['mtemail'] = $_POST['mt']['email'];
        post_sms($arr_sms);
        
        header("HTTP/1.1 200 OK");
        header("Content-Type: application/json");
        $datos = array();
        $datos['Codigo'] = 1;
        echo json_encode($datos);
    }
}
