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
    $form->set("trajeto", "");
    $form->set("aeronave", "");
    $form->set("horario", "");
    $this->message = $form->saida();
  }
  public function salvar()
  {
    if (isset($_POST["trajeto"]) && isset($_POST["aeronave"]) && isset($_POST["horario"])) {
      try {
        $conexao = Transaction::get();
        $voo = new Crud("voo");
        $trajeto = $conexao->quote($_POST["trajeto"]);
        $aeronave = $conexao->quote($_POST["aeronave"]);
        $horario = $conexao->quote($_POST["horario"]);
        if (empty($_POST["id"])) {
          $voo->insert(
            "trajeto, aeronave, horario",
            "$trajeto, $aeronave, $horario"
          );
        } else {
          $id = $conexao->quote($_POST["id"]);
          $voo->update(
            "trajeto = $trajeto, aeronave = $aeronave, horario = $horario",
            "id = $id"
          );
        }
        $this->message = $voo->getMessage();
        $this->error = $voo->getError();
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
        $voo = new Crud("voo");
        $resultado = $voo->select("*", "id = $id");
        if (!$voo->getError()) {
          $form = new Template("view/form.html");
          foreach ($resultado[0] as $cod => $horario) {
            $form->set($cod, $horario);
          }
          $this->message = $form->saida();
        } else {
          $this->message = $voo->getMessage();
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