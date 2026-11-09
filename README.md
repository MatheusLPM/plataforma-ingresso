# Plataforma de Ingresso

  Plataforma de venda de ingressos para eventos, construída em PHP puro seguindo o metodo de implementacao TDD.

  ## Requisitos

  - Docker com Docker Compose
  - Git

  O PHP e o Composer rodam dentro do container.

  ## Como subir o ambiente

  ```bash
  docker compose up -d --build
  ```

  A aplicação fica disponível em <http://localhost:8080>. Para conferir:

  ```bash
  curl -i localhost:8080/saude
  ```

  A resposta esperada é um `200` com o corpo:

  ```json
  {"status":"ok","versao":"dev"}
  ```

  ## Como rodar os testes

  Instale as dependências de desenvolvimento dentro do container:

  ```bash
  docker compose exec php composer install
  ```

  Depois, rode a suíte:

  ```bash
  docker compose exec php vendor/bin/phpunit
  ```

  ## Rotas

  | Caminho | Status | Resposta |
  |---|---|---|
  | `/` | 200 | Página inicial |
  | `/saude` | 200 | JSON com `status` e `versao` |
  | Qualquer outro | 404 | Página não encontrada |

  ## Estrutura

  - `public/index.php`: front controller, a única porta de entrada da aplicação
  - `src/`: decide o que responder para cada caminho
  - `tests/`: testes do PHPUnit
  - `docker/nginx/`: configuração do nginx