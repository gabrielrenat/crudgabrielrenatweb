<?php
class Form
{
  private $message = "";
  private $error = "";
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
        $this->message = $livro->getMessage();
        $this->error = $livro->getError();
      } catch (Exception $e) {
        $this->message = $e->getMessage();
        $this->error = true;
      }
    } else {
      $this->message = "Campos não informados!";
      $this->error = true;
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
        if (!$livro->getError()) {
          $form = new Template("view/form.html");
          foreach ($resultado[0] as $cod => $resenha) {
            $form->set($cod, $resenha);
          }
          $this->message = $form->saida();
        } else {
          $this->message = $livro->getMessage();
          $this->error = true;
        }
      } catch (Exception $e) {
        $this->message = $e->getMessage();
        $this->error = true;
      }
    }
  }
  public function getMessage()
  {
    if (is_string($this->error)) {
      return $this->message;
    } else {
      $msg = new Template("view/msg.html");
      if ($this->error) {
        $msg->set("cor", "danger");
      } else {
        $msg->set("cor", "success");
      }
      $msg->set("msg", $this->message);
      $msg->set("uri", "?class=Tabela");
      return $msg->saida();
    }
  }
  public function __destruct()
  {
    Transaction::close();
  }
}