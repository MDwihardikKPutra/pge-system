<script>
// Vanilla JavaScript Project Select Component (Non-Alpine)
(function() {
    'use strict';
    
    const searchUrl = '{{ route("projects.search") }}';
    
    function initProjectSelect(container) {
        // Each instance has its own timeout and controller
        let searchTimeout = null;
        let fetchController = null;
        const input = container.querySelector('.project-select-input');
        const hidden = container.querySelector('.project-select-hidden');
        const dropdown = container.querySelector('.project-select-dropdown');
        const loading = container.querySelector('.project-select-loading');
        const empty = container.querySelector('.project-select-empty');
        const list = container.querySelector('.project-select-list');
        const clearBtn = container.querySelector('.project-select-clear');
        const parentFormName = container.dataset.parentForm;
        
        let selectedId = null;
        let selectedDisplay = '';
        let projects = [];
        let isInitialized = false;
        
        // Get Alpine parent form data
        function getAlpineParent() {
            if (typeof Alpine === 'undefined') return null;
            
            // Try to find closest element with x-data
            const alpineElement = container.closest('[x-data]');
            if (alpineElement && alpineElement.__x) {
                try {
                    const alpineData = Alpine.$data(alpineElement);
                    if (alpineData && alpineData.formData) return alpineData;
                } catch (e) {
                    // Silently fail if Alpine data not ready
                }
            }
            
            // Try to find parent with formData
            let parent = container.parentElement;
            while (parent) {
                if (parent.__x) {
                    try {
                        const data = Alpine.$data(parent);
                        if (data && data.formData) return data;
                    } catch (e) {
                        // Silently fail if Alpine data not ready
                    }
                }
                parent = parent.parentElement;
            }
            return null;
        }
        
        // Sync with Alpine parent
        function syncToAlpine() {
            const parent = getAlpineParent();
            if (parent && parent.formData) {
                parent.formData.project_id = selectedId || '';
            }
        }
        
        // Initialize from Alpine parent
        function initFromAlpine() {
            const parent = getAlpineParent();
            if (parent && parent.formData && parent.formData.project_id && !isInitialized) {
                selectedId = parent.formData.project_id;
                hidden.value = selectedId;
                if (selectedId) {
                    fetchProjectById(selectedId);
                }
                isInitialized = true;
            }
        }
        
        // Fetch project by ID
        function fetchProjectById(projectId) {
            if (!projectId) return;
            
            if (fetchController) {
                fetchController.abort();
            }
            
            fetchController = new AbortController();
            loading.classList.remove('hidden');
            dropdown.classList.remove('hidden');
            empty.classList.add('hidden');
            list.classList.add('hidden');
            
            fetch(`${searchUrl}?id=${encodeURIComponent(projectId)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: fetchController.signal
            })
            .then(response => response.json())
            .then(data => {
                if (data.projects && data.projects.length > 0) {
                    const project = data.projects[0];
                    selectedId = project.id;
                    selectedDisplay = project.display || `${project.name} (${project.code})`;
                    input.value = selectedDisplay;
                    hidden.value = selectedId;
                    syncToAlpine();
                    updateClearButton();
                }
                loading.classList.add('hidden');
                dropdown.classList.add('hidden');
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    console.error('Error fetching project:', error);
                }
                loading.classList.add('hidden');
                dropdown.classList.add('hidden');
            });
        }
        
        // Search projects
        function searchProjects(query) {
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            if (fetchController) {
                fetchController.abort();
            }
            
            searchTimeout = setTimeout(() => {
                if (query.length < 2 && query.length > 0) {
                    projects = [];
                    renderProjects();
                    return;
                }
                
                fetchController = new AbortController();
                loading.classList.remove('hidden');
                empty.classList.add('hidden');
                list.classList.add('hidden');
                dropdown.classList.remove('hidden');
                
                const url = query.length === 0 
                    ? `${searchUrl}?q=&limit=20`
                    : `${searchUrl}?q=${encodeURIComponent(query)}&limit=20`;
                
                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    signal: fetchController.signal
                })
                .then(response => response.json())
                .then(data => {
                    projects = data.projects || [];
                    renderProjects();
                    loading.classList.add('hidden');
                })
                .catch(error => {
                    if (error.name !== 'AbortError') {
                        console.error('Error searching projects:', error);
                    }
                    projects = [];
                    renderProjects();
                    loading.classList.add('hidden');
                });
            }, 300);
        }
        
        // Render projects list
        function renderProjects() {
            list.innerHTML = '';
            
            if (projects.length === 0) {
                if (input.value.length > 0) {
                    empty.classList.remove('hidden');
                } else {
                    empty.classList.add('hidden');
                }
                list.classList.add('hidden');
                return;
            }
            
            empty.classList.add('hidden');
            list.classList.remove('hidden');
            
            projects.forEach(project => {
                const li = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'w-full text-left px-4 py-2 hover:bg-blue-50 focus:bg-blue-50 focus:outline-none transition-colors' + 
                    (selectedId == project.id ? ' bg-blue-50' : '');
                
                button.innerHTML = `
                    <div class="font-medium text-gray-900">${project.name}</div>
                    <div class="text-xs text-gray-500">${project.code}</div>
                `;
                
                button.addEventListener('click', () => {
                    selectedId = project.id;
                    selectedDisplay = project.display || `${project.name} (${project.code})`;
                    input.value = selectedDisplay;
                    hidden.value = selectedId;
                    dropdown.classList.add('hidden');
                    syncToAlpine();
                    updateClearButton();
                });
                
                li.appendChild(button);
                list.appendChild(li);
            });
        }
        
        // Update clear button visibility
        function updateClearButton() {
            if (selectedId) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }
        
        // Clear selection
        function clearSelection() {
            selectedId = null;
            selectedDisplay = '';
            input.value = '';
            hidden.value = '';
            dropdown.classList.add('hidden');
            syncToAlpine();
            updateClearButton();
            searchProjects('');
        }
        
        // Event listeners
        input.addEventListener('input', (e) => {
            searchProjects(e.target.value);
            if (!e.target.value) {
                selectedId = null;
                hidden.value = '';
                syncToAlpine();
                updateClearButton();
            }
        });
        
        input.addEventListener('focus', () => {
            if (projects.length > 0 || input.value.length > 0) {
                dropdown.classList.remove('hidden');
            }
        });
        
        input.addEventListener('blur', () => {
            setTimeout(() => {
                dropdown.classList.add('hidden');
            }, 200);
        });
        
        clearBtn.addEventListener('click', (e) => {
            e.preventDefault();
            clearSelection();
        });
        
        // Initialize from Alpine parent after a delay
        setTimeout(() => {
            initFromAlpine();
        }, 100);
        
        // Watch for Alpine parent changes
        if (typeof Alpine !== 'undefined') {
            const parent = getAlpineParent();
            if (parent && parent.formData) {
                // Watch for changes in parent formData.project_id
                const originalProjectId = parent.formData.project_id;
                setInterval(() => {
                    if (parent.formData && parent.formData.project_id !== selectedId) {
                        if (parent.formData.project_id && parent.formData.project_id !== selectedId) {
                            selectedId = parent.formData.project_id;
                            hidden.value = selectedId;
                            fetchProjectById(selectedId);
                        } else if (!parent.formData.project_id && selectedId) {
                            clearSelection();
                        }
                    }
                }, 200);
            }
        }
    }
    
    // Initialize all project selects when DOM is ready
    function initAllProjectSelects() {
        document.querySelectorAll('.project-select-container:not([data-initialized])').forEach(container => {
            container.setAttribute('data-initialized', 'true');
            initProjectSelect(container);
        });
    }
    
    // Wait for Alpine to be ready before initializing
    function waitForAlpine(callback) {
        if (typeof Alpine !== 'undefined' && Alpine.version) {
            // Alpine is already loaded
            setTimeout(callback, 100);
        } else {
            // Wait for Alpine to initialize
            document.addEventListener('alpine:init', () => {
                setTimeout(callback, 100);
            }, { once: true });
        }
    }
    
    // Initialize on DOM ready and Alpine ready
    function initialize() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                waitForAlpine(initAllProjectSelects);
            });
        } else {
            waitForAlpine(initAllProjectSelects);
        }
    }
    
    initialize();
    
    // Re-initialize when Alpine updates (for modals)
    if (typeof Alpine !== 'undefined') {
        document.addEventListener('alpine:init', () => {
            setTimeout(initAllProjectSelects, 200);
        });
        
        // Watch for modal openings - debounced to avoid excessive calls
        // Only observe specific containers, not entire body
        let observerTimeout = null;
        const modalContainer = document.querySelector('.py-4[x-data]') || document.body;
        const observer = new MutationObserver((mutations) => {
            // Only process if new nodes were added
            const hasNewNodes = mutations.some(mutation => mutation.addedNodes.length > 0);
            if (!hasNewNodes) return;
            
            if (observerTimeout) clearTimeout(observerTimeout);
            observerTimeout = setTimeout(() => {
                document.querySelectorAll('.project-select-container:not([data-initialized])').forEach(container => {
                    container.setAttribute('data-initialized', 'true');
                    initProjectSelect(container);
                });
            }, 200);
        });
        
        observer.observe(modalContainer, {
            childList: true,
            subtree: true
        });
    }
})();
</script>

