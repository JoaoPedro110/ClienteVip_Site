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
require_once '../../models/Tarefas.php';
require_once '../../controllers/TarefasController.php';
require_once '../../models/Empresa.php';
require_once '../../controllers/EmpresaController.php';

$tarefaController = new TarefaController(new TarefaModel());
$empresaController = new EmpresaController(new EmpresaModel());

// Lógica para exibir, editar e excluir tarefas
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_GET['action'] == 'editar') {
        $tarefaData = $tarefaController->listarPorId($id);
    } elseif ($_GET['action'] == 'excluir') {
        $resultado = $tarefaController->excluir($id);
        $_SESSION['mensagem'] = [
            'tipo' => $resultado ? 'success' : 'error',
            'texto' => $resultado ? 'Tarefa excluída com sucesso!' : 'Erro ao excluir tarefa!'
        ];
        $_SESSION['redirecionar'] = true;
        header("Location: tarefas.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dadosTarefa = [
        'titulo' => $_POST['titulo'],
        'descricao' => $_POST['descricao'],
        'status' => $_POST['status'],
        'prioridade' => $_POST['prioridade'],
        'empresa_id' => $_POST['empresa_id'],
        'data_termino' => $_POST['data_termino']
    ];

    if (!empty($_POST['id'])) {
        // Atualizar tarefa existente
        $resultado = $tarefaController->editar($_POST['id'], $dadosTarefa);
        $_SESSION['mensagem'] = [
            'tipo' => $resultado ? 'success' : 'error',
            'texto' => $resultado ? 'Tarefa editada com sucesso!' : 'Erro ao editar tarefa.'
        ];
    } else {
        // Cadastrar nova tarefa
        $resultado = $tarefaController->cadastrar($dadosTarefa);
        $_SESSION['mensagem'] = [
            'tipo' => $resultado ? 'success' : 'error',
            'texto' => $resultado ? 'Tarefa cadastrada com sucesso!' : 'Erro ao cadastrar tarefa.'
        ];
    }

    $_SESSION['redirecionar'] = true;
    header("Location: tarefas.php");
    exit();
}

