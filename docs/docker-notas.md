# Resumen

## Capas
Son las modificaciones que se le hacen a la imagen original para configurarla, por ejemplo con Dockerfile.

## Namespaces
Instrucciones que aíslan el contenedor del host, haciéndole creer que e proceso que o creó es el verdadero host.

## Volúmenes
Unidades creadas para dar persistencia. Es decir, ya que la memoria de los contenedores es volátil ( solo existe mientras el contenedor trabaja, al detener el proceso la información dentro desaparece y la imagen queda como al principio), los volúmenes almacenan dicha información para que el contenedor, luego de iniciarse de nuevo, recupere dicha información y continúe con su trabajo sin retrasos. Si en la ruta específica, al momento de enlazar el contenedor con el volumen, éste se encuentra vacío, el contenedor copiará sus archivos en el volumen.

## Redes
Conexión entre contenedores, existen dos tipos: las **locales** (creadas por el usuario) y las **bridge**, la principal diferencia entre ellas es que aquellas manejan un DNS propio, por lo que al reiniciar los contenedores y reasignarse las IP (comúnmente cambian) , se conserve la conexión y no sea necesaria reconfigurarla de nuevo.
