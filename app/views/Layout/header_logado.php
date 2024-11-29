<?php
// config.php

// Caminho base do projeto
$base_url = "/ClienteVip";



if (!isset($_SESSION['cliente'])) {
    header('Location: index.php');
    exit();
}

$cliente = $_SESSION['cliente'];
?>


<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Bootstrap -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
<!-- FontAwesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $base_url; ?>/principal.php">CRM Cliente VIP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>/principal.php"><i class="fas fa-home me-2"></i>Principal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>/app/views/Negocio/negocios.php"><i class="fas fa-briefcase me-2"></i>Negócios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>/app/views/negocio/tarefas.php"><i class="fas fa-columns me-2"></i>Quadro de Tarefas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>/app/views/Lead/LeadHome.php"><i class="fas fa-clipboard me-2"></i>Gestão de Leads</a>
                </li>
            </ul>
        </div>
        <div class="">
            <!-- Botão para abrir o menu lateral em telas pequenas -->
            <button class="btn btn-link d-md-none">
                <i class="fa fa-bars"></i>
            </button>

            <!-- Menu do usuário logado -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <span class="mr-2 d-none d-lg-inline text-gray-600">
                            Bem-vindo, <?php echo htmlspecialchars($cliente['nome']); ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="<?php echo $base_url; ?>/app/Views/cliente/perfil.php">Perfil</a>
                        <a class="dropdown-item" href="#">Configurações</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/ClienteVip/app/Views/cliente/logout.php">Sair</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>