$empresas = $empresaController->listarEmpresas();
$tarefas = $tarefaController->listar();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Tarefas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php include '../layout/header_logado.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center mb-4"><i class="fas fa-tasks me-1"></i>Gestão de Tarefas</h1>

        <!-- Botão de adicionar tarefa -->
        <div class="d-flex justify-content-between mb-4">
            <button id="addTarefaButton" class="btn btn-success w-md-auto">
                <i class="fas fa-plus-circle"></i> Adicionar Tarefa
            </button>
            <button id="closeFormsButton" class="btn btn-danger" style="display: none;">
                <i class="fas fa-times-circle"></i> Fechar Formulários
            </button>
        </div>

        <!-- Formulário de Cadastro de Tarefa -->
        <div id="addTarefaFormContainer" class="card shadow mb-4" style="display: none;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-briefcase"></i> Cadastrar Tarefa</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="tarefas.php">
                    <input type="hidden" name="cadastrar_tarefa" value="1">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" name="titulo" required>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-3">
                            <label for="prioridade" class="form-label">Prioridade</label>
                            <select name="prioridade" class="form-select" required>
                                <option value="Alta">Alta</option>
                                <option value="Média">Média</option>
                                <option value="Baixa">Baixa</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Pendente">Pendente</option>
                            <option value="Em Progresso">Em Progresso</option>
                            <option value="Concluída">Concluída</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="data_termino" class="form-label">Data de Término</label>
                        <input type="date" class="form-control" name="data_termino" required>
                    </div>
                    <div class="mb-3">
                        <label for="empresa_id" class="form-label">Empresa</label>
                        <select name="empresa_id" class="form-select" required>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= htmlspecialchars($empresa['empresa_id']) ?>"><?= htmlspecialchars($empresa['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Salvar Tarefa</button>
                </form>
            </div>
        </div>

        <!-- Formulário de edição de tarefa -->
        <?php if (isset($tarefaData)): ?>
            <div id="editTarefaFormContainer" class="card shadow mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Editar Tarefa</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="tarefas.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($tarefaData['id']) ?>">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" name="titulo" value="<?= htmlspecialchars($tarefaData['titulo']) ?>" required>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-3">
                                <label for="prioridade" class="form-label">Prioridade</label>
                                <select name="prioridade" class="form-select" required>
                                    <option value="Alta" <?= $tarefaData['prioridade'] === 'Alta' ? 'selected' : '' ?>>Alta</option>
                                    <option value="Média" <?= $tarefaData['prioridade'] === 'Média' ? 'selected' : '' ?>>Média</option>
                                    <option value="Baixa" <?= $tarefaData['prioridade'] === 'Baixa' ? 'selected' : '' ?>>Baixa</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Pendente" <?= $tarefaData['status'] === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="Em Progresso" <?= $tarefaData['status'] === 'Em Progresso' ? 'selected' : '' ?>>Em Progresso</option>
                                <option value="Concluída" <?= $tarefaData['status'] === 'Concluída' ? 'selected' : '' ?>>Concluída</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($tarefaData['descricao']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="data_termino" class="form-label">Data de Término</label>
                            <input type="date" class="form-control" name="data_termino" value="<?= htmlspecialchars($tarefaData['data_termino']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="empresa_id" class="form-label">Empresa</label>
                            <select name="empresa_id" class="form-select" required>
                                <?php foreach ($empresas as $empresa): ?>
                                    <option value="<?= htmlspecialchars($empresa['empresa_id']) ?>" <?= $empresa['empresa_id'] === $tarefaData['empresa_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($empresa['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning w-100"><i class="fas fa-save"></i> Atualizar Tarefa</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabela de tarefas -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">Lista de Tarefas</div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Título</th>
                                    <th>Status</th>
                                    <th>Prioridade</th>
                                    <th>Empresa</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tarefas as $tarefa): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($tarefa['titulo'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($tarefa['status'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($tarefa['prioridade'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($tarefa['empresa_nome'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php
                                            $dataTermino = isset($tarefa['data_termino']) ? date("d/m/Y", strtotime($tarefa['data_termino'])) : 'N/A';
                                            echo htmlspecialchars($dataTermino);
                                            ?>
                                        </td>
                                        <td>
                                            <a href="tarefas.php?action=editar&id=<?= urlencode($tarefa['id'] ?? '') ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="tarefas.php?action=excluir&id=<?= urlencode($tarefa['id'] ?? '') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">
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
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        const addTarefaButton = document.getElementById('addTarefaButton');
        const closeFormsButton = document.getElementById('closeFormsButton');
        const addTarefaFormContainer = document.getElementById('addTarefaFormContainer');
        const editTarefaFormContainer = document.getElementById('editTarefaFormContainer');

        // Exibir formulário de adicionar tarefa
        addTarefaButton.addEventListener('click', function () {
            addTarefaFormContainer.style.display = 'block';
            if (editTarefaFormContainer) editTarefaFormContainer.style.display = 'none';
            addTarefaButton.style.display = 'none';
            closeFormsButton.style.display = 'inline-block';
        });

        // Fechar todos os formulários
        closeFormsButton.addEventListener('click', function () {
            if (confirm('Tem certeza que deseja fechar os formulários? Dados não salvos serão perdidos.')) {
                addTarefaFormContainer.style.display = 'none';
                if (editTarefaFormContainer) editTarefaFormContainer.style.display = 'none';
                closeFormsButton.style.display = 'none';
                addTarefaButton.style.display = 'inline-block';
            }
        });

        // Mostrar formulário de edição se existir
        <?php if (isset($tarefaData)): ?>
        document.addEventListener('DOMContentLoaded', function () {
            editTarefaFormContainer.style.display = 'block';
            addTarefaFormContainer.style.display = 'none';
            addTarefaButton.style.display = 'none';
            closeFormsButton.style.display = 'inline-block';
        });
        <?php endif; ?>
    </script>
</body>

</html>
