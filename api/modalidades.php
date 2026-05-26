<?php
// api/modalidades.php
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
            $query = "SELECT id, tipo_plano_id AS tipoPlanoId, nome, valor_base AS valorBase, tipo_cobranca AS recorrente, duracao_dias AS duracaoDias 
                      FROM modalidades_plano WHERE id = :id LIMIT 0,1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $modalidade = $stmt->fetch();
            if ($modalidade) {
                echo json_encode($modalidade);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Modalidade não encontrada."]);
            }
        } else {
            $query = "SELECT id, tipo_plano_id AS tipoPlanoId, nome, valor_base AS valorBase, tipo_cobranca AS recorrente, duracao_dias AS duracaoDias 
                      FROM modalidades_plano ORDER BY id ASC";
            $stmt = $db->query($query);
            $modalidades = $stmt->fetchAll();
            echo json_encode($modalidades);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->tipoPlanoId) && !empty($data->nome) && isset($data->valorBase) && !empty($data->recorrente)) {
            $query = "INSERT INTO modalidades_plano (tipo_plano_id, nome, valor_base, tipo_cobranca, duracao_dias) 
                      VALUES (:tipo_plano_id, :nome, :valor_base, :tipo_cobranca, :duracao_dias)";
            $stmt = $db->prepare($query);
            
            $tipoPlanoId = intval($data->tipoPlanoId);
            $nome = $data->nome;
            $valorBase = floatval($data->valorBase);
            $recorrente = $data->recorrente;
            $duracaoDias = isset($data->duracaoDias) ? intval($data->duracaoDias) : 0;
            
            $stmt->bindParam(':tipo_plano_id', $tipoPlanoId);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':valor_base', $valorBase);
            $stmt->bindParam(':tipo_cobranca', $recorrente);
            $stmt->bindParam(':duracao_dias', $duracaoDias);
            
            if ($stmt->execute()) {
                $newId = $db->lastInsertId();
                http_response_code(201);
                echo json_encode([
                    "message" => "Modalidade criada com sucesso.",
                    "id" => intval($newId)
                ]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Erro ao criar modalidade."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            
            $db->beginTransaction();
            try {
                // Remove reference in projects (set to NULL)
                $queryProj = "UPDATE projetos SET modalidade_id = NULL WHERE modalidade_id = :id";
                $stmtProj = $db->prepare($queryProj);
                $stmtProj->bindParam(':id', $id);
                $stmtProj->execute();
                
                // Delete modalidade
                $queryMod = "DELETE FROM modalidades_plano WHERE id = :id";
                $stmtMod = $db->prepare($queryMod);
                $stmtMod->bindParam(':id', $id);
                $stmtMod->execute();
                
                $db->commit();
                echo json_encode(["message" => "Modalidade excluída com sucesso."]);
            } catch (PDOException $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(["message" => "Erro ao excluir modalidade: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID da modalidade não informado."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}
?>
