<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Candidato;
use App\Models\Admin;
use App\Models\Empresa;
use \Exception;
use \PDOException;
use \InvalidArgumentException;

class Usuario extends BaseModel
{
    const TABLENAME = 'Usuario';

	private  $login;
	private  $senha;
	private  $idUsuario;
    private  $tipoUsuario;
    private  $dtRegistro;
    private  $img;
    private  $status;

    private $data = [];

    protected function parseCommit()
    {
        $arrayPase = [];
        for ($i=0; !($i == count($this->columns())) ; $i++) { 
            $chave = $this->columns()[$i]->Field;
            if(array_key_exists($chave, $this->data)){
                $arrayPase[$chave] = $this->data[$chave];
            }
        }
        return $arrayPase;
    }


    public function save()
    {

        $result = $this->parseCommit();

        return $this->insert($result);
    }

    public function modify()
    {
        
    }

    public function getLogin()
    {
        if((! isset($this->data['login'])) || (strlen($this->data['login']) == 0)){

            if(isset($this->login) && (strlen($this->login) > 0)){
                return $this->login;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['login'];

    }

    public function setLogin(String $login)
    {
        if((! isset($login)) || (strlen(trim($login)) == 0)){

            $this->setErrors("Email  inválido\n");
            return false;
            
        }

        $this->data['login'] = $login;

        return true;

    }

    public function getSenha()
    {
        if((! isset($this->data['senha'])) || (strlen($this->data['senha'] == 0))){
            if(isset($this->senha) && (strlen($this->senha) > 0)){
                return $this->senha;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['senha'];
    }

    public function setSenha(String $senha)
    {
        if((! isset($senha)) || (strlen(trim($senha)) < 6) || (strlen(trim($senha)) > 9)){

            $this->setErrors("A senha deve ter entre 6 e 9 carácteres\n");
            return false;
            
        }

        $this->data['senha'] = $senha;

        return true;

    }

    public function getTipoUsuario()
    {
        if((! isset($this->data['tipoUsuario'])) || (strlen($this->data['tipoUsuario'] == 0))){
            if(isset($this->tipoUsuario) && (strlen($this->tipoUsuario) > 0)){
                return $this->tipoUsuario;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['tipoUsuario'];
    }

    public function setTipoUsuario(String $tipoUsuario)
    {
        if((! isset($tipoUsuario)) || (strlen(trim($tipoUsuario)) == 0)){

            throw new PDOException("Usuario iválido\n");

        }


        $sentinela = false;

        switch ($tipoUsuario) {
                        
            case 'admin':
                $sentinela = true;
                break;
            
        }

        if($sentinela == false){
             throw new PDOException("Usuario iválido\n");
        }

        $this->data['tipoUsuario'] = $tipoUsuario;

        return true;

    }

    public function findForId(Int $id)
    {
        $result = $this->select(['*'],
            [
                ['key'=>'idUsuario', 'val' => $id, 'comparator' => '=', 'operator'=>'and'],
                ['key'=>'status', 'val' => 'abilitado', 'comparator'=>'=']
            
            ],null, 1, null, true, false);


        return $result;
    }


    public function getIdUsuario()
    {
        if((! isset($this->data['idUsuario'])) || ($this->data['idUsuario'] <= 0)){

            if(isset($this->idUsuario) && ($this->idUsuario > 0)){
                return $this->idUsuario;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['idUsuario'];

    }

    public function setDtRegistro(String $dtRegistro)
    {
        if((! isset($dtRegistro)) || (strlen(trim($dtRegistro))  == 0)){

            $this->setErrors("Parãmetro inválido\n");
            return false;
        }

        $this->data['dtRegistro'] = $dtRegistro;

        return true;

    }

    public function getDtRegistro()
    {
         if((! isset($this->data['dtRegistro'])) || (strlen($this->data['dtRegistro'] == 0))){

            if(isset($this->dtRegistro) && (strlen($this->dtRegistro) > 0)){
                return $this->dtRegistro;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['dtRegistro'];

    }


    public function findLogin(String $user, String $password)
    {
        
         $result = $this->select(['*'],
            [
                ['key'=>'login', 'val' => $user, 'comparator' => '=', 'operator'=>'and'],
                ['key'=>'status', 'val' => 'abilitado', 'comparator'=>'=', 'operator'=>'and'],
                ['key'=>'senha', 'val' => $password, 'comparator'=>'=']
            
            ],null, 1, null, true, false);

        return $result;  
    }



    public function getStatus()
    {
        if((! isset($this->data['status'])) || ($this->data['status'] <= 0)){

            if(isset($this->status) && ($this->status > 0)){
                return $this->status;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['status'];

    }


    public function setStatus(String $status)
    {
        if((! isset($status)) || (strlen($status) == 0)){

            $this->setErrors("Status inválido\n");
            return false;
        }

        if(($status != 'abilitado') && ($status != 'desabilitado')){

            $this->setErrors("Status inválido\n");
            return false;
        }

        $this->data['status'] = $status;

        return true;

    }
}
