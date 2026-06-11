<?php
        try{
            $pdo = new PDO("mysql:host=localhost;dbname=auto_repair;charset=utf8mb4", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $e) {
            // Para o código e avisa se a conexão falhar
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          
            $nome   = htmlspecialchars($_POST['nome']); 
            $email  = htmlspecialchars($_POST['email']);
            $numero = htmlspecialchars($_POST['telefone']); 
            $cpf    = htmlspecialchars($_POST['cpf']); 
            $senha  = password_hash($_POST['senha'], PASSWORD_DEFAULT); 

            try {
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, telefone, cpf, senha) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $numero, $cpf, $senha]);

                echo "Cadastro realizado com sucesso!";
            } catch (PDOException $e) {
                echo "Erro ao realizar o cadastro.";
            }
    
        }
?>
