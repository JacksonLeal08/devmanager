<?php
// api/planos.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Não foi possível conectar ao banco de dados."]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $query = "SELECT id, nome, descricao FROM tipos_plano WHERE id = :id LIMIT 0,1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $plano = $stmt->fetch();
            if ($plano) {
                echo json_encode($plano);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Tipo de plano não encontrado."]);
            }
        } else {
            $query = "SELECT id, nome, descricao FROM tipos_plano ORDER BY criado_em DESC";
            $stmt = $db->query($query);
            $planos = $stmt->fetchAll();
            echo json_encode($planos);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->nome)) {
            $query = "INSERT INTO tipos_plano (nome, descricao) VALUES (:nome, :descricao)";
            $stmt = $db->prepare($query);
            
            $nome = $data->nome;
            $descricao = !empty($data->descricao) ? $data->descricao : null;
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            
            if ($stmt->execute()) {
                $newId = $db->lastInsertId();
                http_response_code(201);
                echo json_encode([
                    "message" => "Tipo de plano criado com sucesso.",
                    "id" => intval($newId)
                ]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Erro ao criar tipo de plano."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            
            // Delete Plan Type cascades to Modalidades, but we should make sure we delete in a transaction.
            // Also, any projects linked to these modalities should be updated or deleted.
            // Let's check how the database schema behaves. Since modalidade_id is NULLable in table 'projetos' (Null = YES, Key = MUL),
            // if we delete a modality, we should set the project's modalidade_id to NULL.
            // So we start a transaction. First set modalidade_id = NULL in 'projetos' for all modalities of this plan type,
            // then delete those modalities, and finally delete the plan type.
            
            $db->beginTransaction();
            try {
                // Get all modality IDs under this plan type
                $stmtMod = $db->prepare("SELECT id FROM modalidades_plano WHERE tipo_plano_id = :tipo_plano_id");
                $stmtMod->bindParam(':tipo_plano_id', $id);
                $stmtMod->execute();
                $modIds = $stmtMod->fetchAll(PDO::FETCH_COLUMN);
                
                if (!empty($modIds)) {
                    // Update projects referencing these modalities to NULL
                    $inQuery = implode(',', array_map('intval', $modIds));
                    $db->query("UPDATE projetos SET modalidade_id = NULL WHERE modalidade_id IN ($inQuery)");
                    
                    // Delete the modalities
                    $db->query("DELETE FROM modalidades_plano WHERE tipo_plano_id = $id");
                }
                
                // Delete the plan type
                $queryDel = "DELETE FROM tipos_plano WHERE id = :id";
                $stmtDel = $db->prepare($queryDel);
                $stmtDel->bindParam(':id', $id);
                $stmtDel->execute();
                
                $db->commit();
                echo json_encode(["message" => "Tipo de plano e suas modalidades associadas excluídos com sucesso."]);
            } catch (PDOException $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(["message" => "Erro ao excluir tipo de plano: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID do plano não informado."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}
?>
