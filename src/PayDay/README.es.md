[EN](./README.md) | [**ES**](./README.es.md)

---

# PayDay

**PayDay** es un módulo PHP para calcular el desglose de efectivo
en billetes y monedas para pagos en efectivo.

Hace algún tiempo no era inusual realizar pagos periódicos en efectivo como, por ejemplo, complementos, dietas, gastos de representación etc. Esto representaba ciertas dificultades ya que, una vez elaborado el informe de pagos:

- **Coordinar con el banco** Era necesario comunicar al banco el total en efectivo que se iba a retirar, cuidando de que hubiera suficiente combinación de billetes y monedas para cada importe individual. Esto se hacía a ojo y no siempre se calculaba bien.

- **Rellenar los sobres** rellenar cada podía resultar tedioso y problemático, si no había cambio suficiente. Cuando no había cambio entonces hay que abrir sobres ya procesados y reorganizar.

- **Probabilidad de error** Si el importe del último sobre por rellenar no coincide con el efectivo restante significa que se ha cometido algún error. Toca abrir todos los sobres y comprobarlos hasta que todo esté OK.

Afortunadamente esta práctica es casi inexistente hoy en día y este módulo, por tanto, no tiene una aplicación práctica, es más bien un ejemplo de caso de uso de la técnica de **recursión**.

No obstante las ventajas serían:

- **Precisión**. Poder comunicar al banco la cantidad exacta de billetes y monedas necesarios.

- **Eficiencia** Se ahorra tiempo al rellenar los sobres porque se aplica el mismo método para todos: Usar la mínima cantidad posible de billetes y monedas.

- **Reducción de errores**. Rellenar un sobre de 1,500€ con 15 billetes de 100€ tiene más probabilidad de error que cuando se toman 3 billetes de 500€.

## Componentes

### Currency

Que representa una moneda con sus denominaciones de billetes y monedas.

### Cash

Que calcula el desglose de una cantidad en billetes y monedas.

El método `Cash::breakdown(float $amount)` recibe el importe inicial a desglosar y, mediante **recursión**, recorre todas las unidades disponibles (billetes y monedas) hasta finalizar.

El método `Cash::breakdownCollection(iterable $collection, ?string $attributeName = 'amount')` desglosa cada importe individual y retorna un resumen. Es decir, el detalle que hay que darle al banco para que prepare en efectivo.

### currencies.php

Archivo de configuración que define las denominaciones de billetes y monedas para cada moneda soportada (EUR, USD...).

### Command.php

El comando `make payday` invoca a `src/PayDay/Command.php` que es un ejemplo de funcionamiento. Lo que hace es generar un conjunto de pagos con importes aleatorios, procesarlos con `Cash::breakdownCollection` y emitir un reporte por terminal:

```text
**PAYROL**
┌────────────┬──────────────┐
│ Employee   │ Amount (EUR) │
├────────────┼──────────────┤
│ Employee 1 │     1,036.87 │
│ Employee 2 │     1,034.00 │
│ Employee 3 │     1,197.67 │
├────────────┼──────────────┤
│            │     3,268.54 │
└────────────┴──────────────┘
**BREAKDOWN SUMMARY**
┌───────┬────────┬──────┬──────────────┐
│ Count │   Unit │ Type │ Amount (EUR) │
├───────┼────────┼──────┼──────────────┤
│     6 │ 500.00 │ note │     3,000.00 │
│     1 │ 100.00 │ note │       100.00 │
│     1 │  50.00 │ note │        50.00 │
│     4 │  20.00 │ note │        80.00 │
│     2 │  10.00 │ note │        20.00 │
│     2 │   5.00 │ note │        10.00 │
│     3 │   2.00 │ coin │         6.00 │
│     1 │   1.00 │ coin │         1.00 │
│     2 │   0.50 │ coin │         1.00 │
│     1 │   0.20 │ coin │         0.20 │
│     2 │   0.10 │ coin │         0.20 │
│     2 │   0.05 │ coin │         0.10 │
│     2 │   0.02 │ coin │         0.04 │
├───────┼────────┼──────┼──────────────┤
│       │        │      │     3,268.54 │
└───────┴────────┴──────┴──────────────┘
```

## Uso

### Ejemplo básico

```php
use J4F\PayDay\Currency;
use J4F\PayDay\Cash;

// Cargar configuración de moneda
$currencies = require 'currencies.php';
$currency = Currency::fromArray($currencies['EUR']);

// Crear instancia de Cash
$cash = new Cash($currency);

// Desglosar una cantidad individual
$breakdown = $cash->breakdown(1234.56);
print_r($breakdown);
```

### Procesar una nómina completa

```php
use J4F\PayDay\Currency;
use J4F\PayDay\Cash;

// Definir nómina
$payrol = [
    ['employee' => 'Employee 1', 'amount' => 1500.50],
    ['employee' => 'Employee 2', 'amount' => 1750.25],
    ['employee' => 'Employee 3', 'amount' => 1200.00],
];

// Configurar moneda
$currencies = require 'currencies.php';
$currency = Currency::fromArray($currencies['EUR']);

// Calcular desglose total
$cash = new Cash($currency);
$results = $cash->breakdownCollection($payrol);

// Mostrar resultados
foreach ($results as $result) {
    echo sprintf(
        "%d x %.2f %s = %.2f EUR\n",
        $result['count'], // cantidad de billetes o monedas
        $result['denomination'], // valor de billete o moneda
        $result['type'], // coin | note (moneda o billete)
        $result['amount'] // importe
    );
}
```

## Notas técnicas

- **Precisión**: Todas las cantidades se procesan internamente en **céntimos** (multiplicadas por 100) para evitar problemas de precisión con números flotantes.

- **Recursión**: El método `breakdown()` es recursivo y utiliza una bandera `$processing` para controlar el estado.

- **Redondeo**: La función `round()` se aplica para evitar errores de redondeo en operaciones con flotantes.

## Extensión

Para agregar soporte para una nueva moneda, editar el archivo de configuración `currencies.php`:

```php
return [
    'EUR' => [...],
    'USD' => [...],
    'GBP' => [
        'code' => 'GBP',
        'coins' => [0.01, 0.02, 0.05, 0.10, 0.20, 0.50, 1, 2],
        'notes' => [5, 10, 20, 50],
    ],
];
```
