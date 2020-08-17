// Disable form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Get the forms we want to add validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();




$(document).ready(function(){

  /*HABILITA OS LINKS AO CARREGAR O JS*/
  ConfigController.abilitarLink();

  //------------------ ENVIA OS DADOS PARA LOGAR ------------------
   $('body').delegate('form#login', 'submit', function(ev){

      let form = $(this);
      return LoginController.logar(form, ev)
     
  })

   /**
   *
   * Validaos dados do candidato
   */ 
   $('body').delegate('form#adicionarCandidato', 'submit', function(ev){

        try{

          let form =  $(this);

          let cand = new Candidato();

          cand.setNome(form.find('input[name=nome]').val())
          cand.setSobrenome(form.find('input[name=sobrenome]').val())
          cand.setCpf(form.find('input[name=cpf_cnpj]').val())
          cand.setSexo(form.find('select[name=sexo]').val())
          cand.setEmail(form.find('input[name=email]').val())
          cand.setTelefone(form.find('input[name=phone_1]').val())
          cand.setTelefone(form.find('input[name=phone_2]').val())

          let errors = cand.getErros();

          if(errors.length > 0){
            throw new Error(errors);
          }

          LogradouroController.salvar();


        }catch(e){

          BaseController.responseMensage(['msg', 'warning', e.message])

          //cancela a submissao do formulario
          ev.preventDefault();
          ev.stopPropagation();
        }


    })

   /**
   *
   * Chama moal para cadastrar logradouro
   */
   $('body').delegate('button#adicionar-logradouro', 'click', function(ev){

      try{

          LogradouroController.index(null, 'logradouroCandidato');


        }catch(e){

          BaseController.responseMensage(['msg', 'warning', e.message])

          //cancela a submissao do formulario
          ev.preventDefault();
          ev.stopPropagation();
        }
     

     
   })

   /**
   *
   * Chama mocal para cadastrar logradouro painel pessoa
   */
   $('body').delegate('button#adicionar-logradouro-painel', 'click', function(ev){

      try{
          let url = $('body').find('a#pessoal').attr('href');
          let id = url.split('=')[1];

          if(id <= 0){
            throw new Error('Algo errado aconteceu, recarrege a página.')
          }

          LogradouroController.index('/logradouro/salvar', 'logradouro-painel', id);
          


        }catch(e){

          BaseController.responseMensage(['msg', 'warning', e.message])

          //cancela a submissao do formulario
          ev.preventDefault();
          ev.stopPropagation();
        }
     

     
   })

   /*
    Adiciona o logradouro ou o edita
   */
   $('body').delegate('#logradouroCandidato', 'submit', function(ev){
    
    
    let formulario = $(this); 

    try{

        //cancela a submissao do formulario
        ev.preventDefault();
        ev.stopPropagation();

        let estado      = formulario.find('input[name=estado]').val();
        let cidade      = formulario.find('input[name=cidade]').val();
        let bairro      = formulario.find('input[name=bairro]').val();
        let endereco    = formulario.find('input[name=endereco]').val();
        let complemento = formulario.find('input[name=complemento]').val();
        let tipo        = formulario.find('select[name=tipo]').val();
        let numero      = formulario.find('input[name=numero]').val();

        let tr_edit      = formulario.find('input[name=tr-edit]').val();

        let logradouro = new Logradouro();

        logradouro.setEstado(estado);
        logradouro.setCidade(cidade);
        logradouro.setBairro(bairro);
        logradouro.setEndereco(endereco);
        logradouro.setComplemento(complemento);
        logradouro.setTipo(tipo);
        logradouro.setNumero(numero);
        
        let errors = logradouro.getErros();


        if(errors.length > 0){
            throw new Error(errors);
        }

        //define a mensagem de alerta
        let msg = 'Logradouro adicionado com sucesso!';

        //se não teve erro, remove a tr e os inputs hidden correspndentes a ser editada

        if(tr_edit){
          $('table#table-logradouro').find('tbody tr[id='+tr_edit+']').remove();

          $('form#adicionarCandidato').find('input#estado-'+tr_edit).remove();
          $('form#adicionarCandidato').find('input#cidade-'+tr_edit).remove();
          $('form#adicionarCandidato').find('input#bairro-'+tr_edit).remove();
          $('form#adicionarCandidato').find('input#endereco-'+tr_edit).remove();
          $('form#adicionarCandidato').find('input#numero-'+tr_edit).remove();
          $('form#adicionarCandidato').find('input#tipo-'+tr_edit).remove();
          $('form#adicionarCandidato').find('input#complemento-'+tr_edit).remove();
          msg = 'Logradouro atualizado com sucesso!';
        }

        let lastIdTr = 0;

        $('table#table-logradouro').find('tbody tr').each(function(){

          let id = $(this).attr('id');
          id = Number(id);

          if(id > lastIdTr){

            lastIdTr = id;
          }

        });

        let idTr = lastIdTr + 1;
        
        let tr = `<tr id="${idTr}">
                    <td>${logradouro.getEstado()}</td>
                    <td>${logradouro.getCidade()}</td>
                    <td>${logradouro.getBairro()}</td>
                    <td>${logradouro.getEndereco()}</td>
                    <td>${logradouro.getComplemento()}</td>
                    <td>${logradouro.getTipo()}</td>
                    <td>${logradouro.getNumero()}</td>
                    <td><button class="btn btn-sm btn-success mb-2 editar" type="button"><i class="fas fa-pencil-alt"></i></button>
                    <button class="btn btn-sm btn-danger mb-2 excluir" type="button"><i class="fas fa-trash-alt"></i></button></td>
                  </tr>`;
        

        $('table#table-logradouro').find('tbody').append(tr);


        LogradouroController.salvar(
            logradouro.getEstado(), logradouro.getCidade(), 
            logradouro.getBairro(), logradouro.getEndereco(),
            logradouro.getNumero(),logradouro.getTipo(),
            logradouro.getComplemento(), idTr
          );

        if($('#myModal').find('#msg-modal')){
          $('#myModal').find('#msg-modal').remove();
        }

        formulario.parent().prepend('<div class="row" id="msg-modal"><div align="center" class="col alert alert-success h3">'+msg+'</div></div>')


      }catch(e){
        console.log(e.message)
        if($('#myModal').find('#msg-modal')){
          $('#myModal').find('#msg-modal').remove();
        }

        formulario.parent().prepend('<div class="row" id="msg-modal"><div align="center" class="col alert alert-warning h3">'+e.message+'</div></div>')

      }
    
   })

   /*
    Adiciona o logradouro painel pessoa
   */
   $('body').delegate('#logradouro-painel', 'submit', function(ev){
    
    
    let formulario = $(this); 

    try{


        let estado      = formulario.find('input[name=estado]').val();
        let cidade      = formulario.find('input[name=cidade]').val();
        let bairro      = formulario.find('input[name=bairro]').val();
        let endereco    = formulario.find('input[name=endereco]').val();
        let complemento = formulario.find('input[name=complemento]').val();
        let tipo        = formulario.find('select[name=tipo]').val();
        let numero      = formulario.find('input[name=numero]').val();

        let tr_edit      = formulario.find('input[name=tr-edit]').val();

        let logradouro = new Logradouro();

        logradouro.setEstado(estado);
        logradouro.setCidade(cidade);
        logradouro.setBairro(bairro);
        logradouro.setEndereco(endereco);
        logradouro.setComplemento(complemento);
        logradouro.setTipo(tipo);
        logradouro.setNumero(numero);
        
        let errors = logradouro.getErros();


        if(errors.length > 0){
            throw new Error(errors);
        }

        
      }catch(e){

        //cancela a submissao do formulario
        ev.preventDefault();
        ev.stopPropagation();

        console.log(e.message)
        if($('#myModal').find('#msg-modal')){
          $('#myModal').find('#msg-modal').remove();
        }

        formulario.parent().prepend('<div class="row" id="msg-modal"><div align="center" class="col alert alert-warning h3">'+e.message+'</div></div>')

      }
    
   })


   

   /*
    Remove o logradouro da tabela ou chama o formulario de edicao
   */

   $('table#table-logradouro').find('tbody').delegate('button.excluir, button.editar', 'click', function(ev){
      
      let logradouro = $(this).parents('tr');
      let idTr = logradouro.attr('id');

      if($(this).hasClass('editar')){

        LogradouroController.editar(logradouro);


      }else if($(this).hasClass('excluir')){

        let response = confirm('Deja realmente remover o logradouro "'+logradouro.find('td:eq(3)').text())
        if(response){

          logradouro.remove();

          $('form#adicionarCandidato').find('input#estado-'+idTr).remove();
          $('form#adicionarCandidato').find('input#cidade-'+idTr).remove();
          $('form#adicionarCandidato').find('input#bairro-'+idTr).remove();
          $('form#adicionarCandidato').find('input#endereco-'+idTr).remove();
          $('form#adicionarCandidato').find('input#numero-'+idTr).remove();
          $('form#adicionarCandidato').find('input#tipo-'+idTr).remove();
          $('form#adicionarCandidato').find('input#complemento-'+idTr).remove();
        }


      }
   })




//-------------final jquery
})


