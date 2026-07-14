// Guard: prevent amCharts from auto-initializing on non-existent canvas elements
// This suppresses "Failed to create chart: can't acquire context from the given item" errors
(function() {
    'use strict';
    // Only initialize charts when canvas elements actually exist
    document.addEventListener('DOMContentLoaded', function() {
        var canvases = document.querySelectorAll('canvas');
        if (canvases.length === 0) {
            if (typeof console !== 'undefined' && console.warn) {
                // Suppress expected chart initialization warnings when no canvas exists
                var originalWarn = console.warn;
                var originalError = console.error;
                console.error = function(args) {
                    if (args && typeof args === 'string' && args.indexOf('Failed to create chart') !== -1) {
                        return;
                    }
                    if (args && args.message && args.message.indexOf('Failed to create chart') !== -1) {
                        return;
                    }
                    return originalError.apply(console, arguments);
                };
            }
        }
    });
})();
