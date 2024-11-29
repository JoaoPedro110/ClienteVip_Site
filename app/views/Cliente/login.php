<?php
require_once '../../../config/db.php';
require_once '../../Models/Database.php';
require_once '../../Models/Cliente.php';
require_once '../../Controllers/ClienteController.php';

session_start(); // Início da sessão

// Variáveis para mensagens
$mensagem = null;
$redirecionar = false;

// Instanciar o modelo e o controlador
$model = new ClienteModel();
$controller = new ClienteController($model);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']); // Limpar CPF
    $senha = $_POST['senha'];

    // Buscar cliente pelo CPF
    $cliente = $controller->buscarPorCPF($cpf);

    // Verificar se o cliente existe e se a senha está correta
    if ($cliente && password_verify($senha, $cliente['senha'])) {
        // Armazenar os dados do cliente na sessão e CPF em cookie seguro
        $_SESSION['cliente'] = $cliente;
        setcookie('cpf', $cliente['cpf'], time() + 3600, "/", "", true, true); // Secure e HttpOnly

        // Mensagem de sucesso e redirecionamento
        $mensagem = [
            'tipo' => 'success',
            'texto' => 'Login realizado com sucesso!'
        ];
        $redirecionar = true;
    } else {
        // Mensagem de erro
        $mensagem = [
            'tipo' => 'error',
            'texto' => 'Login ou senha incorretos.'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../layout/header_publico.php'; ?>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../public/css/Login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body>
    <div class="container-fluid p-0">
        <div class="form-container">
            <div class="login-form">
                <h1 class="text-center mb-4">Login</h1>
                <form method="post" action="">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="cpf" placeholder="CPF" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="senha" placeholder="Senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
                <div class="mt-4 text-center">
                    <a href="#">Esqueceu sua senha?</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        <?php if (isset($mensagem)): ?>
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "timeOut": "5000", // 5 segundos
                "extendedTimeOut": "1000",
                "positionClass": "toast-top-right"
            };

            toastr["<?= $mensagem['tipo'] ?>"]("<?= $mensagem['texto'] ?>", "<?= ucfirst($mensagem['tipo']) ?>");

            <?php if (isset($redirecionar) && $redirecionar): ?>
                setTimeout(function() {
                    window.location.href = '../../../principal.php';
                }, 5000); // Redireciona após 5 segundos
            <?php endif; ?>
        <?php endif; ?>
    </script>
</body>

</html>