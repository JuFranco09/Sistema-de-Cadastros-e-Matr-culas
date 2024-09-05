<?php
include 'config.php';
include 'header.php';

$itensPorPagina = 5;
$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = ($pagina - 1) * $itensPorPagina;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_POST['add'])) {
        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);
        $tipo = trim($_POST['tipo']);

        if (strlen($nome) >= 3) {
            try {
                $stmt = $pdo->prepare("INSERT INTO turmas (nome, descricao, tipo) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $descricao, $tipo]);
                $classeMensagem = 'alerta-sucesso';
                $textoMensagem = 'Turma adicionada com sucesso!';
            } catch (Exception $e) {
                $classeMensagem = 'alerta-erro';
                $textoMensagem = 'Erro ao adicionar turma: ' . $e->getMessage();
            }
        } else {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = 'O nome deve ter pelo menos 3 caracteres.';
        }
    } elseif (isset($_POST['edit'])) {
        $id = (int)$_POST['id'];
        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);
        $tipo = trim($_POST['tipo']);

        if (strlen($nome) >= 3) {
            try {
                $stmt = $pdo->prepare("UPDATE turmas SET nome = ?, descricao = ?, tipo = ? WHERE id = ?");
                $stmt->execute([$nome, $descricao, $tipo, $id]);
                $classeMensagem = 'alerta-sucesso';
                $textoMensagem = 'Turma atualizada com sucesso!';
            } catch (Exception $e) {
                $classeMensagem = 'alerta-erro';
                $textoMensagem = 'Erro ao atualizar turma: ' . $e->getMessage();
            }
        } else {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = 'O nome deve ter pelo menos 3 caracteres.';
        }
    }
}

if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM turmas WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $classeMensagem = 'alerta-sucesso';
        $textoMensagem = 'Turma excluída com sucesso!';
    } catch (Exception $e) {
        if ($e->getCode() == 23000 && strpos($e->getMessage(), '1451') !== false) {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = "Não foi possivel excluir a turma devido a alunos estarem matriculados nela.";
        } else {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = 'Erro ao excluir aluno: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['edit']) && !isset($_POST['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM turmas WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $turmaParaEditar = $stmt->fetch();
}

$stmt = $pdo->prepare("SELECT * FROM turmas ORDER BY nome ASC LIMIT $itensPorPagina OFFSET $total");
$stmt->execute();
$turmas = $stmt->fetchAll();

$totalTurmas = $pdo->query("SELECT COUNT(*) FROM turmas")->fetchColumn();
$paginasTotais = ceil($totalTurmas / $itensPorPagina);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de cadastro de aluno</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Gerenciamento de Turmas</h1>

        <?php if (isset($textoMensagem)): ?>
            <div class="<?php echo $classeMensagem; ?>"><?php echo $textoMensagem; ?></div>
        <?php endif; ?>

        <?php if (isset($turmaParaEditar)): ?>
            <h2>Editar Turma</h2>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($turmaParaEditar['id']); ?>">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($turmaParaEditar['nome']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" required><?php echo htmlspecialchars($turmaParaEditar['descricao']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <input type="text" id="tipo" name="tipo" value="<?php echo htmlspecialchars($turmaParaEditar['tipo']); ?>" required>
                </div>
                <button type="submit" name="edit" class="btn btn-primary">Atualizar Turma</button>
            </form>
        <?php else: ?>
            <h2>Adicionar Nova Turma</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" required></textarea>
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <input type="text" id="tipo" name="tipo" required>
                </div>
                <button type="submit" name="add" class="btn btn-primary">Adicionar Turma</button>
            </form>
        <?php endif; ?>

        <h2>Lista de Turmas</h2>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turmas as $turma): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($turma['nome']); ?></td>
                        <td><?php echo htmlspecialchars($turma['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($turma['tipo']); ?></td>
                        <td>
                            <a href="?edit=<?php echo $turma['id']; ?>" class="btn btn-warning">Editar</a>
                            <a href="turmas.php?delete=<?php echo $turma['id']; ?>" class="btn btn-danger">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <a href="?page=<?php echo max($pagina - 1, 1); ?>" class="btn btn-secondary">Anterior</a>
            <span>Página <?php echo $pagina; ?> de <?php echo $paginasTotais; ?></span>
            <a href="?page=<?php echo min($pagina + 1, $paginasTotais); ?>" class="btn btn-secondary">Próxima</a>
        </div>
    </div>
</body>
</html>