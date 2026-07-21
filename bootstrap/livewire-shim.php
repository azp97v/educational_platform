<?php

/**
 * Livewire compatibility shim.
 *
 * Several Blade views were committed with pre-compiled Livewire guard calls
 * (isRenderingLivewireComponent, openLoop, etc.) from before Livewire was
 * removed as a dependency. These guards are no-ops in non-Livewire views
 * (the condition always returns false), but PHP throws a fatal error if the
 * classes don't exist at all. This shim provides the missing classes so that
 * the views render normally without requiring Livewire to be reinstalled.
 */

namespace Livewire\Mechanisms\ExtendBlade {
    if (!class_exists(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::class)) {
        class ExtendBlade
        {
            public static function isRenderingLivewireComponent(): bool
            {
                return false;
            }
        }
    }
}

namespace Livewire\Features\SupportCompiledWireKeys {
    if (!class_exists(\Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::class)) {
        class SupportCompiledWireKeys
        {
            public static function openLoop(): void {}
            public static function closeLoop(): void {}
            public static function startLoopIteration(): void {}
            public static function endLoop(): void {}
        }
    }
}
