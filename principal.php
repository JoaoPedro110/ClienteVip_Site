<?php
session_start();

if (!isset($_SESSION['cliente'])) {
    header('Location: index.php');
    exit();
}

$cliente = $_SESSION['cliente'];

require_once 'config/db.php';
require_once 'app/models/Database.php';

$db = new Database();

try {
    // Contagem de negócios por status
    $db->query("SELECT status, COUNT(*) as total FROM negocios GROUP BY status");
    $contagemNegocios = $db->resultSet();

    // Todas as transações para JSON
    $db->query("SELECT n.titulo, n.valor_transacao, n.status FROM negocios AS n");
    $dadosTransacoes = $db->resultSet();

    // Consulta para obter apenas as duas principais empresas
    $sqlFinal = "
        SELECT e.nome AS empresa,
               (COALESCE(COUNT(n.id_negocio), 0) + COALESCE(COUNT(t.id), 0)) AS total
        FROM empresas AS e
        LEFT JOIN negocios AS n ON e.empresa_id = n.empresa_id
        LEFT JOIN tarefas AS t ON e.empresa_id = t.empresa_id
        GROUP BY e.empresa_id
        ORDER BY total DESC
        LIMIT 2
    ";
    $db->query($sqlFinal);
    $topEmpresas = $db->resultSet();
} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}

