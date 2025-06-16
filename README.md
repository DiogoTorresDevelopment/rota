# Sistema de Gestão de Rota

## Sobre o Projeto
Este é um sistema desenvolvido em Laravel para gerenciamento de rotas, permitindo o controle e organização de trajetos, veículos e motoristas. O sistema oferece uma interface intuitiva para planejamento e acompanhamento de rotas, otimizando a gestão de transportes.

## Requisitos do Sistema
- PHP >= 8.0
- Composer
- MySQL
- Node.js e NPM (para compilação de assets)

## Instalação

1. Clone o repositório
```bash
git clone [URL_DO_REPOSITÓRIO]
cd rota
```

2. Instale as dependências do PHP via Composer
```bash
composer install
```

3. Copie o arquivo de ambiente
```bash
cp .env.example .env
```

4. Gere a chave da aplicação
```bash
php artisan key:generate
```

5. Configure o banco de dados
- Edite o arquivo `.env` e configure as seguintes variáveis:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sgdb_rota
DB_USERNAME=root
DB_PASSWORD=
```

6. Execute as migrações do banco de dados
```bash
php artisan migrate
```

7. Instale as dependências do Node.js
```bash
npm install
```

8. Compile os assets
```bash
npm run dev
```

9. Inicie o servidor de desenvolvimento
```bash
php artisan serve
```

## Acessando o Sistema
Após iniciar o servidor, acesse o sistema através do navegador:
```
http://localhost:8000
```

## Funcionalidades Principais
- Gestão de rotas
- Cadastro de veículos
- Cadastro de motoristas
- Planejamento de trajetos
- 
- Acompanhamento em tempo real
- Relatórios e estatísticas

## API do Motorista
Todas as rotas do aplicativo do motorista utilizam autenticação JWT. Para obter
um token envie uma requisição `POST /api/login/driver` com `email` e `password`.
Use o token retornado no cabeçalho `Authorization: Bearer <token>` para acessar
os endpoints abaixo:

- `GET /api/driver/profile`
- `PUT /api/driver/profile`
- `GET /api/driver/routes`
- `GET /api/driver/routes/{route}`
- `GET /api/driver/deliveries`
- `GET /api/driver/deliveries/{delivery}`
- `POST /api/driver/deliveries/{delivery}/complete`
- `POST /api/driver/deliveries/{delivery}/complete-stop`
- `POST /api/driver/upload-photo` *(enviar arquivo `photo`)*
- `DELETE /api/driver/photos/{photo}`
- `POST /api/logout`

## Suporte
Para suporte ou dúvidas, entre em contato com a equipe de desenvolvimento.
