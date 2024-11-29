<?php
require_once '../../../config/db.php';
require_once '../../Models/Database.php';
require_once '../../models/Lead.php';
require_once '../../controllers/LeadController.php';

$controller = new LeadController(new LeadModel());

session_start();

// Verificar se o cliente está logado
if (!isset($_SESSION['cliente'])) {
    header('Location: ../cliente/login.php');
    exit();
}

$leadData = null;
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_GET['action'] == 'editar') {
        $leadData = $controller->buscarPorID($id);
    } elseif ($_GET['action'] == 'excluir') {
        $result = $controller->excluir($id);
        $_SESSION['message'] = $result['message'];
        $_SESSION['messageType'] = $result['success'] ? 'alert-success' : 'alert-danger';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $result = $controller->atualizar($_POST['id'], $_POST);
    } else {
        $result = $controller->cadastrar($_POST);
    }
    $_SESSION['message'] = $result['message'];
    $_SESSION['messageType'] = $result['success'] ? 'alert-success' : 'alert-danger';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'];
    unset($_SESSION['message'], $_SESSION['messageType']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Leads</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../../public/css/lead.css">
</head>

<body>
    <?php include '../layout/header_logado.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center my-4"><i class="fas fa-clipboard-check me-1"></i> Gestão de Leads</h1>


        <?php if (isset($message)): ?>
            <div id="feedbackMessage" class="alert <?= $messageType ?> text-center mt-4">
                <?= $message ?>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById("feedbackMessage").style.display = "none";
                }, 5000);
            </script>
        <?php endif; ?>

        <div class="d-flex justify-content-between mb-4">
            <button id="addLeadButton" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Adicionar Lead
            </button>
            <button id="closeFormsButton" class="btn btn-danger" style="display: none;">
                <i class="fas fa-times-circle"></i> Fechar Formulários
            </button>

        </div>

        <div id="addLeadFormContainer" class="card mb-4" style="display: none;">
            <div class="card-header bg-primary text-white">Cadastrar Lead</div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="id" value="">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="col">
                            <label for="status" class="form-label">Status</label>
                            <input type="text" class="form-control" id="status" name="status" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col">
                            <label for="empresa" class="form-label">Empresa</label>
                            <input type="text" class="form-control" id="empresa" name="empresa" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="cargo" class="form-label">Cargo</label>
                            <input type="text" class="form-control" id="cargo" name="cargo">
                        </div>
                        <div class="col">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mensagem" class="form-label">Descrição</label>
                        <textarea class="form-control" id="mensagem" name="mensagem"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar Lead</button>
                </form>
            </div>
        </div>

        <div id="editLeadFormContainer" class="card mb-4" style="display: none;">
            <div class="card-header bg-warning text-white">Editar Lead</div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= $leadData['id'] ?? '' ?>">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="editNome" name="nome" required value="<?= $leadData['nome'] ?? '' ?>">
                        </div>
                        <div class="col">
                            <label for="status" class="form-label">Status</label>
                            <input type="text" class="form-control" id="editStatus" name="status" required value="<?= $leadData['status'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required value="<?= $leadData['email'] ?? '' ?>">
                        </div>
                        <div class="col">
                            <label for="empresa" class="form-label">Empresa</label>
                            <input type="text" class="form-control" id="editEmpresa" name="empresa" required value="<?= $leadData['empresa'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="cargo" class="form-label">Cargo</label>
                            <input type="text" class="form-control" id="editCargo" name="cargo" value="<?= $leadData['cargo'] ?? '' ?>">
                        </div>
                        <div class="col">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="editTelefone" name="telefone" value="<?= $leadData['telefone'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mensagem" class="form-label">Descrição</label>
                        <textarea class="form-control" id="editMensagem" name="mensagem"><?= $leadData['mensagem'] ?? '' ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-pencil"></i> Atualizar Lead</button>
                </form>

            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">Lista de Leads</div>
            <div class="card-body table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Status</th>
                            <th>Email</th>
                            <th>Empresa</th>
                            <th>Cargo</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $leads = $controller->listar();
                        foreach ($leads as $lead) {
                            echo "<tr>";
                            echo "<td>{$lead['nome']}</td>";
                            echo "<td>{$lead['status']}</td>";
                            echo "<td>{$lead['email']}</td>";
                            echo "<td>{$lead['empresa']}</td>";
                            echo "<td>{$lead['cargo']}</td>";
                            echo "<td>{$lead['telefone']}</td>";
                            echo "<td>";
                            echo "<a href='?action=editar&id={$lead['id']}' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i> Editar</a> ";
                            echo "<a href='?action=excluir&id={$lead['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Tem certeza que deseja excluir este lead?\")'><i class='bi bi-trash'></i> Excluir</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Referências aos elementos do DOM
        const addLeadButton = document.getElementById('addLeadButton');
        const closeFormsButton = document.getElementById('closeFormsButton');
        const addLeadFormContainer = document.getElementById('addLeadFormContainer');
        const editLeadFormContainer = document.getElementById('editLeadFormContainer');

        // Função para ocultar todos os formulários
        function hideForms() {
            addLeadFormContainer.style.display = 'none';
            editLeadFormContainer.style.display = 'none';
            closeFormsButton.style.display = 'none';
            addLeadButton.style.display = 'inline-block';
        }

        // Função para exibir o formulário de adicionar
        function showAddForm() {
            addLeadFormContainer.style.display = 'block';
            editLeadFormContainer.style.display = 'none';
            addLeadButton.style.display = 'none';
            closeFormsButton.style.display = 'inline-block';
        }

        // Função para exibir o formulário de edição
        function showEditForm() {
            editLeadFormContainer.style.display = 'block';
            addLeadFormContainer.style.display = 'none';
            addLeadButton.style.display = 'none';
            closeFormsButton.style.display = 'inline-block';
        }

        // Evento para o botão "Adicionar Lead"
        addLeadButton.addEventListener('click', showAddForm);

        // Evento para o botão "Fechar Formulários"
        closeFormsButton.addEventListener('click', function() {
            if (confirm('Você tem certeza que deseja fechar os formulários? Dados não salvos serão perdidos.')) {
                hideForms();
            }
        });

        // Verificar se existe leadData (edição ativa) ao carregar a página
        <?php if (isset($leadData)): ?>
            document.addEventListener('DOMContentLoaded', showEditForm);
        <?php endif; ?>
    </script>

    <?php include '../layout/footer.php'; ?>
</body>

</html>