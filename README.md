# MerLiN

Proyecto para presentar en **Mer**cado **Li**bre como challenge para **N**uevo ingreso.

## El challenge

El challenge se presenta como un problema para determinar la posición de una nave espacial respecto de tres satélites cuyas posiciones son conocidas. Como otra parte del mismo, debe determinarse el mensaje enviado por dicha nave y recibido por cada uno de los satélites.

Presenta, además, tres niveles de desarrollo:

1. Desarrollo de la solución del problema
2. Desarrollo de una API para obtener el resultado basado en la información total
3. Ampliación de la API para recibir información parcial y retornar el resultado

## El problema

El problema se puede dividir en dos:

1. Encontrar la posición respecto de la información de distancias recogida por cada uno de los satélites
2. Encontrar el mensaje respecto de la información parcial recibida por cada uno de los satélites

### 1. Posición - Fundamento matemático

Para determinar la posición de un punto respecto de la distancia a otros tres puntos, debemos utilizar la ecuación de la circunferencia centrada en cada uno de los puntos cuya información es conocida, ya que cada uno de los puntos de una circunferencia tiene la misma distancia respecto de su centro.

Al encontrar el punto en que las tres circunferencias se intersecan, se encuentra el punto solución del sistema.

Este proceso se denomina trilateración.

**Trilateración**

Existen variados métodos de solución de la trilateración, este proyecto utiliza el [algoritmo de trilateración](https://confluence.slac.stanford.edu/display/IEPM/TULIP+Algorithm+Alternative+Trilateration+Method) presentado por investigadores de la Universidad de Stanford debido a su simplicidad y alta performance.

### 2. Mensaje

Para determinar el mensaje, se analiza cada una de las recepciones de los satélites, se determina el "delay" que presenta cada uno y se elimina para obtener tres mensajes parciales de iguales dimensiones.

Luego, para obtener el mensaje, se unifican dichos arrays sólo tomando en cuenta los mensajes que tienen contenido no vacío.

## La implementación

El desarrollo de la solución se hizo sobre [Lumen](https://lumen.laravel.com/), un micro-framework PHP para el desarrollo de micro servicios y APIs desarrollado por Laravel.

### Instalación local del entorno

Para utilizar localmente el proyecto se debe disponer de git y docker-compose, ya que se ha desarrollado una imagen de docker basada en PHP v7.4 con Apache y MySQL hosteada en dockerhub


    $ git clone git@github.com:Kijote/merlin.git
    $ cd merlin

En la siguiente línea hay que cambiar _tu_pass_para_usuario_root_de_db_ por una contraseña a elección para el usuario root de la base de datos

    $ echo "tu_pass_para_usuario_root_de_db" > .db_root_password

Luego se utiliza docker-compose para iniciar el entorno

    $ docker-compose up -d

Este comando inicia MySQL, el proyecto merlin y un proyecto de configuración del mismo que prepara el entorno utilizando composer y artisan.

La imagen contiene todo lo necesario para ejecutar el proyecto.

### Solución Cloud-based

La solución se encuentra hosteada en Heroku, con deploy automático desde GitHub.

El acceso es vía la siguiente url:

    https://www.kijote.com.ar/

con los endpoint siguientes

    https://www.kijote.com.ar/topsecret
    https://www.kijote.com.ar/topsecret_split

para los niveles 2 y 3 respectivamente.

### API-TOKEN

Para el acceso a los servicios de la API es necesario setear en los headers la clave api-token con el valor ChEwi3.

    api-token: ChEwi3

