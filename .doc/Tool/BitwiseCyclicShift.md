# Побитовый циклический сдвиг на PHP

```php
namespace X\Tool;
trait BitwiseCyclicShift {
    /**
     * Побитовый циклический сдвиг вправо (32bit)
     * @param  int $v value
     * @param  int $c count
     * @return int
     */
    protected function BitwiseCROR($v, $c) {}

    /**
     * Побитовый циклический сдвиг влево (32bit)
     * @param  int $v value
     * @param  int $c count
     * @return int
     */
    protected function BitwiseCROL($v, $c) {}
}
```