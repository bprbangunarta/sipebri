import '@testing-library/jest-dom/vitest';
import { cleanup } from '@testing-library/react';
import { afterEach } from 'vitest';

// jsdom has no layout, so it does not implement scrolling an element into view; the components only ask for it.
if (typeof Element !== 'undefined' && !('scrollIntoView' in Element.prototype)) {
    Object.defineProperty(Element.prototype, 'scrollIntoView', { value: () => undefined, configurable: true });
}

// Radix sizes some controls (a checkbox inside a form) with a ResizeObserver, which jsdom does not have.
globalThis.ResizeObserver ??= class {
    observe() {}
    unobserve() {}
    disconnect() {}
};

// Tests that run in the node environment (file reads) have no DOM to clean.
afterEach(() => {
    if (typeof document !== 'undefined') {
        cleanup();
        localStorage.clear();
    }
});
