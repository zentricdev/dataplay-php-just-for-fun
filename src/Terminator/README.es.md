[EN](./README.md) | [**ES**](./README.es.md)

---

# SkyNet y Terminator: Una Simulación en PHP

Este directorio alberga un **prototipo funcional** que emula el universo de "Terminator" de una manera lúdica y educativa. El proyecto tiene un doble propósito: recrear conceptualmente una misión asignada a una unidad Terminator en un hipotético entorno de **PHP 11.4**, a la vez que actúa como un caso de estudio práctico de técnicas y patrones de arquitectura modernos en PHP.

## Contexto Temático

Inspirada en la película de 1984 _The Terminator_, la simulación presenta a la superinteligencia artificial **SkyNet** (`Skynet.php`), que construye y despliega una unidad de infiltración conocida como **T-800** (`T800.php`). La unidad es enviada a una coordenada espaciotemporal específica para eliminar a su objetivo principal: **Sarah Connor**.

El archivo `Command.php` sirve como punto de entrada, donde se instancia al Terminator, se le asigna su objetivo y se le ordena cumplir su misión.

## Conceptos de PHP Ilustrados

Este proyecto utiliza varios patrones de diseño y características modernas de PHP (8.1+).

### 1. Clases Abstractas y Herencia

- **`Skynet.php`**: Definida como una `abstract class`. No se puede instanciar directamente, pero establece la base del sistema, conteniendo constantes fundamentales como la corporación (`Cyberdyne Systems`) y el versionado, bajo la suposición lúdica de que SkyNet se desarrolla en un futuro entorno de **PHP 11.4**.

- **`T800.php`**: Hereda de `Skynet` (`extends Skynet`), especializando la funcionalidad base para una unidad de combate.

### 2. Clases Finales

- **`T800.php`**, los DTOs y la `SkyNetException` están declarados como `final`. Esto previene la herencia posterior, asegurando que la implementación permanezca inmutable y única, una buena práctica para la lógica de dominio específico.

### 3. Data Transfer Objects (DTOs) y Clases Readonly

Para garantizar la integridad de los datos, el proyecto utiliza DTOs inmutables:

- **`DTOs/SpatioTemporalLocation.php`**: Una `readonly class` que representa las coordenadas de espacio-tiempo.

- **`DTOs/Target.php`**: Una `readonly class` que define el objetivo, que encapsula el DTO `SpatioTemporalLocation`.

- **Promoción de Propiedades del Constructor**: Ambas clases usan esta sintaxis concisa para declarar e inicializar propiedades directamente en la firma del constructor.

- **Representación de Cadenas**: Ambos DTOs implementan el método mágico `__toString()`. Esto permite que los objetos se incrusten directamente en los mensajes de registro, ya que PHP los convierte automáticamente en una cadena legible por humanos (por ejemplo, coordenadas y marcas de tiempo), simplificando significativamente la lógica de registro dentro de las clases principales.

### 4. Patrón Builder e Interfaz Fluida

- La clase `T800` implementa una variante del **patrón Builder**. Métodos como `setTarget()` y `relocate()` devuelven `$this`, permitiendo una **Interfaz Fluida**. Esto da como resultado un código altamente legible dentro de `Command.php`.

### 5. Recursión y Persistencia de Estado Estático

- **Recursión con Estado**: El método `accomplish()` utiliza la **recursión** para reflejar la naturaleza implacable de la máquina.

- **Variables Estáticas**: En lugar de pasar el recuento de intentos como un argumento de la función (lo que saturaría la firma del método), el proyecto utiliza una variable `static $attempts` dentro del método. Esta técnica avanzada de PHP permite que la función "recuerde" su estado a través de llamadas recursivas, manteniendo un contador persistente a lo largo de múltiples bucles temporales sin exponerlo al mundo exterior. Esto demuestra un enfoque más limpio y encapsulado para la gestión del estado en algoritmos recursivos.

### 6. Manejo Semántico de Excepciones

- **`Exceptions/SkyNetException.php`**: Una excepción personalizada utilizada para manejar errores específicos del dominio. Esto demuestra un enfoque robusto y semántico para la gestión de errores más allá de las excepciones genéricas de PHP.

- **Patrón de Lanzamiento Estático**: La excepción implementa un método estático `throw()`. Esto permite que el sistema active errores usando una sintaxis más limpia (`SkyNetException::throw('...')`) en lugar de la instanciación tradicional `throw new`. Este patrón centraliza la creación de excepciones y puede extenderse para manejar tipos de error específicos con nombres de método descriptivos.

### 7. Sincronización Espaciotemporal

