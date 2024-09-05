<?php
include 'config.php';
include 'header.php';

$itensPorPagina = 5;
$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = ($pagina - 1) * $itensPorPagina;

$classeMensagem = ''; 
$textoMensagem = '';  

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_POST['matricular'])) {
        $aluno_id = $_POST['aluno_id'];
        $turma_id = $_POST['turma_id'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM matriculas WHERE aluno_id = ? AND turma_id = ?");
            $stmt->execute([$aluno_id, $turma_id]);
            if ($stmt->fetch()) {
                $classeMensagem = 'alerta-erro';
                $textoMensagem = "Este aluno já está matriculado nesta turma.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO matriculas (aluno_id, turma_id) VALUES (?, ?)");
                $stmt->execute([$aluno_id, $turma_id]);
                $classeMensagem = 'alerta-sucesso';
                $textoMensagem = "Aluno matriculado com sucesso!";
            }
        } catch (Exception $e) {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = 'Erro ao matricular aluno: ' . $e->getMessage();
        }
    } elseif (isset($_POST['edit'])) {
        $matricula_id = (int)$_POST['matricula_id'];
        $novo_aluno_id = $_POST['novo_aluno_id'];
        $nova_turma_id = $_POST['nova_turma_id'];

        try {
            $stmt = $pdo->prepare("UPDATE matriculas SET aluno_id = ?, turma_id = ? WHERE id = ?");
            $stmt->execute([$novo_aluno_id, $nova_turma_id, $matricula_id]);
            $classeMensagem = 'alerta-sucesso';
            $textoMensagem = "Matrícula atualizada com sucesso!";
        } catch (Exception $e) {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = 'Erro ao atualizar matrícula: ' . $e->getMessage();
        }
    } elseif (isset($_POST['delete'])) {
        $matricula_id = (int)$_POST['matricula_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM matriculas WHERE id = ?");
            $stmt->execute([$matricula_id]);
            $classeMensagem = 'alerta-sucesso';
            $textoMensagem = "Matrícula excluída com sucesso!";
        } catch (Exception $e) {
            $classeMensagem = 'alerta-erro';
            $textoMensagem = 'Erro ao excluir matrícula: ' . $e->getMessage();
        }
    }
}

$turmas = $pdo->query("SELECT * FROM turmas ORDER BY nome ASC")->fetchAll();
$alunos = $pdo->query("SELECT * FROM alunos ORDER BY nome ASC")->fetchAll();

$matriculados = [];
$totalMatriculados = 0;
$paginasTotais = 0;

if (isset($_GET['turma_id'])) {
    $turma_id = (int)$_GET['turma_id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM matriculas WHERE turma_id = ?");
    $stmt->execute([$turma_id]);
    $totalMatriculados = $stmt->fetchColumn();

    $paginasTotais = ceil($totalMatriculados / $itensPorPagina);

    $stmt = $pdo->prepare("SELECT matriculas.id, alunos.nome AS aluno_nome, turmas.nome AS turma_nome 
                           FROM matriculas 
                           JOIN alunos ON matriculas.aluno_id = alunos.id 
                           JOIN turmas ON matriculas.turma_id = turmas.id 
                           WHERE matriculas.turma_id = ? 
                           LIMIT $itensPorPagina OFFSET $total");
    $stmt->execute([$turma_id]);
    $matriculados = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrícula de Alunos</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>Matrícula de Alunos</h1>

        <?php if (!empty($textoMensagem)): ?>
            <div class="<?php echo $classeMensagem; ?>"><?php echo $textoMensagem; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="aluno_id">Aluno:</label>
                <select id="aluno_id" name="aluno_id" required>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?php echo $aluno['id']; ?>"><?php echo htmlspecialchars($aluno['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="turma_id">Turma:</label>
                <select id="turma_id" name="turma_id" required>
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']. ' - '. $turma['descricao']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="matricular" class="btn btn-primary">Matricular</button>
        </form>

        <h2>Alunos Matriculados</h2>

        <form method="GET">
            <div class="form-group">
                <label for="turma_id">Turma:</label>
                <select id="turma_id" name="turma_id" required>
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?php echo $turma['id']; ?>" <?php if (isset($_GET['turma_id']) && $_GET['turma_id'] == $turma['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($turma['nome']. ' - '. $turma['descricao']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Ver Alunos Matriculados</button>
            </div>
        </form>

        <?php if ($matriculados): ?>
            <h3>Editar ou Excluir Matrículas</h3>
            <?php foreach ($matriculados as $matricula): ?>
                <div class="matricula-item">
                    <p>Aluno: <?php echo htmlspecialchars($matricula['aluno_nome']); ?> | Turma: <?php echo htmlspecialchars($matricula['turma_nome']); ?></p>
                    <button class="btn btn-warning" onclick="mostrarEdicaoFormulario(<?php echo $matricula['id']; ?>)">Editar</button>
                    
                    <div id="editarFormulario_<?php echo $matricula['id']; ?>" class="editar-formulario" style="display:none;">
                        <h3>Editar Matrícula</h3>
                        <form method="POST">
                            <input type="hidden" name="matricula_id" value="<?php echo $matricula['id']; ?>">
                            <div class="form-group">
                                <label for="novo_aluno_id_<?php echo $matricula['id']; ?>">Aluno:</label>
                                <select id="novo_aluno_id_<?php echo $matricula['id']; ?>" name="novo_aluno_id" required>
                                    <?php foreach ($alunos as $aluno): ?>
                                        <option value="<?php echo $aluno['id']; ?>" <?php if ($aluno['id'] == $matricula['aluno_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($aluno['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nova_turma_id_<?php echo $matricula['id']; ?>">Nova Turma:</label>
                                <select id="nova_turma_id_<?php echo $matricula['id']; ?>" name="nova_turma_id" required>
                                    <?php foreach ($turmas as $turma): ?>
                                        <option value="<?php echo $turma['id']; ?>" <?php if ($turma['id'] == $matricula['turma_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($turma['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="edit" class="btn btn-primary">Atualizar Matrícula</button>
                            <button type="button" class="btn btn-secondary" onclick="esconderEdicaoFormulario(<?php echo $matricula['id']; ?>)">Cancelar</button>
                        </form>
                    </div>
                    
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="matricula_id" value="<?php echo $matricula['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <div class="pagination">
                <?php if ($pagina > 1): ?>
                    <a href="?turma_id=<?php echo $turma_id; ?>&page=<?php echo $pagina - 1; ?>" class="btn btn-secondary">Anterior</a>
                <?php endif; ?>

                <?php if ($pagina < $paginasTotais): ?>
                    <a href="?turma_id=<?php echo $turma_id; ?>&page=<?php echo $pagina + 1; ?>" class="btn btn-secondary">Próximo</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <script>
            function mostrarEdicaoFormulario(matriculaId) {
                document.querySelectorAll('.editar-formulario').forEach(function(form) {
                    form.style.display = 'none';
                });
                document.getElementById('editarFormulario_' + matriculaId).style.display = 'block';
            }

            function esconderEdicaoFormulario(matriculaId) {
                document.getElementById('editarFormulario_' + matriculaId).style.display = 'none';
            }
        </script>
    </div>
</body>

</html>
