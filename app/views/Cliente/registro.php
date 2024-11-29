<?php
session_start(); // Inicia a sessão

require_once '../../../config/db.php'; // Arquivo de configuração do banco
require_once '../../Models/Database.php'; // Classe de manipulação do banco
require_once '../../Models/Cliente.php'; // Modelo de cliente
require_once '../../Controllers/ClienteController.php'; // Controlador de cliente

// Inicializa o modelo e o controlador
$clienteModel = new ClienteModel();
$clienteController = new ClienteController($clienteModel);

$mensagem = null; // Variável para armazenar a mensagem de erro/sucesso

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados enviados pelo formulário
    $dados = [
        'nome' => $_POST['nome'] ?? '',
        'cpf' => $_POST['cpf'] ?? '',
        'cep' => $_POST['cep'] ?? '',
        'logradouro' => $_POST['logradouro'] ?? '',
        'bairro' => $_POST['bairro'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? '',
        'numero' => $_POST['numero'] ?? 'S/N',
        'complemento' => $_POST['complemento'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'idade' => $_POST['idade'] ?? '',
        'email' => $_POST['email'] ?? '',
        'genero' => $_POST['genero'] ?? '',
        'senha' => $_POST['senha'] ?? ''
    ];

    // Verifica se os campos obrigatórios estão preenchidos
    if (empty($dados['nome']) || empty($dados['cpf']) || empty($dados['email']) || empty($dados['senha'])) {
        $mensagem = [
            'tipo' => 'error',
            'texto' => 'Preencha todos os campos obrigatórios.'
        ];
    } else {
        // Chama o método de cadastro do controlador
        $resultado = $clienteController->cadastrar($dados);

        if ($resultado['status'] === 'success') {
            // Define a mensagem de sucesso
            $mensagem = [
                'tipo' => 'success',
                'texto' => 'Cliente cadastrado com sucesso. Redirecionando para o login...'
            ];
            // Define o redirecionamento para a página de login
            $redirecionar = true;
        } else {
            // Define a mensagem de erro
            $mensagem = [
                'tipo' => 'error',
                'texto' => $resultado['message']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include '../layout/header_publico.php'; ?>
    <title>Cadastro de Cliente</title>
    <link rel="stylesheet" href="../../../public/css/Cadastro.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="form-container">
            <h1>Cadastro de Cliente</h1>
            <form method="post" action="">
                <div class="form-group row mb-3">
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="nome" placeholder="Nome" required>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="cpf" placeholder="CPF" required>
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="cep" name="cep" placeholder="CEP" required>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="logradouro" name="logradouro" placeholder="Logradouro" readonly required>
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="bairro" name="bairro" placeholder="Bairro" readonly>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade" readonly>
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="estado" name="estado" placeholder="Estado" readonly>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="numero" placeholder="Número">
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="complemento" placeholder="Complemento">
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="telefone" placeholder="Telefone" required>
                    </div>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="idade" placeholder="Idade" required>
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <div class="col-sm-6">
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="genero" placeholder="Gênero" required>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <input type="password" class="form-control" name="senha" placeholder="Senha" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Salvar</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#cep').on('blur', function() {
                const cep = $(this).val().replace(/\D/g, '');

                if (cep.length !== 8) {
                    toastr.error('O CEP deve conter 8 dígitos.', 'Erro');
                    return;
                }

                $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                    if (data.erro) {
                        toastr.error('CEP não encontrado.', 'Erro');
                        $('#logradouro, #bairro, #cidade, #estado').val('Não informado');
                        return;
                    }

                    $('#logradouro').val(data.logradouro || 'Não informado');
                    $('#bairro').val(data.bairro || 'Não informado');
                    $('#cidade').val(data.localidade || 'Não informado');
                    $('#estado').val(data.uf || 'Não informado');
                    toastr.success('Endereço preenchido com sucesso.', 'Sucesso');
                }).fail(function() {
                    toastr.error('Erro ao consultar o CEP.', 'Erro');
                });
            });

            <?php if (isset($mensagem)): ?>
                toastr["<?= $mensagem['tipo'] ?>"]("<?= $mensagem['texto'] ?>", "<?= ucfirst($mensagem['tipo']) ?>", {
                    timeOut: 5000,
                    closeButton: true,
                    progressBar: true
                });

                <?php if (isset($redirecionar) && $redirecionar): ?>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 5000); // Redireciona após 5 segundos
                <?php endif; ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>