- **Ajuste Dinámico de la Línea de Tiempo**: El método `relocate()` realiza un cambio de estado interno crucial. Cuando el Terminator es desplazado, el reloj interno del sistema se sincroniza con la línea de tiempo del objetivo (`$this->timeline = $this->target->location->timeline`). Esto se refleja en los registros de ejecución, donde se puede observar el salto desde la fecha de origen en 2029 hasta la fecha de llegada del objetivo en 1984, simulando un desplazamiento temporal en tiempo real.

- **Nota técnica sobre el registro (Logging)**: La clase `Logger` opera de manera independiente a la hora del sistema anfitrión. En su lugar, calcula las marcas de tiempo combinando la propiedad interna `$timeline`, inicializada en el momento del despliegue (11 de julio de 2029), con el `$timelineClock`. Este reloj captura el `microtime()` exacto del inicio de la línea de tiempo, permitiendo al sistema desplazar la fecha futura mediante los segundos transcurridos reales de la operación. Esto garantiza que los registros mantengan la coherencia cronológica con el canon de la película, incluso cuando la unidad transita del futuro al pasado durante la secuencia `relocate()`.

### 8. Divergencia y Persistencia de la Línea de Tiempo

Mientras que las películas a menudo representan el fracaso del Terminator, esta simulación refleja la estrategia definitiva de SkyNet en múltiples líneas de tiempo. Cada ejecución de `accomplish()` representa una rama específica de la realidad.

El uso de **variables estáticas dentro de la recursión** permite a la unidad rastrear sus intentos a través de estos bucles temporales. Si una misión falla (simulación de `random_int`), la unidad activa una excepción "Volveré" y se vuelve a ejecutar. Desde la perspectiva de SkyNet, la misión solo se completa cuando el objetivo es nulo, lo que significa que la simulación persistirá hasta que encuentre la línea de tiempo específica en la que el Terminator elimina con éxito al objetivo.

### 9. Sistema de Registro

La clase `Logger` proporciona un mecanismo de registro sincronizado con la línea de tiempo. Mantiene una línea de tiempo interna y un reloj de misión, calculando las marcas de tiempo mediante el desplazamiento de la fecha de inicio con los segundos transcurridos. Los mensajes se almacenan en un array con marcas de tiempo precisas y se envían a la consola con códigos de color ANSI para una mejor legibilidad. Esto asegura que los registros permanezcan consistentes con la cronología ficticia de la simulación, incluso durante los desplazamientos temporales.

## Desglose de Archivos

- **`Command.php`**: El script principal de ejecución.

- **`Models/`**:

  - `Skynet.php`: La base abstracta que define los atributos principales de SkyNet.
  - `T800.php`: La implementación del T-800 que contiene la lógica de la misión.
  - `Logger.php`: Sistema de registro sincronizado con la línea de tiempo con salida coloreada.

- **`DTOs/`**:

  - `Target.php`: Estructura para los datos del objetivo (Sarah Connor).
  - `SpatioTemporalLocation.php`: Definición de coordenadas de espacio-tiempo (Los Ángeles, 1984).

- **`Exceptions/`**:
  - `SkyNetException.php`: Excepción personalizada específica del dominio.

## Ejecución

Una vez que se descarga el repositorio y se instalan las dependencias (a través de `composer install`), puedes iniciar la misión directamente desde tu terminal.

El proyecto incluye un `Makefile` para simplificar la interacción con la unidad T-800. Para ejecutar la simulación y presenciar el desplazamiento temporal y los registros de la misión, ejecuta `make terminator`:

```text
2029-07-11 22:38:14 BUILDING UNIT Terminator SERIES T-800 MODEL 101
2029-07-11 22:38:15 TARGET SET TO Sarah Connor, Big Jeff's waitress, Born 1965
1984-05-12 01:52:01 UNIT RELOCATED TO Lat: 34.0522, Lon: 118.2437, 1984-05-12 01:52:00 (America/Los_Angeles)
1984-05-12 01:52:02 ACQUIRING TARGET...
1984-05-12 01:52:04 MISSION FAILED - TARGET ESCAPED - I'LL BE BACK
1984-05-12 01:52:06 ACQUIRING TARGET...
1984-05-12 01:52:09 MISSION FAILED - TARGET ESCAPED - I'LL BE BACK
1984-05-12 01:52:12 ACQUIRING TARGET...
1984-05-12 01:52:16 MISSION FAILED - TARGET ESCAPED - I'LL BE BACK
1984-05-12 01:52:20 ACQUIRING TARGET...
1984-05-12 01:52:25 MISSION ACCOMPLISHED - TARGET TERMINATED AFTER 4 ATTEMPTS
```
