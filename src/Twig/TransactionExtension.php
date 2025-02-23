<?php

namespace App\Twig;

use App\Enum\TransactionTypeEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TransactionExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('type', [$this, 'type']),
            new TwigFunction('color', [$this, 'color']),
            new TwigFunction('contrast', [$this, 'tableColorContrast']),
        ];
    }

    public function color(int $type): string
    {
        return match ($type) {
            TransactionTypeEnum::Profit->value => 'style="background-color:rgba(52,211,153)"',
            TransactionTypeEnum::Expense->value => 'style="background-color:rgba(248,113,13)"',
            TransactionTypeEnum::Transfer->value => 'style="background-color:rgba(96,165,250)"',
            default => 'style="background-color:rgba(156,163,175)"',
        };
    }

    public function type(int $type): string
    {
        return match ($type) {
            TransactionTypeEnum::Profit->value => "Income",
            TransactionTypeEnum::Expense->value => "Expense",
            default => ""
        };
    }

    public function tableColorContrast($color): string
    {
        if (empty($color)) {
            return '';
        }
        // Convert hex color to RGB
        list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");

        // Calculate perceived brightness (Luma)
        $brightness = ($r * 0.299 + $g * 0.587 + $b * 0.114) / 255;

        // Return white or black based on background brightness
        $contrast = $brightness > 0.5 ? '#0a0a0a' : '#eeeeee';
        return "style=background:$color;color:$contrast";
    }
}