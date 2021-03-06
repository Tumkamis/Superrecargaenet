<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of registro
 *
 * @author marioeduardo
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
class registro extends CI_Controller{
    public function __construct() {
        parent::__construct();
//        if (!$this->session->userdata('is_login')) {
//            redirect(base_url() . "login", 'refresh');
//        }
        $this->load->model('usuario_model');
        $this->load->model('operador_model');
        $this->load->model('paquete_model');
        $this->load->model('beneficiario_model');
        $this->load->model('propietario_model');
    }
    
    public function index() {
        $data = array();
        $data['tipoinsts'] = $this->propietario_model->tipo_instituciones();
        $data['titulo'] = "Súper Recarga | Registro";
        /**
         * Cargamos la vista completa de la seccion correspondiente
         */
        $data = $this->load->view('app/public/registro_view', $data, FALSE);
        //$data = $this->load->view('error_view', $data, FALSE);
    }
    
    public function buscar_institucion() {
        $data = array();
        $options = "";
        if ($this->input->post('tipo')) {
            $tipo = $this->input->post('tipo');
            $instituciones = $this->propietario_model->consultar_instituciones($tipo);
            ?>
                <option selected="">Selecciona</option>
                <?php
            foreach ($instituciones as $fila) {
                ?>
                 <option value="<?= $fila->idinstitucion ?>"><?= $fila->nombre ?></option>
                <?php
            }
        }
    }
    
