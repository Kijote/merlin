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

Existen variados métodos de solución de la trilateración, este proyecto utiliza el [algoritmo de trilateración](https://confluence.slac.stanford.edu/display/IEPM/TULIP+Algorithm+Alternative+Trilateration+Method) presentado por investigadores de la Universidad de Standford debido a su simplicidad y alta performance.

### 2. Mensaje

Para determinar el mensaje, se analiza cada una de las recepciones de los satélites, se determina el "delay" que presenta cada uno y se elimina para obtener tres mensajes parciales de iguales dimensiones.

Luego, para obtener el mensaje, se unifican dichos arrays sólo tomando en cuenta los mensajes que tienen contenido no vacío.


