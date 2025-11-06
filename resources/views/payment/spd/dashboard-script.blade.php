<script>
// Register Alpine data component for Dashboard - must be defined before Alpine scans DOM
(function() {
    function registerSpdForm() {
        if (typeof Alpine === 'undefined') {
            console.error('Alpine.js not loaded');
            return;
        }
        
        Alpine.data('spdForm', () => ({
        showModal: false,
        showPreviewModal: false,
        editMode: false,
        modalTitle: 'Ajukan SPD Baru',
        previewData: null,
        formData: {
            id: null,
            project_id: '',
            destination: '',
            departure_date: '',
            return_date: '',
            purpose: '',
            costs: [{ name: '', description: '', amount: 0 }],
            notes: '',
            documents: null,
        },
        errors: {},
        projects: @json($projects),
        totalCostDisplay: '0',
        
        init() {
            this.calculateTotal();
            // Expose to global scope for dashboard access
            window.spdFormComponent = this;
            
            // Listen for open modal event from dashboard
            window.addEventListener('open-spd-modal', () => {
                this.openCreateModal();
            });
        },
        
        calculateTotal() {
            let total = 0;
            if (this.formData.costs && this.formData.costs.length > 0) {
                this.formData.costs.forEach(cost => {
                    total += parseFloat(cost.amount || 0);
                });
            }
            this.totalCostDisplay = total.toLocaleString('id-ID');
        },
        
        addCostRow() {
            this.formData.costs.push({ name: '', description: '', amount: 0 });
        },
        
        removeCostRow(index) {
            if (this.formData.costs.length <= 1) {
                alert('Minimal harus ada 1 item biaya!');
                return;
            }
            this.formData.costs.splice(index, 1);
            this.calculateTotal();
        },
        
        handleFileChange(event) {
            this.formData.documents = event.target.files;
        },
        
        openCreateModal() {
            this.resetForm();
            this.editMode = false;
            this.modalTitle = 'Ajukan SPD Baru';
            this.showModal = true;
        },
        
        resetForm() {
            this.formData = {
                id: null,
                project_id: '',
                destination: '',
                departure_date: '',
                return_date: '',
                purpose: '',
                costs: [{ name: '', description: '', amount: 0 }],
                notes: '',
                documents: null,
            };
            this.errors = {};
            this.totalCostDisplay = '0';
        },
        
        async submitForm() {
            this.errors = {};
            
            // Sync project_id from vanilla JS project-select to Alpine formData
            const projectSelectHidden = document.querySelector('[data-parent-form="spdForm"] .project-select-hidden');
            if (projectSelectHidden && projectSelectHidden.value) {
                this.formData.project_id = projectSelectHidden.value;
            }
            
            const routePrefix = 'user';
            const url = this.editMode 
                ? `/${routePrefix}/spd/${this.formData.id}`
                : `/${routePrefix}/spd`;
            const method = this.editMode ? 'PUT' : 'POST';
            
            try {
                const formData = new FormData();
                
                // Append basic fields
                if (this.formData.id) formData.append('id', this.formData.id);
                if (this.formData.project_id) formData.append('project_id', this.formData.project_id);
                formData.append('destination', this.formData.destination);
                formData.append('departure_date', this.formData.departure_date);
                formData.append('return_date', this.formData.return_date);
                formData.append('purpose', this.formData.purpose);
                if (this.formData.notes) formData.append('notes', this.formData.notes);
                
                // Append costs array
                if (this.formData.costs && this.formData.costs.length > 0) {
                    this.formData.costs.forEach((cost, index) => {
                        formData.append(`cost_name[${index}]`, cost.name || '');
                        formData.append(`cost_description[${index}]`, cost.description || '');
                        formData.append(`cost_amount[${index}]`, cost.amount || 0);
                    });
                }
                
                // Append documents if any
                if (this.formData.documents && this.formData.documents.length > 0) {
                    for (let i = 0; i < this.formData.documents.length; i++) {
                        formData.append('documents[]', this.formData.documents[i]);
                    }
                }
                
                formData.append('_method', method === 'PUT' ? 'PUT' : 'POST');
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            }
        },
        
        closeModal() {
            this.showModal = false;
            this.resetForm();
        }
    }));
    }
    
    // Multiple strategies to ensure registration
    if (typeof Alpine !== 'undefined' && Alpine.version) {
        registerSpdForm();
    } else {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    if (typeof Alpine !== 'undefined') {
                        registerSpdForm();
                    }
                }, 100);
            });
        } else {
            setTimeout(() => {
                if (typeof Alpine !== 'undefined') {
                    registerSpdForm();
                }
            }, 100);
        }
    }
    
    document.addEventListener('alpine:init', registerSpdForm);
})();
</script>