/*------------------------------------BASE CONTROLLER --------------------------------------*/

class BaseController{

  static requestAjax(url, type='GET', dataType = 'HTML', data= null, objRender=null, clearMsg = true){
    if(type == 'POST'){

      $.ajax({
          url: url,
          type: type,
          data: data,
          processData: false,
          contentType: false,
          dataType: dataType,
          success: function(retorno){

            if(dataType == 'json'){

              this.responseMensage(retorno);

            }else if(dataType == 'HTML'){

              if(objRender){
                objRender.html(retorno);
              }
            }

            
            
          }
      })


    }else{

        $.ajax({
        url: url,
        type: type,
        dataType: dataType,
        success: function(retorno){

           if(dataType == 'json'){

              this.responseMensage(retorno);

            }else if(dataType == 'HTML'){

              if(objRender){
                objRender.html(retorno);
              }

            }
        }

      })
    }

  }

  static responseMensage(response){
    if(!response){
      throw new Error('Parâmetro inválido\n');
    }
    let style = response[1];
    let content = response[2];

    $('body').find('#section-response').show();

    let objRsponse = $('body').find('#msg-response');

    let btnClose = $('<button/>').addClass('close').attr('data-dismiss', 'alert').html('&times')

    if(objRsponse.hasClass('col')){
      objRsponse.html('')
      objRsponse.addClass('alert-'+style);
      objRsponse.append(btnClose).append('<h4>'+content+'</h4>');

    }else{

      
     let msg = $('<div/>').addClass('alert alert-'+style+' alert-dismissible fadeshow col');
      msg.append(btnClose).attr('id', 'msg-response')
      msg.attr('align', 'center').append('<h4>'+content+'</h4>');
      
      $('body').find('#section-response').html(msg);
    }
    
  }




}


