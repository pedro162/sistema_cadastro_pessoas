<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Chate;
use App\Models\ConversaChate;
use App\Models\Experiencia;
use App\Models\Curso;
use \Exception;
use \InvalidArgumentException;

class Logradouro extends BaseModel
{
	private $data = [];
    const TABLENAME = 'Logradouro';
    
    private $idLogradouro;
    private $cidade;
    private $estado;
    private $bairro;
    private $endereco;
    private $complemento;
    private $numero;
    private $tipo;
    private $dtRegistro;
    private $idUsuario;
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

        return $this->update($result, $this->idLogradouro);
    }

    public function findForId(Int $id)
    {
    	if((!isset($id)) || ($id <= 0)){

            $this->setErrors("Parametro inválido\n");
            return false;
        }

        $result = $this->select(
                ['*'], [
                    ['key'=>'idLogradouro', 'val' => $id, 'comparator'=>'=', 'operator'=>'and'],
                    ['key'=>'status', 'val' => 'abilitado', 'comparator'=>'=']
                ], null, 1, null, true, false
            );

        return $result;

    }


    public function pessoaLogradouro()
    {
        $pessoa = new PessoaLogradouro();

        $result = $pessoa->select(
                ['*'],

                [
                    ['key'=>'idLogradouro', 'val' => $this->idLogradouro, 'comparator'=>'=', 'operator'=>'and'],
                    ['key'=>'status', 'val' => 'abilitado', 'comparator'=>'=']
                ],

                [

                    ['key'=>'nome', 'order'=>'asc']

                ], null, null, true, false
            );

        return $result;
    }


// -------------------- SETTERS E GETTERS ----------------------------------

    public function getIdLogradouro()
    {
        if((! isset($this->data['idLogradouro'])) || ($this->data['idLogradouro'] <= 0)){

            if(isset($this->idLogradouro) && ($this->idLogradouro > 0)){
                return $this->idLogradouro;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['idLogradouro'];

    }

    public function setIdLogradouro(Int $logradouro)
    {
        if((! isset($id)) || ($id <= 0)){

            $this->setErrors("Logradouro inválido\n");
            return false;
        }

        $this->data['idLogradouro'] = $id;

        return true;

    }


    public function getCidade()
    {
        if((! isset($this->data['cidade'])) || (strlen($this->data['cidade']) == 0)){

            if(isset($this->cidade) && (strlen($this->cidade) > 0)){
                return $this->cidade;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['cidade'];

    }

    public function setCidade(String $cidade)
    {
        if((! isset($cidade)) || (strlen($cidade) == 0)){

            $this->setErrors("Cidade inválida\n");
            return false;
        }

        $this->data['cidade'] = $cidade;

        return true;

    }


    public function getEstado()
    {
        if((! isset($this->data['estado'])) || (strlen($this->data['estado']) != 2)){

            if(isset($this->estado) && (strlen($this->estado) > 0)){
                return $this->estado;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['estado'];

    }

    public function setEstado(String $estado)
    {
        if((! isset($estado)) || (strlen($estado) != 2)){

            $this->setErrors("Estado inválida\n");
            return false;
        }

        $this->data['estado'] = $estado;

        return true;

    }

    public function getBairro()
    {
        if((! isset($this->data['bairro'])) || (strlen($this->data['bairro']) == 2)){

            if(isset($this->bairro) && (strlen($this->bairro) > 0)){
                return $this->bairro;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['bairro'];

    }

    public function setBairro(String $bairro)
    {
        if((! isset($bairro)) || (strlen($bairro) == 0)){

            $this->setErrors("Bairro inválida\n");
            return false;
        }

        $this->data['bairro'] = $bairro;

        return true;

    }


    public function getEndereco()
    {
        if((! isset($this->data['endereco'])) || (strlen($this->data['endereco']) == 2)){

            if(isset($this->endereco) && (strlen($this->endereco) > 0)){
                return $this->endereco;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['endereco'];

    }

    public function setEndereco(String $endereco)
    {
        if((! isset($endereco)) || (strlen($endereco) == 0)){

            $this->setErrors("Endereco inválida\n");
            return false;
        }

        $this->data['endereco'] = $endereco;

        return true;

    }


    public function getComplemento()
    {
        if((! isset($this->data['complemento'])) || (strlen($this->data['complemento']) == 2)){

            if(isset($this->complemento) && (strlen($this->complemento) > 0)){
                return $this->complemento;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['complemento'];

    }

    public function setComplemento(String $complemento)
    {
        if((! isset($complemento)) || (strlen($complemento) == 0)){

            $this->setErrors("Complemento inválida\n");
            return false;
        }

        $this->data['complemento'] = $complemento;

        return true;

    }


    public function getNumero()
    {
        if((! isset($this->data['numero'])) || (strlen($this->data['numero']) == 2)){

            if(isset($this->numero) && (strlen($this->numero) > 0)){
                return $this->numero;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['numero'];

    }

    public function setNumero(Int $numero)
    {
        if((! isset($numero)) || ($numero <= 0)){

            $this->setErrors("Numero inválida\n");
            return false;
        }

        $this->data['numero'] = $numero;

        return true;

    }

    public function getTipo()
    {
        if((! isset($this->data['tipo'])) || (strlen($this->data['tipo']) == 0)){

            if(isset($this->tipo) && (strlen($this->tipo) > 0)){
                return $this->tipo;
            }

            throw new Exception("Propriedade não definida\n");
            
        }

        return $this->data['tipo'];

    }

    public function setTipo(String $tipo)
    {
        if((! isset($tipo)) || (strlen($tipo) == 0)){

            $this->setErrors("Tipo inválida\n");
            return false;
        }

        if( ($tipo != 'casa') && ($tipo != 'apartamento') ){
        	$this->setErrors("Tipo inválida\n");
            return false;
        }

        $this->data['tipo'] = $tipo;

        return true;

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

    public function setIdUsuario(Int $id)
    {
        if((! isset($id)) || ($id <= 0)){

            $this->setErrors("Parametro inválido\n");
            return false;
        }

        $this->data['idUsuario'] = $id;

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
        if((! isset($this->data['status'])) || (strlen($this->data['status']) == 0)){

            if(isset($this->status) && (strlen($this->status) > 0)){
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
