<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Chate;
use App\Models\ConversaChate;
use App\Models\Experiencia;
use App\Models\Curso;
use \Core\Utilitarios\Utils;
use \Exception;
use \InvalidArgumentException;

class Pessoa extends BaseModel
{
	private $data = [];
    const TABLENAME = 'Pessoa';
    
    private $idPessoa;
    private $nome;
    private $sobrenome;
    private $cpfCnpj;
    private $sexo;
    private $rgIe;
    private $img;
    private $dtNascimento;
    private $dtRegistro;
    private $idUsuario;
    private $email;
    private $tipo;
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

        return $this->update($result, $this->idPessoa);
    }

    public function findForId(Int $id)
    {
        if((!isset($id)) || ($id <= 0)){

            $this->setErrors("Parametro inválido\n");
            return false;
        }

        $result = $this->select(
                ['*'], [
                    ['key'=>'idPessoa', 'val' => $id, 'comparator'=>'=', 'operator'=>'and'],
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
                    ['key'=>'idPessoa', 'val' => $this->idPessoa, 'comparator'=>'=', 'operator'=>'and'],
                    ['key'=>'status', 'val' => 'abilitado', 'comparator'=>'=']
                ],

                [

                    ['key'=>'idPessoaLogradouro', 'order'=>'asc']

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

    public function getNome()
    {
        if((! isset($this->data['nome'])) || (strlen($this->data['nome'] == 0))){
            if(isset($this->nome) && (strlen($this->nome) > 0)){
                return $this->nome;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['nome'];
    }

    public function setNome(String $nome)
    {
        if((! isset($nome)) || (strlen(trim($nome))  == 0)){

            $this->setErrors("Nome inválido\n");
            return false;
        }

        $this->data['nome'] = $nome;

        return true;

    }

    public function getTipo()
    {
        if((! isset($this->data['tipo'])) || (strlen($this->data['tipo'] == 0))){
            if(isset($this->tipo) && (strlen($this->tipo) > 0)){
                return $this->tipo;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['tipo'];
    }

    public function setTipo(String $tipo)
    {
        if((! isset($tipo)) || (strlen(trim($tipo))  == 0)){

            $this->setErrors("Tipo inválido\n");
            return false;
        }

        if(($tipo != 'cpf') && ($tipo != 'cnpj')){

            $this->setErrors("Tipo inválido\n");
            return false;
        }

        $this->data['tipo'] = $tipo;

        return true;

    }

    public function getEmail()
    {
        if((! isset($this->data['email'])) || (strlen($this->data['email'] == 0))){
            if(isset($this->email) && (strlen($this->email) > 0)){
                return $this->email;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['email'];
    }

    public function setEmail(String $email)
    {
        if((! isset($email)) || (strlen(trim($email))  == 0)){

            $this->setErrors("Email inválido\n");
            return false;
        }

        $this->data['email'] = $email;

        return true;

    }



    public function getSobrenome()
    {
        if((! isset($this->data['sobrenome'])) || (strlen($this->data['sobrenome'] == 0))){
            if(isset($this->sobrenome) && (strlen($this->sobrenome) > 0)){
                return $this->sobrenome;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['sobrenome'];
    }


    public function setSobrenome(String $sobrenome)
    {
        if((! isset($sobrenome)) || (strlen(trim($sobrenome))  == 0)){

            $this->setErrors("Sobrenome inválido\n");
            return false;
        }

        $this->data['sobrenome'] = $sobrenome;

        return true;

    }

    public function getCpfCnpj()
    {
        if((! isset($this->data['cpfCnpj'])) || (strlen($this->data['cpfCnpj'] == 0))){
            if(isset($this->cpfCnpj) && (strlen($this->cpfCnpj) > 0)){
                return $this->cpfCnpj;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['cpfCnpj'];

    }

    public function setcpfCnpj(String $cpfCnpj)
    {
        if((! isset($cpfCnpj)) || (strlen(trim($cpfCnpj))  == 0)){

            $this->setErrors("Documento inválido\n");
            return false;
        }

        if((strlen(trim($cpfCnpj)) != 14) && (strlen(trim($cpfCnpj)) != 11 ) ){
            $this->setErrors("Documento inválido\n");
            return false;
        }

        if(strlen(trim($cpfCnpj)) == 11 ){

            $cpfCnpj = Utils::clearMask($cpfCnpj);
            $cpfCnpj = Utils::validaCpf($cpfCnpj);

            if($cpfCnpj == false){
                
                $this->setErrors("Documento inválido\n");
                return false;
            }

        }
        
        $this->data['cpfCnpj'] = $cpfCnpj;

        return true;

    }


    public function getRgIe()
    {
        if((! isset($this->data['rgIe'])) || (strlen($this->data['rgIe'] == 0))){
            if(isset($this->rgIe) && (strlen($this->rgIe) > 0)){
                return $this->rgIe;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['rgIe'];

    }

    public function setRgIe(String $rgIe)
    {
        if((! isset($rgIe)) || (strlen(trim($rgIe))  == 0)){

            $this->setErrors("Documento inválido\n");
            return false;
        }

        $this->data['rgIe'] = $rgIe;

        return true;

    }


    public function getImg()
    {
        if((! isset($this->data['img'])) || (strlen($this->data['img'] == 0))){
            if(isset($this->img) && (strlen($this->img) > 0)){
                return $this->img;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['img'];

    }

    public function setImg(String $img)
    {
        if((! isset($img)) || (strlen(trim($img))  == 0)){

            $this->setErrors("Imagem inválida\n");
            return false;
        }

        $this->data['img'] = $img;

        return true;

    }


    public function getDtNascimento()
    {
        if((! isset($this->data['dtNascimento'])) || (strlen($this->data['dtNascimento'] == 0))){

            if(isset($this->dtNascimento) && (strlen($this->dtNascimento) > 0)){
                return $this->dtNascimento;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['dtNascimento'];

    }

    public function setDtNascimento(String $dtNascimento)
    {
        if((! isset($dtNascimento)) || (strlen(trim($dtNascimento))  == 0)){

            $this->setErrors("Data de nascimento inválida\n");
            return false;
        }

        $this->data['dtNascimento'] = $dtNascimento;

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


    public function setSexo(String $sexo)
    {
        if((! isset($sexo)) || (strlen(trim($sexo))  == 0)){

            $this->setErrors("Sexo  inválido \n");
            return false;
        }

        if(($sexo != 'm') && ($sexo != 'f')){

            $this->setErrors("Sexo  inválido\n");
            return false;
        }

        $this->data['sexo'] = $sexo;

        return true;

    }

    public function getSexo()
    {
         if((! isset($this->data['sexo'])) || (strlen($this->data['sexo'] == 0))){

            if(isset($this->sexo) && (strlen($this->sexo) > 0)){
                return $this->sexo;
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->data['sexo'];

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
