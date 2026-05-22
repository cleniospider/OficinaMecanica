   <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          
            $nome   = htmlspecialchars($_POST['nome']); 
            $email  = htmlspecialchars($_POST['email']);
            $numero = htmlspecialchars($_POST['telefone']); 
            $cpf    = htmlspecialchars($_POST['cpf']); 
            $senha  = password_hash($_POST['senha'], PASSWORD_DEFAULT); 
    
        }
?>
