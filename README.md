# uc4-aula18-17Nov-avaliativa-final-front-back-database


Projeto Final (Individual): Sistema de Gerenciamento de Usuários com Autenticação
Objetivo Geral: Desenvolver uma aplicação web completa (Front-end + Back-end + Banco de Dados) aplicando as quatro operações básicas de um CRUD (Create, Read, Update, Delete) aplicadas ao cadastro de usuários, adicionando controle de acesso por meio de uma tela de login funcional.

Especificações Detalhadas


1.
Banco de Dados:
•
Criar um banco de dados e uma tabela Usuário contendo os campos necessários para cadastro e autenticação (id, nome, email, login e senha).
•
Inserir previamente via script SQL ao menos um usuário administrador padrão para permitir o primeiro acesso ao sistema.


2.
Tela de Login e Controle de Sessão:
•
Criar uma página inicial de login (index.php) que valide o usuário e a senha informados diretamente no banco de dados.
•
Utilizar sessões ($_SESSION) no PHP para proteger as páginas internas do CRUD, impedindo que usuários não autenticados acessem o sistema diretamente pela URL.
•
Incluir uma opção de Sair (Logout) para encerrar a sessão e retornar à tela de login.


3.
Operações do CRUD (Área Interna):
•
Cadastrar (insert.php): Formulário HTML e lógica PHP para adicionar novos usuários no banco de dados.
•
Listar/Relatório (select.php): Tabela exibindo todos os usuários cadastrados no sistema.
•
Atualizar (update.php): Formulário de edição de usuários.
•
Excluir (delete.php): Formulário de deleção de usuários.


4.
Navegação e Interface:
•
Manter um menu de navegação (<nav>) acessível em todas as telas internas para alternar entre as operações do CRUD e a opção de logout.
•
Estilizar formulários, tabelas e a tela de login com CSS de forma limpa e organizada.


5.
Desafio Extra — Sistema de Log com Triggers:
•
Criar uma tabela Log_Auditoria no banco de dados.
•
Implementar Triggers no MySQL (AFTER INSERT, AFTER UPDATE, AFTER DELETE) na tabela Usuario para registrar automaticamente toda criação, alteração e exclusão
de registros, armazenando o tipo de ação, os dados afetados e a data/hora do evento.
•
Criar uma página de Relatório de Logs no sistema para consultar e exibir o histórico registrado pela tabela de log.
Entrega: Compactar a pasta completa do projeto ou encaminhar o link do repositório público no github e enviar até a data combinada. Encaminhar junto o script de criação de banco para poder replicar o processo.