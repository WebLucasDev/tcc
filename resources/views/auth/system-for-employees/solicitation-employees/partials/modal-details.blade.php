<!-- Modal de Detalhes da Solicitação -->
<div id="modalDetails" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-[var(--color-background)] rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header do Modal -->
        <div class="bg-[var(--color-main)] text-white px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fa-solid fa-circle-info mr-2"></i>
                    Detalhes da Solicitação
                </h3>
                <button onclick="closeDetailsModal()" class="text-white hover:text-gray-200 transition-colors">
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Corpo do Modal -->
        <div class="p-6 space-y-6" id="detailsContent">
            <!-- Informações Básicas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-[var(--color-main)]/5 p-4 rounded-lg border border-[var(--color-text)]/10">
                    <p class="text-xs text-[var(--color-text)]/60 uppercase font-semibold mb-1">Data do Registro</p>
                    <p class="text-lg font-bold text-[var(--color-text)]" id="detail-date">-</p>
                </div>

                <div class="bg-[var(--color-main)]/5 p-4 rounded-lg border border-[var(--color-text)]/10">
                    <p class="text-xs text-[var(--color-text)]/60 uppercase font-semibold mb-1">Status</p>
                    <p class="text-lg font-bold" id="detail-status">-</p>
                </div>
            </div>

            <!-- Horários -->
            <div class="border-t border-[var(--color-text)]/10 pt-4">
                <h4 class="font-bold text-[var(--color-text)] mb-4 flex items-center">
                    <i class="fa-solid fa-clock mr-2 text-[var(--color-main)]"></i>
                    Horários
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-[var(--color-text)]/5 p-4 rounded-lg border border-[var(--color-text)]/10">
                        <p class="text-xs text-[var(--color-text)]/60 uppercase font-semibold mb-2">Horário Antigo</p>
                        <p class="text-base text-[var(--color-text)]" id="detail-old-time">-</p>
                    </div>

                    <div class="bg-[var(--color-main)]/10 p-4 rounded-lg border-2 border-[var(--color-main)]/30">
                        <p class="text-xs text-[var(--color-text)]/60 uppercase font-semibold mb-2">Horário Solicitado</p>
                        <p class="text-base font-bold text-[var(--color-main)]" id="detail-new-time">-</p>
                    </div>
                </div>
            </div>

            <!-- Motivo -->
            <div class="border-t border-[var(--color-text)]/10 pt-4">
                <h4 class="font-bold text-[var(--color-text)] mb-2 flex items-center">
                    <i class="fa-solid fa-comment-dots mr-2 text-[var(--color-main)]"></i>
                    Motivo da Solicitação
                </h4>
                <div class="bg-yellow-500/10 p-4 rounded-lg border border-yellow-500/30">
                    <p class="text-[var(--color-text)] whitespace-pre-wrap" id="detail-reason">-</p>
                </div>
            </div>

            <!-- Comentário do Admin (se houver) -->
            <div id="admin-comment-section" class="border-t border-[var(--color-text)]/10 pt-4 hidden">
                <h4 class="font-bold text-[var(--color-text)] mb-2 flex items-center">
                    <i class="fa-solid fa-user-tie mr-2 text-[var(--color-main)]"></i>
                    Comentário do Administrador
                </h4>
                <div class="bg-blue-500/10 p-4 rounded-lg border border-blue-500/30">
                    <p class="text-[var(--color-text)] whitespace-pre-wrap" id="detail-admin-comment">-</p>
                </div>
            </div>

            <!-- Data de Criação -->
            <div class="border-t border-[var(--color-text)]/10 pt-4">
                <p class="text-sm text-[var(--color-text)]/60">
                    <i class="fa-solid fa-calendar-plus mr-1"></i>
                    Solicitação criada em: <span class="font-semibold text-[var(--color-text)]" id="detail-created-at">-</span>
                </p>
            </div>
        </div>

        <!-- Footer do Modal -->
        <div class="bg-[var(--color-text)]/5 border-t border-[var(--color-text)]/10 px-6 py-4 rounded-b-xl flex justify-end">
            <button
                onclick="closeDetailsModal()"
                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                <i class="fa-solid fa-times mr-2"></i>
                Fechar
            </button>
        </div>
    </div>
</div>
