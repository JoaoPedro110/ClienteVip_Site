<?php
session_start();

// Verificar se o cliente está logado
if (!isset($_SESSION['cliente'])) {
    header('Location: ../cliente/login.php');
    exit();
}

// Inclui dependências e inicia a sessão
require_once '../../../config/db.php';
require_once '../../models/Database.php';
require_once '../../models/Negocio.php';
require_once '../../controllers/NegocioController.php';
require_once '../../models/Empresa.php';
require_once '../../controllers/EmpresaController.php';

$negocioController = new NegocioController(new NegocioModel());
$empresaController = new EmpresaController(new EmpresaModel());
// Para mensagens de sucesso



// Lógica para exibir, editar e excluir negócios
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_GET['action'] == 'editar') {
        $negocioData = $negocioController->buscarPorID($id);
    } elseif ($_GET['action'] == 'excluir') {
        $resultado = $negocioController->excluir($id);
        $_SESSION['message'] = $resultado ? 'Negócio excluído com sucesso!' : 'Erro ao excluir negócio!';
        $_SESSION['messageType'] = $resultado ? 'success' : 'error';
        header("Location: negocios.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Atualizar negócio existente
        $dadosAtualizados = [
            'data_negocio' => $_POST['data_negocio'],
            'valor_transacao' => $_POST['valor_transacao'],
            'notas' => $_POST['notas'],
            'status' => $_POST['status'],
            'titulo' => $_POST['titulo'],
            'descricao' => $_POST['descricao'],
            'empresa_id' => $_POST['empresa_id']
        ];
        $resultado = $negocioController->atualizar($_POST['id'], $dadosAtualizados);
        $_SESSION['message'] = $resultado ? 'Negócio atualizado com sucesso!' : 'Erro ao atualizar negócio!';
        $_SESSION['messageType'] = $resultado ? 'success' : 'error';
        header("Location: negocios.php");
        exit();
    } elseif (isset($_POST['cadastrar_negocio'])) {
        // Cadastrar novo negócio
        $dadosNegocio = [
            'data_negocio' => $_POST['data_negocio'],
            'valor_transacao' => $_POST['valor_transacao'],
            'notas' => $_POST['notas'],
            'status' => $_POST['status'],
            'titulo' => $_POST['titulo'],
            'descricao' => $_POST['descricao'],
            'empresa_id' => $_POST['empresa_id']
        ];
        $resultado = $negocioController->cadastrar($dadosNegocio);
        $_SESSION['message'] = $resultado ? 'Negócio cadastrado com sucesso!' : 'Erro ao cadastrar negócio!';
        $_SESSION['messageType'] = $resultado ? 'success' : 'error';
        header("Location: negocios.php");
        exit();
    }
}

$empresas = $empresaController->listarEmpresas();
$negocios = $negocioController->listar();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Negócios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="../../../public/css/negocio.css">
</head>

