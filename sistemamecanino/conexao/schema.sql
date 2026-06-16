SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema oficina mecanica
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `oficinamecanica` DEFAULT CHARACTER SET utf8 ;
USE `oficinamecanica` ;

-- -----------------------------------------------------
-- Table `clientes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
  `nome completo` VARCHAR(100) NOT NULL,
  `cpf` VARCHAR(11) NOT NULL,
  `telefone` VARCHAR(9) NOT NULL,
  `email` VARCHAR(100) NULL,
  PRIMARY KEY (`cpf`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `veiculo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `veiculo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `placa` VARCHAR(10) NOT NULL,
  `marca/modelo` VARCHAR(50) NULL,
  `ano` INT NULL,
  `cor` VARCHAR(20) NULL,
  `cliente` VARCHAR(100) NULL,
  `clientes_cpf` VARCHAR(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_veiculo_clientes1`
    FOREIGN KEY (`clientes_cpf`)
    REFERENCES `clientes` (`cpf`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `OS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `OS` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `veiculo_id` INT NOT NULL,
  `mecanico_id` INT NOT NULL,
  `data_entrada` DATETIME NOT NULL,
  `veiculo_id1` INT NOT NULL,
  `clientes_cpf` VARCHAR(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_OS_veiculo1`
    FOREIGN KEY (`veiculo_id1`)
    REFERENCES `veiculo` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_OS_clientes1`
    FOREIGN KEY (`clientes_cpf`)
    REFERENCES `clientes` (`cpf`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome_completo` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `cpf` VARCHAR(11) NOT NULL,
  `perfil` ENUM('Admin', 'Mecanico', 'Recepcionista') NOT NULL,
  `nivel_acesso` INT NULL DEFAULT 1,
  `telefone` VARCHAR(20) NULL,
  `senha` VARCHAR(255) NOT NULL,
  `foto_perfil` VARCHAR(255) NULL,
  `salario_base` DECIMAL(10,2) NULL,
  `decimal_comissao` DECIMAL(5,2) NULL DEFAULT 0.00,
  `data_cadastro` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `OS_id` INT NOT NULL,
  PRIMARY KEY (`id`, `OS_id`),
  CONSTRAINT `fk_usuarios_OS`
    FOREIGN KEY (`OS_id`)
    REFERENCES `OS` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `pecas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pecas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `preco_venda` DECIMAL(10,2) NOT NULL,
  `estoque_atual` INT NULL DEFAULT 0,
  `estoque_minimo` INT NULL DEFAULT 5,
  `url_imagem` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `estoque_pecas_has_OS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `estoque_pecas_has_OS` (
  `estoque_pecas_id` INT NOT NULL,
  `OS_id` INT NOT NULL,
  `OS_veiculo_id1` INT NOT NULL,
  PRIMARY KEY (`estoque_pecas_id`, `OS_id`, `OS_veiculo_id1`),
  CONSTRAINT `fk_estoque_pecas_has_OS_estoque_pecas1`
    FOREIGN KEY (`estoque_pecas_id`)
    REFERENCES `pecas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_estoque_pecas_has_OS_OS1`
    FOREIGN KEY (`OS_id`)
    REFERENCES `OS` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `estoque_pecas_has_OS1`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `estoque_pecas_has_OS1` (
  `estoque_pecas_id` INT NOT NULL,
  `OS_id` INT NOT NULL,
  `OS_veiculo_id1` INT NOT NULL,
  PRIMARY KEY (`estoque_pecas_id`, `OS_id`, `OS_veiculo_id1`),
  CONSTRAINT `fk_estoque_pecas_has_OS1_estoque_pecas1`
    FOREIGN KEY (`estoque_pecas_id`)
    REFERENCES `pecas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_estoque_pecas_has_OS1_OS1`
    FOREIGN KEY (`OS_id`)
    REFERENCES `OS` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Financeiro`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Financeiro` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `data` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descricao` VARCHAR(255) NULL,
  `valor` DECIMAL(10,2) NULL,
  `tipo` ENUM('1: Receita', '2: Despesa') NOT NULL,
  `status` ENUM('PAGO', 'Aguardando', 'Cancelado') NOT NULL,
  `OS_id` INT NOT NULL,
  PRIMARY KEY (`id`, `OS_id`),
  CONSTRAINT `fk_Financeiro_OS1`
    FOREIGN KEY (`OS_id`)
    REFERENCES `OS` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `pecas_na_OS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pecas_na_OS` (
  `pecas_id` INT NOT NULL,
  `OS_id` INT NOT NULL,
  PRIMARY KEY (`pecas_id`, `OS_id`),
  CONSTRAINT `fk_pecas_has_OS_pecas1`
    FOREIGN KEY (`pecas_id`)
    REFERENCES `pecas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pecas_has_OS_OS1`
    FOREIGN KEY (`OS_id`)
    REFERENCES `OS` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `servicos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `servicos` (
  `idservicos` INT NOT NULL,
  `nome` VARCHAR(45) NULL,
  `preco` DECIMAL(10,2) NULL,
  PRIMARY KEY (`idservicos`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `servicos_has_OS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `servicos_has_OS` (
  `servicos_idservicos` INT NOT NULL,
  `OS_id` INT NOT NULL,
  PRIMARY KEY (`servicos_idservicos`, `OS_id`),
  CONSTRAINT `fk_servicos_has_OS_servicos1`
    FOREIGN KEY (`servicos_idservicos`)
    REFERENCES `servicos` (`idservicos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_servicos_has_OS_OS1`
    FOREIGN KEY (`OS_id`)
    REFERENCES `OS` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
