<?php
include 'config.php';
include 'header.php';

$itensPorPagina = 5;
$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = ($pagina - 1) * $itensPorPagina;

$classeMensagem = '';
$textoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_POST['add'])) {
        $nome = $_POST['nome'];
        $data_nascimento = $_POST['data_nascimento'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM alunos where nome like ? and data_nascimento = ?");
            $stmt->execute(["$nome", $data_nascimento]);
            $resultado = $stmt->fetchAll();

            if ($resultado && count($resultado) != 0) {
                $classeMensagem = 'alerta-erro';
                $textoMensagem = "Usuário já existente!";
            }
            else if(strlen($nome) >= 3){
                $stmt = $pdo->prepare("INSERT INTO alunos (nome, data_nascimento) VALUES (?, ?)");
                $stmt->execute([$nome, $data_nascimento]);
                $classeMensagem = 'alerta-sucesso';
                $textoMensagem = "Aluno adicionado com sucesso!";
            }
            else {
                $classeMensagem = 'alerta-erro';
                $textoMensagem = "O nome deve ter pelo menos 3 caracteres.";
            }
        } catch (Exception $e) {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = 'Erro ao adicionar aluno: ' . $e->getMessage();
        }
    } elseif (isset($_POST['edit'])) {
        $aluno_id = (int)$_POST['aluno_id'];
        $novo_nome = $_POST['novo_nome'];
        $nova_data_nascimento = $_POST['nova_data_nascimento'];

        try {
            if (strlen($novo_nome) >= 3) {
                $stmt = $pdo->prepare("UPDATE alunos SET nome = ?, data_nascimento = ?, usuario = null WHERE id = ?");
                $stmt->execute([$novo_nome, $nova_data_nascimento, $aluno_id]);
                $classeMensagem = 'alerta-sucesso';
                $textoMensagem = "Aluno atualizado com sucesso!";
            } else {
                $classeMensagem = 'alerta-erro';
                $textoMensagem = "O nome deve ter pelo menos 3 caracteres.";
            }
        } catch (Exception $e) {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = 'Erro ao atualizar aluno: ' . $e->getMessage();
        }
    } elseif (isset($_GET['delete'])) {
        try {
            $stmt = $pdo->prepare("SELECT aluno_id, nome, descricao from matriculas join turmas on matriculas.turma_id = turmas.id where aluno_id = ?");
            $stmt->execute([$_GET['delete']]);
            $resultado = $stmt->fetchAll();

            $stmt = $pdo->prepare("DELETE FROM alunos WHERE id = ?");
            $stmt->execute([$_GET['delete']]);
            $classeMensagem = 'alerta-sucesso';
            $textoMensagem = "Aluno excluído com sucesso!";
        } catch (Exception $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), '1451') !== false) {
                $classeMensagem = 'alerta-erro';
                $textoMensagem = "Não foi possível excluir o usuario ". $_GET['nome'].", pois está matriculado na ".$resultado[0]['nome']." - ".$resultado[0]['descricao'].".";
            } else {
                $classeMensagem = 'alerta-erro';
                $textoMensagem = 'Erro ao excluir aluno: ' . $e->getMessage();
            }
        }
    }
}

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $pdo->prepare("SELECT * FROM alunos WHERE nome LIKE ? ORDER BY nome ASC LIMIT $itensPorPagina OFFSET $total");
    $stmt->execute(['%' . $search . '%']);
} else {
    $stmt = $pdo->prepare("SELECT * FROM alunos ORDER BY nome ASC LIMIT $itensPorPagina OFFSET $total");
    $stmt->execute();
}
$alunos = $stmt->fetchAll();

$totalAlunos = $pdo->query("SELECT COUNT(*) FROM alunos")->fetchColumn();
$paginasTotais = ceil($totalAlunos / $itensPorPagina);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Alunos</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>Gerenciamento de Alunos</h1>

        <?php if (!empty($textoMensagem)): ?>
            <div class="<?php echo $classeMensagem; ?>"><?php echo $textoMensagem; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Adicionar Aluno</button>
        </form>

        <h2>Lista de Alunos</h2>

        <form method="GET">
            <div class="form-group">
                <input type="text" name="search" placeholder="Buscar por nome" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <?php if ($alunos): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($aluno['id']); ?></td>
                            <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                            <td><?php echo htmlspecialchars($aluno['data_nascimento']); ?></td>
                            <td>
                                <button class="btn btn-warning" onclick="mostrarEdicaoFormulario(<?php echo $aluno['id']; ?>)">Editar</button>
                                <a href="alunos.php?delete=<?php echo $aluno['id'] . "&nome=".$aluno['nome']; ?>" class="btn btn-danger">Excluir</a>
                            </td>
                        </tr>
                        <tr id="editarFormulario_<?php echo $aluno['id']; ?>" class="editar-formulario" style="display:none;">
                            <td colspan="3">
                                <form method="POST">
                                    <input type="hidden" name="aluno_id" value="<?php echo $aluno['id']; ?>">
                                    <div class="form-group">
                                        <label for="novo_nome_<?php echo $aluno['id']; ?>">Nome:</label>
                                        <input type="text" id="novo_nome_<?php echo $aluno['id']; ?>" name="novo_nome" value="<?php echo htmlspecialchars($aluno['nome']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="nova_data_nascimento_<?php echo $aluno['id']; ?>">Data de Nascimento:</label>
                                        <input type="date" id="nova_data_nascimento_<?php echo $aluno['id']; ?>" name="nova_data_nascimento" value="<?php echo htmlspecialchars($aluno['data_nascimento']); ?>" required>
                                    </div>
                                    <button type="submit" name="edit" class="btn btn-primary">Atualizar Aluno</button>
                                    <button type="button" class="btn btn-secondary" onclick="esconderEdicaoFormulario(<?php echo $aluno['id']; ?>)">Cancelar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum aluno encontrado.</p>
        <?php endif; ?>

        <div class="pagination">
            <a href="?page=<?php echo max($pagina - 1, 1); ?>&search=<?php echo htmlspecialchars($search); ?>" class="btn btn-secondary">Anterior</a>
            <span>Página <?php echo $pagina; ?> de <?php echo $paginasTotais; ?></span>
            <a href="?page=<?php echo min($pagina + 1, $paginasTotais); ?>&search=<?php echo htmlspecialchars($search); ?>" class="btn btn-secondary">Próxima</a>
        </div>
    </div>

    <script>
        function mostrarEdicaoFormulario(alunoId) {
            document.querySelectorAll('.editar-formulario').forEach(function(form) {
                form.style.display = 'none';
            });
            document.getElementById('editarFormulario_' + alunoId).style.display = 'table-row';
        }

        function esconderEdicaoFormulario(alunoId) {
            document.getElementById('editarFormulario_' + alunoId).style.display = 'none';
        }
    </script>
</body>

</html>