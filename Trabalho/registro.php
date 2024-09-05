<?php
include 'config.php';
include 'header.php';

$itensPorPagina = 5;
$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = ($pagina - 1) * $itensPorPagina;

$classeMensagem = ''; 
$textoMensagem = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_POST['register'])) {
        $nome = trim($_POST['nome']);
        $usuario = trim($_POST['usuario']);
        $senha = trim($_POST['senha']);
        
        $nome = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
        $usuario = htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8');
        
        if (strlen($nome) >= 3 && strlen($usuario) >= 3 && !empty($senha)) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = ?");
                $stmt->execute([$usuario]);
                
                if ($stmt->fetchColumn() > 0) {
                    $classeMensagem = 'alerta-erro';
                    $textoMensagem = 'Usuário já existente no banco de dados!';
                } else {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, usuario, senha) VALUES (?, ?, ?)");
                    
                    if ($stmt->execute([$nome, $usuario, $senhaHash])) {
                        $classeMensagem = 'alerta-sucesso';
                        $textoMensagem = "Usuário registrado com sucesso!";
                    } else {
                        $classeMensagem = 'alerta-erro';
                        $textoMensagem = "Ocorreu um erro ao registrar o usuário. Tente novamente.";
                    }
                }
            } catch (Exception $e) {
                $classeMensagem = 'alerta-erro';
                $textoMensagem = 'Erro ao registrar o usuário: ' . $e->getMessage();
            }
        } else {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = "Todos os campos são obrigatórios e devem ter no mínimo 3 caracteres.";
        }
    } elseif (isset($_POST['edit'])) {
        $usuario_id = (int)$_POST['usuario_id'];
        $novo_nome = trim($_POST['novo_nome']);
        $novo_usuario = trim($_POST['novo_usuario']);
        $nova_senha = trim($_POST['nova_senha']);

        $novo_nome = htmlspecialchars($novo_nome, ENT_QUOTES, 'UTF-8');
        $novo_usuario = htmlspecialchars($novo_usuario, ENT_QUOTES, 'UTF-8');

        try {
            if (strlen($novo_nome) >= 3 && strlen($novo_usuario) >= 3 && !empty($nova_senha)) {
                $senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, usuario = ?, senha = ? WHERE id = ?");
                $stmt->execute([$novo_nome, $novo_usuario, $senhaHash, $usuario_id]);
                $classeMensagem = 'alerta-sucesso';
                $textoMensagem = "Usuário atualizado com sucesso!";
            } else {
                $classeMensagem = 'alerta-erro';
                $textoMensagem = "Todos os campos são obrigatórios e devem ter no mínimo 3 caracteres.";
            }
        } catch (Exception $e) {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = 'Erro ao atualizar o usuário: ' . $e->getMessage();
        }
    } elseif (isset($_GET['delete'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$_GET['delete']]);
            $classeMensagem = 'alerta-sucesso';
            $textoMensagem = "Usuário excluído com sucesso!";
        } catch (Exception $e) {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = 'Erro ao excluir o usuário: ' . $e->getMessage();
        }
    }
}

$stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
$totalUsuarios = $stmt->fetchColumn();
$paginasTotais = ceil($totalUsuarios / $itensPorPagina);

$usuarios = [];
if ($totalUsuarios > 0) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios ORDER BY nome ASC LIMIT $itensPorPagina OFFSET $total");
    $stmt->execute();
    $usuarios = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Usuários</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Registro e Gerenciamento de Usuários</h1>

        <?php if (!empty($textoMensagem)): ?>
            <div class="<?php echo $classeMensagem; ?>"><?php echo $textoMensagem; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="usuario">Usuário:</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary">Registrar</button>
        </form>

        <h2>Lista de Usuários</h2>

        <?php if ($usuarios): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Usuário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                            <td>
                                <button class="btn btn-warning" onclick="mostrarEdicaoFormulario(<?php echo $usuario['id']; ?>)">Editar</button>
                                <a href="registro.php?delete=<?php echo $usuario['id']; ?>" class="btn btn-danger">Excluir</a>
                            </td>
                        </tr>
                        <tr id="editarFormulario_<?php echo $usuario['id']; ?>" class="editar-formulario" style="display:none;">
                            <td colspan="3">
                                <form method="POST">
                                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                    <div class="form-group">
                                        <label for="novo_nome_<?php echo $usuario['id']; ?>">Nome:</label>
                                        <input type="text" id="novo_nome_<?php echo $usuario['id']; ?>" name="novo_nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="novo_usuario_<?php echo $usuario['id']; ?>">Usuário:</label>
                                        <input type="text" id="novo_usuario_<?php echo $usuario['id']; ?>" name="novo_usuario" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="nova_senha_<?php echo $usuario['id']; ?>">Senha:</label>
                                        <input type="password" id="nova_senha_<?php echo $usuario['id']; ?>" name="nova_senha" required>
                                    </div>
                                    <button type="submit" name="edit" class="btn btn-primary">Atualizar Usuário</button>
                                    <button type="button" class="btn btn-secondary" onclick="esconderEdicaoFormulario(<?php echo $usuario['id']; ?>)">Cancelar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum usuário encontrado.</p>
        <?php endif; ?>

        <div class="pagination">
            <a href="?page=<?php echo max($pagina - 1, 1); ?>" class="btn btn-secondary">Anterior</a>
            <span>Página <?php echo $pagina; ?> de <?php echo $paginasTotais; ?></span>
            <a href="?page=<?php echo min($pagina + 1, $paginasTotais); ?>" class="btn btn-secondary">Próxima</a>
        </div>
    </div>

    <script>
        function mostrarEdicaoFormulario(usuarioId) {
            document.querySelectorAll('.editar-formulario').forEach(function(form) {
                form.style.display = 'none';
            });
            document.getElementById('editarFormulario_' + usuarioId).style.display = 'table-row';
        }

        function esconderEdicaoFormulario(usuarioId) {
            document.getElementById('editarFormulario_' + usuarioId).style.display = 'none';
        }
    </script>
</body>
</html>