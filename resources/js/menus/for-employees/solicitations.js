/**
 * solicitations.js
 * Gerenciamento de solicitações de alteração de ponto
 */

// Função para mostrar detalhes da solicitação
async function showDetails(id) {
    try {
        const response = await fetch(`/sistema-colaboradores/solicitations/${id}`);
        const data = await response.json();

        // Preencher informações básicas
        document.getElementById('detail-date').textContent =
            new Date(data.time_tracking.date).toLocaleDateString('pt-BR');

        // Status com badge colorido
        const statusBadge = `
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ${getStatusColor(data.status)}">
                <i class="${getStatusIcon(data.status)} mr-1"></i>
                ${getStatusLabel(data.status)}
            </span>
        `;
        document.getElementById('detail-status').innerHTML = statusBadge;

        // Horários antigos
        if (data.old_time_start && data.old_time_finish) {
            const oldStart = new Date(data.old_time_start).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            const oldFinish = new Date(data.old_time_finish).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('detail-old-time').textContent = `${oldStart} - ${oldFinish}`;
        } else {
            document.getElementById('detail-old-time').textContent = 'Não informado';
        }

        // Novos horários
        const newStart = new Date(data.new_time_start).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        const newFinish = new Date(data.new_time_finish).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        document.getElementById('detail-new-time').textContent = `${newStart} - ${newFinish}`;

        // Motivo
        document.getElementById('detail-reason').textContent = data.reason || '-';

        // Comentário do admin (se houver)
        const adminCommentSection = document.getElementById('admin-comment-section');
        if (data.admin_comment) {
            document.getElementById('detail-admin-comment').textContent = data.admin_comment;
            adminCommentSection.classList.remove('hidden');
        } else {
            adminCommentSection.classList.add('hidden');
        }

        // Data de criação
        document.getElementById('detail-created-at').textContent =
            new Date(data.created_at).toLocaleString('pt-BR');

        // Mostrar modal
        document.getElementById('modalDetails').classList.remove('hidden');
        document.getElementById('modalDetails').classList.add('flex');

    } catch (error) {
        console.error('Erro ao carregar detalhes:', error);
        alert('Erro ao carregar os detalhes da solicitação. Tente novamente.');
    }
}

// Função para fechar modal de detalhes
function closeDetailsModal() {
    document.getElementById('modalDetails').classList.add('hidden');
    document.getElementById('modalDetails').classList.remove('flex');
}

// Função para abrir modal de cancelamento
function openCancelModal(id) {
    const form = document.getElementById('formCancel');
    form.action = `/sistema-colaboradores/solicitations/${id}/cancel`;

    // Resetar o botão de submit (caso tenha sido desabilitado antes)
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        submitBtn.style.pointerEvents = '';
        submitBtn.innerHTML = `
            <i class="fa-solid fa-ban mr-2"></i>
            Sim, cancelar
        `;
    }

    document.getElementById('modalCancel').classList.remove('hidden');
    document.getElementById('modalCancel').classList.add('flex');
}

// Função para fechar modal de cancelamento
function closeCancelModal() {
    document.getElementById('modalCancel').classList.add('hidden');
    document.getElementById('modalCancel').classList.remove('flex');
}

// Funções auxiliares para status
function getStatusColor(status) {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

function getStatusIcon(status) {
    const icons = {
        'pending': 'fas fa-clock',
        'approved': 'fas fa-check',
        'rejected': 'fas fa-times',
        'cancelled': 'fas fa-ban'
    };
    return icons[status] || 'fas fa-question';
}

function getStatusLabel(status) {
    const labels = {
        'pending': 'Pendente',
        'approved': 'Aprovada',
        'rejected': 'Rejeitada',
        'cancelled': 'Cancelada'
    };
    return labels[status] || status;
}

// Fechar modais ao clicar fora
document.addEventListener('DOMContentLoaded', function() {
    const modalDetails = document.getElementById('modalDetails');
    const modalCancel = document.getElementById('modalCancel');

    if (modalDetails) {
        modalDetails.addEventListener('click', function(e) {
            if (e.target === modalDetails) {
                closeDetailsModal();
            }
        });
    }

    if (modalCancel) {
        modalCancel.addEventListener('click', function(e) {
            if (e.target === modalCancel) {
                closeCancelModal();
            }
        });
    }

    // Fechar modais com tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetailsModal();
            closeCancelModal();
        }
    });

    // ==================== PREVENIR MÚLTIPLAS SUBMISSÕES NO MODAL DE CANCELAMENTO ====================
    const cancelForm = document.getElementById('formCancel');
    
    if (cancelForm) {
        cancelForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (!submitBtn) return;
            
            // Verificar se o botão já está desabilitado
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }
            
            // Desabilitar o botão e mudar o visual
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.style.pointerEvents = 'none';
            
            // Mostrar indicador de carregamento
            submitBtn.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                Cancelando...
            `;
        });
    }
});

// Expor funções globalmente
window.showDetails = showDetails;
window.closeDetailsModal = closeDetailsModal;
window.openCancelModal = openCancelModal;
window.closeCancelModal = closeCancelModal;