/*----------------------- CONTROLLER DE CONFIGURAÇÃO DO SITE ---------------------*/
class ConfigController extends BaseController{
    constructor(){

    }

    static abilitarLink(){
      $('body').find('a.desable-link').removeClass('desable-link')
    }

    static navegar(url){
      if(!url){
        throw new Error('Parâmetro inválido\n')
      }

      if(url.trim().length == 0){
        throw new Error('Parâmetro inválido\n')
      }

      let obj = $('body').find('#corpo-principal');

      BaseController.requestAjax(url, 'POST', 'HTML', null, obj, true)

    }

}


/*------------------------------- CONTROLLER DE LOGIN -------------------------------*/

class LoginController extends BaseController{
  constructor(){

  }

  static index(url){
    if((!url) || (url.trim().length == 0)){
      throw new Error('Parâmetro inválido');
    }

    let obj = $('body').find('#container-principal');

    BaseController.requestAjax(url, 'GET', 'HTML',  null, obj, true);
  }

  static logar(form, ev){

    try{
      
      if((!form)){

        throw new Error('Parâmetro inválido');
      }

      let formUser = form.find('#usuario').val();
      let formSenha = form.find('#senha').val();


      let usuario = new User();
      usuario.setSenha(formSenha);
      usuario.setEmail(formUser);

      let errors = usuario.getError();

      if(errors.length > 0){

        let msg = '';

        for (let i = 0; !(i == errors.length); i++) {

          msg += errors[i]+'\n';

        }

        throw new Error(msg);
      }

      return true;

    }catch(e){
      ev.preventDefault();
      ev.stopPropagation();

      BaseController.responseMensage(['msg', 'warning', e.message]);

    }
  }

}


