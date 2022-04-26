<?php
class Tabela
{
  private $message = "";
  public function controller()
  { 
    Transaction::get();
    $livro = new Crud("livro");
    $resultado = $livro->select();
    print_r($resultado);
    $this->message = "Estou na classe Tabela";
  }
  public function getMessage()
  {
    return $this->message;
  }
}
