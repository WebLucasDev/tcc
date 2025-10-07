<!-- Modal de Cancelamento -->
<div id="modalCancel" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-[var(--color-background)] rounded-xl shadow-2xl max-w-md w-full">
        <!-- Header do Modal -->
        <div class="bg-orange-500 text-white px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fa-solid fa-ban mr-2"></i>
                    Cancelar Solicitação
                </h3>
                <button onclick="closeCancelModal()" class="text-white hover:text-gray-200 transition-colors">
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Corpo do Modal -->
        <form id="formCancel" method="POST">
            @csrf
            @method('PATCH')

            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-500/10 rounded-full flex items-center justify-center border border-orange-500/30">
                            <i class="fa-solid fa-exclamation-triangle text-orange-500 text-xl"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-[var(--color-text)] font-semibold mb-2">
                            Tem certeza que deseja cancelar esta solicitação?
                        </p>
                        <p class="text-[var(--color-text)]/70 text-sm">
                            Esta ação não poderá ser desfeita. A solicitação será marcada como cancelada e você precisará criar uma nova se desejar solicitar alteração novamente.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer do Modal -->
            <div class="bg-[var(--color-text)]/5 border-t border-[var(--color-text)]/10 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                <button
                    type="button"
                    onclick="closeCancelModal()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors shadow-md">
                    <i class="fa-solid fa-times mr-2"></i>
                    Não, voltar
                </button>
                <button
                    type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors shadow-md">
                    <i class="fa-solid fa-ban mr-2"></i>
                    Sim, cancelar
                </button>
            </div>
        </form>
    </div>
</div>