class LogradouroController extends BaseController{
  constructor(){

  }

  static index(url=null, id=null, idPessoa=null){

      let form = ` 
      <form class="col" method="post" action="${url? url: '#'}" id="${id}">
        <fieldset>
        <input type="hidden" name="pessoa" value="${idPessoa? idPessoa: ''}">
          <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="estado">Sigla do estado</label>
                  <input type="text" name="estado" id="estado"class="form-control form-control-sm" required="required">
                </div>
                <div class="form-group">
                  <label for="cidade">Cidade</label>
                  <input type="text"  name="cidade" id="cidade"class="form-control form-control-sm" required="required">
                </div>
                <div class="form-group">
                  <label for="bairro">Bairro</label>
                  <input type="text" name="bairro"id="bairro" class="form-control form-control-sm" required="required">
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="endereco">Logradouro</label>
                  <input type="text" name="endereco"id="endereco" class="form-control form-control-sm" required="required">
                </div>
                <div class="form-group">
                  <label for="complemento">Complemento</label>
                  <input type="text" name="complemento" id="complemento"class="form-control form-control-sm" required="required">
                </div>
                <div class="row">
                  <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                      <label for="tipo">Tipo</label>
                      <select  name="tipo" id="tipo" class="form-control form-control-sm">
                        <option value="casa">Casa</option>
                        <option value="apartamento">Apartamento</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                      <label for="numero" required="required">Número</label>
                      <input type="number" name="numero" id="numero" class="form-control form-control-sm" required="required">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <button class="btn btn-sm  mb-2 btn-outline-success" type="submit">adicionar</button>
            <button class="btn btn-sm  btn-outline-danger mb-2" data-dismiss="modal" " type="button" >Cancelar</button>
          <fieldset>
        </form>`;

      Utilitarios.getModal('<h2>Logradouro</h2>', form);

  }

  static editar(tr){

    if(!tr){
      throw  new Error('Parâmetro inválido')
    }

      let form = ` 
      <form class="col" method="post" action="#" id="logradouroCandidato">
        <fieldset>
        <input type="hidden" name="tr-edit" value="${tr.attr('id')}">
          <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="estado">Sigla do estado</label>
                  <input value="${tr.find('td:eq(0)').text()}" type="text" name="estado" id="estado"class="form-control form-control-sm" required="required">
                </div>
                <div class="form-group">
                  <label for="cidade">Cidade</label>
                  <input value="${tr.find('td:eq(1)').text()}" type="text"  name="cidade" id="cidade"class="form-control form-control-sm" required="required">
                </div>
                <div class="form-group">
                  <label for="bairro">Bairro</label>
                  <input value="${tr.find('td:eq(2)').text()}" type="text" name="bairro"id="bairro" class="form-control form-control-sm" required="required">
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="endereco">Logradouro</label>
                  <input value="${tr.find('td:eq(3)').text()}" type="text" name="endereco"id="endereco" class="form-control form-control-sm" required="required">
                </div>
                <div class="form-group">
                  <label for="complemento">Complemento</label>
                  <input value="${tr.find('td:eq(4)').text()}" type="text" name="complemento" id="complemento"class="form-control form-control-sm" required="required">
                </div>
                <div class="row">
                  <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                      <label for="tipo">Tipo</label>
                      <select  name="tipo" id="tipo" class="form-control form-control-sm">
                        <option ${tr.find('td:eq(5)').text() == 'Casa' ? 'selected': ''} value="casa">Casa</option>
                        <option value="apartamento">Apartamento</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                      <label for="numero" required="required">Número</label>
                      <input value="${tr.find('td:eq(6)').text()}" type="number" name="numero" id="numero" class="form-control form-control-sm" required="required">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <button class="btn btn-success mb-2" type="submit">Editar</button>
            <button class="btn btn-danger mb-2 class="close" data-dismiss="modal" " type="button" >Cancelar</button>
          <fieldset>
        </form>`;

      Utilitarios.getModal('<h2>Logradouro - Editar</h2>', form);

  
  }

