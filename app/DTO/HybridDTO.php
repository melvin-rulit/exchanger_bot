<?php

namespace App\DTO;

use ReflectionClass;

class HybridDTO
{
    /**
     * @throws \ReflectionException
     */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            // Нет конструктора — просто создаём и присваиваем свойства
            $instance = new static();
            foreach ($data as $key => $value) {
                if (property_exists($instance, $key)) {
                    $instance->{$key} = $value;
                }
            }
            return $instance;
        }

        // Собираем аргументы конструктора по именам параметров
        $params = [];
        foreach ($constructor->getParameters() as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $data)) {
                $params[] = $data[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } else {
                throw new \InvalidArgumentException("Missing value for parameter '{$name}' in " . static::class);
            }
        }

        return $reflection->newInstanceArgs($params);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
