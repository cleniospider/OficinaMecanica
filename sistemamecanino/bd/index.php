<?php


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
           
            $nome = htmlspecialchars($_POST['usuario']);
            $senha = $_POST['senha'];

            echo "<h3>Dados recebidos:</h3>";
            echo "Usuário: " . $nome . "<br>";
            echo "Senha: " . $senha . "<br>";
            
        }

        ?>
    </form>

</body>
</html>