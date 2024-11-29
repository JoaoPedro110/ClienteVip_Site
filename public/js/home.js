/*Home.js*/

document.addEventListener("DOMContentLoaded", function () {
    const statusCards = document.querySelectorAll('.card-status');
    const topEmpresasCard = document.getElementById('topEmpresasCard');
    let dadosTransacoes = [];
    let chart, pieChart;

    // Carrega dados de transações e principais empresas
    try {
        dadosTransacoes = JSON.parse(document.getElementById('dadosTransacoes').textContent);
    } catch (error) {
        console.error("Erro ao carregar dados de transações:", error);
    }

    let topEmpresas = [];
    try {
        topEmpresas = JSON.parse(document.getElementById('topEmpresas').textContent);
    } catch (error) {
        console.error("Erro ao carregar dados de principais empresas:", error);
    }

    // Inicializa o gráfico de barras vazio
    function initializeBarChart() {
        const ctx = document.getElementById('graficoTransacoes').getContext('2d');
        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Valor da Transação (R$)',
                    data: [],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            boxWidth: 10,
                            font: { size: 12 }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 0,
                            autoSkip: false,
                            callback: function (value) {
                                let label = this.getLabelForValue(value);
                                return label.length > 10 ? label.substring(0, 10) + '…' : label;
                            }
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    // Inicializa o gráfico de pizza
    // Função para inicializar o gráfico de pizza com melhorias de qualidade
    function initializePieChart() {
        const ctx = document.getElementById('graficoPizza').getContext('2d');
        pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: topEmpresas.map(empresa => empresa.empresa),
                datasets: [{
                    data: topEmpresas.map(empresa => empresa.total_negocios + empresa.total_tarefas),
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5, // Reduz a proporção para tornar o gráfico mais compacto
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12 // Reduz o tamanho da fonte da legenda
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 10,    // Reduz o espaçamento superior
                        bottom: 10  // Reduz o espaçamento inferior
                    }
                }
            }
        });
    }




    // Atualiza o gráfico de barras com dados filtrados
    function updateBarChart(status) {
        const filteredData = dadosTransacoes.filter(transaction => transaction.status === status);
        const labels = filteredData.map(transaction => transaction.titulo.length > 10 ? transaction.titulo.substring(0, 10) + '…' : transaction.titulo);
        const values = filteredData.map(transaction => transaction.valor_transacao);

        chart.data.labels = labels;
        chart.data.datasets[0].data = values;
        chart.update();
    }

    // Função para alternar entre gráficos
    function toggleCharts(showPie) {
        const barChartCanvas = document.getElementById('graficoTransacoes');
        const pieChartCanvas = document.getElementById('graficoPizza');

        if (showPie) {
            barChartCanvas.style.display = 'none';
            pieChartCanvas.style.display = 'block';
        } else {
            barChartCanvas.style.display = 'block';
            pieChartCanvas.style.display = 'none';
        }
    }

    // Inicializa o gráfico de barras ao carregar a página
    initializeBarChart();
    toggleCharts(false); // Esconde o gráfico de pizza inicialmente

    // Adiciona evento de clique nos cards de status para atualizar o gráfico de barras
    statusCards.forEach(card => {
        card.addEventListener('click', function () {
            const status = card.getAttribute('data-status');
            updateBarChart(status);
            toggleCharts(false); // Exibe o gráfico de barras
        });
    });

    // Evento de clique no card de Principais Empresas para exibir o gráfico de pizza
    topEmpresasCard.addEventListener('click', function () {
        if (!pieChart) {
            initializePieChart(); // Inicializa o gráfico de pizza apenas uma vez
        } else {
            pieChart.update(); // Atualiza o gráfico de pizza, caso já exista
        }
        toggleCharts(true); // Exibe o gráfico de pizza
    });
});
