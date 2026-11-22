# Sistema de Gerenciamento de Usuários

Projeto individual desenvolvido como atividade avaliativa final da Unidade Curricular 4 (UC4) do Programa Jovem Programador — SENAC.

A aplicação integra front-end, back-end e banco de dados, permitindo o gerenciamento de usuários por meio de operações CRUD, autenticação com sessões PHP e auditoria automática das operações realizadas.

## Funcionalidades

- Autenticação de usuários por login e senha.
- Controle de acesso às páginas internas por sessão.
- Encerramento da sessão (logout).
- Cadastro de usuários.
- Listagem de usuários cadastrados.
- Atualização de dados cadastrais.
- Exclusão de usuários, com proteção contra exclusão da própria conta.
- Validação dos dados recebidos pelo servidor.
- Proteção CSRF nas operações de alteração de dados.
- Armazenamento de senhas utilizando hash.
- Menu de navegação reutilizável.
- Interface responsiva.
- Registro automático de operações INSERT, UPDATE e DELETE por triggers MySQL.
- Histórico de auditoria com data, hora e dados anteriores e posteriores às operações.
- Relatório de logs protegido por autenticação.

## Tecnologias utilizadas

- PHP
- MySQL
- HTML5
- CSS3
- Bootstrap 5.3.3
- PDO para comunicação com o banco de dados

## Estrutura do projeto

```text
.
├── assets/
│   └── css/
│       └── style.css
├── config/
│   └── conexao.example.php
├── includes/
│   ├── autenticacao.php
│   └── navegacao.php
├── sistema-gerenciamento-usuarios/
│   └── database/
│       └── sistema_gerenciamento_usuarios.sql
├── usuarios/
│   ├── delete.php
│   ├── insert.php
│   ├── logs.php
│   ├── select.php
│   └── update.php
├── .gitignore
├── index.php
├── logout.php
└── README.md
```

O arquivo `config/conexao.php` deve ser criado localmente e não é incluído no repositório.

## Requisitos para execução

- Servidor web com suporte a PHP.
- PHP com extensão PDO MySQL habilitada.
- Servidor MySQL.
- Navegador web.
- XAMPP ou ambiente equivalente para execução local.

O desenvolvimento e os testes foram realizados em ambiente Windows, utilizando XAMPP, PHP 8.2.12 e MySQL 8.0.46.

## Instalação e configuração

### 1. Obter o projeto

Clone o repositório dentro de um diretório servido pelo Apache, como a pasta `htdocs` do XAMPP.

### 2. Criar o banco de dados

Execute o script:

`sistema-gerenciamento-usuarios/database/sistema_gerenciamento_usuarios.sql`

O script cria o banco `sistema_gerenciamento_usuarios`, a tabela `Usuario`, um registro inicial de administrador, a tabela `Log_Auditoria` e três triggers de auditoria (`AFTER INSERT`, `AFTER UPDATE` e `AFTER DELETE`).

As triggers registram automaticamente as operações realizadas na tabela `Usuario`. Os registros anteriores à instalação das triggers não são recuperados retroativamente.

### 3. Configurar a conexão

Na pasta `config`, faça uma cópia de:

`conexao.example.php`

Renomeie a cópia para:

`conexao.php`

Configure os seguintes parâmetros conforme o ambiente:

```php
$host = 'localhost';
$dbname = 'sistema_gerenciamento_usuarios';
$username = 'seu_usuario_mysql';
$password = 'sua_senha_mysql';
```

O arquivo de conexão local é ignorado pelo Git para evitar a publicação das credenciais do banco.

### 4. Iniciar os serviços

Inicie o Apache e o servidor MySQL correspondente à configuração utilizada.

### 5. Acessar a aplicação

Abra o arquivo `index.php` pelo endereço HTTP correspondente à pasta onde o projeto foi instalado.

Exemplo:

`http://localhost/nome-da-pasta-do-projeto/index.php`

Utilize as credenciais do administrador inicial definidas para o ambiente de instalação.

### Primeiro acesso e senha do administrador

O script SQL cria um administrador inicial com login `admin` e e-mail `admin@sistema.local`. A senha é armazenada como hash bcrypt.

Para definir uma senha conhecida no ambiente de instalação, gere um novo hash com a função PHP `password_hash()` e atualize o campo `senha` do administrador no banco de dados.

Por segurança, não utilize credenciais de teste em ambientes compartilhados.

## Organização dos arquivos

| Arquivo | Responsabilidade |
| --- | --- |
| `index.php` | Autenticação e início da sessão |
| `logout.php` | Encerramento da sessão |
| `config/conexao.example.php` | Modelo de configuração do PDO |
| `includes/autenticacao.php` | Verificação de autenticação |
| `includes/navegacao.php` | Menu de navegação reutilizável |
| `usuarios/insert.php` | Cadastro de usuários |
| `usuarios/logs.php` | Relatório de auditoria das operações realizadas |
| `usuarios/select.php` | Listagem de usuários |
| `usuarios/update.php` | Atualização de usuários |
| `usuarios/delete.php` | Exclusão de usuários |
| `assets/css/style.css` | Estilização da aplicação |
| `sistema-gerenciamento-usuarios/database/sistema_gerenciamento_usuarios.sql` | Criação do banco, tabelas e triggers de auditoria |

## Auditoria de operações

O desafio extra utiliza a tabela `Log_Auditoria` para registrar automaticamente as operações realizadas na tabela `Usuario`.

- `INSERT`: registra os dados do usuário cadastrado.
- `UPDATE`: registra os dados anteriores e posteriores à alteração.
- `DELETE`: preserva os dados do usuário excluído no histórico.

Cada evento possui identificador próprio, identificador do usuário afetado, tipo de operação, dados anteriores, dados novos e data/hora.

Os dados são armazenados em formato JSON, sem incluir o campo de senha. O identificador do usuário afetado não representa necessariamente o usuário autenticado que realizou a operação.

O relatório está disponível em `usuarios/logs.php` para usuários autenticados. Os eventos são apresentados do mais recente para o mais antigo.

## Segurança

A aplicação utiliza consultas preparadas com PDO, hashes de senha, validação de entradas, proteção CSRF e controle de sessão.

O projeto possui finalidade acadêmica. Antes de qualquer utilização em produção, são necessárias avaliações adicionais de segurança, permissões, configuração do servidor e gerenciamento de credenciais.

A senha do administrador inicial deve ser alterada antes de disponibilizar o sistema em qualquer ambiente compartilhado.

## Escopo da atividade

Esta entrega contempla os requisitos obrigatórios da atividade avaliativa: banco de dados, autenticação, sessões, operações CRUD, navegação e interface.

O desafio extra também foi implementado, incluindo a tabela `Log_Auditoria`, três triggers MySQL e uma página de relatório para consulta do histórico de operações.

## Autor

Projeto individual desenvolvido para a avaliação final da UC4 — Programa Jovem Programador, SENAC.