<body>
    <?php include '../layout/header_logado.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4"><i class="fas fa-business-time me-1"></i> Gestão de Negócios</h1>

        <!-- Botões para gerenciar os formulários -->
        <div class="d-flex justify-content-between mb-4">
            <button id="addLeadButton" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Adicionar Lead
            </button>
            <button id="closeFormsButton" class="btn btn-danger" style="display: none;">
                <i class="fas fa-times-circle"></i> Fechar Formulários
            </button>
        </div>

        <!-- Formulário de cadastro de novo lead -->
        <div id="addLeadFormContainer" class="card shadow mb-4" style="display: none;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-briefcase"></i> Cadastrar Negócio</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="negocios.php">
                    <input type="hidden" name="cadastrar_negocio" value="1">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="data_negocio" class="form-label">Data do Negócio</label>
                            <input type="date" class="form-control" name="data_negocio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="valor_transacao" class="form-label">Valor da Transação</label>
                            <input type="number" class="form-control" name="valor_transacao" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Aberto">Aberto</option>
                            <option value="Em Progresso">Em Progresso</option>
                            <option value="Fechado">Fechado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="empresa_id" class="form-label">Empresa</label>
                        <select name="empresa_id" class="form-select" required>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= htmlspecialchars($empresa['empresa_id']) ?>"><?= htmlspecialchars($empresa['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Negócio</button>
                </form>
            </div>
        </div>

        <!-- Formulário de edição de lead (aparece se $negocioData for definido) -->
        <?php if (isset($negocioData)): ?>
            <div id="editLeadFormContainer" class="card shadow mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Editar Negócio</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="negocios.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($negocioData['id_negocio']) ?>">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="data_negocio" class="form-label">Data do Negócio</label>
                                <input type="date" class="form-control" name="data_negocio" value="<?= htmlspecialchars($negocioData['data_negocio']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="valor_transacao" class="form-label">Valor da Transação</label>
                                <input type="number" class="form-control" name="valor_transacao" value="<?= htmlspecialchars($negocioData['valor_transacao']) ?>" step="0.01" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Aberto" <?= $negocioData['status'] == 'Aberto' ? 'selected' : '' ?>>Aberto</option>
                                <option value="Em Progresso" <?= $negocioData['status'] == 'Em Progresso' ? 'selected' : '' ?>>Em Progresso</option>
                                <option value="Fechado" <?= $negocioData['status'] == 'Fechado' ? 'selected' : '' ?>>Fechado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" name="titulo" value="<?= htmlspecialchars($negocioData['titulo']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($negocioData['descricao']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="empresa_id" class="form-label">Empresa</label>
                            <select name="empresa_id" class="form-select" required>
                                <?php foreach ($empresas as $empresa): ?>
                                    <option value="<?= htmlspecialchars($empresa['empresa_id']) ?>" <?= $empresa['empresa_id'] == $negocioData['empresa_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($empresa['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Atualizar Negócio</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lista de negócios -->
        <div class="card">
            <div class="card-header bg-dark text-white">Lista de Negócios</div>
            <div class="card-body table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Título</th>
                            <th>Empresa</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($negocios as $negocio): ?>
                            <tr>
                                <td><?= htmlspecialchars($negocio['data_negocio']) ?></td>
                                <td><?= htmlspecialchars($negocio['valor_transacao']) ?></td>
                                <td><?= htmlspecialchars($negocio['status']) ?></td>
                                <td><?= htmlspecialchars($negocio['titulo']) ?></td>
                                <td><?= htmlspecialchars($negocio['empresa_nome']) ?></td>
                                <td>
                                    <a href="negocios.php?action=editar&id=<?= urlencode($negocio['id_negocio']) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="negocios.php?action=excluir&id=<?= urlencode($negocio['id_negocio']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deseja excluir este negócio?')">
                                        <i class="fas fa-trash-alt"></i> Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <script>
        const addLeadButton = document.getElementById('addLeadButton');
        const closeFormsButton = document.getElementById('closeFormsButton');
        const addLeadFormContainer = document.getElementById('addLeadFormContainer');
        const editLeadFormContainer = document.getElementById('editLeadFormContainer');

        addLeadButton.addEventListener('click', function() {
            addLeadFormContainer.style.display = 'block';
            if (editLeadFormContainer) editLeadFormContainer.style.display = 'none';
            addLeadButton.style.display = 'none';
            closeFormsButton.style.display = 'inline-block';
        });

        closeFormsButton.addEventListener('click', function() {
            addLeadFormContainer.style.display = 'none';
            if (editLeadFormContainer) editLeadFormContainer.style.display = 'none';
            closeFormsButton.style.display = 'none';
            addLeadButton.style.display = 'inline-block';
        });

        <?php if (isset($negocioData)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                editLeadFormContainer.style.display = 'block';
                addLeadFormContainer.style.display = 'none';
                addLeadButton.style.display = 'none';
                closeFormsButton.style.display = 'inline-block';
            });
        <?php endif; ?>
    </script>
</body>

</html>