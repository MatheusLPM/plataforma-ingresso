# O que acontece quando o comprador aperta Enter

Quando o comprador digita o endereço de um site e aperta Enter, o navegador passa por quatro etapas antes de ver a página: descobre o IP do servidor (DNS), abre uma conexão segura (TCP e TLS), faz o pedido (HTTP) e, já dentro do servidor, a requisição passa pelo nginx e pelo PHP-FPM até chegar no código. Para ver as três primeiras, usei o `github.com`. Para a última, usei o meu ambiente local.

## 1. DNS: de nome para endereço

O navegador não sabe chegar em `github.com`, ele precisa de um IP. O `dig` mostra essa consulta:

```text
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 15664
;; flags: qr rd ra; QUERY: 1, ANSWER: 1, AUTHORITY: 0, ADDITIONAL: 1

;; ANSWER SECTION:
github.com.             46      IN      A       4.228.31.150

;; Query time: 9 msec
;; SERVER: 127.0.0.53#53(127.0.0.53) (UDP)
```

- `status: NOERROR`: o nome existe.
- `A 4.228.31.150`: é o endereço IPv4 do GitHub.
- `46`: é o TTL, em segundos. É quanto tempo essa resposta ainda pode ficar em cache. Se o GitHub trocar o IP, quem tem a resposta antiga em cache continua indo para o endereço velho até o TTL acabar. O 46 não é um número redondo porque a resposta já estava em cache há alguns segundos.
- `SERVER: 127.0.0.53`: quem respondeu foi o resolvedor local do Ubuntu, na minha própria máquina.
- `Query time: 9 msec`: foi rápido porque veio do cache. Quando consultei um domínio que não existe, a resposta foi `NXDOMAIN` e demorou cerca de 200 ms, porque o resolvedor precisou ir até os servidores do `.com`.

## 2. TLS: conexão segura e confiança

O cliente abre uma conexão TCP na porta 443 e negocia o TLS. O `openssl s_client` mostra a cadeia de certificados:

```text
Certificate chain
 0 s:CN = github.com
   i:C = GB, O = Sectigo Limited, CN = Sectigo Public Server Authentication CA DV E36
   v:NotBefore: Oct 30 00:00:00 2026 GMT; NotAfter: Jan 27 23:59:59 2027 GMT
 1 s:C = GB, O = Sectigo Limited, CN = Sectigo Public Server Authentication CA DV E36
   i:C = GB, O = Sectigo Limited, CN = Sectigo Public Server Authentication Root E46
 2 s:C = GB, O = Sectigo Limited, CN = Sectigo Public Server Authentication Root E46
   i:C = US, ST = New Jersey, L = Jersey City, O = The USERTRUST Network, CN = USERTrust ECC Certification Authority
---
Verification: OK
---
New, TLSv1.3, Cipher is TLS_AES_128_GCM_SHA256
```

Em cada certificado, `s:` é o dono e `i:` é quem assinou. Lendo de baixo para cima, quem assinou quem:

1. A USERTrust ECC, que já vem instalada no meu sistema, assinou a raiz Sectigo Root E46.
2. A Sectigo Root E46 assinou a intermediária Sectigo CA DV E36.
3. A intermediária assinou o certificado do `github.com`.

O meu computador não confia no GitHub diretamente. Ele confia na raiz instalada no sistema e confere as assinaturas, uma por uma, até chegar no site. Também confere se o nome bate com `github.com` e se a data está dentro da validade, que vai de 30/10/2026 a 27/01/2027. O resultado foi `Verification: OK`.

A versão negociada foi o `TLSv1.3`, que faz o handshake numa única ida e volta, contra duas do TLS 1.2. Quando testei o `teste.com`, a verificação falhou com `unable to get local issuer certificate`: a cadeia não chegava a nenhuma raiz em que o meu sistema confia.

## 3. HTTP: o pedido e a resposta

