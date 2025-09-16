(function() {
    'use strict';

    if (typeof expandwpData === 'undefined') {
        return;
    }

    let settings = expandwpData.settings;
    let editorType = expandwpData.editorType;
    let isRTL = expandwpData.isRTL;
    let i18n = expandwpData.i18n;

    let panelStates = {
        left: {
            expanded: false,
            width: settings.defaultWidth,
            element: null,
            handle: null
        },
        right: {
            expanded: false,
            width: settings.defaultWidth,
            element: null,
            handle: null
        }
    };

    let observers = [];
    let isInputActive = false;
    let isDragging = false;

    function init() {
        // DOM準備完了後に実行
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAfterDOM);
        } else {
            initAfterDOM();
        }
    }

    function initAfterDOM() {
        loadSavedWidths();
        setupKeyboardShortcuts();
        setupInputDetection();
        detectAndSetupPanels();

        // 自動/常時モードの処理
        if (shouldAutoActivate()) {
            setTimeout(function() {
                processAutoModes();
            }, 500);
        }

        // MutationObserver設定（自動モード用）
        setupMutationObservers();
    }

    function loadSavedWidths() {
        ['left', 'right'].forEach(function(side) {
            let storageKey = 'expandwp_width_' + editorType + '_' + side;
            let savedWidth = localStorage.getItem(storageKey);
            if (savedWidth && !isNaN(savedWidth)) {
                panelStates[side].width = parseInt(savedWidth, 10);
            }
        });
    }

    function saveWidth(side, width) {
        let storageKey = 'expandwp_width_' + editorType + '_' + side;
        if (width > 0) {
            localStorage.setItem(storageKey, width.toString());
        } else {
            localStorage.removeItem(storageKey);
        }
    }

    function shouldAutoActivate() {
        return window.innerWidth >= settings.minViewport;
    }

    function processAutoModes() {
        if (settings.modes.left === 'always') {
            expandPanel('left');
        } else if (settings.modes.left === 'auto') {
            if (isLeftPanelVisible()) {
                expandPanel('left');
            }
        }

        if (settings.modes.right === 'always') {
            expandPanel('right');
        } else if (settings.modes.right === 'auto') {
            if (isRightPanelVisible()) {
                expandPanel('right');
            }
        }
    }

    function detectAndSetupPanels() {
        // 左パネル検出
        settings.selectors.left.forEach(function(selector) {
            let element = document.querySelector(selector);
            if (element && !panelStates.left.element) {
                panelStates.left.element = element;
                setupDragHandle('left', element);
            }
        });

        // 右パネル検出
        settings.selectors.right.forEach(function(selector) {
            let element = document.querySelector(selector);
            if (element && !panelStates.right.element) {
                panelStates.right.element = element;
                setupDragHandle('right', element);
            }
        });
    }

    function setupDragHandle(side, panel) {
        let handle = document.createElement('div');
        handle.className = 'expandwp-resizer expandwp-resizer-' + side;
        handle.setAttribute('data-side', side);
        handle.style.cssText = 'position: absolute; top: 0; ' +
                               (isRTL ? 'right: 0;' : (side === 'left' ? 'right: 0;' : 'left: 0;')) +
                               ' width: 8px; height: 100%; cursor: col-resize; z-index: 9999; opacity: 0; transition: opacity 0.2s;';

        panel.style.position = 'relative';
        panel.appendChild(handle);

        panelStates[side].handle = handle;

        // ドラッグイベント
        handle.addEventListener('pointerdown', function(e) {
            startDrag(e, side);
        });

        // ホバー表示
        panel.addEventListener('mouseenter', function() {
            if (panelStates[side].expanded) {
                handle.style.opacity = '0.3';
            }
        });

        panel.addEventListener('mouseleave', function() {
            if (!isDragging) {
                handle.style.opacity = '0';
            }
        });
    }

    function startDrag(e, side) {
        if (isDragging) return;

        isDragging = true;
        e.preventDefault();

        let startX = e.clientX;
        let startWidth = panelStates[side].width;

        function onMove(e) {
            let deltaX = e.clientX - startX;
            if (isRTL) deltaX = -deltaX;
            if (side === 'left') deltaX = -deltaX;

            let newWidth = startWidth + deltaX;
            newWidth = Math.max(200, Math.min(800, newWidth));

            applyWidthWithSafetyCheck(side, newWidth);
        }

        function onEnd() {
            isDragging = false;
            document.removeEventListener('pointermove', onMove);
            document.removeEventListener('pointerup', onEnd);

            if (panelStates[side].handle) {
                panelStates[side].handle.style.opacity = '0';
            }

            saveWidth(side, panelStates[side].width);
        }

        document.addEventListener('pointermove', onMove);
        document.addEventListener('pointerup', onEnd);
    }

    function setupKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            if (isInputActive) return;

            if (e.altKey && !e.ctrlKey && !e.shiftKey && !e.metaKey) {
                if (e.code === 'BracketLeft') { // Alt + [
                    e.preventDefault();
                    togglePanel('left');
                } else if (e.code === 'BracketRight') { // Alt + ]
                    e.preventDefault();
                    togglePanel('right');
                } else if (e.code === 'Digit0') { // Alt + 0
                    e.preventDefault();
                    resetPanels();
                }
            }
        });
    }

    function setupInputDetection() {
        function checkInputFocus() {
            let activeElement = document.activeElement;
            isInputActive = activeElement && (
                activeElement.tagName === 'INPUT' ||
                activeElement.tagName === 'TEXTAREA' ||
                activeElement.isContentEditable ||
                activeElement.closest('[contenteditable="true"]')
            );
        }

        document.addEventListener('focusin', checkInputFocus);
        document.addEventListener('focusout', checkInputFocus);
        setInterval(checkInputFocus, 500);
    }

    function setupMutationObservers() {
        if (settings.modes.left === 'auto' || settings.modes.right === 'auto') {
            let observer = new MutationObserver(function(mutations) {
                let shouldCheck = false;
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' ||
                        mutation.type === 'attributes' &&
                        (mutation.attributeName === 'class' || mutation.attributeName === 'style')) {
                        shouldCheck = true;
                    }
                });

                if (shouldCheck) {
                    setTimeout(function() {
                        detectAndSetupPanels();

                        if (shouldAutoActivate()) {
                            if (settings.modes.left === 'auto' && isLeftPanelVisible() && !panelStates.left.expanded) {
                                expandPanel('left');
                            } else if (settings.modes.left === 'auto' && !isLeftPanelVisible() && panelStates.left.expanded) {
                                collapsePanel('left');
                            }

                            if (settings.modes.right === 'auto' && isRightPanelVisible() && !panelStates.right.expanded) {
                                expandPanel('right');
                            } else if (settings.modes.right === 'auto' && !isRightPanelVisible() && panelStates.right.expanded) {
                                collapsePanel('right');
                            }
                        }
                    }, 100);
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class', 'style']
            });

            observers.push(observer);
        }
    }

    function isLeftPanelVisible() {
        return panelStates.left.element &&
               isElementVisible(panelStates.left.element);
    }

    function isRightPanelVisible() {
        return panelStates.right.element &&
               isElementVisible(panelStates.right.element);
    }

    function isElementVisible(element) {
        if (!element) return false;
        let rect = element.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0 &&
               window.getComputedStyle(element).display !== 'none' &&
               window.getComputedStyle(element).visibility !== 'hidden';
    }

    function togglePanel(side) {
        if (panelStates[side].expanded) {
            collapsePanel(side);
        } else {
            expandPanel(side);
        }
    }

    function expandPanel(side) {
        if (!panelStates[side].element) {
            detectAndSetupPanels();
            if (!panelStates[side].element) return;
        }

        applyWidthWithSafetyCheck(side, panelStates[side].width);
        panelStates[side].expanded = true;

        showNotification(side === 'left' ? i18n.leftPanelExpanded : i18n.rightPanelExpanded);
    }

    function collapsePanel(side) {
        if (!panelStates[side].element) return;

        panelStates[side].element.style.width = '';
        panelStates[side].expanded = false;

        panelStates[side].element.classList.remove('expandwp-expanded');
    }

    function resetPanels() {
        collapsePanel('left');
        collapsePanel('right');

        ['left', 'right'].forEach(function(side) {
            panelStates[side].width = settings.defaultWidth;
            let storageKey = 'expandwp_width_' + editorType + '_' + side;
            localStorage.removeItem(storageKey);
        });

        showNotification(i18n.panelsReset);
    }

    function applyWidthWithSafetyCheck(side, targetWidth) {
        if (!panelStates[side].element) return;

        let viewport = window.innerWidth;
        let leftWidth = side === 'left' ? targetWidth : (panelStates.left.expanded ? panelStates.left.width : 0);
        let rightWidth = side === 'right' ? targetWidth : (panelStates.right.expanded ? panelStates.right.width : 0);

        let safeWidths = calculateSafeWidths(leftWidth, rightWidth, viewport);
        let finalWidth = side === 'left' ? safeWidths[0] : safeWidths[1];

        if (finalWidth !== targetWidth) {
            showNotification(i18n.canvasProtected);
        }

        panelStates[side].element.style.width = finalWidth + 'px';
        panelStates[side].width = finalWidth;
        panelStates[side].element.classList.add('expandwp-expanded');

        saveWidth(side, finalWidth);
    }

    function calculateSafeWidths(leftTarget, rightTarget, viewportWidth) {
        let availableWidth = viewportWidth - settings.gutters;
        let totalPanels = leftTarget + rightTarget;
        let remainingCanvas = availableWidth - totalPanels;

        if (remainingCanvas >= settings.minCanvas) {
            return [leftTarget, rightTarget];
        }

        let excess = totalPanels - (availableWidth - settings.minCanvas);

        if (totalPanels > 0) {
            let leftRatio = leftTarget / totalPanels;
            let rightRatio = rightTarget / totalPanels;

            let leftReduction = excess * leftRatio;
            let rightReduction = excess * rightRatio;

            let safeLeft = Math.max(0, leftTarget - leftReduction);
            let safeRight = Math.max(0, rightTarget - rightReduction);

            return [Math.floor(safeLeft), Math.floor(safeRight)];
        }

        return [0, 0];
    }

    function showNotification(message) {
        // 簡易的な通知表示（console.logで代替）
        console.log('ExpandWP: ' + message);

        // 実際の通知UI（オプション）
        if (typeof wp !== 'undefined' && wp.data && wp.data.dispatch) {
            try {
                wp.data.dispatch('core/notices').createInfoNotice(message, {
                    type: 'snackbar',
                    isDismissible: true
                });
            } catch (e) {
                // エラーは無視
            }
        }
    }

    // 初期化実行
    init();

})();