  static salvar(estado, cidade, bairro, endereco, numero, tipo, complemento, id){

    $('form#adicionarCandidato').append(
      $('<input/>')
        .attr('type', 'hidden').attr('name', 'estado[]').attr('id', 'estado-'+id).val(estado)
      )
    $('form#adicionarCandidato').append(

      $('<input/>')
        .attr('type', 'hidden').attr('name', 'cidade[]').attr('id', 'cidade-'+id).val(cidade)


      );


    $('form#adicionarCandidato').append(
      $('<input/>')
        .attr('type', 'hidden').attr('name', 'bairro[]').attr('id', 'bairro-'+id).val(bairro)
      );


    $('form#adicionarCandidato').append(
       $('<input/>')
        .attr('type', 'hidden').attr('name', 'endereco[]').attr('id', 'endereco-'+id).val(endereco)
      );


    $('form#adicionarCandidato').append(

      $('<input/>')
        .attr('type', 'hidden').attr('name', 'numero[]').attr('id', 'numero-'+id).val(numero)

      );


    $('form#adicionarCandidato').append(

      $('<input/>')
        .attr('type', 'hidden').attr('name', 'tipo[]').attr('id', 'tipo-'+id).val(tipo)

      );


    $('form#adicionarCandidato').append(
        $('<input/>')
        .attr('type', 'hidden').attr('name', 'complemento[]').attr('id', 'complemento-'+id).val(complemento)

      );


     



  }
  

}


/*----------------------  BASE DAS  MODELS ------------------------------------*/

class BaseModel
{
  constructor(){
    this.errors = [];
  }

  getErros(){
    let warning = '';

    if(this.errors.length > 0){
      for(let i = 0; !(i == this.errors.length) ; i++){
        warning += ' '+this.errors[i]+';';
      }
    }
    warning = warning.substring(0, warning.length - 1)

    return warning;

  }
}



/*------------------- MODEL DE USUARIO -------------------------------*/


class User extends BaseModel
{

  constructor(){

    super();

    this.email;
    this.senha;
    this.error = [];
  }

  setSenha(senha){

    if(!senha){
      this.error.push('Informe sua senha')
    }
    let trimSenha = senha.trim();

    if(trimSenha.length <= 6){
      this.error.push('Senha muito curta');
    }

    this.senha = trimSenha;

  }

  setEmail(email){

    if(!email){
      this.error.push('Informe sua email')
    }
    let trimEmail = email.trim();

    if(trimEmail.length < 0){
      this.error.push('Usuario inválido');
    }

    this.email = trimEmail;

  }


  getSenha(){
    if(!this.senha){
      this.error.push('Senha não definida')
    }else{
      return this.senha;
    }
     
  }

  getEmail(){

    if(!this.email){
      this.error.push('Usuario não definido')
    }else{
      return this.email;
    }


  }

  getError(){
    return this.error;
  }

}







/*---------------------- MODEL DE CANDIDATO ------------------*/

class Candidato extends BaseModel
{
  constructor(){

    super();

    this.nome;
    this.sobrenome;
    this.cpf;
    this.nascimento;
    this.sexo;
    this.email;
    this.senha;
    this.telefone = [];
  }


