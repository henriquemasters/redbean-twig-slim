# Microframework MVC - RedBeanPHP + Twig + SlimPHP
Um microframework PHP que facilita sua vida na hora de fazer pequenas aplicações MVC.

## Tecnologias utilizadas:

Este projeto usa vários outros projetos open source para funcionar, mas é estruturado basicamente por:

- [RedBeanPHP ORM](https://www.redbeanphp.com) - biblioteca de mapeamento objeto-relacional independente, gratuito, licenciado por BSD e de código aberto, escrito por Gabor de Mooij. É uma biblioteca independente, não faz parte de nenhuma estrutura.
- [Twig Template](https://twig.symfony.com) - Template engine de um dos principais frameworks PHP do mundo: o Symfony. É um produto de código aberto licenciado sob uma licença BSD e mantido por Fabien Potencier.
- [SlimPHP Framework](https://www.slimframework.com/docs/v3) - Micro-Framework bastante leve e prático, e possui como principal característica a implementação RESTful. No caso deste projeto, em específico, o Slim está integrado com um helper para criação das View's através do Twig Template.

## Instalação

Requerido [Composer](https://getcomposer.org) para rodar.

Faça o clone do projeto para o diretório raíz de seu servidor.

```sh
git clone https://github.com/henriquemasters/redbean-twig-slim.git
```
Instale as dependências.

```sh
cd redbean-twig-slim
composer install
```

Pronto!


## License

MIT