<?php
// api/usuarios.php
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
            $query = "SELECT id, nome_completo AS nome, email, contato, tipo_conta AS tipoConta, foto_path AS fotoPath 
                      FROM usuarios WHERE id = :id LIMIT 0,1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $user = $stmt->fetch();
            if ($user) {
                echo json_encode($user);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Usuário não encontrado."]);
            }
        } else {
            $query = "SELECT id, nome_completo AS nome, email, contato, tipo_conta AS tipoConta, foto_path AS fotoPath 
                      FROM usuarios ORDER BY criado_em DESC";
            $stmt = $db->query($query);
            $users = $stmt->fetchAll();
            echo json_encode($users);
        }
        break;

    case 'POST':
        // Note: For file uploads, we use multipart/form-data. PHP reads them into $_POST and $_FILES.
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $contato = isset($_POST['contato']) ? trim($_POST['contato']) : null;
        $tipoConta = isset($_POST['tipoConta']) ? trim($_POST['tipoConta']) : '';
        $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
        
        // Validation for Create
        if ($id === 0) {
            if (empty($nome) || empty($email) || empty($senha) || empty($tipoConta)) {
                http_response_code(400);
                echo json_encode(["message" => "Dados incompletos para criação de usuário."]);
                exit();
            }
        } else {
            // Validation for Update
            if (empty($nome) || empty($email) || empty($tipoConta)) {
                http_response_code(400);
                echo json_encode(["message" => "Dados incompletos para atualização de usuário."]);
                exit();
            }
        }

        // Handle Photo Upload
        $fotoPath = null;
        $hasNewPhoto = false;
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['foto']['tmp_name'];
            $fileName = $_FILES['foto']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            // Validate image extension
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = uniqid('user_', true) . '.' . $fileExtension;
                $uploadFileDir = '../public/uploads/usuarios/';
                $dest_path = $uploadFileDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $fotoPath = 'public/uploads/usuarios/' . $newFileName;
                    $hasNewPhoto = true;
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Erro ao mover arquivo de foto para o diretório final."]);
                    exit();
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Extensão de imagem inválida. Permitidos: JPG, JPEG, PNG, GIF, WEBP."]);
                exit();
            }
        }

        if ($id === 0) {
            // --- CREATE MODE ---
            $query = "INSERT INTO usuarios (nome_completo, email, senha, contato, tipo_conta, foto_path) 
                      VALUES (:nome, :email, :senha, :contato, :tipo_conta, :foto_path)";
            
            $stmt = $db->prepare($query);
            
            $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $hashedPassword);
            $stmt->bindParam(':contato', $contato);
            $stmt->bindParam(':tipo_conta', $tipoConta);
            $stmt->bindParam(':foto_path', $fotoPath);
            
            try {
                if ($stmt->execute()) {
                    $newId = $db->lastInsertId();
                    http_response_code(201);
                    echo json_encode([
                        "message" => "Usuário criado com sucesso.",
                        "id" => intval($newId)
                    ]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Erro ao criar usuário no banco de dados."]);
                }
            } catch (PDOException $e) {
                http_response_code(400);
                if ($e->getCode() == 23000) {
                    echo json_encode(["message" => "Este e-mail já está cadastrado para outro usuário."]);
                } else {
                    echo json_encode(["message" => "Erro de banco: " . $e->getMessage()]);
                }
            }
        } else {
            // --- UPDATE MODE ---
            // First fetch current user's old photo path (if we need to delete it)
            $oldFoto = null;
            $fetchQuery = "SELECT foto_path, senha FROM usuarios WHERE id = :id";
            $fetchStmt = $db->prepare($fetchQuery);
            $fetchStmt->bindParam(':id', $id);
            $fetchStmt->execute();
            $currentUser = $fetchStmt->fetch();
            if ($currentUser) {
                $oldFoto = $currentUser['foto_path'];
            }

            // Build dynamic update query depending on whether password and photo are updating
            $updates = [
                "nome_completo = :nome",
                "email = :email",
                "contato = :contato",
                "tipo_conta = :tipo_conta"
            ];
            
            if (!empty($senha)) {
                $updates[] = "senha = :senha";
            }
            if ($hasNewPhoto) {
                $updates[] = "foto_path = :foto_path";
            }
            
            $query = "UPDATE usuarios SET " . implode(", ", $updates) . " WHERE id = :id";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contato', $contato);
            $stmt->bindParam(':tipo_conta', $tipoConta);
            
            if (!empty($senha)) {
                $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);
                $stmt->bindParam(':senha', $hashedPassword);
            }
            if ($hasNewPhoto) {
                $stmt->bindParam(':foto_path', $fotoPath);
            }
            
            try {
                if ($stmt->execute()) {
                    // Delete old photo file if a new one was uploaded
                    if ($hasNewPhoto && !empty($oldFoto)) {
                        $oldFileFullPath = '../' . $oldFoto;
                        if (file_exists($oldFileFullPath)) {
                            unlink($oldFileFullPath);
                        }
                    }
                    
                    echo json_encode(["message" => "Usuário atualizado com sucesso."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Erro ao atualizar usuário."]);
                }
            } catch (PDOException $e) {
                http_response_code(400);
                if ($e->getCode() == 23000) {
                    echo json_encode(["message" => "Este e-mail já está cadastrado para outro usuário."]);
                } else {
                    echo json_encode(["message" => "Erro de banco: " . $e->getMessage()]);
                }
            }
        }
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            // Find old photo path to delete it
            $stmtSelect = $db->prepare("SELECT foto_path, email FROM usuarios WHERE id = :id");
            $stmtSelect->bindParam(':id', $id);
            $stmtSelect->execute();
            $user = $stmtSelect->fetch();
            
            if ($user) {
                // Safeguard: Do not let users delete the default admin account to avoid locking themselves out
                if ($user['email'] === 'admin@techmanager.com') {
                    http_response_code(400);
                    echo json_encode(["message" => "O administrador principal do sistema não pode ser excluído."]);
                    exit();
                }

                $foto = $user['foto_path'];
                
                $query = "DELETE FROM usuarios WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id);
                
                if ($stmt->execute()) {
                    // Delete photo from disk
                    if (!empty($foto)) {
                        $fileFullPath = '../' . $foto;
                        if (file_exists($fileFullPath)) {
                            unlink($fileFullPath);
                        }
                    }
                    echo json_encode(["message" => "Usuário excluído com sucesso."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Erro ao excluir usuário."]);
                }
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Usuário não encontrado."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID do usuário não informado."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}
?>
