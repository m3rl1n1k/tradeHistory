<?php

namespace App\Service\Chart;

use Symfony\Contracts\Translation\TranslatorInterface;

class ChartDataset
{

    protected array $data = [];
    private ?array $categoryColor = null;

    public function __construct(protected TranslatorInterface $translator)
    {
    }

    public function getColors(): array
    {
        return array_values(array_unique($this->categoryColor));
    }

    public function setColor(?string $categoryColor): void
    {
        if ($categoryColor === null) {
            $this->categoryColor[] = "#eeeeee";
        } else {
            $this->categoryColor[] = $categoryColor;
        }
    }

    public function getLabels(): ?array
    {
        $labels = array_keys($this->data);
        foreach ($labels as $label) {
            $result[] = $this->translator->trans($label, [], 'dashboard');
        }
        return $result;
    }

    public function getAmounts(): ?array
    {
        return array_values($this->data);
    }

    public function setData(string $key, $value): void
    {
        if (array_key_exists($key, $this->data)) {
            $this->data[$key] += $value;
        } else {
            $this->data[$key] = $value;
        }
    }
}