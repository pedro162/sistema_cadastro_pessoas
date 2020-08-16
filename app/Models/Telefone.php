<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Chate;
use App\Models\ConversaChate;
use App\Models\Experiencia;
use App\Models\Curso;
use \Exception;
use \InvalidArgumentException;

class Telefone extends BaseModel
{
	private $data = [];
    const TABLENAME = 'Telefone';
    
    private $idTelefone;
    private $numero;
    private $dtRegistro;
    private $idUsuario;
    private $idPessoa;
    private $status;

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
        $result = $this->parseCommit();

        return $this->update($result, $this->idTelefone);
    }

    public function findForId(Int $id)
    {
        if((!isset($id)) || ($id <= 0)){

            $this->setErrors("Parametro inválido\n");
            return false;
        }

        $result = $this->select(
                ['*'], [
                    ['key'=>'idTelefone', 'val' => $id, 'comparator'=>'=', 'operator'=>'and'],
                    ['key'=>'status', 'val' => 'abilitado', 'comparator'=>'=']
                ], null, 1, null, true, false
            );

        return $result;

    }


    public function pessoa()
    {
        $pessoa = new Pesssoa();

        $result = $pessoa->select(
                ['*'],

                [
                    ['key'=>'idPessoa', 'val' => $this->idPessoa, 'comparator'=>'=', 'operator'=>'and'],
                    ['key'=>'status', 'val' => 'abilitado', 'comparator'=>'=']
                ],

                [

                    ['key'=>'nome', 'order'=>'asc']

                ], null, null, true, false
            );

        return $result;
    }

    

// -------------------- SETTERS E GETTERS ----------------------------------

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

    public function setIdUsuario(Int $id)
    {
        if((! isset($id)) || ($id <= 0)){

            $this->setErrors("Parametro inválido\n");
            return false;
        }

        $this->data['idUsuario'] = $id;

        return true;

    }

    public function getIdTelefone()
    {
        if((! isset($this->data['idTelefone'])) || ($this->data['idTelefone'] <= 0)){

            if(isset($this->idTelefone) && ($this->idTelefone > 0)){
                return $this->idTelefone;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['idTelefone'];

    }

    public function setIdTelefone(Int $id)
    {
        if((! isset($id)) || ($id <= 0)){

            $this->setErrors("Parametro inválido\n");
            return false;
        }

        $this->data['idTelefone'] = $id;

        return true;

    }

    public function getIdPessoa()
    {
        if((! isset($this->data['idPessoa'])) || ($this->data['idPessoa'] <= 0)){

            if(isset($this->idPessoa) && ($this->idPessoa > 0)){
                return $this->idPessoa;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['idPessoa'];

    }

    public function setIdPessoa(Int $id)
    {
        if((! isset($id)) || ($id <= 0)){

            $this->setErrors("Parametro inválido\n");
            return false;
        }

        $this->data['idPessoa'] = $id;

        return true;

    }

    public function getNumero()
    {
        if((! isset($this->data['numero'])) || (strlen($this->data['numero'] == 0))){
            if(isset($this->numero) && (strlen($this->numero) > 0)){
                return $this->numero;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['numero'];
    }

    public function setNumero(String $numero)
    {
        if((! isset($numero)) || (strlen(trim($numero))  == 0)){

            $this->setErrors("Numero inválido\n");
            return false;
        }

        $this->data['numero'] = $numero;

        return true;

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


    public function __get($prop)
    {
        if(method_exists($this, 'get'.ucfirst($prop))){

            return call_user_func([$this,'get'.ucfirst($prop)]);
        }
    }

    public function __set($prop, $value)
    {   
        if(method_exists($this, 'set'.ucfirst($prop))){ 
            return call_user_func([$this,'set'.ucfirst($prop)], $value);
        }
    }

    
 




}
