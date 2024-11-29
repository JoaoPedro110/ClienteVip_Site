<?php
require_once '../../../config/db.php';
require_once '../../Models/Database.php';
require_once '../../Models/Cliente.php';
require_once '../../Controllers/ClienteController.php';

session_start(); // Inicia a sessão

// Verificar se existe uma sessão ou cookie de CPF válido
if (!isset($_SESSION['cpf']) && !isset($_COOKIE['cpf'])) {
    header('Location: ../cliente/login.php'); // Redireciona para login
    exit();
}

// Restaurar sessão do cookie se necessário
if (!isset($_SESSION['cpf']) && isset($_COOKIE['cpf'])) {
    $_SESSION['cpf'] = $_COOKIE['cpf'];
}

// Instanciar modelo e controlador
$model = new ClienteModel();
$controller = new ClienteController($model);

// Buscar cliente pelo CPF na sessão
$cpf = $_SESSION['cpf'];
$cliente = $controller->buscarPorCPF($cpf);

if (!$cliente) {
    echo '<div class="alert alert-danger text-center mt-4">Erro: Cliente não encontrado.</div>';
    exit();
}

// Lógica para alteração de senha ou atualização de dados via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json'); // Define o cabeçalho para JSON

    try {
        // Alteração de senha com verificação da senha atual
        if (isset($_POST['nova_senha']) && isset($_POST['senha_atual'])) {
            $novaSenha = $_POST['nova_senha'];
            $senhaAtual = $_POST['senha_atual'];

            // Chama o método redefinirSenha no Controller
            $resultado = $controller->redefinirSenha($cpf, $senhaAtual, $novaSenha);

            // Retorna a resposta em JSON com o resultado
            echo json_encode($resultado);
            exit();
        }

        // Atualização dos dados do cliente
        $dadosAtualizados = [
            'nome' => $_POST['nome'],
            'telefone' => $_POST['telefone'],
            'email' => $_POST['email'],
            'endereco_completo' => $_POST['endereco_completo'],
            'idade' => $_POST['idade'],
            'genero' => $_POST['genero']
        ];

        if ($controller->atualizarCliente($cpf, $dadosAtualizados)) {
            echo json_encode(['success' => true, 'message' => 'Dados atualizados com sucesso.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar os dados.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Cliente</title>
    <link rel="stylesheet" href="../../../public/css/Perfil.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <?php include '../layout/header_logado.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-4">
                <div class="list-group shadow-sm">
                    <a href="#" class="list-group-item active" id="btnInformacoes">Informações pessoais</a>
                    <a href="#" class="list-group-item" id="btnSenha">Senha</a>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Seção de informações pessoais -->
                <div class="card card-profile shadow-sm" id="secaoInformacoes">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h3 class="mt-3"><?php echo htmlspecialchars($cliente['nome']); ?></h3>
                            <p class="text-muted">Membro</p>
                        </div>

                        <!-- Div para exibir feedback de sucesso ou erro -->
                        <div id="mensagem" class="alert" style="display:none;"></div>

                        <!-- Profile Information -->
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <strong>Email:</strong>
                            <span><?php echo htmlspecialchars($cliente['email']); ?></span>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <strong>Telefone:</strong>
                            <span><?php echo htmlspecialchars($cliente['telefone']); ?></span>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-id-card Parede_Informacao"></i>
                            <strong>CPF:</strong>
                            <span><?php echo htmlspecialchars($cliente['cpf']); ?></span>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <strong>Endereço:</strong>
                            <span><?php echo htmlspecialchars($cliente['endereco_completo']); ?></span>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-lock"></i>
                            <strong>Senha:</strong>
                            <span>*****</span>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-birthday-cake Parede_Informacao"></i>
                            <strong class="">Idade:</strong>
                            <span><?php echo htmlspecialchars($cliente['idade']); ?></span>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-venus-mars Parede_Informacao2"></i>
                            <strong class="">Gênero:</strong>
                            <span><?php echo htmlspecialchars($cliente['genero']); ?></span>
                        </div>

                        <!-- Button to trigger the edit form -->
                        <button type="button" class="btn btn-primary mt-3" id="editarInformacoesBtn">
                            Editar Informações
                        </button>

                        <!-- Formulário de Edição (escondido por padrão) -->
                        <div id="formEdicao" style="display: none; margin-top: 20px;">
                            <form id="editarClienteForm">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="endereco" class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo htmlspecialchars($cliente['endereco']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="idade" class="form-label">Idade</label>
                                    <input type="number" class="form-control" id="idade" name="idade" value="<?php echo htmlspecialchars($cliente['idade']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="genero" class="form-label">Gênero</label>
                                    <select class="form-control" id="genero" name="genero" required>
                                        <option value="M" <?php echo ($cliente['genero'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                        <option value="F" <?php echo ($cliente['genero'] == 'F') ? 'selected' : ''; ?>>Feminino</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success">Salvar Alterações</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Seção de alteração de senha -->
                <div class="card card-profile shadow-sm" id="secaoSenha" style="display: none;">
                    <div class="card-body">
                        <h3 class="text-center">Alterar Senha</h3>

                        <div id="mensagemSenha" class="alert" style="display: none;"></div>

                        <form id="alterarSenhaForm">
                            <!-- Campo de Nova Senha -->
                            <div class="mb-3">
                                <label for="novaSenha" class="form-label">Nova Senha</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="novaSenha" name="nova_senha" required>
                                </div>
                            </div>

                            <!-- Campo de Senha Atual -->
                            <div class="mb-3">
                                <label for="senhaAtual" class="form-label">Senha Atual</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="senhaAtual" name="senha_atual" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Alterar Senha</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Alternar entre informações e senha
            $('#btnInformacoes').click(function() {
                $('#secaoInformacoes').show();
                $('#secaoSenha').hide();
                $('#mensagem').hide();
                $('#mensagemSenha').hide();
                $('#formEdicao').hide();
            });

            $('#btnSenha').click(function() {
                $('#secaoSenha').show();
                $('#secaoInformacoes').hide();
                $('#mensagem').hide();
                $('#mensagemSenha').hide();
            });

            // Mostrar o formulário de edição
            $('#editarInformacoesBtn').click(function() {
                $('#formEdicao').toggle();
                $('#mensagem').hide(); // Esconde mensagem anterior
            });

            // Submeter o formulário de edição
            $('#editarClienteForm').on('submit', function(e) {
                e.preventDefault(); // Evita o envio padrão do formulário

                $.ajax({
                    type: 'POST',
                    url: '', // Aqui você pode definir a URL correta se necessário
                    data: $(this).serialize(), // Serializa os dados do formulário
                    success: function(response) {
                        console.log(response); // Para verificar a resposta do servidor
                        if (response.success) {
                            $('#mensagem').show();
                            $('#mensagem').removeClass('alert-danger').addClass('alert-success').html(response.message);

                            // Recarregar a página e fechar o formulário de edição
                            $('#formEdicao').hide(); // Fecha o formulário de edição
                            location.reload(); // Recarrega a página
                        } else {
                            $('#mensagem').show();
                            $('#mensagem').removeClass('alert-success').addClass('alert-danger').html(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Para verificar o erro no console
                        $('#mensagem').show();
                        $('#mensagem').removeClass('alert-success').addClass('alert-danger').html('Erro: ' + xhr.responseJSON.message);
                    }
                });
            });

            // Submeter o formulário de alteração de senha
            $('#alterarSenhaForm').on('submit', function(e) {
                e.preventDefault(); // Evita o envio padrão do formulário

                $.ajax({
                    type: 'POST',
                    url: '', // Aqui você pode definir a URL correta se necessário
                    data: $(this).serialize(), // Serializa os dados do formulário
                    success: function(response) {
                        console.log(response); // Para verificar a resposta do servidor
                        if (response.success) {
                            $('#mensagemSenha').show();
                            $('#mensagemSenha').removeClass('alert-danger').addClass('alert-success').html(response.message);
                        } else {
                            $('#mensagemSenha').show();
                            $('#mensagemSenha').removeClass('alert-success').addClass('alert-danger').html(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Para verificar o erro no console
                        $('#mensagemSenha').show();
                        $('#mensagemSenha').removeClass('alert-success').addClass('alert-danger').html('Erro: ' + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
</body>

</html>