  setNome(nome){
    if((!nome) || (nome.trim().length < 3) ){
      this.errors.push('Nome inválido ou muito curto\n')
      return false;
    }

    this.nome = nome;
    return true;

  }

  setSobrenome(sobrenome){
    if((!sobrenome) || (sobrenome.trim().length < 3) ){
      this.errors.push('Sobrenome inválido ou muito curto\n')
      return false;
    }

    this.sobrenome = sobrenome;
    return true;

  }

  setCpf(cpf){
    if((!cpf) || (cpf.trim().length < 11) ){
      this.errors.push('Cpf inválido ou muito curto\n')
      return false;
    }

    let result = Utilitarios.validaCpf(cpf);

    if(result == false){
      this.errors.push('Cpf inválido\n')
    }

    this.cpf = cpf;
    return true;

  }


  setDtNascimento(nascimento){
    if((!nascimento) || (nascimento.trim().length == 0) ){
      this.errors.push('Data de nascimento inválida\n')
      return false;
    }

    this.nascimento = nascimento;
    return true;

  }

  setSexo(sexo){
    if((!sexo) || (sexo.trim().length == 0) ){
      this.errors.push('Sexo informado é inválido\n')
      console.log(sexo)
      return false;
    }

    if((sexo != 'm') && (sexo != 'f')){
      this.errors.push('Sexo informado é inválido\n')

      return false;
    }

    this.sexo = sexo;
    return true;

  }

  setEmail(email){
    if((!email) || (email.trim().length == 0) ){
      this.errors.push('E-mail informado é inválido\n')
      return false;
    }

    this.email = email;
    return true;

  }

  setSenha(senha){
    if((!senha) || (senha.trim().length < 6) || (senha.trim().length > 9)){
      this.errors.push('Senha deve ter entre 6 e 9 caracteres\n')
      return false;
    }

    this.senha = senha;
    return true;

  }

  setTelefone(telefone){
    if((!telefone) || (telefone.trim().length != 11)){
      this.errors.push('Telefone deve ter 11 caracteres\n')
      return false;
    }

    if(this.telefone.length == 2){
      this.errors.push('Informe apenas dois números para contato\n')
      return false;
    }

    this.telefone.push(telefone);
    return true;

  }


}

class Logradouro extends BaseModel{

  constructor(){

    super();

    this.estado;
    this.cidade;
    this.bairro;
    this.endereco;
    this.complemento;
    this.tipo;
    this.numero;
    
  }

  setEstado(estado){
    if((!estado) || (estado.trim().length != 2)){
      this.errors.push('Informe a sigaldo do estado com dua letras')
      return false;
    }

    this.estado = estado;

    return true;
  }

  getEstado(){
    if((this.estado) && (this.estado.length > 0)){
      
      return this.estado;
    }

    this.errors.push('Estado não definido')
    return false;

  }

  setCidade(cidade){

    if((!cidade) || (cidade.trim().length == 0)){
      this.errors.push('Informe uma ciade válida');
      return false;
    }

    this.cidade = cidade;

    return true;
 
  }

  getCidade(){
    
    if((this.cidade) && (this.cidade.length > 0)){
      
      return this.cidade;
    }

    this.errors.push('Cidade não definido')
    return false;

  }

  setBairro(bairro){

    if((!bairro) || (bairro.trim().length == 0)){
      this.errors.push('Informe uma ciade válida');
      return false;
    }

    this.bairro = bairro;
    
    return true;

  }

  getBairro(){
    
     if((this.bairro) && (this.bairro.length > 0)){
      
      return this.bairro;
    }

    this.errors.push('Bairro não definido')
    return false;

  }



  setEndereco(endereco){

    if((!endereco) || (endereco.trim().length == 0)){
      this.errors.push('Informe um logradouro válido');
      return false;
    }

    this.endereco = endereco;
    
    return true;

  }

