<?php

class ClienteController
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function cadastrar($dados)
    {
        // Validação adicional para campos do endereço
        foreach (['logradouro', 'bairro', 'cidade', 'estado', 'cep'] as $campo) {
            if (empty($dados[$campo])) {
                $dados[$campo] = 'Não informado';
            }
        }

        $dados['endereco_completo'] = sprintf(
            '%s, %s, %s - %s, %s. Número: %s. Complemento: %s',
            $dados['logradouro'],
            $dados['bairro'],
            $dados['cidade'],
            $dados['estado'],
            $dados['cep'],
            $dados['numero'] ?? 'S/N',
            $dados['complemento'] ?? 'Não informado'
        );

        $resultado = $this->model->cadastrarCliente($dados);

        if ($resultado['success']) {
            $_SESSION['mensagem'] = [
                'tipo' => 'success',
                'texto' => 'Cliente cadastrado com sucesso.'
            ];
            return ['status' => 'success', 'message' => 'Cliente cadastrado com sucesso.'];
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'error',
                'texto' => $resultado['message']
            ];
            return ['status' => 'error', 'message' => $resultado['message']];
        }
    }

    public function buscarPorCPF($cpf)
    {
        try {
            $cliente = $this->model->buscarClientePorCPF($cpf);
            if ($cliente) {
                return $cliente;
            } else {
                return ['success' => false, 'message' => 'Cliente não encontrado.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function redefinirSenha($cpf, $senhaAtual, $novaSenha)
    {
        try {
            // Verifica se o CPF e as senhas foram fornecidos
            if (empty($cpf) || empty($senhaAtual) || empty($novaSenha)) {
                return ['success' => false, 'message' => 'Todos os campos são obrigatórios.'];
            }

            // Verifica se a senha atual está correta
            if ($this->model->verificarSenhaAtual($cpf, $senhaAtual)) {
                // Gera o hash da nova senha
                $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

                // Atualiza a senha no banco de dados
                if ($this->model->atualizarSenha($cpf, $novaSenhaHash)) {
                    return ['success' => true, 'message' => 'Senha atualizada com sucesso.'];
                } else {
                    return ['success' => false, 'message' => 'Erro ao atualizar senha.'];
                }
            } else {
                // Senha atual incorreta
                return ['success' => false, 'message' => 'Senha atual incorreta.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao redefinir senha: ' . $e->getMessage()];
        }
    }


    public function atualizarCliente($cpf, $dados)
    {
        try {
            if (!$this->model->buscarClientePorCPF($cpf)) {
                return ['success' => false, 'message' => 'Cliente não encontrado.'];
            }

            if ($this->model->atualizarCliente($cpf, $dados)) {
                return ['success' => true, 'message' => 'Dados atualizados com sucesso.'];
            } else {
                return ['success' => false, 'message' => 'Erro ao atualizar cliente.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
