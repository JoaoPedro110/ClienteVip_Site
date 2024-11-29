<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include 'app/Views/layout/header_publico.php'; ?>
    <title>Bem-vindo ao CRM Cliente VIP</title>
    <link rel="stylesheet" href="public/css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5 text-center">
        <div class="jumbotron bg-success text-white p-5 rounded shadow">
            <h1>Bem-vindo ao CRM Cliente VIP</h1>
            <p class="lead">Gerencie seus clientes de forma eficiente e personalizada.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="app/Views/cliente/login.php" class="btn btn-light btn-lg">Acessar Conta</a>
                <a href="app/Views/cliente/registro.php" class="btn btn-light btn-lg">Cadastre-se</a>
            </div>
        </div>

        <div class="row text-center mt-3">
            <div class="col-md-4">
                <h2>Gestão de Clientes</h2>
                <p>Crie, edite e gerencie os dados de seus clientes facilmente.</p>
            </div>
            <div class="col-md-4">
                <h2>Campanhas Personalizadas</h2>
                <p>Crie campanhas de marketing e acompanhe os resultados em tempo real.</p>
            </div>
            <div class="col-md-4">
                <h2>Relatórios de Vendas</h2>
                <p>Visualize relatórios detalhados sobre suas vendas e clientes.</p>
            </div>
        </div>
    </div>

    <?php include 'app/Views/layout/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>