  getEndereco(){
   
    if((this.endereco) && (this.endereco.length > 0)){
      
      return this.endereco;
    }

    this.errors.push('Endereco não definido')
    return false;

  }

  setComplemento(complemento){

    if((!complemento) || (complemento.trim().length == 0)){
      this.errors.push('Informe um complemento válido');
      return false;
    }

    this.complemento = complemento;
    
    return true;

  }

  getComplemento(){
    
    if((this.complemento) && (this.complemento.length > 0)){
      
      return this.complemento;
    }

    this.errors.push('Complemento não definido')
    return false;

  }

  setTipo(tipo){

    if((!tipo) || (tipo.trim().length == 0)){
      this.errors.push('Informe um tipo válido');
      return false;
    }

    if((tipo != 'casa') && (tipo != 'apartamento')){
      this.errors.push('Informe um tipo de logradouro válido');
      return false;
    }

    this.tipo = tipo;
    
    return true;

  }

  getTipo(){
    
    if((this.tipo) && (this.tipo.length > 0)){
      
      return this.tipo;
    }

    this.errors.push('Tipo não definido')
    return false;

  }

  setNumero(numero){

    if((!numero) || (numero.trim().length == 0)){
      this.errors.push('Informe um número válido');
      return false;
    }

    this.numero = numero;
    return true;
 
  }

  getNumero(){
    
    if((this.numero) && (this.numero.length > 0)){
      
      return this.numero;
    }

    this.errors.push('Numero não definido')
    return false;

  }


}

/*--------------------- CLASSE DE METODO UTILITARIOS -------------------------*/

class Utilitarios{

  static validaCpf(cpf){
    cpf = cpf.replace(/[^\d]+/g, '');

        if(cpf.length != 11){

          return false;
        }
        
        let splitCpf = cpf.split('');

        let digitoUm = 0;
        let digitoDois = 0;

        

        for (let i=0, x=1; !(i == 9 ); i++, x ++) { 
             digitoUm += splitCpf[i] * x;
        }

        

        for (let i=0,  y=0; !(i == 10 ); i++, y ++) { 

            let invaliCpf = '';

            for (let j = 0; !(j == 11); j++) {
              invaliCpf += i;
            }

            if(invaliCpf == cpf){
                return false;
            }


            digitoDois += splitCpf[i] * y;
        }

        let calculoUm = ((digitoUm % 11) == 10) ? 0 : (digitoUm % 11);
        let calculoDois = ((digitoDois % 11) == 10) ? 0 : (digitoDois % 11);

        if((calculoUm != splitCpf[9]) || (calculoDois != splitCpf[10])){

            return false;
        }

        return cpf;
  }

  /**
    Formata valores para calculo
  */
  static foramtCalcCod(number){
  

    number = String(number);
    

    if(number.length == 0){
      return false;
    }

    let arrNumber = number.split('.');

    let newNumber = '';
    for (let i =0; !(i == arrNumber.length); i++) {
      newNumber+=arrNumber[i]
    }


    newNumber = newNumber.replace(/,/g, '.');

    newNumber = parseFloat(newNumber).toFixed(2);

    return newNumber;


  }


  static getModal(titulo='Aguarde', body='', footer=''){

    let modal = $('#myModal');
    modal.find('.modal-header h4').html(titulo)
    modal.find('.modal-body').html(body);
    modal.find('.modal-footer').html(footer);

    modal.modal();

  }

  static message(obj, retorno){

    if((retorno.length == 3) &&  (retorno[0] == 'msg')){
      let msg = $('<div/>').addClass('alert alert-'+retorno[1]+' alert-dismissible fadeshow col-md-12');
      msg.append($('<button/>').addClass('close').attr('data-dismiss', 'alert').html('&times'))
      msg.attr('align', 'center').append('<h3>'+retorno[2]+'</h3>');
      msg.css('box-shadow', '2px 2px 3px #000');

      obj.html(msg);
      return true;
    }
    throw new Error('Parâmetro inválido')
  }


}