O `curl -v` mostra a conversa inteira. Linhas com `*` são informações do curl, `>` é o que foi enviado e `<` é o que chegou:

```text
* IPv4: 4.228.31.150
*   Trying 4.228.31.150:443...
* ALPN: curl offers h2,http/1.1
* SSL connection using TLSv1.3 / TLS_AES_128_GCM_SHA256 / X25519
* ALPN: server accepted h2
*  SSL certificate verify ok.
> GET / HTTP/2
> Host: github.com
> User-Agent: curl/8.5.0
> Accept: */*
>
< HTTP/2 200
< content-type: text/html; charset=utf-8
< cache-control: max-age=0, private, must-revalidate
< strict-transport-security: max-age=31536000; includeSubdomains; preload
< server: github.com
```

A versão do HTTP é escolhida durante o TLS, pelo ALPN: o curl ofereceu `h2` e `http/1.1`, e o servidor escolheu `h2`, ou seja, HTTP/2. O status foi `200`, que significa sucesso. Três cabeçalhos da resposta:

- `content-type: text/html; charset=utf-8`: o corpo é uma página HTML em UTF-8.
- `cache-control: max-age=0, private, must-revalidate`: a página não pode ficar em cache compartilhado e precisa ser confirmada com o servidor antes de ser reaproveitada, porque ela muda conforme quem está logado.
- `strict-transport-security: max-age=31536000`: durante um ano, o navegador só deve acessar o GitHub por HTTPS.

## 4. No meu ambiente: nginx, FastCGI, PHP-FPM e index.php

No meu projeto não tem DNS público nem TLS, porque ele roda só na minha máquina. A requisição vai direto para o nginx:

```text
$ curl -v localhost:8080/saude

* Connected to localhost (::1) port 8080
> GET /saude HTTP/1.1
> Host: localhost:8080
>
< HTTP/1.1 200 OK
< Server: nginx/1.29.1
< Content-Type: text/html; charset=UTF-8
< X-Powered-By: PHP/8.4.27
<
{"status":"ok","versao":"dev"}
```

Aqui a versão é HTTP/1.1, porque não existe TLS e, portanto, não há ALPN para negociar o HTTP/2. O caminho foi este:

1. O Docker repassa a porta 8080 da minha máquina para a porta 80 do container do nginx.
2. O nginx compara `/saude` com os blocos `location` do `default.conf`. Ele cai no `location /`, e o `try_files $uri /index.php?$query_string` procura um arquivo `public/saude`. Como não existe, a requisição vai para `/index.php`.
3. O `/index.php` casa com o `location = /index.php`, a correspondência exata, que tem prioridade sobre o `location ~ \.php$`.
4. Esse bloco usa o `fastcgi_pass php:9000`. O nginx não fala HTTP com o PHP-FPM, ele fala FastCGI, e envia variáveis como `SCRIPT_FILENAME` e `REQUEST_URI`. É delas que nasce o `$_SERVER`.
5. Um worker do PHP-FPM executa o `public/index.php`, que lê o caminho e a `APP_VERSAO`, chama a classe `Resposta` e envia o status 200 e o JSON.

O cabeçalho `X-Powered-By: PHP/8.4.27` prova que o PHP participou. O log do container confirma: `"GET /index.php" 200`. Quando pedi um arquivo `.css` de teste, a resposta veio sem esse cabeçalho, porque o nginx entregou o arquivo direto do disco.

## Onde mora um timeout

No nginx, o `fastcgi_read_timeout` define quanto tempo ele espera o PHP-FPM responder. O padrão é de 60 segundos. Se o `index.php` ficar preso mais que isso, por exemplo esperando um banco lento, o nginx desiste e o comprador recebe `504 Gateway Timeout`. O `max_execution_time` do PHP não protege desse caso: no Linux, ele só conta o tempo em que o script está executando, e não o tempo parado esperando o banco.
