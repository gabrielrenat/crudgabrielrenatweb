<?php
class Form
{
  private $message = "";
  public function __construct()
  {
    Transaction::open();
  }
  public function controller()
  {
    $form = new Template("view/form.html");
    $this->message = $form->saida();
  }
  public function salvar()
  {
    if (isset($_POST["titulo"]) && isset($_POST["autor"]) && isset($_POST["resenha"])) {
      try {
        $conexao = Transaction::get();
        $livro = new Crud("livro");
        $titulo = $conexao->quote($_POST["titulo"]);
        $autor = $conexao->quote($_POST["autor"]);
        $resenha = $conexao->quote($_POST["resenha"]);
        $resultado = $livro->insert("titulo, autor, resenha", "$titulo, $autor, $resenha");
      } catch (Exception $e) {
        echo $e->getMessage();
      }
    }
  }
  public function getMessage()
  {
    return $this->message;
  }
  public function __destruct()
  {
    Transaction::close();
  }
}