// JSON para o JavaScript
$dadosTransacoesJSON = json_encode($dadosTransacoes);
$topEmpresasJSON = json_encode($topEmpresas);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cliente VIP</title>
    <link rel="stylesheet" href="public/css/home.css" importance="">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Duas Navbars Fixas -->
        <div class="fixed-top" id="navbar1">
            <?php include 'app/views/Layout/header_logado.php'; ?>
        </div>

        <!-- Conteúdo Principal -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <!-- Cards de Status com Contagem -->
                    <div class="row mb-4 justify-content-center">
                        <?php foreach ($contagemNegocios as $status): ?>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <div class="card shadow-sm rounded-3 p-2 card-status" data-status="<?php echo $status['status']; ?>">
                                    <div class="card-body text-center">
                                        <h6><?php echo htmlspecialchars($status['status']); ?></h6>
                                        <p class="font-teste"><?php echo $status['total']; ?> negócios</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Card para Principais Empresas com apenas os dois principais nomes -->
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3" id="topEmpresasCard">
                            <div class="card shadow-sm rounded-3 p-2 text-center" onclick="showPieChart()">
                                <div class="card-body">
                                    <h6>Principais Empresas</h6>
                                    <ul class="list-unstyled mb-0">
                                        <?php foreach ($topEmpresas as $empresa): ?>
                                            <li class="font-teste">
                                                <?php echo htmlspecialchars($empresa['empresa']); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Gráficos: Barra e Pizza -->
                        <div class="col-lg-6 col-md-8 col-sm-10 mb-4">
                            <div class="card">
                                <div class="card-header bg-info text-white text-center">
                                    <h5>Gráfico de Negócios</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Gráfico de Barras -->
                                    <canvas id="graficoTransacoes" class="grafico"></canvas>
                                    <!-- Gráfico de Pizza -->
                                    <canvas id="graficoPizza" class="grafico" style="display: none;"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!--Parte das Tarefas-->
                    <div class="row mb-4 justify-content-center">
                        <!-- Tarefas Mais Importantes -->
                        <div class="col-md-5 mb-4"> <!-- Alterado para col-md-5 para cards mais estreitos -->
                            <div class="card card-tarefa">
                                <div class="card-header bg-primary text-white text-center">
                                    <h6 class="mb-0">Tarefas Mais Importantes</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($tarefasImportantes)): ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($tarefasImportantes as $tarefa): ?>
                                                <li class="list-group-item tarefa-item">
                                                    <div class="tarefa-titulo">
                                                        <strong><?php echo htmlspecialchars($tarefa['titulo']); ?></strong>
                                                    </div>
                                                    <div class="tarefa-detalhes">
                                                        <span class="text-muted">Empresa: <?php echo htmlspecialchars($tarefa['nome_empresa'] ?? 'Não especificada'); ?></span><br>
                                                        <span class="badge bg-danger">Prioridade: <?php echo htmlspecialchars($tarefa['prioridade']); ?></span>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted text-center">Nenhuma tarefa importante no momento.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tarefas Próximas do Prazo -->
                        <div class="col-md-5 mb-4"> <!-- Alterado para col-md-5 para cards mais estreitos -->
                            <div class="card card-tarefa">
                                <div class="card-header bg-warning text-white text-center">
                                    <h6 class="mb-0">Tarefas Próximas do Prazo</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($tarefasProximas)): ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($tarefasProximas as $tarefa): ?>
                                                <li class="list-group-item tarefa-item">
                                                    <div class="tarefa-titulo">
                                                        <strong><?php echo htmlspecialchars($tarefa['titulo']); ?></strong>
                                                    </div>
                                                    <div class="tarefa-detalhes">
                                                        <span class="text-muted">Empresa: <?php echo htmlspecialchars($tarefa['nome_empresa'] ?? 'Não especificada'); ?></span><br>
                                                        <span class="text-danger">Prazo: <?php echo date("d/m/Y", strtotime($tarefa['data_termino'])); ?></span>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted text-center">Nenhuma tarefa próxima do prazo.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <?php include 'app/views/Layout/footer.php'; ?>
            </div>
        </div>

        <!-- Dados JSON para gráficos -->
        <div id="dadosTransacoes" style="display: none;"><?php echo $dadosTransacoesJSON; ?></div>
        <div id="topEmpresas" style="display: none;"><?php echo $topEmpresasJSON; ?></div>

        <!-- JavaScript para Gráficos e Ajustes -->
        <script src="public/js/home.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Ajusta o espaçamento do conteúdo para as duas navbars fixas
            document.addEventListener("DOMContentLoaded", function() {
                const navbar1 = document.getElementById("navbar1");
                const contentWrapper = document.getElementById("content-wrapper");

                // Calcula a altura da navbar fixa e adiciona um offset extra (20px)
                const totalNavbarHeight = navbar1 ? navbar1.offsetHeight + 20 : 20;

                // Define o padding-top no content-wrapper
                if (contentWrapper) {
                    contentWrapper.style.paddingTop = `${totalNavbarHeight}px`;
                }
            });

            // Funções para mostrar gráficos
            function showPieChart() {
                const graficoTransacoes = document.getElementById('graficoTransacoes');
                const graficoPizza = document.getElementById('graficoPizza');

                if (graficoTransacoes && graficoPizza) {
                    graficoTransacoes.style.display = 'none';
                    graficoPizza.style.display = 'block';
                    loadPieChart();
                }
            }

            function showBarChart() {
                const graficoTransacoes = document.getElementById('graficoTransacoes');
                const graficoPizza = document.getElementById('graficoPizza');

                if (graficoTransacoes && graficoPizza) {
                    graficoTransacoes.style.display = 'block';
                    graficoPizza.style.display = 'none';
                    loadBarChart();
                }
            }

            // Função para carregar o gráfico de Pizza
            function loadPieChart() {
                const graficoPizza = document.getElementById('graficoPizza');
                const topEmpresasData = document.getElementById('topEmpresas');

                if (graficoPizza && topEmpresasData) {
                    const ctx = graficoPizza.getContext('2d');
                    const topEmpresas = JSON.parse(topEmpresasData.innerText);

                    const data = {
                        labels: topEmpresas.map(empresa => empresa.empresa),
                        datasets: [{
                            data: topEmpresas.map(empresa => empresa.total),
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                        }]
                    };

                    new Chart(ctx, {
                        type: 'pie',
                        data: data,
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom' // Posiciona a legenda abaixo do gráfico
                                }
                            }
                        }
                    });
                }
            }

            // Função para carregar o gráfico de Barras
            function loadBarChart() {
                const graficoTransacoes = document.getElementById('graficoTransacoes');
                const dadosTransacoesData = document.getElementById('dadosTransacoes');

                if (graficoTransacoes && dadosTransacoesData) {
                    const ctx = graficoTransacoes.getContext('2d');
                    const dadosTransacoes = JSON.parse(dadosTransacoesData.innerText);

                    const data = {
                        labels: dadosTransacoes.map(transacao => transacao.titulo),
                        datasets: [{
                            label: 'Valor da Transação',
                            data: dadosTransacoes.map(transacao => parseFloat(transacao.valor_transacao)),
                            backgroundColor: '#4BC0C0'
                        }]
                    };

                    new Chart(ctx, {
                        type: 'bar',
                        data: data,
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false // Oculta a legenda
                                }
                            }
                        }
                    });
                }
            }

            // Inicializa o gráfico de barras ao carregar a página
            document.addEventListener("DOMContentLoaded", function() {
                loadBarChart();
            });
        </script>

</body>

</html>