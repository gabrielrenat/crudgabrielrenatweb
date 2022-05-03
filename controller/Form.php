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
    $form->set("id", "");
    $form->set("titulo", "");
    $form->set("autor", "");
    $form->set("resenha", "");
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
        if (empty($_POST["id"])) {
          $livro->insert(
            "titulo, autor, resenha",
            "$titulo, $autor, $resenha"
          );
        } else {
          $id = $conexao->quote($_POST["id"]);
          $livro->update(
            "titulo = $titulo, autor = $autor, resenha = $resenha",
            "id = $id"
          );
        }
      } catch (Exception $e) {
        echo $e->getMessage();
      }
    }
  }
  public function editar()
  {
    if (isset($_GET["id"])) {
      try {
        $conexao = Transaction::get();
        $id = $conexao->quote($_GET["id"]);
        $livro = new Crud("livro");
        $resultado = $livro->select("*", "id = $id");
        $form = new Template("view/form.html");
        foreach ($resultado[0] as $cod => $valor) {
          $form->set($cod, $valor);
        }
        $this->message = $form->saida();
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