    public function registro_post() {
        header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

        
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('telefono1', 'telefono1', 'trim|required|xss_clean');
            $this->form_validation->set_rules('telefono2', 'telefono2', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'email', 'trim|required|xss_clean');
            $this->form_validation->set_rules('banco', 'banco', 'trim|required|xss_clean');
            $this->form_validation->set_rules('instituciones', 'instituciones', 'trim|required|xss_clean');
            
            if ($this->form_validation->run()) {
                $usuario = $this->input->post('telefono1');
                $correo = $this->input->post('email');
                $datos_propietario = $this->propietario_model->info_propietario($usuario);

//                if (!is_null($datos_propietario)) {
//                    http_error(403);
//                    //redirect(base_url() . "login", 'refresh');
//                } else {

                    $cadena_base = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                    $cadena_base .= '0123456789';
                    //$cadena_base .= '!@#%^&*()_,./<>?;:[]{}\|=+';

                    $largo = 9;
                    $password = '';
                    $limite = strlen($cadena_base) - 1;

                    for ($i = 0; $i < $largo; $i++) {
                        $password .= $cadena_base[rand(0, $limite)];
                    }

                    $arr_propietario = array();
                    $arr_propietario['telefono'] = $this->input->post('telefono1');
                    $arr_propietario['correo'] = $this->input->post('email');
                    $arr_propietario['idinstitucion'] = $this->input->post('instituciones');
                    $arr_propietario['contrasenainicial'] = $password;
                    $arr_propietario['contrasena'] = md5($password);
                    $arr_propietario['banco'] = $this->input->post('banco');
                    $arr_propietario['estatus'] = 2;
                    
                    $idpropietario=$this->propietario_model->insertar($arr_propietario);

                    $idinstitucion = $this->input->post('instituciones');
                    $rutaimg = $this->propietario_model->ruta_img_correo($idinstitucion);
                    $mail = new PHPMailer(true);
                    try {
                        //Server settings
                        $mail->SMTPDebug = 0;                      // Enable verbose debug output
                        $mail->isSMTP();                                            // Send using SMTP
                        //$mail->Host = 'smtp.gmail.com';                       // Set the SMTP server to send through
                        $mail->Host = 'mail.diatel.com.mx';                       // Set the SMTP server to send through
                        //$mail->Host = 'superrecarga.com.mx';                       // Set the SMTP server to send through
                        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
                        //$mail->Username = 'apacan99@gmail.com';                     // SMTP username
                        //$mail->Username = 'registrossuperrecarga@diatel.com.mx';                     // SMTP username
                        $mail->Username = 'cuentas@diatel.com.mx';                     // SMTP username
                        //$mail->Password = 'zaznhekisdvtsgdb';                               // SMTP password
                        $mail->Password = 'roA,uo1}{Os_';                               // SMTP password
                        //$mail->Password = 'RtyO$R[tG7G-';                               // SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                        $mail->Port = 465;                                   // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                        //Recipients
                        $mail->setFrom('registrossuperrecarga@diatel.com.mx', 'Super Recarga');
                        $mail->addAddress($correo);
                        //$mail->addAddress('noresponda@diatel.com.mx');
                        //Cambiar a correo de rh de la empresa
                        //$mail->addAddress('rulp@diatel.com.mx');
//            $mail->addAddress('ellen@example.com');               // Name is optional
//            $mail->addReplyTo('info@example.com', 'Information');
//            $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');
                        // Attachments
                        //Para archivos
//            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                        // Content
                        //$mail->addAttachment('static/pdf/CartaDescuento.pdf');
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'SÚPERRECARGA Confirmación de Registro';
//            $mail->Body = 'This is the HTML message body <b>in bold!</b>';
                        //$mail->Body = 'Correo de comprobación';

                        $mail->AddEmbeddedImage('static/instituciones/' . $rutaimg->img . '.jpg', 'imagen-inst');
                        $mail->AddEmbeddedImage('static/Logotipo-Super-Recarga-R.png', 'imagen-sr');
                        $cuerpo = '
                <html>
                    <head></head>
                    <body>
                        <label style="font-family: sans-serif; font-size: 14px">
                            Bienvenido al Registro SÚPERRECARGA
                        </label>
                        <br>
                        <br>
                        <label style="font-family: sans-serif; font-size: 14px">
                            A partir de hoy vas a AHORRAR y APOYAR en todas tus recargas a: 
                        </label>
                        <br>
                        <br>
                        <img src="cid:imagen-inst" height="250px" width="250px">
                        <br>
                        <br>
                        <label style="font-family: sans-serif; font-size: 14px">
                            El número celular registrado es: ' . $usuario . '
                        </label>
                        <br>
                        <label style="font-family: sans-serif; font-size: 14px">
                            Tu contraseña inicial es: ' . $password . '
                        </label>
                        <br>
                        <br>
                        <label style="font-family: sans-serif; font-size: 14px">
                            Para tu seguridad, el sistema te solicitará una nueva contraseña en tu primer Login. 
                        </label>
                        <br>
                        <br>
                        <label style="font-family: sans-serif; font-size: 14px">
                            Atte 
                        </label>
                        <br>
                        <br>
                        <img src="cid:imagen-sr" height="150px" width="150px">
                    </body>
                </html>'
                        ;
                        $mail->Body = $cuerpo;
                        $mail->CharSet = 'UTF-8';
//            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send();
                        //echo 'El mensaje se envió correctamente';
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }

                    echo json_encode(array("response_code" => 200, "evento_id" => $idpropietario));
                    //$this->propietario_model->insertar($arr_propietario);
                    //redirect(base_url() . "login", 'refresh');
                //}
            }
            else{
                http_error(403);
            }
        }
        else{
            http_error(400);
        }
    }
    
    public function reenviarcorreo() {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('correo', 'correo', 'trim|required|xss_clean');
            $this->form_validation->set_rules('idinstitucion', 'idinstitucion', 'trim|required|xss_clean');
            $this->form_validation->set_rules('telefono', 'telefono', 'trim|required|xss_clean');
            if ($this->form_validation->run()) {
                $institucion = $this->input->post('idinstitucion');
                $correo = $this->input->post('correo');
                $usuario = $this->input->post('telefono');
                
                $datos_propietario = $this->propietario_model->info_propietario($usuario);
                $password=$datos_propietario->contrasenainicial;
                $rutaimg = $this->propietario_model->ruta_img_correo($institucion);
                $mail = new PHPMailer(true);
                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    //$mail->Host = 'superrecarga.com.mx';
                    $mail->Host = 'mail.diatel.com.mx';
                    $mail->SMTPAuth = true;
                    //$mail->Username = 'registro@superrecarga.com.mx';
                    $mail->Username = 'registrossuperrecarga@diatel.com.mx';
                    //$mail->Password = 'nlI?Vf{ROl{}>armidas*';
                    $mail->Password = 'RtyO$R[tG7G-';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;
                    $mail->setFrom('registrossuperrecarga@diatel.com.mx', 'Super Recarga');
                    $mail->addAddress($correo);
                    $mail->isHTML(true);
                    $mail->Subject = 'SÚPERRECARGA Confirmación de Registro';
                    $mail->AddEmbeddedImage('static/instituciones/' . $rutaimg->img . '.jpg', 'imagen-inst');
                    $mail->AddEmbeddedImage('static/Logotipo-Super-Recarga-R.png', 'imagen-sr');
                    $cuerpo = '
                <html>
                    <head></head>
                    <body>
                        <label style="font-family: sans-serif; font-size: 14px">
                            Bienvenido al Registro SÚPERRECARGA
                        </label>
                        <br>
                        <br>
                        <label style="font-family: sans-serif; font-size: 14px">
                            A partir de hoy vas a AHORRAR y APOYAR en todas tus recargas a: 
                        </label>
                        <br>
                        <br>
                        <img src="cid:imagen-inst" height="250px" width="250px">
                        <br>
                        <br>
                        <label style="font-family: sans-serif; font-size: 14px">
                            El número celular registrado es: ' . $usuario . '
                        </label>
                        <br>
                        <label style="font-family: sans-serif; font-size: 14px">
                            Tu contraseña inicial es: ' . $password . '
                        </label>
                        <br>
                        <br>
                        <label style="font-family: sans-serif; font-size: 14px">
                            Para tu seguridad, el sistema te solicitará una nueva contraseña en tu primer Login. 
                        </label>
                        <br>
                        <br>
                        <label style="font-family: sans-serif; font-size: 14px">
                            Atte 
                        </label>
                        <br>
                        <br>
                        <img src="cid:imagen-sr" height="150px" width="150px">
                    </body>
                </html>'
                        ;
                    $mail->Body = $cuerpo;
                    $mail->CharSet = 'UTF-8';
                    $mail->send();
                    echo json_encode(array("response_code" => 200, "evento_id" => $usuario));
                }
                catch(Exception $e){
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
            else{
                http_error(403);
            }
        }
        else{
            http_error(400);
        }
    }
    
    public function registroget($fundacion) {
        $data = array();
        //Para url con guiones
        $fundacionguion=str_replace("-", " ", $fundacion);
        $fundacionn=  rawurldecode($fundacionguion);
        $infoinst=$this->propietario_model->consultar_institucion($fundacionn);
        $data['fundacion'] = $infoinst;
        
        //Para espacios
//        $fundacionn=  rawurldecode($fundacion);
//        $infoinst=$this->propietario_model->consultar_institucion($fundacionn);
//        $data['fundacion'] = $infoinst;
        $data['tipoinsts'] = $this->propietario_model->tipo_instituciones();
        $data['titulo'] = "Súper Recarga | Registro";
        /**
         * Cargamos la vista completa de la seccion correspondiente
         */
        $data = $this->load->view('app/public/registroget_view', $data, FALSE);
    }
    
    public function registrogetGESA() {
        $data = array();
        
        //Para espacios
//        $fundacionn=  rawurldecode($fundacion);
//        $infoinst=$this->propietario_model->consultar_institucion($fundacionn);
//        $data['fundacion'] = $infoinst;
        $data['tipoinsts'] = $this->propietario_model->tipo_instituciones();
        $data['titulo'] = "Súper Recarga | Registro";
        /**
         * Cargamos la vista completa de la seccion correspondiente
         */
        $data = $this->load->view('app/public/registroget_view_gesa', $data, FALSE);
    }
    
    public function registrogetINUH() {
        $data = array();
        
        //Para espacios
//        $fundacionn=  rawurldecode($fundacion);
//        $infoinst=$this->propietario_model->consultar_institucion($fundacionn);
//        $data['fundacion'] = $infoinst;
        $data['tipoinsts'] = $this->propietario_model->tipo_instituciones();
        $data['titulo'] = "Súper Recarga | Registro";
        /**
         * Cargamos la vista completa de la seccion correspondiente
         */
        $data = $this->load->view('app/public/registroget_view_inuh', $data, FALSE);
    }
}
